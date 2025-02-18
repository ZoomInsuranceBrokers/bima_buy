<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\Document;
use App\Models\RetailUser;
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
                $query->select('id', 'lead_id', 'is_accepted', 'price', 'updated_at');
            }
        ])
            ->where('final_status', 0)
            ->where('user_id', $userId)
            ->where('is_cancel', 0)
            ->select('id', 'first_name', 'last_name', 'mobile_no', 'payment_receipt', 'is_payment_complete', 'is_zm_verified', 'is_retail_verified', 'final_status', 'is_cancel', 'is_accepted', 'payment_link', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // return $pending_lead_details;

        // return $lead_details;


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
            'vehicle_type' => 'required|in:Motorcycle,Private Car,Commercial Vehicle',
            'policy_type' => 'required|in:New,Fresh,Renewal',
            'email' => 'email',
            'claim_status' => 'required|in:yes,no',
            'vehicle_number' => 'required|string|max:255',
            'mobile_number' => 'required|numeric|digits:10',
            'documents.*.name' => 'required_with:documents.*.file,null|string|max:255',
            'documents.*.file' => 'file|mimes:jpeg,jpg,png,pdf|max:20480',
        ]);



        // Save the lead information
        $lead = Lead::create([
            'user_id' => Auth::user()->id,
            'zm_id' => Auth::user()->zm_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'policy_type' => $request->policy_type,
            'email' => $request->email,
            'claim_status' => $request->claim_status,
            'gender' => $request->gender,
            'vehicle_type' => $request->vehicle_type,
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


        if ($request->filled('comments')) {

            $notification = Notification::create([
                'lead_id' => $lead->id,
                'sender_id' => Auth::user()->id,
                'receiver_id' => ZonalManager::where('id', Auth::user()->zm_id)->first()->user_id,
                'message' => $request->comments,
            ]);

            $notification = Notification::create([
                'lead_id' => $lead->id,
                'sender_id' => Auth::user()->id,
                'receiver_id' => 2,
                'message' => $request->comments,
            ]);
        }

        // $user_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;


        $notification = Notification::create([
            'lead_id' => $lead->id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => ZonalManager::where('id', Auth::user()->zm_id)->first()->user_id,
            'message' => 'Lead created by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);
        // event(new LeadCreated($user_name));

        // broadcast(new NotificationSent($notification));

        return redirect()->route('user.dashboard')->with('success', 'Lead created successfully with Trackid: ' . $lead->id);
    }

    public function showFoamToUpdateLead($id)
    {
        $lead = Lead::with('documents')->find(Crypt::decrypt($id));

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        // dd($lead);

        // return $lead;

        return view('userpages.updateLead', compact('lead'));
    }

    public function updateLead(Request $request, $id)
    {
        // dd($request->all());
        // Find the lead
        $lead = Lead::find($id);

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'vehicle_type' => 'required|in:Motorcycle,Private Car,Commercial Vehicle',
            'policy_type' => 'required|in:New,Fresh,Renewal',
            'email' => 'email',
            'claim_status' => 'required|in:yes,no',
            'vehicle_number' => 'required|string|max:255',
            'mobile_number' => 'required|numeric|digits:10',
            'documents.*.name' => 'required_with:documents.*.file,null|string|max:255',
            'documents.*.file' => 'file|mimes:jpeg,jpg,png,pdf|max:20480',
        ]);

        // Handle lead cancellation
        if ($request->has('cancelLead')) {
            $lead->update([
                'is_cancel' => 1,
                'is_issue' => 0
            ]);

            // Send notification to ZM
            $notification = Notification::create([
                'sender_id' => Auth::user()->id,
                'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                'message' => 'Lead ' . $id . ' has been cancelled by Rc',
            ]);

            // Send notification to retail team
            $notification = Notification::create([
                'sender_id' => Auth::user()->id,
                'receiver_id' => 2,
                'message' => 'Lead Id ' . $id . ' has been cancelled by Rc',
            ]);

            return redirect()->route('user.dashboard')->with('success', 'Lead is cancelled successfully');
        }

        // Validate the input data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'vehicle_type' => 'required|in:Motorcycle,Private Car,Commercial Vehicle',
            'vehicle_number' => 'required|string|max:255',
            'mobile_number' => 'required|numeric|digits:10',
            'documents.*.name' => 'required_with:documents.*.file,null|string|max:255',
            'documents.*.file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:20480',
        ]);

        // Update the lead details
        $lead->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'policy_type' => $request->policy_type,
            'email' => $request->email,
            'claim_status' => $request->claim_status,
            'vehicle_type' => $request->vehicle_type,
            'mobile_no' => $request->mobile_number,
            'vehicle_number' => $request->vehicle_number,
            'is_issue' => 0,
        ]);


        // Update documents if necessary
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $document) {
                // Find the existing document or create a new one
                $existingDocument = $lead->documents[$index] ?? null;

                if ($existingDocument) {
                    // Update the document name
                    $existingDocument->update([
                        'document_name' => $document['name'],
                    ]);

                    // If a new file is uploaded, update the file path
                    if (isset($document['file'])) {
                        $file = $document['file'];
                        $filePath = $file->store('documents/' . $lead->id, 'public');
                        $existingDocument->update([
                            'file_path' => $filePath,
                        ]);
                    }
                } else {
                    // If it's a new document, create a new record
                    if (isset($document['file'])) {
                        $file = $document['file'];
                        $filePath = $file->store('documents/' . $lead->id, 'public');

                        Document::create([
                            'lead_id' => $lead->id,
                            'document_name' => $document['name'],
                            'file_path' => $filePath,
                        ]);
                    }
                }
            }
        }

        // Send notification
        $notification = Notification::create([
            'lead_id' => $lead->id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => $lead->is_zm_verified ? 2 : ZonalManager::where('id', Auth::user()->zm_id)->first()->user_id,
            'message' => 'Lead updated by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);


        if ($request->filled('comments')) {

            $notification = Notification::create([
                'lead_id' => $lead->id,
                'sender_id' => Auth::user()->id,
                'receiver_id' => ZonalManager::where('id', Auth::user()->zm_id)->first()->user_id,
                'message' => $request->comments,
            ]);
        }

        return redirect()->route('user.dashboard')->with('success', $lead->first_name . ' ' . $lead->last_name . ' details updated successfully');
    }


    // public function getQuoteDetails($leadId)
    // {
    //     $quotes = Quote::where('lead_id', $leadId)->get();

    //     return response()->json([
    //         'quotes' => $quotes
    //     ]);
    // }


    public function submitQuoteAction(Request $request)
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

                $lead = Lead::find($quote->lead_id);
                if ($request->hasFile('aadharCardFront') && $request->hasFile('aadharCardBack') && $request->hasFile('panCard')) {
                    // Upload the Aadhar Card Front Page
                    $aadharFileFrontpage = $request->file('aadharCardFront');
                    $aadharPathFront = $aadharFileFrontpage->store('documents/' . $lead->id, 'public');

                    // Upload the Aadhar Card Back Page
                    $aadharPathBackPage = $request->file('aadharCardBack');
                    $aadharPathBack = $aadharPathBackPage->store('documents/' . $lead->id, 'public');

                    // Upload the PAN Card
                    $panFile = $request->file('panCard');
                    $panPath = $panFile->store('documents/' . $lead->id, 'public');

                    // Save the Aadhar card Front Page 
                    Document::create([
                        'lead_id' => $quote->lead_id,
                        'document_name' => 'Aadhar Card Front Page',
                        'file_path' => $aadharPathFront,
                    ]);

                    // Save the Aadhar card back page
                    Document::create([
                        'lead_id' => $quote->lead_id,
                        'document_name' => 'Aadhar Card Back Page',
                        'file_path' => $aadharPathBack,
                    ]);


                    // Save the PAN card document
                    Document::create([
                        'lead_id' => $quote->lead_id,
                        'document_name' => 'Pan Card',
                        'file_path' => $panPath,
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Both Aadhar Card and PAN Card must be uploaded.',
                    ]);
                }


                //////////send notification to zm ////////////
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', Lead::find($quote->lead_id)->zm_id)->first()->user_id,
                    'message' => 'The quote has been accepted for Lead ID ' . $quote->lead_id . 'Please Send Payment Link',
                ]);
                // broadcast(new NotificationSent($notification));

                //////////send notification to retail team////////////
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 2,
                    'message' => 'The quote has been accepted for Lead ID ' . $quote->lead_id . 'Please Send Payment Link',
                ]);
                // broadcast(new NotificationSent($notification));
                break;
            case 'ask_for_another':
                Lead::where('id', $quote->lead_id)->update(['ask_another_quotes' => 1]);
                //////////send notification to zm ////////////
                $notification = Notification::create([
                    'lead_id' => $quote->lead_id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', Lead::find($quote->lead_id)->zm_id)->first()->user_id,
                    'message' => request('message') . ' Regional cordinator  ask another quote for lead id  ' . $quote->lead_id,
                ]);
                // broadcast(new NotificationSent($notification));
                //////////send notification to retail team////////////
                $notification = Notification::create([
                    'lead_id' => $quote->lead_id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 2,
                    'message' => request('message') . 'Regional cordinator  ask another quote for lead id  ' . $quote->lead_id,
                ]);
                // broadcast(new NotificationSent($notification));
                break;

            case 'cancel':
                Lead::where('id', $quote->lead_id)->update([
                    'is_cancel' => 1,
                ]);
                //////////send notification to zm ////////////
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', Lead::find($quote->lead_id)->zm_id)->first()->user_id,
                    'message' => request('message') . ' Lead' . $quote->lead_id . '' . 'has been cancelled by Rc',
                ]);
                // broadcast(new NotificationSent($notification));
                //////////send notification to retail team////////////
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 2,
                    'message' => request('message') . ' Lead Id ' . $quote->lead_id . '' . ' has been cancelled by Rc',
                ]);
                // broadcast(new NotificationSent($notification));
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
            ->orderBy('updated_at', 'desc')
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


    public function uploadPaymentScreenShort($id, Request $request)
    {

        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        $action = request('paymentAction');

        switch ($action) {
            case 'upload':
                $path = $request->file('paymentScreenShort')->store('Payments');

                $lead->update([
                    'payment_receipt' => $path,
                ]);

                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 2,
                    'message' => 'Payment Screen Short is uploaded for Lead ID ' . $lead->id . '. Please Verify.',
                ]);
                // broadcast(new NotificationSent($notification));

                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Payment Screen Short is uploaded for Lead ID ' . $lead->id . '.Please Verify.',
                ]);
                // broadcast(new NotificationSent($notification));

                return response()->json(['success' => true, 'message' => 'Payment screen short uploaded successfully']);
                break;
            case 'cancel':
                Lead::where('id', $id)->update([
                    'is_cancel' => 1,
                ]);
                //////////send notification to zm ////////////
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => request('message') . ' Lead' . $id . '' . 'has been cancelled by Rc',
                ]);
                // broadcast(new NotificationSent($notification));
                //////////send notification to retail team////////////
                $notification = Notification::create([
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 2,
                    'message' => request('insufficientDetailsMessage2') . ' Lead Id ' . $id . '' . ' has been cancelled by Rc',
                ]);
                // broadcast(new NotificationSent($notification));

                return response()->json(['success' => true, 'message' => 'Lead cancelled successfully']);
                break;

            case 'resend_payment_link':
                $lead->update([
                    'payment_link' => null,
                ]);

                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => 2,
                    'message' => 'Payment Link has been resent for Lead ID ' . $lead->id ,
                ]);
                // broadcast(new NotificationSent($notification));

                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Payment Link has been resent for Lead ID ' . $lead->id ,
                ]);
                // broadcast(new NotificationSent($notification));

                return response()->json(['success' => true, 'message' => 'Payment link resent request sent successfully']);
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Invalid lead status']);
        }


    }

    public function cancelLeads()
    {
        $leads = Lead::where('is_cancel', 1)
            ->where('user_id', Auth::user()->id)
            ->select('id', 'first_name', 'last_name', 'mobile_no', 'is_issue', 'is_zm_verified', 'is_retail_verified', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('userpages.cancelLeads', compact('leads'));
    }
}