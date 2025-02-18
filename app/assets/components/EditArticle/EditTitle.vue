<script setup>
/** 
 * Component EditTitle
 * Posledná zmena(last change): 18.02.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.2
 * 
 */

import EditMenu from "./EditMenu.vue"			
import UserChange from "./UserChange.vue" 

import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()

const props = defineProps({
	edit_enabled: {
		type: Boolean,
		default: false
	},
	article: {
		type: Object,
		required: true
	},
	article_hlavicka: {
		type: Number,
		default: 1,
	},
	editMenuColorType: { // Farebná schéma tlačídiel editačného menu
		type: String
	}
})

const emit = defineEmits(['reloadArticle'])
const doReloadArticle = (data) => {
	emit('reloadArticle', data)
}
</script>

<template>
	<div class="position-relative w-100">
		<h1 class="text-center title-article" v-if="props.article != null">
			{{ props.article.view_name }}
			<small v-if="props.article.h1part2 != null">
				{{ props.article.h1part2 }}
			</small>
		</h1>
		<div class="position-absolute top-0 end-0">
			<edit-menu 
				v-if="props.edit_enabled" 
				:color_type="props.editMenuColorType"
				:article="props.article"
				@reloadArticle="doReloadArticle"
			/>
		</div>
	</div>
	<div v-if="props.article != null" class="text-center">
		<small v-if="props.article_hlavicka & 1 || props.edit_enabled" class="title-info">
			{{ store.texts.base_last_change }}{{ props.article.modified }}
		</small>
		<small v-if="props.article.datum_platnosti != null" class="title-info">
			{{ store.texts.base_platnost_do }}{{ props.article.datum_platnosti }}
		</small>
		<small v-if="props.article_hlavicka & 2 || props.edit_enabled" class="title-info">
			<user-change 
				:article="props.article"
				@reloadArticle="doReloadArticle"
			/>
		</small>
	</div>
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
	h1 > small {
		font-size: 65%
	}
</style>