<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Attachment extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'att_type',
        'img_original',
        'img_middle',
        'img_small',
        'mov',
        'doc',
        'user_id',
        'channel',
        'att_status'
    ];

    protected $hidden = [
    ];
}
