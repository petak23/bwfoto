<script setup>
/**
 * Component audios
 * Posledna zmena 21.08.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 * 
 */
import { ref, watch } from 'vue'
import MainService from '../../services/MainService'

import { useMainStore } from '../../store/main'
const store = useMainStore()

const audios = ref(null) 
const id = ref(0)

const getAttachments = () => {
	MainService.getVisibleAttachments(store.article.id_hlavne_menu, "audios")
		.then(response => {
			//console.log(response.data.length)
			audios.value = response.data.length > 0 ? response.data : null
		})
		.catch((error) => {
			console.error(error);
		});
}
// Generovanie url pre lazyloading obrázky
const getFileUrl = (text) => {
	return store.udaje_webu.config.prilohy_dir + text
}

watch(() => store.article, () => {
	if (store.article != null) getAttachments()
})
</script>

<template>
	<div v-if="audios != null" class="row attachments">
		<div class="col-12">
			<hr />
			<h4>{{ store.texts.clanky_h3_prilohy_audios }}:</h4>
		</div>
		<div class="col-12 col-md-6" v-for="im in audios" :key="im.id">
			<div class="thumbnail">
				<audio controls>
					<source :src="getFileUrl(im.main_file)" type="audio/mp3">
				</audio>
				<div>
					<strong>{{im.name}}</strong>
					<div v-if="im.description != null" class="popis">{{ im.description }}</div>
				</div>
			</div>
		</div>
	</div>
</template>