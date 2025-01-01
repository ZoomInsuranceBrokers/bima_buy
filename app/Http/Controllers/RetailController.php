<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\PolicyCopy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\LeadCreated;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\ZonalManager;
use App\Events\NotificationSent;
use App\Events\UpdateLead;
use Illuminate\Support\Facades\Crypt;

class RetailController extends Controller
{

    public function index()
    {
        $pendingLeads = Lead::with([
            'quotes' => function ($query) {
                $query->select('id', 'updated_at', 'is_accepted', 'price', 'lead_id');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->where('is_zm_verified', 1)
            // ->where('is_retail_verified', 0)
            // ->where('user_id', Auth::user()->id)
            ->where('final_status', 0)
            ->where('is_cancel', 0)
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'is_issue', 'is_retail_verified', 'is_cancel', 'is_accepted', 'is_payment_complete', 'final_status', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // return $pendingLeads;

        return view('retailpages.retaildashboard', compact('pendingLeads'));

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
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' .This Message For Lead ID ' . $lead->id . '.',
                ]);
                
                broadcast(new NotificationSent($notification));

                $update_message = [
                    'lead_id' =>Crypt::encrypt($lead->id),
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' .This Message For Tracking ID ' . $lead->id . '.',
                ];
                broadcast(new UpdateLead($update_message));
             

                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => $request->input('message') . ' .This Message For Lead ID ' . $lead->id . '.',
                ]);
                broadcast(new NotificationSent($notification));

                break;
            case 'verified':
                $lead->is_retail_verified = true;
                break;
            case 'cancel':
                $lead->is_cancel = true;
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Lead ID ' . $lead->id . ' has been cancelled by Retail Team.',
                ]);
                broadcast(new NotificationSent($notification));

                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Lead ID ' . $lead->id . ' has been cancelled by Reatil Team.',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid action'], 400);
        }

        $lead->save();

        return response()->json(['success' => true, 'message' => 'Action processed successfully']);
    }

    public function getQuotes($leadId)
    {
        $quotes = Quote::where('lead_id', $leadId)->get();
        return response()->json([
            'quotes' => $quotes->map(function ($quote) {
                return [
                    'id' => $quote->id,
                    'quote' => $quote->quote_name,
                    'price' => $quote->price,
                    'description' => $quote->description,
                    'is_accepted' => $quote->is_accepted,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'quotes' => 'required|array',
            'quotes.*.quote_name' => 'required|string',
            'quotes.*.features' => 'required|array',
            'quotes.*.features.*' => 'string',
            // 'quotes.*.prices' => 'required|array',
            'quotes.*.prices.*' => 'numeric',
        ]);

        foreach ($request->quotes as $quoteData) {
            $quote = Quote::create([
                'lead_id' => $request->lead_id,
                'quote_name' => $quoteData['quote_name'],
                'price' => $quoteData['price'],
                'description' => $quoteData['features'],
            ]);
        }

        $notification = Notification::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => ZonalManager::where('id', Lead::find($request->lead_id)->zm_id)->first()->user_id,
            'message' => 'Quote is sending for Lead ID ' . $request->lead_id . '.',
        ]);

        broadcast(new NotificationSent($notification));

        $notification = Notification::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => Lead::find($request->lead_id)->user_id,
            'message' => 'Quote is sending for Lead ID ' . $request->lead_id . '.',
        ]);

        broadcast(new NotificationSent($notification));

        return response()->json(['message' => 'Quotes added successfully!']);
    }

    public function upadtePaymentStatus($id, Request $request)
    {
        $lead = Lead::find($id);
        $action = $request->input('action');

        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        switch ($action) {
            case 'complete':
                $lead->is_payment_complete = true;
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Payment is completed for Lead ID ' . $lead->id . '.',
                ]);
                broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Payment is completed for Lead ID ' . $lead->id . '.',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            case 'notify':
                //////i write code send notification///////
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Payment is pending for Lead ID ' . $lead->id . '.',
                ]);
                broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Payment is pending for Lead ID ' . $lead->id . '.',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            case 'cancel':
                $lead->is_cancel = true;
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Lead ID ' . $lead->id . ' has been cancelled by Retail Team.',
                ]);
                broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Lead ID ' . $lead->id . ' has been cancelled by Retail Team.',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid action'], 400);
        }
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Payment status updated successfully']);
    }


    public function uploadPolicy($id, Request $request)
    {
        $request->validate([
            'policyCopy' => 'required|file|mimes:pdf,jpeg,jpg,png,gif,bmp,tiff',
        ]);

        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        $path = $request->file('policyCopy')->store('policies');

        $document = PolicyCopy::create([
            'lead_id' => $id,
            'user_id' => Auth::user()->id,
            'zm_id' => $lead->zm_id,
            'path' => $path,
        ]);
        $lead->final_status = true;
        $lead->save();

        $notification = Notification::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $lead->user_id,
            'message' => 'Policy is uploaded for Lead ID ' . $lead->id . '.',
        ]);
        broadcast(new NotificationSent($notification));
        $notification = Notification::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
            'message' => 'Policy is uploaded for Lead ID ' . $lead->id . '.',
        ]);
        broadcast(new NotificationSent($notification));
        return response()->json(['success' => true, 'message' => 'Policy uploaded successfully']);
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
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->where('final_status', 1)
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // return $completedLeads;
        return view('retailpages.completedleads', compact('completedLeads'));
    }
}