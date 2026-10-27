<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Channel extends Model
{

    use HasFactory, Notifiable;

    protected $fillable = [
        'channel',
        'reception_user',
        'sending_user',
        'to_user_id',
    ];

    protected $hidden = [
    ];
    
    
    
}