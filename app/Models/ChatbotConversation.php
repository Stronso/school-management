<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_input',
        'bot_response',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 