/* 
 * Main Vue.js app file
 * Posledná zmena(last change): 13.02.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.2.4
 */

import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import MySlider from './components/MySlider.vue';

import MainArticleView from './views/MainArticleView.vue'
import HomepageView from './views/HomepageView.vue'
import LogInView from './views/LogInView.vue'
import ProductsLikeView from './views/ProductsLikeView.vue'
import BasketView from './views/BasketView.vue'

import EditArticle from '../../../components/EditArticle/EditArticle';
import FlashMessage from '../../../components/FlashMessages/FlashMessage';
import BwfotoTreeMain from './components/Menu/BWfoto_Tree_Main.vue'
import VueDndZone from 'vue-dnd-zone'
import 'vue-dnd-zone/vue-dnd-zone.css'
import Vuetify from 'vuetify'
import store from "./store/index.js"
import MainMenuLoad from "./components/Menu/MainMenuLoad.vue"
import MainMenu from './components/Menu/MainMenu.vue'
import Breadcrumb from './components/Menu/Breadcrumb.vue'
import ProductsLike from './components/ProductsLike/ProductsLike.vue'
import ShowArticle from './components/ShowArticle.vue'
import UserMenu from './components/User/UserMenu.vue'
import ToogleMode from './components/ToggleMode.vue'
import BfFooter from './components/BfFooter.vue'
import BfNav from './components/BfNav.vue'

import VueSession from 'vue-session'
Vue.use(VueSession)

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

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

Vue.use(VueDndZone);

Vue.use(Vuetify);

import VueRouter from 'vue-router'

Vue.use(VueRouter)

let vm = new Vue({
	el: '#vueapp',
	store,
	components: { 
		MySlider, 
		EditArticle,
		FlashMessage,

		MainArticleView,
		HomepageView,
		LogInView,
		ProductsLikeView,
		BasketView,

		BwfotoTreeMain,
		MainMenu,
		MainMenuLoad,
		Breadcrumb,
		ProductsLike,
		ShowArticle,
		UserMenu,
		ToogleMode,
		BfFooter,
		BfNav,
	},
	data() {
		return {
			isDarkMode: false,
		};
	},
	methods: {
		handleDarkModeChange(isDarkMode, isAutomatic = false) {
			// Zmeňte CSS triedy na body elemente podľa hodnoty isDarkMode
			if (isDarkMode) {
				document.body.classList.add("dark-mode");
				document.body.classList.remove("light-mode");
				this.isDarkMode = true;
			} else {
				document.body.classList.add("light-mode");
				document.body.classList.remove("dark-mode");
				this.isDarkMode = false;
			}
			
			// Umožnite automatickú zmenu podľa času
			if (isAutomatic) {
				const currentTime = new Date().getHours();
				const isNightTime = currentTime < 6 || currentTime >= 18; // Predpokladáme, že noc je od 18:00 do 6:00
				if (isNightTime) {
					this.isDarkMode = true;
				} else {
					this.isDarkMode = false;
				}
			}
		},
	},
	mounted() {
		// Načítanie aktuálneho režimu podľa času pri načítaní stránky
		this.handleDarkModeChange(null, true);
	},
	created() {
		// Reaguje na načítanie hl. menu
		this.$root.$on('darkModeChanged', data => {
			//console.log(data)
			this.handleDarkModeChange(data);
		})
	}
});   