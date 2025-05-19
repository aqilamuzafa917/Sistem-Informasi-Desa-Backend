<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_message',
        'chatbot_reply',
        'ip_address',
        'user_id',
        'session_id',
    ];

    /**
     * Get the user that owns the chatbot log.
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Pastikan model User ada jika Anda menggunakan user_id
    }
}