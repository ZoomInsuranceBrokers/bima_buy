<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id','document_name', 'file_path'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
