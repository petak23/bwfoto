<script setup>
/** 
 * Component EditArticle
 * Posledná zmena(last change): 25.10.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 * 
 */
import { computed } from 'vue'
import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()

import EditTitle from "./EditTitle.vue";

const props = defineProps({
	edit_enabled: {
		type: Boolean,
		default: false
	},
	article_hlavicka: { 
		type: Number,
		default: 1,
	},
	article_avatar_view_in: {
		type: Number,
		default: 0,
	}
})

const avatarView = computed(() => {
	let avatar_en = store.article != null && (store.article.avatar !== undefined || store.article.avatar != null)
	let kol = (props.article_avatar_view_in & 2) && avatar_en
	//console.log(kol)
  return kol
})

const avatarImg = computed(() => {
	let out = store.baseUrl + '/www/'
	let ko = store.udaje_webu.length > 0 && store.article != null ? store.udaje_webu.config.dir_to_menu + store.article.avatar : false
	return ko ? out + store.udaje_webu.config.dir_to_menu + store.article.avatar : false
})
</script>

<template>
	<span>
		<div class="page-header">
			<div  
				class="col-sm-12 col-md-3"
				v-show="avatarView"
			>	
				<img  
					v-if="avatarImg != false"
					:src="avatarImg" 
					alt="Title image"
					class="img-fluid"
				/>
			</div>
			<edit-title
				:edit_enabled="props.edit_enabled"
				:article_hlavicka="props.article_hlavicka"
			></edit-title>
		</div>

		<span class="popis" v-if="store.article != null && store.article.text_c" v-html="store.article.text_c"></span>
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