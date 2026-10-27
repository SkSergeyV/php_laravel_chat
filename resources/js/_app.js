import './bootstrap';

import { createApp } from 'vue';

import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import 'element-plus/theme-chalk/index.css';
import "font-awesome/css/font-awesome.css";

  
//import IncrementCounter from './components/IncrementCounter.vue'; 
import AuthBlock from './components/AuthBlock.vue'; 
import ChatBlock from './components/ChatBlock.vue';
 
 createApp({})
 // .component('IncrementCounter', IncrementCounter)
  .component('AuthBlock', AuthBlock)
  .component('ChatBlock', ChatBlock)
  .use(ElementPlus)
  .mount('#app')
  
  
  
  



