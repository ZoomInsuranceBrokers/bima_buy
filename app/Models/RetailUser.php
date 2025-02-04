<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetailUser extends Model
{
    use HasFactory;
  
    protected $table = 'retail_users';

    protected $primaryKey = 'id';


    protected $fillable = ['user_id', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
