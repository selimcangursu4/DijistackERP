<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationSession extends Model
{
    protected $table = 'conversation_sessions';

    protected $fillable = [
        'company_id',
        'contact_id',
        'session_id',
        'message_count',
        'avg_response_time',
        'avg_csat',
    ];
}
