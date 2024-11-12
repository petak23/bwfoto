<script setup>
/** 
 * Component ShowArticle
 * Posledná zmena(last change): 12.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 */
import { ref, watch, computed, onMounted } from 'vue'
import { useMainStore } from '../store/main.js'
const store = useMainStore()

import EditTexts from "../../../../components/EditArticle/EditTexts.vue" //"./Article/EditTexts.vue"
import EditTitle from "../../../../components/EditArticle/EditTitle.vue" //"./Article/EditTitle.vue"
import MainService from '../services/MainService.js'

const props = defineProps({
	id_hlavne_menu_lang: {
		type: Number,
		required: true,
	},
	view_h1: {	// Povolenie zobrazenia nadpisu H1
		type: Boolean,
		default: false, //Povolenie zobrazenia
	},
	text_class: {	// Doplnkový class pre textové pole
		type: String,
		default: "",
	},
	container_id: { // Id hlavného kontajnera
		type: String,
	}
})

const article = ref(null)
const edit_enabled = ref(false)

// Načítanie aktuálneho článku
const getArticle = () => {
	MainService.getOneMenuArticle(props.id_hlavne_menu_lang)
		.then(response => {
			article.value = response.data
			//console.log(response.data)
		})
		.catch((error) => {
			console.error(error);
		})
}

const modalNameTitle = computed(() => {
	return 'EditTitleModalID' + props.id_hlavne_menu_lang
})
const modalNameText = computed(() => {
	return 'EditTextModalID' + props.id_hlavne_menu_lang 
})

watch(() => store.user, () => {
	edit_enabled.value = (store.user != null) ? store.checkUserPermission('Front:Clanky', 'edit') : false
})

watch(() => store.article, () => {
	article.value = store.article
})

onMounted(() => {
	getArticle()
})
</script>

<template>
	<div
		:id="props.container_id"
		class="sa-container"
		v-if="article != null"
	>
		<!--div class="page-header">
			<div  
				class="col-sm-12 col-md-3"
				v-if="(parseInt(article_avatar_view_in) & 2) && $store.state.article.avatar != null"
			>
				<img  :src="filesPath + 'www/' + $store.state.article.avatar" 
							alt="Title image"
							class="img-fluid"
				>
			</div>
			<edit-title
				:edit_enabled="parseInt(edit_enabled)"
				:link="link"
				:link_to_admin="link_to_admin"
				:article_hlavicka="article_hlavicka"
			></edit-title>
		</div -->
		<h1 v-if="props.view_h1">
			{{ article.view_name }}
			<small v-if="article.h1part2 != null" class="ml-2">
				{{ article.h1part2 }}
			</small>
		</h1>
		<div v-if="edit_enabled"
				class="btn-group btn-group-sm editable" 
				role="group"
		>
			<b-button
				v-if="props.view_h1"
				variant="outline-warning"
				size="sm"
				v-b-modal="modalNameTitle"
				:title="store.texts.base_edit_title"
			>
				<i class="fas fa-pen"></i>
			</b-button>
			<b-button 
				variant="outline-warning"
				size="sm"
				:title="store.texts.base_edit_texts"
				v-b-modal="modalNameText"
			>
				<i class="fa-solid fa-file-lines"></i>
			</b-button>
			<edit-title
				:button_prefix="'buttonTitleID' + props.id_hlavne_menu_lang"
				:article="article"
				:modal_name="modalNameTitle"
			></edit-title>
			<edit-texts
				:button_prefix="'buttonTextID' + props.id_hlavne_menu_lang"
				:article="article"
				:modal_name="modalNameText"
			>
			</edit-texts>
		</div>
		<span 
			class="popis"
			:class="props.text_class"
			v-html="article.text_c">
		</span>
	</div>
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
	.sa-container{
		position: relative;
	}
	.editable {
		position: absolute;
		top: 0;
		right: 0;
	}
	h1{
		text-align: center;
	}
</style>