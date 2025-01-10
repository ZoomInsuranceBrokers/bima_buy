<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Document;
use App\Models\Quote;
use App\Models\User; 
use App\Models\ZonalManager;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zm_id',
        'first_name', 
        'last_name', 
        'gender', 
        'date_of_birth',
        'mobile_no',
        'email',
        'vehicle_number',
        'claim_status',
        'policy_type',
        'is_issue',
        'is_zm_verified',
        'is_retail_verified',
        'is_cancel',
        'is_accepted',
        'payment_link',
        'payment_receipt',
        'is_payment_complete',
        'final_status'
    
    ];
    protected $casts = [
        'is_issue' => 'boolean',
        'is_zm_verified' => 'boolean',
        'is_retail_verified' => 'boolean',
        'is_cancel' => 'boolean',
        'is_accepted' => 'boolean',
        'is_payment_complete' => 'boolean',
        'final_status' => 'boolean'
    ];
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zonalManager()
    {
        return $this->belongsTo(ZonalManager::class, 'zm_id');
    }
}
