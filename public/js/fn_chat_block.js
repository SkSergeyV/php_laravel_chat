function editMessage(id) { 
  clearBeforeEdit();
  
  var element = document.getElementById('ms_'+id);  
  var content =  element.innerHTML;
  
  var area = document.getElementById('txt_dialogue'); 
      area.value = '';
      area.value = content;
  
   var edit = document.getElementById('edit_ms_id'); 
       edit.value = id;
       
       document.querySelector('#cancel_edit').style.cssText = 'display:block;';
       
       document.querySelector('#msg_'+id).style.cssText = 'border: 2px solid #127d09;';                      
 }
 
 
 function deleteMessage(id) {  
 
     clearBeforeDelete();
 
     document.querySelector('#delete_message').style.cssText = 'display:block;';
     var del = document.getElementById('delete_ms_id'); 
     del.value = id;     
     document.querySelector('#cancel_delete').style.cssText = 'display:block;'; 
      
     document.querySelector('#msg_'+id).style.cssText = 'border: 2px solid red;';     
 }
 
 
 function toQuoteMessage(id) {
 
     clearBeforeQuote();
 
     var quote = document.getElementById('quote_ms_id'); 
     quote.value = id;     
     document.querySelector('#cancel_quote').style.cssText = 'display:block;'; 
      
     document.querySelector('#msg_'+id).style.cssText = 'border: 2px solid #fabd78;'; 
 
 } 
 
 
 
function clearBeforeEdit(){
               var del = document.getElementById('delete_ms_id');   
               del.value = '';
               document.querySelector('#cancel_delete').style.cssText = 'display:none;';
               document.querySelector('#delete_message').style.cssText = 'display:none;';  
               
               var quote = document.getElementById('quote_ms_id'); 
               quote.value = '';
               document.querySelector('#cancel_quote').style.cssText = 'display:none;';              
}   


function clearBeforeDelete(){
            var edit = document.getElementById('edit_ms_id');
            edit.value = '';           
            var txt_dialogue = document.getElementById('txt_dialogue'); 
            txt_dialogue.value = '';           
            document.querySelector('#cancel_edit').style.cssText = 'display:none;';
            
             var quote = document.getElementById('quote_ms_id');   
             quote.value = '';
             document.querySelector('#cancel_quote').style.cssText = 'display:none;';         
}


function clearBeforeQuote(){
            var edit = document.getElementById('edit_ms_id');
            edit.value = '';           
            var txt_dialogue = document.getElementById('txt_dialogue'); 
            txt_dialogue.value = '';           
            document.querySelector('#cancel_edit').style.cssText = 'display:none;';
            
            
             var del = document.getElementById('delete_ms_id');   
             del.value = '';
             document.querySelector('#cancel_delete').style.cssText = 'display:none;';
             document.querySelector('#delete_message').style.cssText = 'display:none;';                              
}