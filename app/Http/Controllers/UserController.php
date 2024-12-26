<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class UserController extends Controller
{

    public function index()
    {
        return view('userpages.dashboard');
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
            'mobile_number'=>'required|numeric|digits:10',
            'documents.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
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

        // Save document paths
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filePath = $file->store('documents/' . $lead->id, 'public');
                Document::create([
                    'lead_id' => $lead->id,
                    'file_path' => $filePath,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Lead created successfully with Trackid: ' . $lead->id);

    }

    public function pendingLead()
    {
        // $leads = Lead::where('zm_id', Auth::user()->zm_id)
        //     ->where('is_doc_complete', 1)
        //     ->where('is_zm_verified', 0)
        //     ->where('is_payment_complete', 0)
        //     ->where('is_cancel', 0)
        //     ->get();

        return view('userpages.pendingLead', );
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