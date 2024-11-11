<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { RouterLink, RouterView } from 'vue-router'

import MySettings from './components/MySettings.vue'	//v3
import MainMenuLoad from './components/Menu/MainMenuLoad.vue'	//v3
import BfFooter from './components/BfFooter.vue' //v3
import MySlider from './components/MySlider.vue' //v3
import BfNav from './components/BfNav.vue'	//v3
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
		id_hlavne_menu_lang.value = store.searchMenuSpecNazov(store.main_menu[0], newId)
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
		store.basePath + '/www/' + store.udaje_webu.config.slider.dir : ""
})
</script>

<template>
	<my-settings />
	<main-menu-load 
		:id_hlavne_menu_lang="id_hlavne_menu_lang"
	/>
	<flash-message />
	<div id="slider" ref="myslider">
		<my-slider
			:files-path="sliderFilesPath"
		/>

		<bf-nav 
			link-home="{link Homepage:}"
			link-clanky="{link Clanky:}/"
		/>

		<div class="frame">
			<section id="webContent">
				<a href="{$link_page_font}" n:if="$user->isLoggedIn()">
					Prepni na: {if $page_font == 'c'}Monserat{else}Cinzel{/if}
				</a>
				<breadcrumb
					homepage="{plink 'Homepage:'}"
				></breadcrumb>
				<flash-message
					flash-messages="{($flashes|to_json)}"
				/>
				<small>
					RouterView(id: {{ $route.params.id }}, {{ id_hlavne_menu_lang }})<br />
				</small>
				<RouterView />
			</section>
			<products-like />
			
			<bf-footer />
		</div>
		<toogle-mode @darkModeChanged="handleDarkModeChange"></toogle-mode>
	</div>

	<!--header>
		<div class="row top-header">
			<div class="col-12 d-flex justify-content-between">
				<RouterLink to="/" :title="store.udaje_webu.titulka">
					<img :src="store.baseUrl + '/www/images/echo_logo_uv1.png'" class="logo" alt="logo Echo">
				</RouterLink>
				<h1 class="h1-text">
					<RouterLink to="/" :title="store.udaje_webu.titulka">{{store.udaje_webu.titulka}}</RouterLink>
				</h1>
				<RouterLink to="/" :title="store.udaje_webu.titulka">
					<img :src="store.baseUrl + '/www/images/logo_CC.png'" class="logo" alt="Logo Cantus Cordis">
				</RouterLink>
			</div>
		</div>
			
		<div class="main-header row">
			<div class="col-12 col-md-6 col-lg-6 col-xl-6">
				<echo-fixed-main />
				<div class="top-panel">
					<user-menu />
				</div>
				<div class="breadcrumb-panel">
					<echo-breadcrumb />
				</div>
			</div>
			<div class="col-12 col-md-6 col-lg-6 col-xl-6 slider-panel">
				<my-slider />
			</div>  
		</div>
	</header>

	<section-- class="main-content content-1 row">
		<div class="article-podclanky col-12 col-md-3 col-lg-2">
			<single-menu />
			<show-news />
		</div>
		<div class="col-12 col-md-9 col-lg-10 main-col">
			<div class="row">
				<div class="col-12">
					<small>
						RouterView(id: {{ $route.params.id }}, {{ id_hlavne_menu_lang }})<br />
					</small>
					<RouterView />
				</div>
			</div>
		</div>
	</section-->
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
