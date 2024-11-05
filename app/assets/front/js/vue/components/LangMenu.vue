<script setup>
/** 
 * Component BWfoto_Tree_Main
 * Posledná zmena(last change): 05.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 */
import { onMounted, ref } from 'vue'
import MainService from '../services/MainService'

const langs = ref(null)

const getLangs = () => {
	MainService.getActLangs()
		.then(response => {
			langs.value = response.data
		})
		.catch((error) => {
			console.error(error);
		});
}

onMounted(() => {
	getLangs()
})
</script>

<template>
	<div  
		class="btn-group user-lang-menu" role="group" aria-label="Lang-menu" 
		v-if="langs !== null && langs.count" 
	>
		<a
			v-for="lang in langs.langs" :key="lang.id" 
			:href="lang.link" 
			:title="lang.title" role="button" 
			:aria-label="lang.name"
			class="btn"
			:class="lang.class.length ? lang.class : ''"
		>
			<img :src="lang.image.src" :alt="lang.image.alt" />
		</a>
	</div>
</template>


<style lang="sass" scoped>

</style>