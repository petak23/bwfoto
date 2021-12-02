/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 01.12.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */

import Vue from 'vue'
import LazyLoadDirective from "./directives/LazyLoadDirective"
import vuetify from './plugins/vuetify.js'
import store from "./store/index.js"

//import mainframe from './components/MainFrame.vue'
import cardorder from './components/CardOrder.vue'
import adminmenu from './components/MainFrame/AdminMenu.vue'
import adminmenu_homepage from './components/MainFrame/AdminMenu_Homepage.vue'
import mainmenu from './components/MainFrame/MainMenu.vue'

Vue.directive("lazyload", LazyLoadDirective)

var vm = new Vue({
  el: '#vueappadmin',
  vuetify,
  store,
  /*data: function () {
    return {}
  },*/
  components: { 
    //mainframe,
    cardorder,
    adminmenu,
    adminmenu_homepage,
    mainmenu,
  },
});   