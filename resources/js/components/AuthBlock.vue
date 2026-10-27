<script>
export default {
  data() {
    return {
      login: '',
      password: '',
      send_loading: false,
      login_error: false,
    }
  },
  
  computed: {
      sending(){
        if(this.login!='' && this.password!=''){
                return false;
         } else {
                return true;
            }
        }, 
                          
  },
   
   methods: {
	  sendAuthData(){
          this.send_loading = true; 
          this.login_error= false;
          axios.post("/login", {
          send_auth_data: 1,
          login: this.login,
          password: this.password
          }).then(({data}) => {
            if(data.message=='OK'){                
                 if(data.type=='User'){
                    window.location.href = "/user/chat";
                 }
                 if(data.type=='Admin'){
                    window.location.href = "/admin";
                 }
            }             
            if(data.message=='ERROR'){ 
                 this.loginError();
            }                                                    
          }).finally(() => {
             this.send_loading = false;
             this.login='';
             this.password='';
          });                    
      },
      
       loginError(){
           this.login_error=true;
       },
       
       loginErrorFalse(){
           this.login_error=false;
       }
      
      
	}
}


</script>
 
<template>  
  
<div class="auth_inp_ico"><i class="fa fa-user-o" aria-hidden="true"></i></div>
<div class="auth_inp_bx"> 
  <el-input
    placeholder=""
    v-model="login" @focus="loginErrorFalse()">
  </el-input>
</div> 

<div class="auth_inp_ico"><i class="fa fa-key" aria-hidden="true"></i></div>
<div class="auth_inp_bx">   
    <el-input
    placeholder=""
    v-model="password" 
    type="password">
   </el-input>
</div>

<div class="auth_inp_bx">
<el-button type="success"  @click="sendAuthData()" v-loading="send_loading" :disabled="sending">
    Вход <i class="fa fa-play mrl5" aria-hidden="true"></i>
</el-button>   
</div>

<div class="auth_inp_bx">
<div v-if="login_error" class="login_error"><i class="fa fa-exclamation-triangle mrr5 clr_red" aria-hidden="true"></i>ERROR</div>   
</div>

  
</template>