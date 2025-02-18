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
use App\Models\Document;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeadsExport;
use Illuminate\Support\Facades\Log;

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
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' .This Message For Lead ID ' . $lead->id . '.',
                ]);

                // broadcast(new NotificationSent($notification));

                $update_message = [
                    'lead_id' => Crypt::encrypt($lead->id),
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' .This Message For Tracking ID ' . $lead->id . '.',
                ];
                // broadcast(new UpdateLead($update_message));


                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => $request->input('message') . ' .This Message For Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));

                break;
            case 'verified':
                $lead->is_retail_verified = true;
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid action'], 400);
        }

        $lead->save();

        return response()->json(['success' => true, 'message' => 'Action processed successfully']);
    }

    public function getQuotes($leadId)
    {
        $quotes = Quote::select('id', 'quote_name', 'price', 'od_premium', 'tp_premium', 'vehicle_idv', 'file_path', 'remarks')
            ->where('lead_id', $leadId)->get();
        $quotes->transform(function ($quote) {
            if (!empty($quote->file_path)) { // Ensure file_path is not null or empty
                // Check if the file exists
                if (Storage::disk('local')->exists($quote->file_path)) {
                    // Generate a temporary URL for the file
                    $quote->temporary_url = Storage::disk('local')->temporaryUrl(
                        $quote->file_path,
                        now()->addMinutes(5)
                    );
                } else {
                    // If the file does not exist, set the temporary URL to null
                    $quote->temporary_url = null;
                }
            } else {
                // Handle cases where file_path is null
                $quote->temporary_url = null;
            }
            return $quote;
        });

        return response()->json($quotes);
    }
    public function store(Request $request)
    {
        // Validation for the incoming request (including file upload)
        $validated = $request->validate([
            'quote_name' => 'required|array',
            'quote_name.*' => 'required|string|max:255',
            'price' => 'required|array',
            'price.*' => 'required|numeric',
            'od_premium' => 'required|array',
            'od_premium.*' => 'required|numeric',
            'tp_premium' => 'required|array',
            'tp_premium.*' => 'required|numeric',
            'vehicle_idv' => 'required|array',
            'vehicle_idv.*' => 'required|numeric',
            'remark' => 'required|array',
            'file_path' => 'nullable|array',
            'file_path.*' => 'nullable|file|max:10240',
        ]);

        // Process the quotes and files
        foreach ($request->quote_name as $key => $quoteName) {
            $quote = new Quote();
            $quote->quote_name = $quoteName;
            $quote->price = $request->price[$key];
            $quote->od_premium = $request->od_premium[$key];
            $quote->tp_premium = $request->tp_premium[$key];
            $quote->vehicle_idv = $request->vehicle_idv[$key];
            $quote->remarks = $request->remark[$key];

            // Check if a file was uploaded for this quote
            if ($request->hasFile('file_path') && $request->file('file_path')[$key]) {
                // Store the file and save the file path
                $file = $request->file('file_path')[$key];
                $path = $file->store('quotes');  // Save in the 'quotes' folder
                $quote->file_path = $path;
            }

            $quote->lead_id = $request->lead_id;
            $quote->save();
        }
        Lead::where('id', $request->lead_id)->update(['quotes_send' => 1, 'ask_another_quotes' => 0]);

        $notification = Notification::create([
            'lead_id' => $request->lead_id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => ZonalManager::where('id', Lead::find($request->lead_id)->zm_id)->first()->user_id,
            'message' => 'Quote is sending for Lead ID ' . $request->lead_id . '.',
        ]);

        // broadcast(new NotificationSent($notification));

        $notification = Notification::create([
            'lead_id' => $request->lead_id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => Lead::find($request->lead_id)->user_id,
            'message' => 'Quote is sending for Lead ID ' . $request->lead_id . '.',
        ]);

        // broadcast(new NotificationSent($notification));

        return response()->json(['message' => 'Quotes submitted successfully!'], 200);
    }
    public function upadtePaymentStatus($id, Request $request)
    {
        $lead = Lead::find($id);
        $action = $request->input('action');

        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found']);
        }

        switch ($action) {
            case 'complete':
                $lead->is_payment_complete = true;
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Payment is completed for Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Payment is completed for Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
                break;
            case 'reupload':
                $lead->payment_receipt = null;
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . 'Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => $request->input('message') . 'Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));

                break;
            case 'notify':
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . 'Payment is pending for Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => $request->input('message') . 'Payment is pending for Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
                break;
            case 'send_payment_link':
                $lead->payment_link = $request->paymentLink;
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => 'Payment link is send for Lead ID ' . $lead->id,
                ]);
                // broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => 'Payment link is send for Lead ID ' . $lead->id,
                ]);
                // broadcast(new NotificationSent($notification));
                break;
            case 'incomplete_documents':
                $lead->is_issue = true;
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $lead->user_id,
                    'message' => $request->input('message') . ' for Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
                $notification = Notification::create([
                    'lead_id' => $lead->id,
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
                    'message' => $request->input('message') . 'for Lead ID ' . $lead->id . '.',
                ]);
                // broadcast(new NotificationSent($notification));
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
            'policyCopy' => 'required|file|mimes:pdf,jpeg,jpg,png,gif,bmp,tiff,doc,docx',
            'policy_start_date' => 'required|date',
            'policy_end_date' => 'required|date',

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

        Quote::where(['lead_id' => $id, 'is_accepted' => 1])->update(['policy_start_date' => $request->policy_start_date, 'policy_end_date' => $request->policy_end_date]);

        $notification = Notification::create([
            'lead_id' => $lead->id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => $lead->user_id,
            'message' => 'Policy is uploaded for Lead ID ' . $lead->id . '.',
        ]);
        // broadcast(new NotificationSent($notification));
        $notification = Notification::create([
            'lead_id' => $lead->id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => ZonalManager::where('id', $lead->zm_id)->first()->user_id,
            'message' => 'Policy is uploaded for Lead ID ' . $lead->id . '.',
        ]);
        // broadcast(new NotificationSent($notification));
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

    public function getPaymentScreenShortAndLink($id)
    {

        // Find the lead by ID
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found']);
        }

        // Retrieve the payment link from Quote model
        // $payment_link = Quote::where('lead_id', $id)
        //     ->whereNotNull('payment_link')
        //     ->select('payment_link')
        //     ->first();

        // Initialize response data
        $responseData = [];

        $documents = Document::where('lead_id', $id)
            ->whereIn('document_name', ['Aadhar Card Front Page', 'Aadhar Card Back Page', 'Pan Card'])
            ->select('document_name', 'file_path')
            ->get();

        $aadharCardFrontPage = $documents->where('document_name', 'Aadhar Card Front Page');
        $aadharCardBacktPage = $documents->where('document_name', 'Aadhar Card Back Page');
        $panCard = $documents->where('document_name', 'Pan Card');

        // Assign the results to the response data
        $responseData['aadhar_card_front_page'] = $aadharCardFrontPage->isEmpty() ? null : $aadharCardFrontPage->first()->file_path;
        $responseData['aadhar_card_back_page'] = $aadharCardBacktPage->isEmpty() ? null : $aadharCardBacktPage->first()->file_path;
        $responseData['pan_card'] = $panCard->isEmpty() ? null : $panCard->first()->file_path;

        // Check if payment screenshot exists
        if ($lead->payment_receipt) {
            $screenShort = Storage::disk('local')->temporaryUrl($lead->payment_receipt, now()->addMinutes(30)); // expires in 30 minutes
            $responseData['screenShort'] = $screenShort; // Include the screenshot URL in the response
        }

        // If a payment link exists, include it in the response
        if ($lead->payment_link) {
            $responseData['paymentLink'] = $lead->payment_link; // Include the payment link in the response
        }

        // Check if either payment data or screenshot is available
        if (isset($responseData['screenShort']) || isset($responseData['paymentLink']) || isset($responseData['aadhar_card']) || isset($responseData['pan_card'])) {
            return response()->json(['success' => true] + $responseData);
        } else {
            return response()->json(['success' => true, 'message' => 'No payment data available']);
        }
    }


    public function savePaymentLink($id, Request $request)
    {
        // Validate the request data
        $request->validate([
            'paymentLink' => 'required|url',
        ]);

        // Find the lead by its ID
        $lead = Lead::find($id);

        // Check if the lead exists
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        // Update the lead's payment_link column
        $lead->payment_link = $request->paymentLink;
        $lead->save(); // Save the changes

        return response()->json(['success' => true, 'message' => 'Payment link updated successfully']);
    }


    public function cancelLeads()
    {
        $cancelLeads = Lead::with([

            'user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->where('is_cancel', 1)
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'is_issue', 'is_zm_verified', 'is_retail_verified', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // return $cancelLeads;
        return view('retailpages.cancelLeads', compact('cancelLeads'));
    }
    public function totalSalesReport()
    {
        return view('retailpages.report');
    }

    public function downloadReport(Request $request)
    {
        // Validate the date inputs
        $request->validate([
            'from_date' => 'required|date|before_or_equal:to_date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        // dd($request->all());


        $leads = Lead::with([

            'user' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'mobile');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            },
            'quotes' => function ($query) {
                $query->select('id', 'quote_name', 'lead_id', 'price', 'od_premium', 'tp_premium', 'vehicle_idv', 'policy_start_date', 'policy_end_date', 'is_accepted')
                    ->latest('created_at')
                    ->limit(1);
            },
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id');
            },
        ])
            ->select('id', 'user_id', 'zm_id', 'mobile_no', 'first_name', 'last_name', 'mobile_no', 'email', 'vehicle_type', 'vehicle_number', 'is_issue','is_zm_verified', 'is_retail_verified','is_cancel', 'payment_link', 'payment_receipt', 'is_payment_complete', 'final_status', 'updated_at', 'created_at')
            ->whereBetween('created_at', [$request->from_date, $request->to_date.' 23:59:59'])
            ->get();

            // echo '<pre>';
            // print_r($leads);
            // exit;

        try {
            return Excel::download(new LeadsExport($leads), 'leads_report.xlsx');
        } catch (\Exception $e) {
            Log::error('Error generating leads report: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'There was an issue generating the report.');
        }
    }
}
