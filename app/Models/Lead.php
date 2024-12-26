<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'vehicle_number',
        'is_doc_complete',
        'is_zm_verified',
        'is_payment_complete',
        'is_cancel',
    
    ];
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
