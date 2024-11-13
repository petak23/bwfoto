<script setup>
/** 
 * Component BfNav
 * Posledná zmena(last change): 13.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 * 
 */
import { computed } from 'vue'

import { useMainStore } from '../store/main.js'
const store = useMainStore()

import { 	BNavbar, BNavbarBrand, BNavbarToggle, BCollapse, 
					BDropdown, BDropdownItem, BDropdownDivider } from 'bootstrap-vue-next';
					
import Autocomplete from './Autocomplete.vue'  //v3
import BwfotoTreeMain from './Menu/BWfoto_Tree_Main.vue' //v3
import LangMenu from './LangMenu.vue' 	//v3
//import BasketNav from './Basket/BasketNav' /** @TODO vue 3*/
import UserMenu_not_logged from "./User/UserMenu_not_logged.vue" //v3

const logo_img = computed(() => {
	return store.udaje_webu != undefined && store.udaje_webu.config != undefined ?
		store.baseUrl + '/' + store.udaje_webu.config.dir_to_images + 'logo_bw-g.png': null
})


</script>

<template>
	<BNavbar toggleable="md" class="fixed-top top-nav" id="topNav">
		<BNavbarBrand to="/" title="Homepage" class="navbar-brand ml-sm-5 p-3 logo">
			<img v-if="logo_img != null" :src="logo_img" alt="logo bw foto" class="logo">
		</BNavbarBrand>
		<BNavbarToggle target="nav-collapse" class="navbar-toggler bf-nt">
			<template #default><i class="fa-solid fa-bars" style="font-size: 2rem;"></i></template>
		</BNavbarToggle>
		<BCollapse id="nav-collapse" is-nav>
			<bwfoto-tree-main 
				:part="2" 
			/>

			<autocomplete />

			<lang-menu />

			<basket-nav />

			<user-menu_not_logged 
				v-if="store.user == null"
			/>
			<div class="btn-group ml-2" 
				v-if="store.user != null"
			>
				<BDropdown toggle-class="bf-nt py-1 px-2 ms-2" variant="light" size="sm">
					<template #button-content>
						<i class="fa-regular fa-user"></i>
					</template>
					<BDropdownItem 
						to="/user"
						:title="'Editácia profilu užívateľa: ' + store.user.name" 
					>
						<i class="fa-regular fa-address-card"></i> Profil
					</BDropdownItem>
					<BDropdownItem
						v-if="store.adminerLink != null"
						:href="store.adminerLink"
						target="_blank"
					>
						<i class="fa-solid fa-database"></i> Adminer
					</BDropdownItem>
					<BDropdownItem
						v-if="store.adminLink != null"
						:href="store.adminLink" 
						:title="store.texts.base_AdminLink_name" 
					>
						<i class="fa-solid fa-screwdriver-wrench"></i> {{ store.texts.base_AdminLink_name }}
					</BDropdownItem>
					<BDropdownDivider />
					<BDropdownItem to="/logout">
						<i class="fa-solid fa-arrow-right-from-bracket"></i> Odhlás sa
					</BDropdownItem>
				</BDropdown>
			</div>
		</BCollapse>
	</BNavbar>
</template>

<style scoped>
	.bf-nt{
		background-color: rgba(128, 128, 128, .3)
	}
	.bw-top-nav {
		align-items: start !important;
	}
</style>