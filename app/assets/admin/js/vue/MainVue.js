/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 10.11.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 */

import Vue from 'vue'
import LazyLoadDirective from "./directives/LazyLoadDirective"
import vuetify from './plugins/vuetify.js'
import store from "./store/index.ts"

//import mainframe from './components/MainFrame.vue'
import cardorder from './components/CardOrder.vue'
import adminmenu from './components/MainFrame/AdminMenu.vue'
import lastlogin from './components/MainFrame/LastLogin.vue'

Vue.directive("lazyload", LazyLoadDirective)

var vm = new Vue({
  el: '#vueappadmin',
  vuetify,
  store,
  /*data: function () {
    return {
      
    }
  },*/
  components: { 
    //mainframe,
    cardorder,
    adminmenu,
    lastlogin,
  },
});   