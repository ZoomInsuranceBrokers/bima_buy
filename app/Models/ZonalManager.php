<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ZonalManager extends Model
{
    use HasFactory;
    protected $table = 'zonal_managers';
    protected $fillable = [
        'name',
    ];
}
