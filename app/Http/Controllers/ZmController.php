<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\PolicyCopy;
use App\Models\ZonalManager;
use Illuminate\Support\Facades\Storage;
use App\Events\NotificationSent;
use App\Events\UpdateLead;


class ZmController extends Controller
{
    public function index()
    {
        $leads = Lead::with([
            'quotes' => function ($query) {
                $query->select('id', 'lead_id', 'is_accepted', 'price','updated_at');  
            },
            'user' => function ($query) {
                $query->select('id', 'first_name','mobile' ,'last_name'); 
            }
        ])
            ->where('zm_id', Auth::user()->zm_id)
            ->where('is_cancel',0)
            ->select(
                'id','user_id','first_name','last_name','is_issue','is_zm_verified','is_accepted','is_retail_verified','is_cancel','is_payment_complete','final_status','updated_at'
            )
            ->orderBy('updated_at', 'desc')
            ->get();

        //  return $leads;

        return view('zmpages.zmdashboard', compact('leads'));
    }


    public function getLeadDetails($id)
    {
        $lead = Lead::select('id', 'first_name', 'last_name', 'gender', 'date_of_birth', 'mobile_no', 'vehicle_number')
            ->with(['documents:id,lead_id,document_name,file_path'])
            ->find($id);


        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        return response()->json([
            'success' => true,
            'lead' => $lead
        ]);
    }

    public function postLeadAction($id, Request $request)
    {
        $action = $request->input('action');
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        switch ($action) {
            case 'insufficient_details':
                $lead->is_issue = true;
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' .This Message For Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));

                $update_message = [
                    'lead_id' =>Crypt::encrypt($lead->id),
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' .This Message For Tracking ID ' . $lead->id . '.',
                ];
                broadcast(new UpdateLead($update_message));
                break;
            case 'verified':
                $lead->is_zm_verified = true;
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 4,
                    'message' => 'Please send a quote for Lead ID ' . $lead->id . '.',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            case 'cancel':
                $lead->is_cancel = true;
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Lead ID ' . $lead->id . ' has been cancelled by Zonal Manger.',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid action'], 400);
        }

        // $lead->save();

        return response()->json(['success' => true, 'message' => 'Action processed successfully']);
    }


    public function policyCopy()
    {
        return view('zmpages.policycopy');
    }

    public function getPolicyCopyDetails(Request $request)
    {
        $trackingId = $request->input('tracking_id');
        $mobileNo = $request->input('mobile_no');

        // Validate input
        if (empty($trackingId) && empty($mobileNo)) {
            return response()->json(['success' => false, 'message' => 'Please provide Tracking ID or Mobile No.'], 400);
        }

        // Search for policy by tracking ID or mobile number
        $policy = PolicyCopy::query()
            ->when($trackingId, function ($query) use ($trackingId) {
                $query->where('lead_id', $trackingId);
            })
            ->when($mobileNo, function ($query) use ($mobileNo) {
                $query->whereHas('lead', function ($query) use ($mobileNo) {
                    $query->where('mobile_no', $mobileNo);
                });
            })
            ->with('lead')
            ->first();

        if ($policy) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $policy->id,
                    'tracking_id' => $policy->lead_id,
                    'mobile_no' => $policy->lead->mobile_no,
                    'name' => $policy->lead->first_name . ' ' . $policy->lead->last_name,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Policy not found.']);
    }

    public function download(Request $request)
    {
        $policyId = $request->query('policy_id');

        // Validate the policy ID
        if (!$policyId) {
            return response()->json(['success' => false, 'message' => 'Policy ID is required.'], 400);
        }

        // Find the policy
        $policy = PolicyCopy::find($policyId);

        if (!$policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        // Get the file path from the `path` field
        $filePath = $policy->path;

        // Check if the file exists
        if (!Storage::exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        // Return the file for download
        return Storage::download($filePath);
    }

    public function completedLeads()
    {
        $completedLeads = Lead::with([
            'quotes' => function ($query) {
                $query->select('id', 'updated_at', 'lead_id', 'price')
                    ->where('is_accepted', 1);
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }
        ])
            ->where('final_status', 1)
            ->where('zm_id', ZonalManager::where('user_id', Auth::user()->id)->first()->id)
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // return $completedLeads;
        return view('zmpages.completedleads', compact('completedLeads'));
    }

}