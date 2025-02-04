<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ZonalManager;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicyCopy extends Model
{
    use HasFactory;
    protected $table = 'policy_copy';

    // Fillable properties for mass assignment
    protected $fillable = [
        'user_id',
        'zm_id',
        'lead_id',
        'path',
    ];

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
    public function zonalManager()
    {
        return $this->belongsTo(ZonalManager::class, 'zm_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
