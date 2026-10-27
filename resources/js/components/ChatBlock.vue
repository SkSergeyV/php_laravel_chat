<script>

export default {
  props: ['user_id',
          'to_user_name', 
          'to_user_avatar',
          'to_user_id',
          'token',
          'reception_user',
          'sending_user',
          'channel',
          'user_current_status'
          ],
  data() {
    return {
         message_loading : false,
         messages: [],
         desc_dialogue: '',
         send_loading: false,
         timestamp: '',
         up_file: null,
         file_selected: false,
         file_type: '',
         file_selectedZZZ: false,
         up_file_url_preview: '',
         up_file_url_full : [],
         file_upload_success: false,
         file_upload_process: false,
         attachment_id: '',
         attachment_type: '',
         actions: ['Печатает...', 'Загружает изображение...', 'Отправляет изображение...', 'Отменил отправку изображения...', '...', 'Подключился к диалогу...', 'Отключился...'],
         current_action: '',
         u_current_status: this.user_current_status,
         visible_smile_box: false,

    }
  },
  
  computed: {
    sending(){
        if(this.desc_dialogue!='' || this.attachment_id!=''){
                return false;
         } else {
                return true;
            }
        },            
  },
   
   methods: {
   
     sendMsg(){
         var edit = document.getElementById('edit_ms_id'); 
         var edit_ms_id = edit.value;
         
         var quote = document.getElementById('quote_ms_id'); 
         var quote_ms_id = quote.value;
           
         if(edit_ms_id!='' || quote_ms_id!=''){
           if(edit_ms_id!=''){ 
            this.sendEditedMessage(edit_ms_id);
           }
         if(quote_ms_id!=''){ 
            this.sendQuoteMessage(quote_ms_id);
           } 
         } else {
            this.sendMessage();
         }
     },
     
      
    sendEditedMessage(id){
            this.send_loading = true; 
            axios.post("/app/edit_message", {
                        edit_message: 1,
                        token: this.token,
                        user_id: this.user_id,
                        message_id: id,
                        message: this.desc_dialogue,
                        sending_user: this.sending_user,
                        timestamp: this.timestamp,
               }).then(({data}) => {
                       if(data.message=='OK'){                                                                                 
                       }                                                                
                   }).finally(() => {
                         var mess_edited = document.getElementById('ms_'+id); 
                         mess_edited.innerHTML=this.desc_dialogue;     
                         this.send_loading = false;
                                                 
                         this.desc_dialogue='';
                         var edit = document.getElementById('edit_ms_id'); 
                         edit.value = '';
                         document.querySelector('#cancel_edit').style.cssText = 'display:none;';
                         
                        var lab_edited = document.getElementById('mes_lab_'+id); 
                            lab_edited.innerHTML='<span style="margin-left:10px;">[ <i class="fa fa-pencil" aria-hidden="true" style="color: #000000;"></i> '+this.timestamp+']</span>';
                          document.querySelector('#msg_'+id).style.cssText = 'border: none;';                                                               
               });
    },  
    
    
    sendQuoteMessage(id){
            this.send_loading = true; 
            axios.post("/app/quote_message", {
                        quote_message: 1,
                        token: this.token,
                        user_id: this.user_id,
                        message_id: id,
                        message: this.desc_dialogue,
                        sending_user: this.sending_user,
                        timestamp: this.timestamp,
                        channel: this.channel,
                        attachment_id: this.attachment_id,
                        to_user_id: this.to_user_id, 
               }).then(({data}) => {
                       if(data.message=='OK'){  
                          var message_id = data.message_id;
                          var quote='<div class="quote_bx"><div class="q_user">'+data.quote_user_name+'</div><div class="q_msg">'+data.quote+'</div></div>';
                          this.messages.push('<div class="msg_user" id="msg_'+message_id+'"><div class="mes_user_name">'+'<div class="usr_n">Вы</div><div class="act_panel"><i class="fa fa-pencil-square-o mrr5 csp clr_green2 edit_mess" aria-hidden="true" style="font-size: 17px;" title="Редактировать" onclick="editMessage('+message_id+')"></i><i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" onclick="toQuoteMessage('+message_id+')"></i><i class="fa fa-trash-o mrl5 clr_red csp delete_mess" aria-hidden="true" style="font-size:17px;" title="Удалить" onclick="deleteMessage('+message_id+')"></i></div><div class="clr"></div>'+'</div>'+quote+'<div class="send_text"><span id="ms_'+message_id+'">'+this.desc_dialogue+'</span></div>'+'<div class="send_date">'+this.timestamp+'<i class="fa fa-check fa-grey" aria-hidden="true" id="check_message_id_'+message_id+'" style="font-size:12px;"></i><span id="mes_lab_'+message_id+'"></span></div></div>');                                                                               
                       }                                                                
                   }).finally(() => {   
                         this.send_loading = false;                                                
                         this.desc_dialogue='';
                         var quote = document.getElementById('quote_ms_id'); 
                         quote.value = '';
                         document.querySelector('#cancel_quote').style.cssText = 'display:none;';                         
                         document.querySelector('#msg_'+id).style.cssText = 'border: none;';
                         this.scrollText();                                                               
               }); 
    },
     
   
      sendMessage(){
          this.sendAction(this.actions[4]);                               
          this.send_loading = true; 
          axios.post("/app/send_message", {
          send_message: 1,
          message: this.desc_dialogue,
          token: this.token,
          user_id: this.user_id,
          attachment_id: this.attachment_id,
          to_user_id: this.to_user_id,
          timestamp: this.timestamp,
          sending_user: this.sending_user,
          channel: this.channel
          }).then(({data}) => {
            if(data.message=='OK'){
            
                if(data.attachment_id=='' || data.attachment_id==null){
                  var message_id = data.message_id;
                  this.messages.push('<div class="msg_user" id="msg_'+message_id+'"><div class="mes_user_name">'+'<div class="usr_n">Вы</div><div class="act_panel"><i class="fa fa-pencil-square-o mrr5 csp clr_green2 edit_mess" aria-hidden="true" style="font-size: 17px;" title="Редактировать" onclick="editMessage('+message_id+')"></i><i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" onclick="toQuoteMessage('+message_id+')"></i><i class="fa fa-trash-o mrl5 clr_red csp delete_mess" aria-hidden="true" style="font-size:17px;" title="Удалить" onclick="deleteMessage('+message_id+')"></i></div><div class="clr"></div>'+'</div>'+'<div class="send_text"><span id="ms_'+message_id+'">'+this.desc_dialogue+'</span></div>'+'<div class="send_date">'+this.timestamp+'<i class="fa fa-check fa-grey" aria-hidden="true" id="check_message_id_'+message_id+'" style="font-size:12px;"></i><span id="mes_lab_'+message_id+'"></span></div></div>');
                } else {                 
                    var attachment_id = data.attachment_id;
                    var message_id = data.message_id;
                    var att_type= data.att_type;
                    var sended_message=data.sended_message;
                    if(sended_message=='' || sended_message==null){
                        sended_message = '';
                     }
                
                   if(data.att_type==1) {
                         axios.post("/app/get_attachment", {
                              get_attachment: 1,
                              token: this.token,
                              user_id: this.user_id,
                              attachment_id: attachment_id
                          }).then(({data}) => {
                          if(data.message=='OK'){                
                              var src64=data.src64;
                              var src64_f=data.src64_f;                             
                              var img='<a href="'+src64_f+'" data-lightbox="'+attachment_id+'"><img src="'+src64+'" /></a><br>';  
                              this.messages.push('<div class="msg_user" id="msg_'+message_id+'"><div class="mes_user_name">'+'<div class="usr_n">Вы</div><div class="act_panel"><i class="fa fa-pencil-square-o mrr5 csp clr_green2 edit_mess" aria-hidden="true" style="font-size: 17px;" title="Редактировать" onclick="editMessage('+message_id+')"></i><i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" onclick="toQuoteMessage('+message_id+')"></i><i class="fa fa-trash-o mrl5 clr_red csp delete_mess" aria-hidden="true" style="font-size:17px;" title="Удалить" onclick="deleteMessage('+message_id+')"></i></div><div class="clr"></div>'+'</div>'+'<div class="send_text">'+img+'<span id="ms_'+message_id+'">'+sended_message+'</span></div>'+'<div class="send_date">'+this.timestamp+'<i class="fa fa-check fa-grey" aria-hidden="true" id="check_message_id_'+message_id+'" style="font-size:12px;"></i><span id="mes_lab_'+message_id+'"></span></div></div>');                                         
                          }                                                                
                         }).finally(() => {                                  
                         });                                                                  
                   }                      
                }                                            
            }             
                                                   
          }).finally(() => {
              this.send_loading = false;
              this.desc_dialogue='';
              this.scrollText();
              this.attachment_id = '';
                if(this.attachment_type==1){
                  this.remove_upload_image(1);
                  this.attachment_type='';
                }
          });                                                 
       },
      
       scrollText(){
          var $t = $('.dialogue_window');
           $t.animate({"scrollTop": $('.dialogue_window')[0].scrollHeight}, "slow");
        },
        
        logout(){
            window.location.href = "/logout";
        },
        
        setLogout(){
            axios.post("/app/set_logout", {
                        set_logout: 1,
                        token: this.token,
                        user_id: this.user_id,
               }).then(({data}) => {
                       if(data.message=='OK'){ 
                         window.location.href = "/";                                                          
                       }                                                                
                   }).finally(() => {                                  
               });              
        },
        
        addMessage(){
            this.messages.push('<div class="msg">'+message+'</div>');
        }, 
        
                
        showSelectFile(file_type){
           if(file_type==1){
            this.$refs.files_img.click();
           }
           if(file_type==2){
            this.$refs.files_video.click();
           }
           if(file_type==3){
            this.$refs.files_doc.click();
           }

           this.file_type=file_type;
        },
        
        getNow: function() {                     
                  var MyDate = new Date();
                  var MyDateString = '';
                  MyDate.setDate(MyDate.getDate());
                  var tempoMonth = (MyDate.getMonth()+1);
                  var tempoDate = (MyDate.getDate());
                  if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
                  if (tempoDate < 10) tempoDate = '0' + tempoDate;
                  MyDateString = tempoDate + '.' + tempoMonth + '.' + MyDate.getFullYear();
                  
                  var MyTimeString = '';
                  var tempoHours = (MyDate.getHours());
                  if (tempoHours < 10) tempoHours = '0' + tempoHours;
                  var tempoMinutes = (MyDate.getMinutes());
                  if (tempoMinutes < 10) tempoMinutes = '0' + tempoMinutes;
                  var tempoSeconds=MyDate.getSeconds();
                  if (tempoSeconds < 10) tempoSeconds = '0' + tempoSeconds;
                  MyTimeString = tempoHours+ ':' + tempoMinutes+':'+String(tempoSeconds);
                  
                  this.timestamp = MyDateString + ' '+ MyTimeString;                  
        },
        
              
        
         onChangeImageUpload(event) {
                this.up_file=event.target.files[0];
                if(this.up_file!=''){
                   this.file_selected=true;
                   this.upload_image();
                }               
            },
        
         onChangeVideoUpload($event){
              this.up_file=event.target.files[0];
                if(this.up_file!=''){
                   this.upload_video();
                } 
         },
         
         onChangeDocUpload($event){
              this.up_file=event.target.files[0];
                if(this.up_file!=''){
                   this.upload_doc();
                } 
         },    
                                          
         upload_image(){
            console.log(this.up_file);
            var formData = new FormData();
            formData.append('file', this.up_file);
            formData.append('upload_file', 1);
            formData.append('file_type', this.file_type);
            formData.append('user_id', this.user_id);
            formData.append('token', this.token);
           
            this.file_upload_process=true;    
              axios.post( '/app/upload_file',
                formData,
                {
                  headers: {
                      'Content-Type': 'multipart/form-data'
                  }
                }
               ).then(({data}) => {                     
                      this.up_file_url_preview=data.file_url_small;
                      this.up_file_url_full.push(data.file_url_original);
                      this.attachment_id = data.attachment;                                                                                    
               }).finally(() => {
                  this.up_file=null;
                  this.file_selected=false;
                  this.file_type='';
                  this.file_upload_process = false;
                  this.file_upload_success = true;
                  this.attachment_type=1;              
               });
              this.sendAction(this.actions[1]);
         }, 
         
         remove_upload_image(param){
             this.up_file_url_preview='';
             this.up_file_url_full=[];
             this.file_upload_success = false;
             this.attachment_type='';
             this.attachment_id = ''; 
             if(param==0){
                 this.sendAction(this.actions[3]);
             }             
         },
         
         upload_video(){
            console.log('upload_video');
         },
         upload_doc(){
              console.log('upload_doc');
         },
         
         
         setReadMessage(message_id){
            axios.post("/app/set_read_message", {
                        set_read_message: 1,
                        token: this.token,
                        user_id: this.user_id,
                        message_id: message_id,
                        sending_user: this.sending_user,
                        r_date: this.timestamp
                    }).then(({data}) => {
                       if(data.message=='OK'){                                                           
                       }                                                                
                   }).finally(() => {                                  
                   });         
         },
         
         
         setCheckReadMessage(message_id){
            var el= 'check_message_id_'+message_id;                      
            var element = document.querySelector('#'+el);
            element.classList.remove('fa-grey');
            element.classList.add('fa-green'); 
         },
         
         sendAction(action){
            axios.post("/app/send_action", {
                        send_action: 1,
                        token: this.token,
                        user_id: this.user_id,
                        sending_user: this.sending_user,
                        a_date: this.timestamp,
                        action: action
                    }).then(({data}) => {
                       if(data.message=='OK'){                                                           
                       }                                                                
                   }).finally(() => {                                  
                   });  
         },
         
         onFocus() {
            this.sendAction(this.actions[0]);
          },
          
          onBlur() {
            this.sendAction(this.actions[4]);
          },
          
          setOnline(){
            axios.post("/app/set_status", {
                        set_status: 1,
                        token: this.token,
                        user_id: this.user_id,
                        sending_user: this.sending_user,
                        channel: this.channel,
                        status_type: 1
                    }).then(({data}) => {
                       if(data.message=='OK'){                                                           
                       }                                                                
                   }).finally(() => {                                  
             });
          }, 
          
          setOffline(){
            axios.post("/app/set_status", {
                        set_status: 1,
                        token: this.token,
                        user_id: this.user_id,
                        sending_user: this.sending_user,
                        channel: this.channel,
                        status_type: 0
                    }).then(({data}) => {
                       if(data.message=='OK'){
                          window.location.href = "/";                                                           
                       }                                                                
                   }).finally(() => {                                  
             });
          }, 
          
          
          setStatus(){
                if(this.u_current_status=='Онлайн'){
                    this.$refs['status_color'].classList.remove('clr_red');
                    this.$refs['status_color'].classList.add('clr_green');
                }
                if(this.u_current_status=='Оффлайн'){
                    this.$refs['status_color'].classList.remove('clr_green');
                    this.$refs['status_color'].classList.add('clr_red');
                } 
          },
          
          
          getMessages(param){
            this.message_loading=true;
            axios.post("/app/get_messages", {
                        get_messages: 1,
                        token: this.token,
                        user_id: this.user_id,
                        sending_user: this.sending_user,
                        channel: this.channel,
                        param: param,
                        to_user_id: this.to_user_id
                    }).then(({data}) => {
                       if(data.message=='OK'){
                            this.messages.push(data.msg);                                                           
                       }                                                                
                   }).finally(() => {
                            this.message_loading=false;
                            this.scrollText();                                  
                   });
          }, 
          
          getMessagesLimit(param){
               this.messages=[];
               this.getMessages(param);   
          },
          
           insertSmile(smile){                      
              this.InsertInTextarea(smile, document.getElementById('txt_dialogue'));
              var txt_dialogue = document.getElementById('txt_dialogue');   
              var text = txt_dialogue.value;
              this.desc_dialogue = text;
              this.visible_smile_box=false;  
           },
          
          insertSmileZZZ(smile){
             //this.desc_dialogue = this.desc_dialogue+smile;
            // var txt_dialogue = document.getElementById('txt_dialogue');   
           //  var text = txt_dialogue.value;
           //  console.log(text);
           //  txt_dialogue.value = text + smile;
           //  this.visible_smile_box=false;
           
           /*
             var text_cnt = document.getElementById('txt_dialogue').value;
                         
             document.getElementById('txt_dialogue').value = text_cnt + smile; 
             this.desc_dialogue = text_cnt + smile;
             
             this.visible_smile_box=false;
            */ 
             
             /*
            const textarea = document.querySelector('textarea');
            textarea.setRangeText(
                smile,
                textarea.selectionStart,
                textarea.selectionEnd,
                'end'
            );
            var text_cnt = document.getElementById('txt_dialogue').value;
            this.desc_dialogue = text_cnt;
            this.visible_smile_box=false;
            */
            
            // document.getElementById('txt_dialogue').focus();
            
            //document.getElementById('txt_dialogue').dispatchEvent(new Event("change"));
            
             this.InsertInTextarea(smile, document.getElementById('txt_dialogue'));
          },
          
          InsertInTextarea(newText, el = document.activeElement) {  
            const start = el.selectionStart
            const end = el.selectionEnd
            const text = el.value
            const before = text.substring(0, start)
            const after  = text.substring(end, text.length)
            el.value = (before + newText + after)
            el.selectionStart = el.selectionEnd = start + newText.length
            el.focus()
          },
          
         cancelEdit(){                       
            var edit = document.getElementById('edit_ms_id');
            document.querySelector('#msg_'+edit.value).style.cssText = 'border: none;'; 
            edit.value = '';
            
            var txt_dialogue = document.getElementById('txt_dialogue'); 
            txt_dialogue.value = '';
            
            document.querySelector('#cancel_edit').style.cssText = 'display:none;';             
         },
         
         cancelDelete(){
               var del = document.getElementById('delete_ms_id');
               document.querySelector('#msg_'+del.value).style.cssText = 'border: none;';     
               del.value = '';
               document.querySelector('#cancel_delete').style.cssText = 'display:none;';
               document.querySelector('#delete_message').style.cssText = 'display:none;';                
         }, 
         
         deleteMessage(){
            this.message_loading=true; 
            var del = document.getElementById('delete_ms_id');
            var id=del.value;
            axios.post("/app/delete_message", {
                        delete_message: 1,
                        token: this.token,
                        user_id: this.user_id,
                        message_id: id,
                        sending_user: this.sending_user,
                        timestamp: this.timestamp,
               }).then(({data}) => {
                       if(data.message=='OK'){                                                                                 
                       }                                                                
                   }).finally(() => {
                       this.message_loading = false;     
                       var del = document.getElementById('delete_ms_id');
                       document.querySelector('#msg_'+del.value).style.cssText = 'display:none;';
                       del.value = '';
                       document.querySelector('#cancel_delete').style.cssText = 'display:none;';
                       document.querySelector('#delete_message').style.cssText = 'display:none;';                                                        
               });                            
         },
         
         cancelQuote(){
               var quote = document.getElementById('quote_ms_id');
               document.querySelector('#msg_'+quote.value).style.cssText = 'border: none;';     
               quote.value = '';
               document.querySelector('#cancel_quote').style.cssText = 'display:none;';  
         },
         
         sendNotification(title, options) {
              if (!("Notification" in window)) {
                 alert('Ваш браузер не поддерживает уведомления, его необходимо обновить.');
              }
              else if (Notification.permission === "granted") {
                var notification = new Notification(title, options);    
              }
              else if (Notification.permission !== 'denied') {
                Notification.requestPermission(function (permission) {
                  if (permission === "granted") {
                  var notification = new Notification(title, options);        
                  } else {
                     alert('Вы запретили показывать уведомления'); 
                  }
                  });
               } 
          },
                                            
   },
   
       created() {       
           setInterval(this.getNow, 1000);
                 
           Pusher.logToConsole = true;
           var pusher = new Pusher('8fd910642bc2b58c2eb7', {
              cluster: 'eu',
              encrypted: true
            });
            var channel = pusher.subscribe(this.reception_user);
                channel.bind('message', (data) => {
                if(data.attachment_id=='' || data.attachment_id==null){
                  var message_id = data.message_id;
                  this.messages.push('<div class="msg" id="'+message_id+'"><div class="mes_user_name"><div class="usr_n">'+this.to_user_name+'</div><div class="act_panel"><i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" onclick="toQuoteMessage('+message_id+')"></i></div><div class="clr"></div> </div>'+'<div class="send_text">'+data.message+'</div>'+'<div class="send_date">'+data.data_send+'</div></div>');
                } else {
                    var attachment_id = data.attachment_id;
                    var message=data.message;
                    var data_send = data.data_send;
                    var message_id = data.message_id;
                    var att_type= data.att_type;
                    
                     if(message=='' || message==null){
                        message = '';
                     }
                    
                   if(att_type==1) {
                    axios.post("/app/get_attachment", {
                        get_attachment: 1,
                        token: this.token,
                        user_id: this.user_id,
                        attachment_id: attachment_id
                    }).then(({data}) => {
                    if(data.message=='OK'){                
                        var src64=data.src64;
                        var src64_f=data.src64_f;
                        var img='<a href="'+src64_f+'" data-lightbox="'+attachment_id+'"><img src="'+src64+'" /></a><br>';  
                        this.messages.push('<div class="msg" id="msg_'+message_id+'"><div class="mes_user_name"><div class="usr_n">'+this.to_user_name+'</div><div class="act_panel"><i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" onclick="toQuoteMessage('+message_id+')"></i></div><div class="clr"></div></div>'+'<div class="send_text">'+img+message+'</div>'+'<div class="send_date">'+data_send+'</div></div>');                       
                    }                                                                
                   }).finally(() => {                                  
                   });
                 }  
                }
                this.scrollText();
                this.setReadMessage(message_id);
                                
                const options = {
                  body: data.message,
                  silent: true,
                  icon: this.to_user_avatar,
                };               
               // const n = new Notification(this.to_user_name, options);             
               this.sendNotification(this.to_user_name, options);
                                
            });
            
            
            channel.bind('read_message', (data) => {
                this.setCheckReadMessage(data.message_id);
            });
            
             channel.bind('action', (data) => {
                this.current_action=data.action;
            }); 
            
            channel.bind('status', (data) => {
                this.u_current_status=data.user_current_status;
                if(this.u_current_status=='Онлайн'){
                    this.$refs['status_color'].classList.remove('clr_red');
                    this.$refs['status_color'].classList.add('clr_green');
                }
                if(this.u_current_status=='Оффлайн'){
                    this.$refs['status_color'].classList.remove('clr_green');
                    this.$refs['status_color'].classList.add('clr_red');
                }               
            });
            
            channel.bind('edit_message', (data) => {
                var message_id=data.message_id;
                var message_txt=data.message_txt;
                
                 var mess_edited = document.getElementById('ms_'+message_id); 
                     mess_edited.innerHTML=message_txt; 
                     
                 var lab_edited = document.getElementById('mes_lab_'+message_id); 
                     lab_edited.innerHTML=data.message_label;        
            });
            
            
             channel.bind('delete_message', (data) => {
                var message_id=data.message_id;
                document.querySelector('#msg_'+message_id).style.cssText = 'display:none;';
            });
            
            
            channel.bind('quote_message', (data) => {
                  var message_id = data.message_id;
                  var quote='<div class="quote_bx"><div class="q_user">'+data.quote_user_name+'</div><div class="q_msg">'+data.quote+'</div></div>';
                  this.messages.push('<div class="msg" id="'+message_id+'"><div class="mes_user_name"><div class="usr_n">'+this.to_user_name+'</div><div class="act_panel"><i class="fa fa-commenting-o csp clr_orange quote_mess" aria-hidden="true" style="font-size: 17px;" title="Цитировать" onclick="toQuoteMessage('+message_id+')"></i></div><div class="clr"></div> </div>'+quote+'<div class="send_text">'+data.message+'</div>'+'<div class="send_date">'+data.data_send+'</div></div>');
                  
                  this.scrollText();
                  
                  this.setReadMessage(data.message_id);
                                                 
                  const options = {
                    body: data.message,
                    silent: true,
                    icon: this.to_user_avatar,
                  };               
                  //const n = new Notification(this.to_user_name, options);
                  this.sendNotification(this.to_user_name, options);
               
            });
                                                            
    }, 
    
  mounted() { 
        this.setOnline();
        this.setStatus();
        this.getMessages(50); 
                
        const script = document.createElement('script');
        script.src = '/js/fn_chat_block.js?v=1.01';
        document.head.appendChild(script); 
                    
    },
   
}


</script>



<template>

   <div class="panel_box">
   
        <div class="panel_button f_left">
              <el-image
                style="width: 35px; height: 35px"
                :src="to_user_avatar"
                :fit="cover">
               </el-image>
        </div>
        
        <div class="user_info">
              <div class="user_bx">
                   <span class="user_name">{{to_user_name}}</span> 
                   <span class="user_status"><i class="fa fa-circle clr_green" aria-hidden="true" ref="status_color"></i></span> 
                   <span class="user_status">{{u_current_status}}</span>
              </div>
              <div class="user_action">{{ current_action }}</div>
        </div>
        
                  
       <div class="panel_button f_right mrr5">  
            <el-button type="danger" @click="setOffline()"><i class="fa fa-power-off" aria-hidden="true" title="Выход"></i></el-button>
       </div>
       
       <div class="panel_button f_right mrr5">  
                 <el-dropdown>
                     <el-button type="info" plain>
                       <i class="fa fa-tasks" aria-hidden="true"></i>
                    </el-button>
                   <template #dropdown>
                    <el-dropdown-menu>
                      <el-dropdown-item @click="getMessagesLimit(100)"><i class="fa fa-file-text-o mrl5" aria-hidden="true"></i>Последние 100</el-dropdown-item>
                      <el-dropdown-item @click="getMessagesLimit(500)"><i class="fa fa-file-text-o mrl5" aria-hidden="true"></i>Последние 500</el-dropdown-item>
                      <el-dropdown-item @click="getMessagesLimit(1000)"><i class="fa fa-file-text-o mrl5" aria-hidden="true"></i>Последние 1000</el-dropdown-item>
                    </el-dropdown-menu>
                  </template>
                </el-dropdown>   
       </div>
   
   </div> 
   
   

   <div class="dialogue_window" id="dialogue_window" v-loading="message_loading">
      <div v-for="message in messages" v-html="message"></div>        
   </div>
   
   <div class="panel_box">
   
       <div class="panel_button f_left">                   
          <el-popover placement="right" :width="320" trigger="click" :visible="visible_smile_box">
            <template #reference>
              <el-button style="" @click="visible_smile_box = true"><i class="fa fa-smile-o" aria-hidden="true"></i></el-button>
            </template>
            <template #default>
                <span class="bx_smail" @click="insertSmile('&#128512;')">&#128512;</span> 
                <span class="bx_smail" @click="insertSmile('&#128515;')">&#128515;</span>
                <span class="bx_smail" @click="insertSmile('&#128513;')">&#128513;</span>
                <span class="bx_smail" @click="insertSmile('&#128525;')">&#128525;</span>
                <span class="bx_smail" @click="insertSmile('&#129321;')">&#129321;</span>
                <span class="bx_smail" @click="insertSmile('&#128540;')">&#128540;</span>
                <span class="bx_smail" @click="insertSmile('&#128579;')">&#128579;</span>
                <span class="bx_smail" @click="insertSmile('&#128536;')">&#128536;</span>
                <span class="bx_smail" @click="insertSmile('&#128539;')">&#128539;</span>
                <span class="bx_smail" @click="insertSmile('&#128556;')">&#128556;</span>
                <br><br>
                <span class="bx_smail" @click="insertSmile('&#128077;')">&#128077;</span>
                <span class="bx_smail" @click="insertSmile('&#9995;')">&#9995;</span>
                <span class="bx_smail" @click="insertSmile('&#128076;')">&#128076;</span>
                <span class="bx_smail" @click="insertSmile('&#129304;')">&#129304;</span>
                <span class="bx_smail" @click="insertSmile('&#128078;')">&#128078;</span>
                <span class="bx_smail" @click="insertSmile('&#9994;')">&#9994;</span>
                <span class="bx_smail" @click="insertSmile('&#128072;')">&#128072;</span>
                <span class="bx_smail" @click="insertSmile('&#128073;')">&#128073;</span>
                <span class="bx_smail" @click="insertSmile('&#128405;')">&#128405;</span>
                <span class="bx_smail" @click="insertSmile('&#128170;')">&#128170;</span>
            </template>
          </el-popover>                 
       </div>
      
       
       <div class="panel_button f_left">                        
               <el-dropdown>
                <el-button type="info" plain>
                 <i class="fa fa-paperclip" aria-hidden="true"></i>
                </el-button>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item @click="showSelectFile(1)"><i class="fa fa-picture-o mrl5" aria-hidden="true"></i>Изображение</el-dropdown-item>
                    <el-dropdown-item @click="showSelectFile(2)"><i class="fa fa-video-camera mrl5" aria-hidden="true"></i> Видео</el-dropdown-item>                   
                  </el-dropdown-menu>
                </template>
              </el-dropdown>          
       </div>
       
       <div class="panel_button f_left">  
           <el-button type="success" plain @click="upload_file()" v-if="file_selectedZZZ"><i class="fa fa-upload fa-green" aria-hidden="true"></i></el-button>
       </div> 
       
       <div class="panel_button f_left">  
                  <el-image
                    style="width: 32px; height: 32px"
                    :src="up_file_url_preview"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :preview-src-list="up_file_url_full"
                    :fit="cover"
                    :initial-index="1"
                    v-if="file_upload_success">
                  </el-image>
                  <i class="fa fa-refresh fa-spin fa-2x fa-fw" v-if="file_upload_process"></i>
       </div>
       <div class="panel_button f_left mrl5">  
           <el-button type="info" plain @click="remove_upload_image(0)" v-if="file_upload_success"><i class="fa fa-times clr_red" aria-hidden="true"></i></el-button>
       </div>
      
      <div class="panel_button f_left">
            <el-button type="info" plain @click="cancelEdit()" id="cancel_edit" style="display:none;">
               <i class="fa fa-pencil-square-o clr_green2" aria-hidden="true" style="font-size: 20px;"></i>
               <i class="fa fa-times clr_red" aria-hidden="true"></i>
            </el-button>        
      </div>
      
      <div class="panel_button f_left">
            <el-button type="info" plain @click="deleteMessage()" id="delete_message" style="display:none;">
               <i class="fa fa-trash-o clr_red" aria-hidden="true" style="font-size: 16px;"></i>
            </el-button>        
      </div>
       <div class="panel_button f_left">
            <el-button type="info" plain @click="cancelDelete()" id="cancel_delete" style="display:none;">
               <i class="fa fa-trash-o clr_red" aria-hidden="true" style="font-size: 16px;"></i>
               <i class="fa fa-times clr_red" aria-hidden="true"></i>
            </el-button>        
      </div>
      
      <div class="panel_button f_left">
            <el-button type="info" plain @click="cancelQuote()" id="cancel_quote" style="display:none;">
               <i class="fa fa-commenting-o clr_orange" aria-hidden="true" style="font-size: 16px;"></i>
               <i class="fa fa-times clr_red" aria-hidden="true"></i>
            </el-button>        
      </div>
                     
   </div>
                 
   <input type="file" @change="onChangeImageUpload($event)" id="files_img" ref="files_img"  accept="image/gif, image/jpeg, image/png"/>
   <input type="file" @change="onChangeVideoUpload($event)" id="files_video" ref="files_video"  accept="video/mp4" />
   <input type="file" @change="onChangeDocUpload($event)" id="files_doc" ref="files_doc"  accept=".doc, .rar" />
     
   <textarea name="txt_dialogue" id="txt_dialogue" class="txt_dialogue" v-model="desc_dialogue" placeholder="Введите сообщение" @focus="onFocus()" @blur="onBlur()"></textarea>
   
   <input type="hidden" id="edit_ms_id" value="" />
   <input type="hidden" id="delete_ms_id" value="" />
   <input type="hidden" id="quote_ms_id" value="" />
                             
   <div class="bx_button">
    <el-button @click="sendMsg()" type="success" v-loading="send_loading" :disabled="sending">
       Отправить <i class="fa fa-paper-plane-o mrl5" aria-hidden="true"></i>
    </el-button>
   </div> 
             
</template>

<style scoped>
  input[type="file"]{
    position: absolute;
    top: -500px;
  }  
</style>