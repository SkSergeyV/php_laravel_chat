<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Message extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'message',
        'quote',
        'read_message',
        'from_user_id',
        'to_user_id',
        'send_at',
        'read_at',
        'channel',
        'attachment_id',
        'deleted',
        'deleted_at',
        'edited',
        'edited_at',
        'old_message',
        'quote_user_id'
    ];

    protected $hidden = [
    ];
    
}
