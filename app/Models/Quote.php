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
        'file_path',
        'is_accepted',
        'payment_status',
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
