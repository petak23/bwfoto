<script>
/** 
 * Component EditArticle
 * Posledná zmena(last change): 22.02.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 */
import EditTitle from "./EditTitle.vue";
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
	props: {
		apiPath: { // Cesta k API
			type: String,
			required: true
		},
		filesPath: { // Adresár k súborom vrátanekoncového lomítka
			type: String,
			required: true
		}, 
		article_id: String, // hlavne_menu_lang.id
		edit_enabled: String,
		link: String,
		link_to_admin: String,
		article_hlavicka: String,
		article_avatar_view_in: String,
	},
	components: {
		EditTitle,
	},
	data() {
		return {
			article: {
				menu_name: '',
				h1part2: '',
				view_name: '',
				text_c: '',
				id_user_main: 0,
			},
		}
	},
	methods: {
	},
	mounted() {
		if (this.article_id !== "0") { // Len pri editácii
			// Načítanie údajov priamo z DB
			let odkaz = this.apiPath + 'menu/getonemenuarticle/' + this.article_id
			axios.get(odkaz)
						.then(response => {
							this.article = response.data
							//console.log(this.article)
						})
						.catch((error) => {
							console.log(odkaz);
							console.log(error);
						});
		}
	},
	created() {
		// Reaguje na uloženie titulných informácií článku
		this.$root.$on('title-save', data => {
			this.article.menu_name = data.menu_name
			this.article.h1part2 = data.h1part2
			this.article.view_name = data.view_name
		})
		// Reaguje na uloženie textu článku
		this.$root.$on('texts-save', data => { 
			this.article.text_c = data.texts
		})
	}

}
</script>

<template>
	<span>
		<div class="page-header">
			<div  class="col-sm-12 col-md-3" v-if="(parseInt(article_avatar_view_in) & 2) && article.avatar != null">
				<img  :src="filesPath + 'www/' + article.avatar" 
							alt="Title image"
							class="img-fluid"
				>
			</div>
			<edit-title
				:api-path="apiPath"
				:edit_enabled="parseInt(edit_enabled)"
				:article="article"
				:link="link"
				:link_to_admin="link_to_admin"
				:article_hlavicka="article_hlavicka"
			></edit-title>
		</div>

		<span class="popis" v-if="article.text_c" v-html="article.text_c"></span>
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