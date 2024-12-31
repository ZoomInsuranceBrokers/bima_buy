<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'notifications';

    // Columns that are allowed for mass assignment
    protected $fillable = ['sender_id', 'receiver_id', 'message', 'is_read'];

    // Automatically handle timestamps
    public $timestamps = true;

    // Cast attributes (e.g., for boolean handling)
    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Relationships (if required)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
