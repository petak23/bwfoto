<script setup>
import { onMounted } from 'vue';
import { BNavbar, BNavbarToggle, BNavbarBrand, BCollapse, BNavbarNav, BNavText, BNavItem, vBColorMode } from 'bootstrap-vue-next';
import MainService from './services/MainService'
import AdminFooter from './components/MainFrame/AdminFooter.vue';
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
			console.error(error)
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

	<div class="row footer-dark">
		<div class="col-12">
			<admin-footer />
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