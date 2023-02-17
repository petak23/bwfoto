/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 17.02.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.5
 */

import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Slider from './components/Slider.vue';
import Autocomplete from './components/Autocomplete.vue';
import Fotogalery from './components/Fotogalery.vue';
import Fotocollage from './components/Fotocollage.vue';
import Fotopanorama from './components/Fotopanorama.vue';
import EditArticle from '../../../components/EditArticle/EditArticle';
import FlashMessage from '../../../components/FlashMessages/FlashMessage';
import Menucardorder from './components/Menucardorder.vue';
import BwfotoTreeMain from './components/Menu/BWfoto_Tree_Main.vue'
import VueDndZone from 'vue-dnd-zone'
import 'vue-dnd-zone/vue-dnd-zone.css'
import Vuetify from 'vuetify'
import store from "./store/index.js"
import MainMenuLoad from "./components/Menu/MainMenuLoad.vue"
import MainMenu from './components/Menu/MainMenu.vue'
import Breadcrumb from './components/Menu/Breadcrumb.vue'
import ProductsLike from './components/ProductsLike.vue'

import VueSession from 'vue-session'
Vue.use(VueSession)

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

Vue.use(VueDndZone);

Vue.use(Vuetify);

let vm = new Vue({
  el: '#vueapp',
  store,
  components: { 
    Slider, 
    Autocomplete,
    EditArticle,
    FlashMessage,
    Fotogalery,
    Fotocollage,
    Fotopanorama,
    Menucardorder,
    BwfotoTreeMain,
    MainMenu,
    MainMenuLoad,
    Breadcrumb,
    ProductsLike,
  },
});   