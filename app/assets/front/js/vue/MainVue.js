/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 01.10.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.8
 */

import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Slider from './components/Slider.vue';
import Autocomplete from './components/Autocomplete.vue';
import Fotogalery from './components/Fotogalery.vue';
import LazyLoadDirective from "./components/directives/LazyLoadDirective";

Vue.directive("lazyload", LazyLoadDirective);

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

var vm = new Vue({
  el: '#vueapp',
  /*data: function () {
    return {
      
    }
  },*/
  components: { 
    Slider, 
    Autocomplete,
    Fotogalery,
  },
});   