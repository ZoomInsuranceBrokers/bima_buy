<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;
use App\Models\ZonalManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{
    public function todayReport()
    {
        // Get today's date
        $today = now()->toDateString();

        // Execute the query using Query Builder
        $report = DB::table('leads')
            ->selectRaw('COUNT(*) AS total_count')
            ->selectRaw('SUM(is_cancel = 1) AS cancel_count')
            ->selectRaw('
            SUM(
                is_cancel = 0 AND (
                    is_issue = 1 OR
                    (is_issue = 1 AND is_zm_verified = 0) OR
                    (is_issue = 1 AND is_zm_verified = 1 AND is_retail_verified = 0) OR
                    (quotes_send = 1 AND ask_another_quotes = 0 AND is_accepted = 0) OR
                    (is_accepted = 1 AND payment_link IS NOT NULL AND payment_receipt IS NULL)
                )
            ) AS pendin_at_rc
        ')
            ->selectRaw('SUM(is_issue = 0 AND is_zm_verified = 0 AND is_cancel = 0) AS pendin_at_zm')
            ->selectRaw('
            SUM(
                is_cancel = 0 AND (
                    (is_issue = 0 AND is_zm_verified = 1 AND is_retail_verified = 0) OR
                    (is_retail_verified = 1 AND quotes_send = 0) OR
                    (ask_another_quotes = 1 AND is_accepted = 0) OR
                    (is_accepted = 1 AND is_issue = 0 AND payment_link IS NULL) OR
                    (payment_receipt IS NOT NULL AND is_payment_complete = 0) OR
                    (is_payment_complete = 1 AND final_status = 0)
                )
            ) AS pendin_at_retail
        ')
            ->selectRaw('SUM(final_status = 1) AS policy_issued')
            ->whereDate('created_at', $today)
            ->first();

        return view('adminpages.admin-dashboard', compact('report'));
    }
    public function totalReport()
    {
        $report = DB::table('leads')
            ->selectRaw('COUNT(*) AS total_count')
            ->selectRaw('SUM(is_cancel = 1) AS cancel_count')
            ->selectRaw('
                SUM(
                    is_cancel = 0 AND (
                        is_issue = 1 OR
                        (is_issue = 1 AND is_zm_verified = 0) OR
                        (is_issue = 1 AND is_zm_verified = 1 AND is_retail_verified = 0) OR
                        (quotes_send = 1 AND ask_another_quotes = 0 AND is_accepted = 0) OR
                        (is_accepted = 1 AND payment_link IS NOT NULL AND payment_receipt IS NULL)
                    )
                ) AS pendin_at_rc
            ')
            ->selectRaw('SUM(is_issue = 0 AND is_zm_verified = 0 AND is_cancel = 0) AS pendin_at_zm')
            ->selectRaw('
                SUM(
                    is_cancel = 0 AND (
                        (is_issue = 0 AND is_zm_verified = 1 AND is_retail_verified = 0) OR
                        (is_retail_verified = 1 AND quotes_send = 0) OR
                        (ask_another_quotes = 1 AND is_accepted = 0) OR
                        (is_accepted = 1 AND is_issue = 0 AND payment_link IS NULL) OR
                        (payment_receipt IS NOT NULL AND is_payment_complete = 0) OR
                        (is_payment_complete = 1 AND final_status = 0)
                    )
                ) AS pendin_at_retail
            ')
            ->selectRaw('SUM(final_status = 1) AS policy_issued')
            ->first();

        return view('adminpages.admin-total-leads', compact('report'));
    }


    ////////////////////////////////////////////Today Report////////////////////////////////////////

    public function todayTotalLeadsReport()
    {
        $today = now()->toDateString();

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->whereDate('created_at', $today)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function todayCompleteLeadsReport()
    {
        $today = now()->toDateString();

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('final_status', 1)
            ->whereDate('created_at', $today)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }
    public function todayCancelLeadsReport()
    {
        $today = now()->toDateString();

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 1)
            ->whereDate('created_at', $today)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function todayPendLeadsinAtZm()
    {
        $today = now()->toDateString();

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 0)
            ->where('is_issue', 0)
            ->where('is_zm_verified', 0)
            ->whereDate('created_at', $today)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }
    public function todayPendLeadsAtRcEnd()
    {
        $today = now()->toDateString();

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 0)
            ->where(function ($query) {
                $query->where('is_issue', 1)
                    ->orWhere(function ($query) {
                        $query->where('is_issue', 1)
                            ->where('is_zm_verified', 0);
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_issue', 1)
                            ->where('is_zm_verified', 1)
                            ->where('is_retail_verified', 0);
                    })
                    ->orWhere(function ($query) {
                        $query->where('quotes_send', 1)
                            ->where('ask_another_quotes', 0)
                            ->where('is_accepted', 0);
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_accepted', 1)
                            ->whereNotNull('payment_link')
                            ->whereNull('payment_receipt');
                    });
            })
            ->whereDate('created_at', $today) // Apply the date condition here
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function todayPendLeadsAtRetailEnd()
    {
        $today = now()->toDateString();

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 0)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_issue', 0)
                        ->where('is_zm_verified', 1)
                        ->where('is_retail_verified', 0);
                })
                    ->orWhere(function ($q) {
                        $q->where('is_retail_verified', 1)
                            ->where('quotes_send', 0);
                    })
                    ->orWhere(function ($q) {
                        $q->where('ask_another_quotes', 1)
                            ->where('is_accepted', 0);
                    })
                    ->orWhere(function ($q) {
                        $q->where('is_accepted', 1)
                            ->where('is_issue', 0)
                            ->whereNull('payment_link');
                    })
                    ->orWhere(function ($q) {
                        $q->whereNotNull('payment_receipt')
                            ->where('is_payment_complete', 0);
                    })
                    ->orWhere(function ($q) {
                        $q->where('is_payment_complete', 1)
                            ->where('final_status', 0);
                    });
            })
            ->whereDate('created_at', $today)
            ->get();

            return view('adminpages.leads', compact('leads'));
    }

    ////////////////////////////////////////////////////////Total Report////////////////////////////////////////

    public function totalLeadsReport()
    {

        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->get();

        return view('adminpages.leads', compact('leads'));
    }
    public function totalCompleteLeadsReport()
    {
        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('final_status', 1)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function totalCancelLeadsReport()
    {
        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 1)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function totalPendLeadsinAtZm()
    {
        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 0)
            ->where('is_issue', 0)
            ->where('is_zm_verified', 0)
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function totalPendLeadsAtRcEnd()
    {
        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 0)
            ->where(function ($query) {
                $query->where('is_issue', 1)
                    ->orWhere(function ($query) {
                        $query->where('is_issue', 1)
                            ->where('is_zm_verified', 0);
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_issue', 1)
                            ->where('is_zm_verified', 1)
                            ->where('is_retail_verified', 0);
                    })
                    ->orWhere(function ($query) {
                        $query->where('quotes_send', 1)
                            ->where('ask_another_quotes', 0)
                            ->where('is_accepted', 0);
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_accepted', 1)
                            ->whereNotNull('payment_link')
                            ->whereNull('payment_receipt');
                    });
            })
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    public function totalPendLeadsAtRetailEnd()
    {
        $leads = Lead::with([
            'lastNotification' => function ($query) {
                $query->select('id', 'message', 'lead_id', 'created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'first_name', 'mobile', 'last_name');
            },
            'zonalManager' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->select('id', 'user_id', 'zm_id', 'first_name', 'last_name', 'mobile_no', 'created_at')
            ->where('is_cancel', 0)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_issue', 0)
                        ->where('is_zm_verified', 1)
                        ->where('is_retail_verified', 0);
                })
                    ->orWhere(function ($q) {
                        $q->where('is_retail_verified', 1)
                            ->where('quotes_send', 0);
                    })
                    ->orWhere(function ($q) {
                        $q->where('ask_another_quotes', 1)
                            ->where('is_accepted', 0);
                    })
                    ->orWhere(function ($q) {
                        $q->where('is_accepted', 1)
                            ->where('is_issue', 0)
                            ->whereNull('payment_link');
                    })
                    ->orWhere(function ($q) {
                        $q->whereNotNull('payment_receipt')
                            ->where('is_payment_complete', 0);
                    })
                    ->orWhere(function ($q) {
                        $q->where('is_payment_complete', 1)
                            ->where('final_status', 0);
                    });
            })
            ->get();

        return view('adminpages.leads', compact('leads'));
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////

    public function addUser()
    {
        $zonalManagers = ZonalManager::select('id', 'name')->get();
        return view('adminpages.admin-adduser', compact('zonalManagers'));
    }

    public function store(Request $request)
    {


        // Validate the form data
        $validated = $request->validate([
            'role' => 'required|in:zm,user',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'mobile_no' => 'required|numeric|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'zm' => 'required_if:role,user',
        ]);


        DB::beginTransaction();

        try {
            $zmId = $request->role == 'zm'
                ? ZonalManager::create(['name' => "{$request->first_name} {$request->last_name}"])->id
                : $request->zm;

            $user = User::create([
                'role_id' => $request->role == 'zm' ? 3 : 2,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'mobile' => $request->mobile_no,
                'email' => $request->email,
                'zm_id' => $zmId,
                'password' => Hash::make('Novel@123'),
                'image_path' => $request->hasFile('profile_photo')
                    ? $request->file('profile_photo')->store('profile_photos', 'public')
                    : ($request->gender == 'male'
                        ? 'profile_photos/default_photos/male.jpg'
                        : 'profile_photos/default_photos/female.jpg'),
            ]);

            if ($request->role == 'zm') {
                ZonalManager::where('id', $zmId)->update(['user_id' => $user->id]);
            }

            DB::commit();

            return redirect()->route('admin.adduser')->with('success', 'User added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding user', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'An error occurred while adding the user.');
        }
    }



}
