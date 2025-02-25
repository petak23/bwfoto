<script setup>

/*import SingleUpload from './components/Uploader/SingleUpload'
import MultipleUpload from './components/Uploader/MultipleUpload'

import colorBorderChange from './components/ColorBorderChange.vue'
import Edittexts from '../../../components/EditArticle/EditTexts'
import FlashMessage from '../../../components/FlashMessages/FlashMessage';
import SliderGrid from './components/Slider/SliderGrid'
import MainDocumentsPart from "./components/MainFrame/MainDocumentsPart.vue"
import VerzieEditForm from "./components/Verzie/VerzieEditForm.vue"
import NakupView from "./components/Nakup/nakupView.vue"*/

import { ref, onMounted } from 'vue';
import { BNavbar, BNavbarToggle, BNavbarBrand, BCollapse, BNavbarNav, BNavText, BNavItem, vBColorMode } from 'bootstrap-vue-next';
import MainService from './services/MainService'
import { useMainStore } from './store/main'
const store = useMainStore()

import MySettings from './components/MySettings.vue'	//v3
import { RouterView, RouterLink } from 'vue-router'

import FlashMessage from '../../../components/FlashMessages/FlashMessage.vue' //v3
import { useFlashStore } from '../../../components/FlashMessages/store/flash'
const storeF = useFlashStore()

onMounted(() => {
	const app_el_dataset = document.getElementById('app').dataset
	
	let flashes =  JSON.parse(app_el_dataset.flashes)
	if (flashes.length > 0) {
		flashes.forEach((fl) => {
			storeF.showMessage(fl.message, fl.type, null, 60000)
		})
	}

	MainService.getAdminMenu()
	.then(response => {
			store.admin_menu = response.data
		})
		.catch((error) => {
			console.log(error);
		})
})
</script>

<template>
	<my-settings />
	<flash-message />
	<BNavbar  v-b-color-mode="'dark'" toggleable="md" variant="dark" v-if="store.user != null">
		<BNavbarBrand>
			<span class="uroven-registracie-new"
				:class="'uroven-registracie-' + store.user.id_user_roles + '-new'">
				{{ store.user.name }} - {{ store.user.user_role }}
			</span>
		</BNavbarBrand>
		<BNavbarToggle target="nav-text-collapse" variant="light"/>
		<BCollapse id="nav-text-collapse" is-nav>
			<BNavbarNav class="ms-auto mb-2 mb-lg-0">
				<BNavItem :href="store.baseUrl" title=" Vráť sa na web a skontroluj zmeny..." variant="light">
					<i class="fas fa-eye"></i> Späť na web...
				</BNavItem>
				<BNavItem href="https://getbootstrap.com/" title="Bootstrap" variant="light" target="_blank">
					Bootstrap
				</BNavItem>
				<BNavItem href="https://fontawesome.com/search?ic=free" title="FontAwesome" variant="light" target="_blank">
					<i class="fas fa-flag"></i>
				</BNavItem>
				<BNavItem  
					v-if="store.user.user_role == 'admin' && store.udaje_webu.adminerLink != null"
					:href="store.udaje_webu.adminerLink"
					title="Adminer" target="_blank"
					variant="light"
				>
					<i class="fas fa-database"></i>
				</BNavItem>
			</BNavbarNav>
		</BCollapse>
	</BNavbar>

	<div class="row">
		<div class="col-12 col-sm-3 col-md-2 left-col">
			<div class="menu_new">
				<h6 class="mt-1 ms-2">Administračná ponuka</h6>
				<ul>
					<li v-for="am in store.admin_menu" :key="am.id">
						<a :href="am.link" :title="am.name" v-if="am.vue_link == null">
							{{ am.name }}
						</a>
						<RouterLink v-else :to="'/administration' + am.vue_link" :title="am.name">{{ am.name }}</RouterLink>
						<ul v-if="am.child != undefined">
							<li v-for="ch in am.child" :key="ch.id">
								<a :href="ch.link" :title="ch.name">
									&nbsp; - &nbsp; {{ ch.name }}
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-12 col-sm-9 col-md-10">
			<div class="row">
				<RouterView />
			</div>
		</div>
	</div>

	<div class="row footer-dark" v-if="store.user != null && store.udaje_webu != null">
		<div class="col-12">
			<footer>
				<nav class="navbar navbar-expand navbar-dark">
					<div class="text-center justify-content-center">
						<p class="navbar-text">
							© {{ store.udaje_webu.autor }} & {{ store.udaje_webu.copy }} 2015 - {date('Y')}&nbsp;|&nbsp;
							Posledná aktualizácia: 
							<a href="./verzie/" title="Verzie" v-if="store.udaje_webu.last_version != undefined">
								{{ store.udaje_webu.last_change }} - v.{{ store.udaje_webu.last_version.cislo }}
							</a>
							&nbsp;|&nbsp;
							<br>
							<span v-if="store.user.user_role == 'admin'">
								PHP {{ store.udaje_webu.php_version.number }} &nbsp;|&nbsp;
								{{ store.udaje_webu.php_version.server }} &nbsp;|&nbsp;
							</span>
							<a href="https://nette.org/cs/" class="logo-nette" title="Nette Framework - populárny nástroj pre vytváranie webových aplikacií v PHP." target="_blank">
								<img :src="store.baseUrl + '/www/images/nette-powered1.gif'" alt="nette powered">
							</a>
							&nbsp;|&nbsp;
							<a href="https://vuejs.org/" class="logo-nette" title="Vue js." target="_blank">
								<img :src="store.baseUrl + '/www/images/logo_vue.png'" alt="vue powered" class="vue-logo">
							</a>
						</p>
					</div>
				</nav>
			</footer>
		</div>
	</div>

</template>
<style lang="scss" scoped>
.uroven-registracie-new {
  padding: .25rem .4rem;
  border-radius: .25rem;
  border-width: 1px;
  border-style: solid;
	font-size: 80%;
	color: rgba($color: #FFF, $alpha: 0.55)
}

.uroven-registracie-1-new { background-color: #29a0b583; }
.uroven-registracie-2-new { background-color: #40b71c83; }
.uroven-registracie-3-new { 
  background-color: #FBEB0340;
  border-color: #FBEB0383;
}
.uroven-registracie-4-new { 
  background-color: #FB760440; 
  border-color: #FB7604AA;
}
.uroven-registracie-5-new { 
  background-color: #CC0F0840; 
  border-color: #CC0F08AA;
}
</style>