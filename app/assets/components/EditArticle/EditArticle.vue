<script>
/** 
 * Component EditArticle
 * Posledná zmena(last change): 04.12.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.5
 * 
 */

import MainService from '../../front/js/vue/services/MainService.js'
import EditTitle from "./EditTitle.vue";

export default {
	props: {
		filesPath: { // Adresár k súborom bez basePath
			type: String,
			default: "",
		},
		link: String,
		link_to_admin: String,
		article_hlavicka: String,
		article_avatar_view_in: String,
	},
	data() {
		return {
			edit_enabled: false,
		}
	},
	components: {
		EditTitle,
	},
	computed: {
		filesDir() {
			return document.getElementById('vueapp').dataset.baseUrl + '/' + this.filesPath + 'www/'
		},
	},
	watch: {
		'$store.state.user': function () {
			let vm = this
			let data = {
				resource: 'Front:Clanky',
				action: 'edit	',
			}
			MainService.postIsAllowed(this.$store.state.user.id, data)
				.then(function (response) {
					vm.edit_enabled = response.data.result == 1
				})
				.catch(function (error) {
					console.log(error);
				});
		}
	},
}
</script>

<template>
	<span>
		<div class="page-header">
			<div  
				class="col-sm-12 col-md-3"
				v-if="(parseInt(article_avatar_view_in) & 2) && $store.state.article.avatar != null"
			>
				<img  :src="filesDir + $store.state.article.avatar" 
							alt="Title image"
							class="img-fluid"
				>
			</div>
			<edit-title
				:edit_enabled="edit_enabled"
				:link="link"
				:link_to_admin="link_to_admin"
				:article_hlavicka="article_hlavicka"
			></edit-title>
		</div>

		<span class="popis" v-if="$store.state.article.text_c" v-html="$store.state.article.text_c"></span>
	</span>
</template>

<style scoped>
	.title-info {
		border-right: 1px solid #ddd;
		margin-right: .5ex;
		padding-right: .25ex;
	}
	.title-info:last-child {
		border-right: 0;
	}
</style>