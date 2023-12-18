<script>
/** 
 * Component EditArticle
 * Posledná zmena(last change): 18.12.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.6
 * 
 */

import MainService from "../../front/js/vue/services/MainService.js"
import EditTitle from "./EditTitle.vue";

export default {
	props: {
		filesPath: { // Adresár k súborom bez basePath
			type: String,
			default: "",
		},
		link: String,
		link_to_admin: String,
	},
	data() {
		return {
			edit_enabled: false,
			to_check_permission: {
				resource: 'Front:Clanky',
				action: 'edit',
			}
		}
	},
	components: {
		EditTitle,
	},
	methods: {
		checkPermission: function (check_perm) {
			this.edit_enabled = false
			if (this.$store.state.user != null && typeof (this.$store.state.user.id) != 'undefined') {
				console.log(check_perm)
				this.$store.state.user.permission.forEach(function check(item) {
					if (item.resource == check_perm.resource) {
						console.log(item)
						let p = false
						if (item.action == null) {
							p = true
						} else if (item.action.isArray && item.action.includes(check_perm.action)) {
							p = true
						}
						console.log(p)
						this.edit_enabled = p
					}
				}, this)
			}
		}
	},
	computed: {
		filesDir() {
			return document.getElementById('vueapp').dataset.baseUrl + '/' + this.filesPath + 'www/'
		},
		article_avatar_view_in() {
			let s = this.$store.state 
			return s.app_settings && (s.app_settings.article_avatar_view_in & 2) && s.article.avatar != null
		}
	},
	watch: {
		'$store.state.user': function () {
			this.checkPermission(this.to_check_permission)
		}
	},
	mounted () {
		if (this.$store.state.user != null) this.checkPermission(this.to_check_permission)
	},
}
</script>

<template>
	<span>
		<div class="page-header">
			<div  
				class="col-sm-12 col-md-3"
				v-if="article_avatar_view_in"
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
			></edit-title>
		</div>

		<span class="popis" v-if="$store.state.article.text_c" v-html="$store.state.article.text_c"></span>
	</span>
</template>

<style scoped>
	/*.title-info {
		border-right: 1px solid #ddd;
		margin-right: .5ex;
		padding-right: .25ex;
	}
	.title-info:last-child {
		border-right: 0;
	}*/
</style>