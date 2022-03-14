/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 01.02.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.0
 */

import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Slider from './components/Slider.vue';
import Autocomplete from './components/Autocomplete.vue';
import Fotogalery from './components/Fotogalery.vue';
import Fotocollage from './components/Fotocollage.vue';
import Fotopanorama from './components/Fotopanorama.vue';
import Edittitle from './components/EditArticle/EditTitle.vue';
import Menucardorder from './components/Menucardorder.vue';
import VueDndZone from 'vue-dnd-zone'
import 'vue-dnd-zone/vue-dnd-zone.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

Vue.use(VueDndZone);

var vm = new Vue({
  el: '#vueapp',
  components: { 
    Slider, 
    Autocomplete,
    Edittitle,
    Fotogalery,
    Fotocollage,
    Fotopanorama,
    Menucardorder,
  },
});   