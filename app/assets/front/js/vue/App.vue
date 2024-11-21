<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { RouterLink, RouterView } from 'vue-router'

import MySettings from './components/MySettings.vue'	//v3
import MainMenuLoad from './components/Menu/MainMenuLoad.vue'	//v3
import BfFooter from './components/BfFooter.vue' //v3
import MySlider from './components/MySlider.vue' //v3
import BfNav from './components/BfNav.vue'	//v3
import ProductsLike from './components/ProductsLike/ProductsLike.vue'
/*import EchoFixedMain from './components/Menu/EchoFixedMain.vue'
import EchoBreadcrumb from './components/Menu/EchoBreadcrumb.vue'
import SingleMenu from './components/Menu/SingleMenu.vue'
import UserMenu from './components/User/UserMenu.vue'
import ShowNews from './components/ShowNews.vue'*/
import FlashMessage from '../../../components/FlashMessages/FlashMessage.vue'

import { useRoute } from 'vue-router'
const route = useRoute()

import { useMainStore } from './store/main'
const store = useMainStore()

import { useFlashStore } from './store/flash'
const storeF = useFlashStore()

const id_hlavne_menu_lang = ref(0)
const flashes = ref("")
const isDarkMode = ref(false)
const sliderImage = ref("")
const isCinzel = ref(false)

const handleDarkModeChange = (isAutomatic = false) => {
	// Zmeňte CSS triedy na body elemente podľa hodnoty isDarkMode
	if (isDarkMode.value) {
		document.body.classList.add("dark-mode");
		document.body.classList.remove("light-mode");
		isDarkMode.value = true;
	} else {
		document.body.classList.add("light-mode");
		document.body.classList.remove("dark-mode");
		isDarkMode.value = false;
	}
	
	// Umožnite automatickú zmenu podľa času
	if (isAutomatic) {
		const currentTime = new Date().getHours();
		const isNightTime = currentTime < 6 || currentTime >= 18; // Predpokladáme, že noc je od 18:00 do 6:00
		if (isNightTime) {
			isDarkMode.value = true;
		} else {
			isDarkMode.value = false;
		}
	}
}

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
	// Načítanie aktuálneho režimu podľa času pri načítaní stránky
	handleDarkModeChange(true);
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
				<breadcrumb
					homepage="{plink 'Homepage:'}"
				></breadcrumb>
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
		<toogle-mode @darkModeChanged="handleDarkModeChange"></toogle-mode>
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
