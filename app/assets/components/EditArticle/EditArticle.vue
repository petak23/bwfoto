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
import { useFlashStore } from '../../components/FlashMessages/store/flash'
const storeF = useFlashStore()

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
	},
	editMenuColorType: { // Farebná schéma tlačídiel editačného menu
		type: String
	}
})

const saveChanges = (data) => {
	if (data.onlyShow != undefined) {
		//getArticle()
	} else {
		MainService.postH1Save(store.article.value.id, {
			article: data
		})
		.then((response) => {
			if (response.data.result == "OK") {
				storeF.showMessage('Zmeny v článku boli uložené.', 'success', 'Uložené ...', 5000)
				//let	td = response.data
				//delete td.result
				//article.value = td
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
			<edit-title v-if="store.article != null"
				:edit_enabled="props.edit_enabled"
				:article_hlavicka="props.article_hlavicka"
				:editMenuColorType="props.editMenuColorType"
				:article="store.article"
				@reloadArticle="saveChanges"
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