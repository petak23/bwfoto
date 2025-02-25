/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 10.04.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.5
 */

import { createPinia } from 'pinia'
import { createApp } from 'vue'
import { createBootstrap } from 'bootstrap-vue-next'

import App from './App.vue'
import router from './router'

const pinia = createPinia()
const bootstrapVueNext = createBootstrap()
const app = createApp(App)
app.use(pinia)
app.use(router)
app.use(bootstrapVueNext)

app.mount('#app')