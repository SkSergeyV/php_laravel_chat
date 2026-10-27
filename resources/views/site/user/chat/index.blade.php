{{-- Пользователь, чат --}}


@extends('site.layouts.layout')

@section('content')
        

<div class="mc_block">
    <div class="ac_block"> 
 
         <chat-block
         :user_id = "{{$user_id}}" 
         :to_user_name = "'{{$to_user_name}}'"
         :to_user_avatar = "'{{$to_user_avatar}}'"
         :to_user_id = "{{$to_user_id}}"
         :token = "'{{$token}}'"
         :reception_user="'{{$reception_user}}'"
         :sending_user="'{{$sending_user}}'"
         :channel="'{{$cn}}'"
         :user_current_status="'{{$on_line}}'"
         ></chat-block>

    </div>
</div>      
                    


@endsection