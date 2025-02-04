<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'lead_id',
        'quote_name',
        'price',
        'od_premium',
        'tp_premium',
        'vehicle_idv',
        'file_path',
        'is_accepted',
        'payment_status',
        'policy_start_date',
        'policy_end_date',
    ];
    protected $casts = [
        'is_accepted' => 'boolean',
        'payment_status' => 'boolean',
    ];
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
