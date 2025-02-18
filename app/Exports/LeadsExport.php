<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\Quote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $leads;

    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Rc Name',
            'Zm Name',
            'Tracking ID',
            'Rc Mobile No',
            'Verify By Zm',
            'Verify Retail',
            'Send Quote Details',
            'Payment Status',
            'Upload Policy Copy',
            'cancel Status',
            'Customer Name',
            'Reg Number',
            'Mobile Number',
            'Email ID',
            'Vehicle Type',
            'Vehicle IDV',
            'OD Premium',
            'TP Premium',
            'Final Premium',
            'Insurance Company',
            'RSD',
            'RED',
            'Remarks',
            'Lead Created Date',
            'Last Update Date'
        ];
    }

    public function map($lead): array
    {
        $quote = $lead->quotes->isNotEmpty() ? $lead->quotes[0] : null;
        static $index = 1;
        return [
            $index++,
            $lead->user->first_name . ' ' . $lead->user->last_name,
            $lead->zonalManager->name ,
            $lead->id,
            $lead->user->mobile,
            $lead->is_zm_verified ? 'Verified' : 'Pending',
            $lead->is_retail_verified ? 'Verified' : 'Pending',
            $lead->quotes ? 'Send' : 'Pending',
            $lead->is_payment_complete ? 'Payment Complete' : 'Pending',
            $lead->final_status ? 'Uploaded' : 'not uploaded',
            $lead->is_cancel ? 'Cancelled' : '',
            $lead->first_name . ' ' . $lead->last_name,
            $lead->vehicle_number,
            $lead->mobile_no,
            $lead->email,
            $lead->vehicle_type,
            $quote ? $lead->quotes[0]->vehicle_idv : 'Pending',
            $quote ? $lead->quotes[0]->od_premium : 'Pending',
            $quote ? $lead->quotes[0]->tp_premium : 'Pending',
            $quote ? $lead->quotes[0]->price : 'Pending',
            $quote ? $lead->quotes[0]->quote_name : 'Pending',
            $quote ? $lead->quotes[0]->policy_start_date : 'Pending',
            $quote ? $lead->quotes[0]->policy_end_date : 'Pending',
            $lead->lastNotification?$lead->lastNotification->message:'No Remarks',
            $lead->created_at,
            $lead->updated_at,
        ];
    }
}
