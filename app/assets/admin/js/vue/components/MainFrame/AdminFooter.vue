<script setup>
/** 
 * Component AdminFooter
 * Posledná zmena(last change): 04.03.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 * 
 */

import { ref } from 'vue'
import { RouterLink } from 'vue-router'

import { useMainStore } from '../store/main.js'
const store = useMainStore()

const last_year = new Date().getFullYear()

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
	<footer v-if="store.user != null && store.udaje_webu != null">
		<nav class="navbar navbar-expand navbar-dark">
			<div class="text-center justify-content-center">
				<p class="navbar-text">
					© {{ store.udaje_webu.autor }} & {{ store.udaje_webu.copy }} 2015 - {{ last_year }}
					&nbsp;|&nbsp;
					Posledná aktualizácia: 
					<RouterLink to="/administration/verzie" title="Verzie" v-if="store.udaje_webu.last_version != undefined">
						{{ store.udaje_webu.last_change }} - v.{{ store.udaje_webu.last_version.cislo }}
					</RouterLink>
					&nbsp;|&nbsp;
					<br>
					<span v-if="store.user.user_role == 'admin'">
						PHP {{ store.udaje_webu.php_version.number }} &nbsp;|&nbsp;
						{{ store.udaje_webu.php_version.server }} &nbsp;|&nbsp;
					</span>
					<a 
						v-for="(li, index) in links_to_other_pages" :key="index"
						:href="li.link" class="logo-nette" 
						:title="li.title" target="_blank"
					>
						<img :src="store.baseUrl + '/www/images/' + li.img" :alt="li.alt" />
					</a>
				</p>
			</div>
		</nav>
	</footer>
	
</template>

<style lang="scss" scoped>
.logo-nette {
	padding-right: 1em;
	border-right: 1px solid #aaa;
	img {
		max-height: 1.5em;
	}
}
.logo-nette:last-child {
	padding-right: 0;
}
</style>