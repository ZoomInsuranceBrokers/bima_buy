<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;

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



}
