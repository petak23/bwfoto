<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { RouterView } from 'vue-router'

import MySettings from './components/MySettings.vue'	//v3
import MainMenuLoad from './components/Menu/MainMenuLoad.vue'	//v3
import BfFooter from './components/BfFooter.vue' //v3
import MySlider from './components/MySlider.vue' //v3
import BfNav from './components/BfNav.vue'	//v3
import ProductsLike from './components/ProductsLike/ProductsLike.vue' //v3
import Breadcrumb from './components/Menu/Breadcrumb.vue' //v3
/*
import UserMenu from './components/User/UserMenu.vue'
import ShowNews from './components/ShowNews.vue'*/
import DarkModeButton from "./components/DarkModeButton.vue" //v3
import FlashMessage from '../../../components/FlashMessages/FlashMessage.vue' //v3

import { useRoute } from 'vue-router'
const route = useRoute()

import { useMainStore } from './store/main'
const store = useMainStore()

import { useFlashStore } from './store/flash'
const storeF = useFlashStore()

const id_hlavne_menu_lang = ref(0)
const flashes = ref("")
const sliderImage = ref("")
const isCinzel = ref(false)

watch(() => route.params.id, (newId) => {
	if (store.main_menu.length) {
		id_hlavne_menu_lang.value = store.searchMenuSpecNazov(store.main_menu, newId)
	}
})

onMounted(() => {
	const app_el_dataset = document.getElementById('app').dataset
	id_hlavne_menu_lang.value = parseInt(app_el_dataset.id_hlavne_menu_lang)
	
	flashes.value =  JSON.parse(app_el_dataset.flashes)
	if (flashes.value.length > 0) {
		flashes.value.forEach((fl) => {
			storeF.showMessage(fl.message, fl.type, null, 60000)
		})
	}
})

const sliderFilesPath = computed(() => {
	return store.udaje_webu != undefined && store.udaje_webu.config != undefined ?
		store.baseUrl + '/www/' + store.udaje_webu.config.slider.dir : ""
})
const setSliderImage = (si) => {
	sliderImage.value = 'url(' + sliderFilesPath.value + si + ')'
} 
</script>

<template>
	<my-settings />
	<main-menu-load 
		:id_hlavne_menu_lang="id_hlavne_menu_lang"
	/>
	<flash-message />
	<div 
		id="slider" 
		ref="myslider" 
		:style="{ backgroundImage: sliderImage }"
		:class="{ cinzel: isCinzel }"
	>
		<my-slider @reloadSlider="setSliderImage"	/>

		<bf-nav />

		<div class="frame">
			<section id="webContent">
				Prepni na:
				<button 
					@click.prevent="isCinzel = !isCinzel" 
					v-if="store.user != null" 
					v-text="isCinzel ? 'Monserat' : 'Cinzel'"
					class="btn btn-sm btn-outline-secondary ms-2"
				></button>
				<breadcrumb />
				<flash-message
					flash-messages="{($flashes|to_json)}"
				/>
				<small><br />
					RouterView(id: {{ $route.params.id }}, {{ id_hlavne_menu_lang }})<br />
				</small>
				<RouterView />
			</section>
			<products-like />
			
			<bf-footer />
		</div>
		<dark-mode-button />
	</div>
</template>

<style scoped>
.nav-item {
	border-bottom: 1px solid #dadada;
	background-color: #eeeeee;
}
.nav-item:last-child {
	border-bottom: 0;
}
.nav-link {
	padding-top: 0;
	padding-bottom: 0;
	color: #535363;
}
.nav-link:hover {
	color: #131313;
}
.router-link-active {
	background-color: #deb887;
	color: #393939 !important;
}
.router-link-active:hover {
	color: #131313;
}
.router-link-exact-active {
	background-color: #f1a43e;
}
.top-header .router-link-active {
	background-color: transparent !important;
}
.top-header .router-link-exact-active {
	background-color: transparent !important;
}
</style>
