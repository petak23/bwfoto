/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 03.11.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 */

import Vue from 'vue'
import LazyLoadDirective from "./directives/LazyLoadDirective"
import mainframe from './components/MainFrame.vue'
import cardorder from './components/CardOrder.vue'
import vuetify from './plugins/vuetify.js'
import store from "./store/index.ts"

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
    mainframe,
    cardorder
  },
});   