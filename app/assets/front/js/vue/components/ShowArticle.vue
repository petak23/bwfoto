<script setup>
/** 
 * Component ShowArticle
 * Posledná zmena(last change): 18.02.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.5
 * 
 */
import { ref, watch, onMounted } from 'vue'
import { useMainStore } from '../store/main.js'
const store = useMainStore()
import { useFlashStore } from '../../../../components/FlashMessages/store/flash'
const storeF = useFlashStore()

import EditTitle from "../../../../components/EditArticle/EditTitle.vue"
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
		})
		.catch((error) => {
			console.error(error);
		})
}

const saveChanges = (data) => {
	if (data.onlyShow != undefined) {
		getArticle()
	} else {
		MainService.postH1Save(article.value.id, {
			article: data
		})
		.then((response) => {
			if (response.data.result == "OK") {
				storeF.showMessage('Zmeny v článku boli uložené.', 'success', 'Uložené ...', 5000)
				let	td = response.data
				delete td.result
				article.value = td
				store.main_menu_changed = true //Zapíše príznak o zmene hl. menu
			} else {
				storeF.showMessage('Došlo k chybe a zmeny sa neuložili.', 'danger', 'Oopps ...', 10000)
			}
		})
		.catch((error) => {
			storeF.showMessage('Došlo k chybe a zmeny sa neuložili. ' + error, 'danger', 'Oopps ...', 10000)
			console.error(error)
		})
	}
}

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
		<edit-title
			:edit_enabled="edit_enabled"
			editMenuColorType="warning"
			:article="article"
			@reloadArticle="saveChanges"
		/>
		<div v-if="false && edit_enabled" class="btn-group btn-group-sm editable" role="group">
			
		</div>
		<span 
			class="popis"
			:class="props.text_class"
			v-html="article.text_c">
		</span>
	</div>
</template>

<style scoped>
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