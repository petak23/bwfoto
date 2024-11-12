<script setup>
/**
 * Component others
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

const files = ref(null) 

const getAttachments = () => {
	MainService.getVisibleAttachments(store.article.id_hlavne_menu, "others")
		.then(response => {
			//console.log(response.data.length)
			files.value = response.data.length > 0 ? response.data : null
		})
		.catch((error) => {
			console.error(error);
		});
}

watch(() => store.article, () => {
	if (store.article != null) getAttachments()
})
</script>

<template>
	<div v-if="files != null" class="row attachments">
		<div class="col-12">
			<hr />
			<h4>{{ store.texts.clanky_h3_prilohy_others }}:</h4>
		</div>
		<div class="col-12 col-md-3 mb-2" v-for="im in files" :key="im.id">
			<div class="card text-white bg-dark">
				<div class="card-header">
					<h5 class="card-title">{{im.name}}</h5>
				</div>
				<div class="card-body" v-if="im.description != null">
					<p class="card-text">{{im.description}}</p>
				</div>
				<div class="card-footer">
					<a 
						:href="im.fileDownload" 
						class="btn btn-outline-success" 
						:title="im.name + ' - ' + store.texts.clanky_dokument_download"
					>
						<i class="fas fa-download"></i>
					</a>
					<a 
						:href="im.fileView" 
						class="btn btn-outline-success" 
						:title="im.name + ' - ' + store.texts.clanky_dokument_view" 
						target="_blank"
					>
						<i class="fas fa-file-image"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</template>