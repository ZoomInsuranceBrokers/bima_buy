<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\Document;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\LeadCreated;
use App\Models\Quote;
use App\Models\ZonalManager;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
use App\Events\NotificationSent;

class UserController extends Controller
{

    public function index()
    {
        $userId = Auth::user()->id;

        // Fetching both lead details and pending lead details
        $lead_details = Lead::where('is_issue', 1)
            ->where('user_id', $userId)
            ->select('id', 'first_name', 'last_name')
            ->get();

        // Fetching pending leads with related quotes
        $pending_lead_details = Lead::with([
            'quotes' => function ($query) {
                $query->select('id', 'lead_id','is_accepted', 'price','updated_at');
            }
        ])
            ->where('final_status', 0)
            ->where('user_id', $userId)
            ->select('id', 'first_name', 'last_name', 'mobile_no', 'is_payment_complete', 'is_zm_verified', 'is_retail_verified', 'final_status', 'is_cancel', 'is_accepted','updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // return $pending_lead_details;

    
        return view('userpages.userdashboard', compact('lead_details', 'pending_lead_details'));
    }

    public function createLead()
    {
        return view('userpages.createLead');
    }


    public function storeLead(Request $request)
    {
        // Validate the input data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'vehicle_number' => 'required|string|max:255',
            'mobile_number' => 'required|numeric|digits:10',
            'documents.*.name' => 'nullable|required_without:documents.*.file,null|string|max:255', // Name required only when file is uploaded
            'documents.*.file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        // Save the lead information
        $lead = Lead::create([
            'user_id' => Auth::user()->id,
            'zm_id' => Auth::user()->zm_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'mobile_no' => $request->mobile_number,
            'vehicle_number' => $request->vehicle_number,
        ]);

        // Save document names and paths
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                if (isset($document['file'])) {
                    // Only process the document if file is present
                    $file = $document['file'];
                    $filePath = $file->store('documents/' . $lead->id, 'public');

                    // Save both document name and file path in the Document model
                    Document::create([
                        'lead_id' => $lead->id,
                        'document_name' => $document['name'], // Save document name
                        'file_path' => $filePath, // Save file path
                    ]);
                }
            }
        }

        $user_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;


        $notification=Notification::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => ZonalManager::where('id', Auth::user()->zm_id)->first()->user_id,
            'message' => 'Lead created by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);
        // event(new LeadCreated($user_name));

        broadcast(new NotificationSent($notification));

        return redirect()->back()->with('success', 'Lead created successfully with Trackid: ' . $lead->id);
    }

    public function showFoamToUpdateLead($id)
    {
        $lead = Lead::with('documents')->find(Crypt::decrypt($id));

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        return view('userpages.updateLead', compact('lead'));
    }

    public function updateLead(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'vehicle_number' => 'required|string|max:255',
            'mobile_number' => 'required|numeric|digits:10',
            'documents.*.name' => 'nullable|required_without:documents.*.file,null|string|max:255',
            'documents.*.file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $lead = Lead::find($id);

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        // Update the lead details
        $lead->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'mobile_no' => $request->mobile_number,
            'vehicle_number' => $request->vehicle_number,
            'is_issue' => 0,
        ]);

        // Update documents if necessary
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $document) {
                if (isset($document['file'])) {
                    // Process new document upload
                    $file = $document['file'];
                    $filePath = $file->store('documents/' . $lead->id, 'public');

                    // Update document details or add new document
                    $existingDocument = $lead->documents[$index] ?? null;
                    if ($existingDocument) {
                        $existingDocument->update([
                            'document_name' => $document['name'],
                            'file_path' => $filePath,
                        ]);
                    } else {
                        Document::create([
                            'lead_id' => $lead->id,
                            'document_name' => $document['name'],
                            'file_path' => $filePath,
                        ]);
                    }
                }
            }
        }

        $notification=Notification::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $lead->is_zm_verified ? 4 : ZonalManager::where('id', Auth::user()->zm_id)->first()->user_id,
            'message' => 'Lead updated by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);

        broadcast(new NotificationSent($notification));

        return redirect()->route('user.dashboard')->with('success', $lead->first_name . ' ' . $lead->last_name . ' details updated successfully');
    }


    public function getQuoteDetails($leadId)
    {
        $quotes = Quote::where('lead_id', $leadId)->get();

        return response()->json([
            'quotes' => $quotes
        ]);
    }


    public function submitQuoteAction()
    {
        $quoteId = request('quote_id');
        $action = request('action');

        $quote = Quote::find($quoteId);

        if (!$quote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quote not found'
            ]);
        }

        switch ($action) {
            case 'accept':
                $quote->update([
                    'is_accepted' => true,
                ]);
                Lead::where('id', $quote->lead_id)->update([
                    'is_accepted' => 1,
                ]);

                //////////send notification to zm ////////////
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', Lead::find($quote->lead_id)->zm_id)->first()->user_id,
                    'message' => 'The quote has been accepted for Lead ID ' . $quote->lead_id,
                ]);
                broadcast(new NotificationSent($notification));

                //////////send notification to retail team////////////
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 4,
                    'message' => 'The quote has been accepted for Lead ID ' . $quote->lead_id,
                ]);
                broadcast(new NotificationSent($notification));
                break;
            case 'ask_for_another':
                 //////////send notification to zm ////////////
                 $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', Lead::find($quote->lead_id)->zm_id)->first()->user_id,
                    'message' => 'Use Ask another quote for Lead id ' . $quote->lead_id,
                ]);
                broadcast(new NotificationSent($notification));
                //////////send notification to retail team////////////
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 4,
                    'message' => 'Use Ask another quote for Lead id ' . $quote->lead_id,
                ]);
                broadcast(new NotificationSent($notification));
                break;

            case 'cancel':
                Lead::where('id', $quote->lead_id)->update([
                    'is_cancel' => 1,
                ]);
                 //////////send notification to zm ////////////
                 $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' =>ZonalManager::where('id', Lead::find($quote->lead_id)->zm_id)->first()->user_id,
                    'message' => 'Lead'.$quote->lead_id.''.'has been cancelled by Rc',
                ]);
                broadcast(new NotificationSent($notification));
                //////////send notification to retail team////////////
                $notification=Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 4,
                    'message' => 'Lead Id '.$quote->lead_id.''.' has been cancelled by Rc',
                ]);
                broadcast(new NotificationSent($notification));
                break;
            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid action'
                ]);
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Quote action updated successfully'
        ]);
    }

    public function completedLead()
    {
        $completedLeads = Lead::with([
            'quotes' => function ($query) {
                $query->select('lead_id', 'price', 'updated_at')
                    ->where('is_accepted', 1);
            }
        ])
            ->select('id', 'first_name', 'last_name', 'mobile_no', 'updated_at')
            ->where('user_id', Auth::user()->id)
            ->where('final_status', 1)
            ->get();

        // return $completedLeads;

        return view('userpages.completedLead', compact('completedLeads'));
    }


    public function policyCopy()
    {
        return view('userpages.policyCopy');
    }
    public function wallet()
    {
        return view('userpages.wallet');
    }
}