<script setup>
/** 
 * Component EditTitle
 * Posledná zmena(last change): 30.10.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.0
 * 
 */
import { ref } from 'vue'

import EditMenu from "./EditMenu.vue"			//vue 3
import UserChange from "./UserChange.vue" //vue 3

import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()

const props = defineProps({
	edit_enabled: {
		type: Boolean,
		default: false
	},
	article_hlavicka: {
		type: Number,
		default: 1,
	},
})
</script>

<template>
	<div class="d-flex justify-content-between">
		<h1 class="title-article" v-if="store.article != null">
			{{ store.article.view_name }}
			<small v-if="store.article.h1part2 != null">
				{{ store.article.h1part2 }}
			</small>
		</h1>
		<edit-menu v-if="props.edit_enabled" />
	</div>
	<div v-if="store.article != null">
		<small v-if="props.article_hlavicka & 1 || props.edit_enabled" class="title-info">
			{{ store.texts.base_last_change }}{{ store.article.modified }}
		</small>
		<small v-if="store.article.datum_platnosti != null" class="title-info">
			{{ store.texts.base_platnost_do }}{{ store.article.datum_platnosti }}
		</small>
		<small v-if="props.article_hlavicka & 2 || props.edit_enabled" class="title-info">
			<user-change />
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