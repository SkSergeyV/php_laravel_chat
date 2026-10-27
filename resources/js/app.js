import './bootstrap';

import { createApp } from 'vue';

import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import 'element-plus/theme-chalk/index.css';
import "font-awesome/css/font-awesome.css";

//import EmojiPicker from 'vue3-emoji-picker';
//import "/node_modules/vue3-emoji-picker/dist/style.css";

  
import AuthBlock from './components/AuthBlock.vue'; 
import ChatBlock from './components/ChatBlock.vue';
 
 createApp({})
  .component('AuthBlock', AuthBlock)
  .component('ChatBlock', ChatBlock)
//  .component('EmojiPicker',  EmojiPicker)
  .use(ElementPlus)
  .mount('#app')