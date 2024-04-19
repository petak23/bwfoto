<script>
import Autocomplete from './Autocomplete.vue';
import BwfotoTreeMain from './Menu/BWfoto_Tree_Main.vue'
import LangMenu from './LangMenu.vue'
import BasketNav from './Basket/BasketNav'

export default {
	components: {
		Autocomplete,
		BwfotoTreeMain,
		LangMenu,
		BasketNav,
	},
	props: {
		dirToImages: {
			type: String,
			required: true
		},
		linkHome: {
			type: String,
			required: true
		},
		linkClanky: {
			type: String,
			required: true
		},
	},
	data() {
		return {
			base_img: null,
		}
	},
	watch: {
		'$store.state.basePath': function () {
			this.base_img = this.$store.state.basePath + '/' + this.dirToImages
		}
	},
}
</script>

<template>
	<nav id="topNav" class="navbar navbar-expand-md fixed-top">
		<a class="navbar-brand ml-sm-5 p-3 logo" :href="linkHome" title="Homepage">
			<img v-if="base_img != null" :src="base_img + 'logo_bw-g.png'" alt="logo bw foto" class="logo">
		</a>	
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
				:link="linkClanky"
			></autocomplete>

			<lang-menu />

			<basket-nav />
			<a
				v-if="$store.state.user == null"
				class="btn btn-light ml-2"
				:href="$store.state.logInLink" 
				title="Prihlásenie (Log in)"
			>
				<i class="fa-solid fa-arrow-right-to-bracket"></i>
			</a>
			<div class="btn-group ml-2" v-if="$store.state.user != null">
				<button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<i class="fa-regular fa-user"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a 
						:href="$store.state.userLogLink" 
						:title="'Editácia profilu užívateľa: ' + $store.state.user.name" 
						class="dropdown-item" 
					>
						<i class="fa-regular fa-address-card"></i> Profil
					</a>
					<a 
						v-if="$store.state.adminerLink != null"
						:href="$store.state.adminerLink"
						target="_blank"
						class="dropdown-item" 
					>
						<i class="fa-solid fa-database"></i> Adminer
					</a>
					<a 
						v-if="$store.state.adminLink != null"
						:href="$store.state.adminLink" 
						:title="$store.state.texts.base_AdminLink_name" 
						class="dropdown-item" 
					>
						<i class="fa-solid fa-screwdriver-wrench"></i> {{ $store.state.texts.base_AdminLink_name }}
					</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" :href="$store.state.logOutLink">
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