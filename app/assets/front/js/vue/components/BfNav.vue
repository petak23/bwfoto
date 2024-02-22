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
	computed: {
		base_img() {
			return document.getElementById('vueapp').dataset.baseUrl + '/' + this.dirToImages
		},
	}
}
</script>

<template>
	<nav id="topNav" class="navbar navbar-expand-md fixed-top">
		<a class="navbar-brand ml-sm-5 p-3 logo" :href="linkHome" title="home">
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
		</div>
		
	</nav>
</template>



<style scoped>
	.bf-nt{
		background-color: rgba(128, 128, 128, .3)
	}
</style>