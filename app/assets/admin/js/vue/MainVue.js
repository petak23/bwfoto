/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 06.04.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 */

import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import singleupload from './components/Uploader/SingleUpload'
import multipleupload from './components/Uploader/MultipleUpload'
import lastlogin from './components/MainFrame/LastLogin'
import colorBorderChange from './components/ColorBorderChange.vue'


// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

let vm = new Vue({
  el: '#vueapp',
  components: { 
    singleupload,
    lastlogin,
    multipleupload,
    colorBorderChange
  },
});   