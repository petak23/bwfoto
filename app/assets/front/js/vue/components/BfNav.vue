<script setup>
/** 
 * Component BfNav
 * Posledná zmena(last change): 05.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */
import { ref, computed } from 'vue'
import { RouterLink } from 'vue-router'

import { useMainStore } from '../store/main.js'
const store = useMainStore()
import Autocomplete from './Autocomplete.vue'  //v3
import BwfotoTreeMain from './Menu/BWfoto_Tree_Main.vue' //v3
import LangMenu from './LangMenu.vue' 	//v3
//import BasketNav from './Basket/BasketNav' /** @TODO vue 3*/
import UserMenu_not_logged from "./User/UserMenu_not_logged.vue" //v3

const props = defineProps({
	linkHome: {
		type: String,
		required: true
	},
	linkClanky: {
		type: String,
		required: true
	},
})

const logo_img = computed(() => {
	return store.udaje_webu != undefined && store.udaje_webu.config != undefined ?
		store.baseUrl + '/' + store.udaje_webu.config.dir_to_images + 'logo_bw-g.png': ""
})
</script>

<template>
	<nav id="topNav" class="navbar navbar-expand-md fixed-top">
		<RouterLink class="navbar-brand ml-sm-5 p-3 logo" to="/" title="Homepage">
			<img v-if="logo_img != null" :src="logo_img" alt="logo bw foto" class="logo">
		</RouterLink>	
		<button class="navbar-toggler bf-nt" type="button" data-toggle="collapse" 
						data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
						aria-expanded="false" aria-label="Toggle navigation"
						id="topMenuButton">
			<i class="fa-solid fa-bars" style="font-size: 2rem;"></i>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<bwfoto-tree-main 
				part="-2" 
			/>
			<autocomplete
				:link="props.linkClanky"
			></autocomplete>

			<lang-menu />

			<basket-nav />

			<user-menu_not_logged 
				v-if="store.user == null" 
				button-class="btn btn-light ml-2"
			/>
			<!--a
				v-if="store.user == null"
				class="btn btn-light ml-2"
				:href="store.logInLink" 
				title="Prihlásenie (Log in)"
			>
				<i class="fa-solid fa-arrow-right-to-bracket"></i>
			</a!-->
			<div class="btn-group ml-2" v-if="store.user != null">
				<button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<i class="fa-regular fa-user"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a 
						:href="store.userLogLink" 
						:title="'Editácia profilu užívateľa: ' + store.user.name" 
						class="dropdown-item" 
					>
						<i class="fa-regular fa-address-card"></i> Profil
					</a>
					<a 
						v-if="store.adminerLink != null"
						:href="store.adminerLink"
						target="_blank"
						class="dropdown-item" 
					>
						<i class="fa-solid fa-database"></i> Adminer
					</a>
					<a 
						v-if="store.adminLink != null"
						:href="store.adminLink" 
						:title="store.texts.base_AdminLink_name" 
						class="dropdown-item" 
					>
						<i class="fa-solid fa-screwdriver-wrench"></i> {{ store.texts.base_AdminLink_name }}
					</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" :href="store.logOutLink">
						<i class="fa-solid fa-arrow-right-from-bracket"></i> Odhlás sa
					</a>
				</div>
			</div>
		</div>
	</nav>
</template>

<style scoped>
	.bf-nt{
		background-color: rgba(128, 128, 128, .3)
	}
</style>