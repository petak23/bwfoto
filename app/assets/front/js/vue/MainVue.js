/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 27.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.2.6
 */

/** OLD VUE-2 
import VueDndZone from 'vue-dnd-zone'
import 'vue-dnd-zone/vue-dnd-zone.css'

import UserMenu from './components/User/UserMenu.vue'

import sk from "./locale/vee-validator/sk.js"
import VeeValidate, { Validator } from 'vee-validate'

Vue.use(VeeValidate, {
  classes: true,
  classNames: {
    valid: 'is-valid',
    invalid: 'is-invalid'
  }
});

// Localize takes the locale object as the second argument (optional) and merges it.
Validator.localize('sk', sk);

Vue.use(VueDndZone);

});  */ 

/** NEW for VUE-3 */

import { createPinia } from 'pinia'
import { createApp } from 'vue'
import { createBootstrap } from 'bootstrap-vue-next'

import App from './App.vue'
import router from './router'

import Vue3TouchEvents from "vue3-touch-events"

const pinia = createPinia()
const bootstrapVueNext = createBootstrap()
const app = createApp(App)
app.use(pinia)
app.use(router)
app.use(bootstrapVueNext)
app.use(Vue3TouchEvents)

app.mount('#app')