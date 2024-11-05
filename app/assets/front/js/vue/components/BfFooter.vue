<script setup>
/** 
 * Component BfFooter
 * Posledná zmena(last change): 05.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */

import { computed } from 'vue'
import BwfotoTreeMain from './Menu/BWfoto_Tree_Main'

import { useMainStore } from '../../store/main'
const store = useMainStore()
const props = defineProps({
	dirToImages: {
		type: String,
		required: true
	},
	copy: {
		type: String,
		default: "",
	},
	lastUpdate: {
		type: String,
		default: "",
	}
})

const base_img = computed(() => {
	return store.basePath + '/' + props.dirToImages
})

const links_to_other_pages = ref([
	{
		link: "http://nette.org/cs/",
		title: "Nette Framework - populárny nástroj pre vytváranie webových aplikácií v PHP.",
		img: "nette-powered1.gif",
		alt: "nette powered",
	},
	{
		link: "https://vuejs.org/",
		title: "Vue js - The Progressive JavaScript Framework",
		img: "logo_vue.png",
		alt: "vue powered",
	},
])
</script>

<template>
	<footer class="py-3">
		<!-- --- Mapa stranky --- --> 
		<nav class="w-100">
			<bwfoto-tree-main 
				part="-1" 
				ul-class="nav text-center justify-content-center py-3"
			/>
		</nav>
		<!-- --- Info o stranke --- -->
		<div class="row justify-content-center" id="footerContent">
			<div id="logoBWfoto" class="px-3 my-3 col-md">
				<img 
					v-if="base_img != null"
					:src="base_img + 'logo_bw-w.png'"
					title="Logo BWfoto" alt="Logo Bwfoto" class="img-responsive">
			</div>
			<div id="contact" class="px-3 my-3 col-md">
				<h4>Ateliér Zámečník</h4>
				<ul class="list-group">
					<li class="list-unstyled"><span class="fa fa-home"></span> Spišské Bystré</li>
					<li class="list-unstyled"><span class="fa fa-envelope"></span> bwfoto@bwfoto.sk</li>
				</ul>
			</div>
		</div>
		<div class="pv-footer info-layer my-3">
			<ul class="nav justify-content-center">
				<li class="p-2" v-if="copy.length > 0">{{ props.copy }}</li>
				<li class="p-2">
					<a 
						v-for="(li, index) in links_to_other_pages" :key="index"
						:href="li.link" class="logo-nette" 
						:title="li.title" target="_blank"
					>
						<img :src="store.baseUrl + '/www/images/' + li.img" :alt="li.alt" />
					</a>
				</li>
				<li class="p-2">{{ props.lastUpdate }}</li>
				<li class="p-2">created by <a href="http://petak23.echo-msz.eu/" title="petak23.echo-msz.eu" target="_blank">petak23</a></li>
			</ul>
		</div>
	</footer>
</template>

<style lang="scss" scoped>
.logo-nette {
	padding-right: 1em;
	
	img {
		max-height: 1.5em;
	}
}
.logo-nette:last-child {
	padding-right: 0;
}
</style>