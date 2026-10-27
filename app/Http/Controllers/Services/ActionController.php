<?php

// Действия

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Pusher\Pusher;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Attachment;
use Illuminate\Support\Facades\File;
use App\Models\Message;
use Carbon\Carbon;
use App\Models\Online;
use App\Models\User;
use Illuminate\Support\Str;

class ActionController extends Controller
{
     
     
   public function Push($channel, $event, $message){
      $options = array(
          'cluster' => 'eu',
          'useTLS' => true
      );
     $pusher = new Pusher(
            '',
            '',
            '',
            $options
    );
    
     $pusher->trigger($channel, $event, $message); 
   
   }  
   
   

    public function sendMessage(Request $request)
    {    
         $user_id = $request->user_id;
         $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'send_message',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        } 
         
         $message = $request->message;
         $attachment_id = $request->attachment_id;
         $to_user_id = $request->to_user_id;
         $timestamp = $request->timestamp;
         $sending_user = $request->sending_user;
         $channel = $request->channel;
         
         $new_message = Message::create([
                'message' => $message,
                'read_message' => 0,
                'from_user_id' => $user_id,
                'to_user_id' => $to_user_id,
                'send_at' => Carbon::parse($timestamp)->format('Y-m-d H:i:s'),
                'channel' => $channel,
                'attachment_id' =>$attachment_id
        ]); 
        $new_message_id=$new_message->id;
         
        if($attachment_id!='' || $attachment_id!=null){
               $attachment=Attachment::findOrFail($attachment_id);
               $att_type=$attachment->att_type;
               $attachment->att_status=1;
               $attachment->save();
               
               if($att_type==1){
                    $img_original = $attachment->img_original;
                    $img_middle = $attachment->img_middle;
                    $img_small = $attachment->img_small;
                    
                    $cur_dir=date('Y-m-d');       
                    $directoryPath = storage_path().'/app/private/upload/messages/img/'.$cur_dir;
                    if (!File::isDirectory($directoryPath)) {
                        File::makeDirectory($directoryPath, 0755, true, true);
                    }
                    File::copy(public_path().$img_original, storage_path().'/app/private'.$img_original);
                    File::copy(public_path().$img_middle, storage_path().'/app/private'.$img_middle);
                    File::copy(public_path().$img_small, storage_path().'/app/private'.$img_small);
                    File::delete(public_path().$img_original);
                    File::delete(public_path().$img_middle);
                    File::delete(public_path().$img_small);  
               }
               
        } else {
             $att_type='';
        }
       
        $to = $sending_user;
        $type = 'message';                
        $data['message']  = $message;
        $data['attachment_id']  = $attachment_id;
        $data['data_send']  =  $timestamp;
        $data['message_id']  = $new_message_id;
        $data['att_type']  = $att_type;
         
        
        $this->Push($to, $type, $data);
        
            return response()->json([
                'type' => 'send_message',
                'message' => 'OK',
                'attachment_id' =>$attachment_id,
                'message_id' =>$new_message_id,
                'att_type'  => $att_type,
                'sended_message' => $message
            ], 200);
                                                                                 
    }
    
    

    public function setReadMessage(Request $request)
    {
         $user_id = $request->user_id;
         $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'set_read_message',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        }  
        
         $message_id = $request->message_id;
         $r_date = $request->r_date;  
                
         $message=Message::findOrFail($message_id);
         $message->read_message=1;
         $message->read_at = Carbon::parse($r_date)->format('Y-m-d H:i:s');
         $message->save();
         
         $sending_user = $request->sending_user;
         
         $to = $sending_user;
         $type = 'read_message';
         $data['message_id']  = $message_id; 
         $this->Push($to, $type, $data);            
         
         return response()->json([
                'type' => 'set_read_message',
                'message' => 'OK',
        ], 200);
    }
    
    
  public function uploadFile(Request $request)
    {  
         /*
         $validator 
        */
        
         $user_id = $request->user_id;
         $token = $request->token;
         
          $usr = $this->checkUser($user_id, $token);
          if(!$usr){
             return response()->json([
                  'type' => 'upload_file',
                  'message' => 'ERROR',
                  'txt' => 'check_user'
             ], 200);
          }
         
         
         $file_type = $request->file_type;
         
        $cur_dir=date('Y-m-d');
        if($file_type==1) {
            $directoryPath = public_path().'/upload/messages/img/'.$cur_dir;
            if (!File::isDirectory($directoryPath)) {
               File::makeDirectory($directoryPath, 0755, true, true);
            }
        }
        
       if ($request->hasfile('file')) {
            $file=$request->file('file');
            $name = $file->getClientOriginalName();        
            $ext= $file->getClientOriginalExtension();
            
            if($file_type==1) {
              $file->move(public_path() . '/upload/messages/img/'.$cur_dir, $name);
              $file_path = public_path() . '/upload/messages/img/'.$cur_dir.'/';
              $rnd=md5(date("Y-m-d H:i:s"));
              $rnd_pr=rand(1000, 10000).'_';
              $newname= $rnd_pr.'original_'.$rnd.'.'.$ext;
              File::copy($file_path.$name, $file_path.$newname);
              File::delete($file_path.$name);
              
             ini_set('memory_limit', '368M'); 
             $image = Image::read($file_path.$newname);     
             $image->scaleDown(width: 300);
             $imageNameMiddle= $rnd_pr.'middle_'.$rnd.'.'.$ext;
             $image->save($file_path.$imageNameMiddle);
             
             $image = Image::read($file_path.$newname);   
             $image->resize(40,40);
             $imageNameSmall= $rnd_pr.'small_'.$rnd.'.'.$ext;
             $image->save($file_path.$imageNameSmall);
                             
             $url= '/upload/messages/img/'.$cur_dir.'/'; 
             $url_orig= $url.$newname;
             $url_middle= $url.$imageNameMiddle;
             $url_small= $url.$imageNameSmall;
                        
             $attachment = Attachment::create([
                'att_type' => $file_type,
                'img_original' => $url_orig,
                'img_middle' => $url_middle,
                'img_small' => $url_small,
                'user_id' => $user_id
            ]); 
            $attachment_id=$attachment->id;
            
            $image64=$file_path.$imageNameMiddle;
            $imageData64 = base64_encode(file_get_contents($image64));             
            $src64 = 'data: ' . mime_content_type($image64) . ';base64,' . $imageData64;
            
            $image64_f=$file_path.$newname;
            $imageData64_f = base64_encode(file_get_contents($image64_f));             
            $src64_f = 'data: ' . mime_content_type($image64_f) . ';base64,' . $imageData64_f;
           
          }
        
      }
       
       return response()->json([
                 'type' => 'upload_file',
                 'message' => 'OK',
                 'file_url_small' => $url_small,
                 'file_url_original' => $url_orig,
                 'attachment'=>$attachment_id,
                 'src64' => $src64,
                 'src64_f' => $src64_f
            ], 200);  
                                                                                 
    } 
    
    
    
    public function getAttachment(Request $request)
    {    
          $user_id = $request->user_id;
          $token = $request->token; 
          
          $usr = $this->checkUser($user_id, $token);
          if(!$usr){
             return response()->json([
                  'type' => 'get_attachment',
                  'message' => 'ERROR',
                  'txt' => 'check_user'
             ], 200);
          } 
    
          $attachment_id = $request->attachment_id;                         
          $attachment=Attachment::findOrFail($attachment_id);
          $img_middle=$attachment->img_middle;
          $img_original=$attachment->img_original;
          $att_type=$attachment->att_type;
           
          if($att_type==1) {

          $path_img_middle= storage_path().'/app/private'.$img_middle;
          $path_img_original= storage_path().'/app/private'.$img_original;
                   
          $imageData64 = base64_encode(file_get_contents($path_img_middle));             
          $src64 = 'data: ' . mime_content_type($path_img_middle) . ';base64,' . $imageData64;
          
           $imageData64_f = base64_encode(file_get_contents($path_img_original));             
           $src64_f = 'data: ' . mime_content_type($path_img_original) . ';base64,' . $imageData64_f;
           
             return response()->json([
                 'type' => 'get_attachment',
                 'message' => 'OK',
                 'src64' => $src64,
                 'src64_f' => $src64_f
            ], 200); 
          }  
            
    } 
    
    
    
  public function sendAction(Request $request)
    {
        $user_id = $request->user_id;
        $token = $request->token; 
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'send_action',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        } 
               
        $a_date = $request->a_date;                           
        $sending_user = $request->sending_user;
        $action = $request->action; 
        
         $to = $sending_user;
         $type = 'action';
         $data['action']  = $action; 
         $this->Push($to, $type, $data);     
            
         
         return response()->json([
                'type' => 'send_action',
                'message' => 'OK',
        ], 200);
    }
    
    
    
    public function setStatus(Request $request)
    {
        $user_id = $request->user_id;
        $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'set_status',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        }
        
        $sending_user = $request->sending_user; 
        $channel = $request->channel;
        $status_type= $request->status_type;
        
         $to = $sending_user;
         $type = 'action';
         if($status_type==1) {
           $data['action']  = 'Подключился к диалогу...';
         }
         if($status_type==0) {
           $data['action']  = 'Отключился... ';
         }           
         $this->Push($to, $type, $data);
         
         
         $to = $sending_user;
         $type = 'status';
         if($status_type==1) {
           $data['user_current_status']  = 'Онлайн';
         }
         if($status_type==0) {
           $data['user_current_status']  = 'Оффлайн';
         }           
         $this->Push($to, $type, $data);
              
        
        $online = Online::updateOrCreate(
          [
             'user_id'   => $user_id,
             'channel'   => $channel
          ],
          [
             'online'    => $status_type 
          ],
      ); 
        
            
       if($status_type==0) {
           Auth::logout();
           return response()->json([
                'type' => 'set_status',
                'message' => 'OK',
        ], 200);       
       }     
     
     if($status_type==1) {          
        return response()->json([
                'type' => 'set_status',
                'message' => 'OK',
        ], 200);
     }
        
    }
    
    
 public function setLogout(Request $request)
    { 
        $user_id = $request->user_id;
        $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'set_logout',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        } 
       
        Auth::logout();                          
        return response()->json([
                'type' => 'set_logout',
                'message' => 'OK',               
        ], 200);
    }
    
    
    public function  getMessages(Request $request)
    {
        $user_id = $request->user_id;
        $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'get_messages',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        }
               
        $channel = $request->channel;
        $user_id = $request->user_id;
        $sending_user = $request->sending_user;
        $param = $request->param;
        $to_user_id = $request->to_user_id;
        
        $to_user=User::findOrFail($to_user_id);
        $to_user_name=$to_user->name;
        
        $all_messages=Message::where('channel', $channel)->where('read_message', 1)->where('deleted', 0)->get();
        $count_messages=$all_messages->count();
        
        if($count_messages <= $param) {
            $messages=Message::where('channel', $channel)->where('read_message', 1)->where('deleted', 0)->orderBy('id', 'ASC')->get();
        } else {
            $skip = $count_messages - $param;
            $messages=Message::where('channel', $channel)->where('read_message', 1)->where('deleted', 0)->orderBy('id', 'ASC')->skip($skip)->take($param)->get();
        }
        
        
        
        $msg='';                        
        if(count($messages)){
           foreach($messages as $mes) {
               
                 if($user_id == $mes->quote_user_id){
                      $q_user='Вы';
                  } else {
                         if($mes->quote_user_id!=0){
                            $q_user_info=User::findOrFail($mes->quote_user_id);
                            $q_user=$q_user_info->name;
                         } else {
                             $q_user='';
                         }                     
                  }
                  if($mes->quote!=''){                  
                         $quote='<div class="quote_bx"><div class="q_user">'. $q_user.'</div><div class="q_msg">'.stripslashes($mes->quote).'</div></div>';
                  } else {
                     $quote='';
                  }
           
               if($mes->from_user_id==$user_id){
                     $message_class='msg_user';
                     $fa_check='<i class="fa fa-check fa-green" aria-hidden="true" id="check_message_id_'.$mes->id.'" style="font-size:12px;"></i>';
                     $name='<div class="usr_n">Вы</div> 
                     <div class="act_panel">
                     <i class="fa fa-pencil-square-o mrr5 csp clr_green2 edit_mess" aria-hidden="true" style="font-size: 17px;" title="Редактировать" data-mess_id="'.$mes->id.'" onclick="editMessage('.$mes->id.')"></i>
                     <i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" data-mess_id="'.$mes->id.'" onclick="toQuoteMessage('.$mes->id.')"></i>
                     <i class="fa fa-trash-o mrl5 clr_red csp delete_mess" aria-hidden="true" style="font-size:17px;" title="Удалить" data-mess_id="'.$mes->id.'" onclick="deleteMessage('.$mes->id.')"></i>
                     </div>
                     <div class="clr">
                     </div>';
                     if($mes->read_at!=null) {
                         $dt = Carbon::parse($mes->send_at)->format('d.m.Y H:i:s');
                     } else {
                        $dt = Carbon::parse($mes->send_at)->format('d.m.Y H:i:s');
                     }
               } else {
                    $message_class='msg';
                    $fa_check='';
                    $name= '<div class="usr_n">'.$to_user_name.'</div><div class="act_panel">
                    <i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" data-mess_id="'.$mes->id.'" onclick="toQuoteMessage('.$mes->id.')"></i>
                    </div><div class="clr"></div>';
                    $dt= Carbon::parse($mes->send_at)->format('d.m.Y H:i:s');
               }
               
               $atts='';
               $attachment_id=$mes->attachment_id;
               if($attachment_id!=null){
                    $attachment=Attachment::findOrFail($attachment_id);
                    $att_type=$attachment->att_type;
                    if($att_type==1){
                       $att=$this->getAttachmentImage($attachment_id);
                       $atts.='<a href="'.$att[1].'" data-lightbox="'.$attachment_id.'"><img src="'.$att[0].'" /></a><br>';
                    }                                       
               }
                     
               if($mes->edited==1){
                   $edt='<span style="margin-left:10px;">[ <i class="fa fa-pencil" aria-hidden="true" style="color: #000000;"></i> '.Carbon::parse($mes->edited_at)->format('d.m.Y H:i:s').']</span>';
               } else {
                   $edt='';
               }      
                                            
               $msg.='<div class="'.$message_class.'" id="msg_'.$mes->id.'">
                 <div class="mes_user_name">'.$name.'</div>
                 '.$quote.'
                 <div class="send_text">'.$atts.'<span id="ms_'.$mes->id.'">'.stripslashes($mes->message).'</span></div>
                 <div class="send_date">'.$dt.$fa_check.'<span id="mes_lab_'.$mes->id.'">'.$edt.'</span></div>
                 </div>';
           }
        } 
        
        
        $new_msg='';
        $n_msg='';  
        $new_messages=Message::where('channel', $channel)->where('read_message', 0)->where('deleted', 0)->orderBy('id', 'ASC')->get();       
        if(count($new_messages)){
            $message_ids=array();           
            foreach($new_messages as $n_mes) {
                             
                  if($user_id == $n_mes->quote_user_id){
                      $q_user='Вы';
                  } else {
                         if($n_mes->quote_user_id!=0){
                            $q_user_info=User::findOrFail($n_mes->quote_user_id);
                            $q_user=$q_user_info->name;
                         } else {
                             $q_user='';
                         }                     
                  }
                  if($n_mes->quote!=''){
                     $quote='<div class="quote_bx"><div class="q_user">'. $q_user.'</div><div class="q_msg">'.stripslashes($n_mes->quote).'</div></div>';
                  } else {
                     $quote='';
                  }
            
                  if($n_mes->from_user_id==$user_id){
                     $message_class='msg_user';
                     $fa_check='<i class="fa fa-check fa-grey" aria-hidden="true" id="check_message_id_'.$n_mes->id.'" style="font-size:12px;"></i>';
                      $name='<div class="usr_n">Вы</div> 
                     <div class="act_panel">
                     <i class="fa fa-pencil-square-o mrr5 csp clr_green2 edit_mess" aria-hidden="true" style="font-size: 17px;" title="Редактировать" data-mess_id="'.$n_mes->id.'" onclick="editMessage('.$n_mes->id.')"></i>
                     <i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" data-mess_id="'.$n_mes->id.'" onclick="toQuoteMessage('.$n_mes->id.')"></i>
                     <i class="fa fa-trash-o mrl5 clr_red csp delete_mess" aria-hidden="true" style="font-size:17px;" title="Удалить" data-mess_id="'.$n_mes->id.'" onclick="deleteMessage('.$n_mes->id.')"></i>
                     </div>
                     <div class="clr">
                     </div>';
                     if($n_mes->read_at!=null) {
                        $dt = Carbon::parse($n_mes->send_at)->format('d.m.Y H:i:s');
                     } else {
                        $dt = Carbon::parse($n_mes->send_at)->format('d.m.Y H:i:s');
                     }
               } else {
                    $message_class='msg';
                    $fa_check='';
                    $name= '<div class="usr_n">'.$to_user_name.'</div><div class="act_panel">
                    <i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" data-mess_id="'.$n_mes->id.'" onclick="toQuoteMessage('.$n_mes->id.')"></i>
                    </div><div class="clr"></div>';
                    $dt= Carbon::parse($n_mes->send_at)->format('d.m.Y H:i:s');
                    $message_ids[]=$n_mes->id;    
               }
               
               $new_atts='';
               $attachment_id=$n_mes->attachment_id;
               if($attachment_id!=null){
                    $attachment=Attachment::findOrFail($attachment_id);
                    $att_type=$attachment->att_type;
                    if($att_type==1){
                       $att=$this->getAttachmentImage($attachment_id);
                       $new_atts.='<a href="'.$att[1].'" data-lightbox="'.$attachment_id.'"><img src="'.$att[0].'" /></a><br>';
                    }                                       
               }
                     
                if($n_mes->edited==1){
                   $edt='<span style="margin-left:10px;">[ <i class="fa fa-pencil" aria-hidden="true" style="color: #000000;"></i> '.Carbon::parse($n_mes->edited_at)->format('d.m.Y H:i:s').']</span>';
               } else {
                   $edt='';
               }      
                                            
                 $new_msg.='<div class="'.$message_class.'" id="msg_'.$n_mes->id.'">
                 <div class="mes_user_name">'.$name.'</div>
                 '.$quote.'
                 <div class="send_text">'.$new_atts.'<span id="ms_'.$n_mes->id.'">'.stripslashes($n_mes->message).'</span></div>
                 <div class="send_date">'.$dt.$fa_check.'<span id="mes_lab_'.$n_mes->id.'">'.$edt.'</span></div>
                 </div>';
            }
            
               if(!empty($message_ids)){
                    $n_msg='<div class="new_message_bx">Новые сообщения</div>';
                } else {
                     $n_msg='';
                }
        }
        
        if(!empty($message_ids)){                             
            $date = Carbon::now();
            $carbon_date = Carbon::parse($date);
            $carbon_date->addHours(5);
            $dt = Carbon::parse($carbon_date)->format('Y-m-d H:i:s');
            
            Message::whereIn('id', $message_ids)->update(['read_message' => 1, 'read_at'=>$dt]);
            
            $to = $sending_user;
            $type = 'read_message';                    
            foreach ($message_ids as $res) {
               $data['message_id']  = $res; 
               $this->Push($to, $type, $data);  
            }                     
        }
        
        return response()->json([
                'type' => 'get_messages',
                'message' => 'OK',
                'msg' => $msg.$n_msg.$new_msg,
                'cnt' => $count_messages
        ], 200);
    }
    
    
    public function getAttachmentImage($attachment_id)
    {
          $attachment=Attachment::findOrFail($attachment_id);
          $img_middle=$attachment->img_middle;
          $img_original=$attachment->img_original;
          
          $path_img_middle= storage_path().'/app/private'.$img_middle;
          $path_img_original= storage_path().'/app/private'.$img_original;
          
          $imageData64 = base64_encode(file_get_contents($path_img_middle));             
          $src64 = 'data: ' . mime_content_type($path_img_middle) . ';base64,' . $imageData64;
          
          $imageData64_f = base64_encode(file_get_contents($path_img_original));             
          $src64_f = 'data: ' . mime_content_type($path_img_original) . ';base64,' . $imageData64_f;
          
          return [$src64, $src64_f];
    } 
    
    
    public function checkUser($user_id, $token)
    {
        $user = User::where('id', $user_id)->where('current_token', $token)->first();   
        if($user){
           return true;
        } else {
           return false;
        }       
    } 
    
    
  public function editMessage(Request $request)
    {     
        $user_id = $request->user_id;
        $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'edit_message',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        }
    
        $message_id = $request->message_id;
        $message_txt = $request->message;
        
         $timestamp = $request->timestamp;
         $edited_at = Carbon::parse($timestamp)->format('Y-m-d H:i:s');
                                
        $edit_message=Message::findOrFail($message_id);
        $old_message = $edit_message->message;
        $edit_message->old_message=$old_message;
        $edit_message->message= $message_txt;
        $edit_message->edited = 1;
        $edit_message->edited_at=$edited_at;
        $edit_message->save();
        
         $sending_user=$request->sending_user;
        
         $to = $sending_user;
         $type = 'edit_message';
         $data['message_id']  = $message_id; 
         $data['message_txt']  = $message_txt;
         $data['message_label']  = '<span style="margin-left:10px;">[ <i class="fa fa-pencil" aria-hidden="true" style="color: #000000;"></i> '.Carbon::parse($timestamp)->format('d.m.Y H:i:s').']</span>';
         $this->Push($to, $type, $data);
    
        return response()->json([
                'type' => 'edit_message',
                'message' => 'OK',
        ], 200);
    }
    
    
    
    
  public function deleteMessage(Request $request)
    {     
        $user_id = $request->user_id;
        $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'delete_message',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        }
    
         $message_id = $request->message_id;       
         $timestamp = $request->timestamp;
         $deleted_at = Carbon::parse($timestamp)->format('Y-m-d H:i:s');
                                
        $deleted_message=Message::findOrFail($message_id);
        $deleted_message->deleted = 1;
        $deleted_message->deleted_at=$deleted_at;
        $deleted_message->save();
        
         $sending_user=$request->sending_user;
        
         $to = $sending_user;
         $type = 'delete_message';
         $data['message_id']  = $message_id; 
         $this->Push($to, $type, $data);
    
        return response()->json([
                'type' => 'delete_message',
                'message' => 'OK',
        ], 200);
    }
    
    
    
    
  public function quoteMessage(Request $request)
    {     
        $user_id = $request->user_id;
        $token = $request->token;
        
        $usr = $this->checkUser($user_id, $token);
        if(!$usr){
           return response()->json([
                'type' => 'quote_message',
                'message' => 'ERROR',
                'txt' => 'check_user'
           ], 200);
        }
    
         $message_id = $request->message_id;       
         $timestamp = $request->timestamp;
         $message_txt = $request->message;                       

         $quote_message=Message::findOrFail($message_id);
         $q_message = $quote_message->message;
         $q_attachment_id=$quote_message->attachment_id;
         if($q_message!=''){
            $quote = Str::substr($q_message, 0, 30).'...';
         } else {
            if($q_attachment_id!=''){
                $attachment=Attachment::findOrFail($q_attachment_id);
                $att_type= $attachment->att_type;
                if($att_type==1){
                   $quote ='<a>Изображение</a>';
                }
                if($att_type==2){
                   $quote ='Видео';
                }
            } else {
               $quote ='...';
            }            
         }
         
         $from_user_id=$quote_message->from_user_id;
         
         $attachment_id = $request->attachment_id;
         $to_user_id = $request->to_user_id;

         $sending_user = $request->sending_user;
         $channel = $request->channel;
         
                 
         $new_message = Message::create([
                'message' => $message_txt,
                'read_message' => 0,
                'from_user_id' => $user_id,
                'to_user_id' =>  $to_user_id,
                'send_at' => Carbon::parse($timestamp)->format('Y-m-d H:i:s'),
                'channel' => $channel,
                'attachment_id' =>$attachment_id,
                'quote' => $quote,
                'quote_user_id'=>  $from_user_id
        ]); 
        $new_message_id=$new_message->id;
         
                               
         if($from_user_id==$user_id){
            $quote_user_name='Вы';
         } else {
            $q_user_info=User::findOrFail($from_user_id);
            $quote_user_name=$q_user_info->name;
         }
         
         
         
         $to = $sending_user;
         $type = 'quote_message';
         $data['message_id']  = $new_message_id;
         $data['message']  = $message_txt; 
         $data['quote']  = $quote;
         $data['quote_user_name']  = $quote_user_name;  
         $data['data_send']  =  $timestamp; 
         $this->Push($to, $type, $data);  
    
        return response()->json([
                'type' => 'quote_message',
                'message' => 'OK',
                'message_id' =>$new_message_id,
                'sended_message' => $message_txt,
                'quote' => $quote,
                'quote_user_name' =>$quote_user_name
        ], 200);
    }
    
    
    
    
    
} 
