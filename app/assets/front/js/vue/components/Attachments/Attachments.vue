<script>
/**
 * Component attachments
 * Posledna zmena 08.12.2023
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 * 
 */

import AttImages from './Images.vue'
import AttAudios from './Audios.vue'
import AttOthers from './Others.vue'


export default {
	components: {
		AttImages,
		AttAudios,
		AttOthers,
	},
	props: {
		filesPath: { // Adresár k súborom bez basePath
			type: String,
			default: "",
		},
	},
	computed: {
		filesDir() {
			return document.getElementById('vueapp').dataset.baseUrl + '/' + this.filesPath
		},
	},
}
</script>

<template>
	<div class="col-12">
		<att-images
			:file-path="filesDir"
		></att-images>
		<att-audios
			:file-path="filesDir"
		></att-audios>
		<att-others
			:file-path="filesDir"
		></att-others>
	</div>
</template>