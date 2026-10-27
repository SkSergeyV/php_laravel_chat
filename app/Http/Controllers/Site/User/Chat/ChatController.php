<?php

// Пользователь, чат

namespace App\Http\Controllers\Site\User\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Channel;
use App\Models\Online;


class ChatController extends Controller
{
    public function index(){
    
        $user_id=Auth::user()->id;             
        $user=User::findOrFail($user_id);
        $channel=$user->channel;
        $user_name=$user->name;
        $token=$user->current_token;
        
        $user_channel=Channel::findOrFail($channel);
        $reception_user=$user_channel->reception_user;
        $sending_user=$user_channel->sending_user;
        $to_user_id=$user_channel->to_user_id;
        $cn=$user_channel->channel;
        
        $to_user_info=User::findOrFail($to_user_id);
        $to_user_name=$to_user_info->name;
        $to_user_avatar=$to_user_info->avatar;
                       
        $online = Online::where('channel', $cn) ->where('user_id', $to_user_id)->first();                                            
        $on_line = $online->online; 
        
        if($on_line==1){
          $on_line='Онлайн';
        }  
        if($on_line==0){
          $on_line='Оффлайн';
        } 
                   
        return view('site.user.chat.index', compact('user_id', 'to_user_avatar', 'to_user_name', 'to_user_id', 'token', 'reception_user', 'sending_user', 'cn', 'on_line' ));
    }
}