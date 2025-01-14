<script setup>
/** 
 * Component UserChange
 * Posledná zmena(last change): 14.01.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.6
 * 
 */

import { ref, watch, onMounted, computed } from 'vue'
import MainService from '../../front/js/vue/services/MainService'
import { BModal } from 'bootstrap-vue-next'

import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()
import { useFlashStore } from '../FlashMessages/store/flash'
const storeF = useFlashStore()


const users = ref(null)
const id_user_main = ref(0) // id aktuálneho vlastníka článku

const userDialogView = ref(false)

const onSubmit = (event) => {
	event.preventDefault()
	if (event.submitter.classList.contains("user-change-submit")) {
		userDialogView.value = false
		MainService.postSaveMainMenuField(store.article.id_hlavne_menu, {
			data: { id_user_main: id_user_main.value },
			id_hlavne_menu_lang: store.article.id
		})
		.then((response) => {
			store.article = response.data
			storeF.showMessage('Vlastník článku bol zmenený.', 'success', 'OK', 4000)
		})
		.catch((error) => {
			console.error(error)
		})
	}
}

const onReset = (event) => {
	event.preventDefault()
	if (event.explicitOriginalTarget.classList.contains("user-change-reset")) {
		userDialogView.value = false
		id_user_main.value = store.article.id_user_main
	}
}

watch(store.article, (newArticle) => {
	id_user_main.value = store.article.id_user_main
})

const owner_name = computed(() => {
	return store.article != null ? store.texts.base_zadal + store.article.owner : ""
})

onMounted(() => {
	MainService.getUserChangeFormUsers()
	.then(response => {
		users.value = response.data
		//console.log(this.users)
	})
	.catch((error) => {
		console.log(error);
	});
})
</script>

<template>
	{{ owner_name }}
	<button 
		class="btn btn-link btn-sm"
		@click.prevent="userDialogView = true"
		title="Zmeň vlastníka položky"
	>
		<i class="fas fa-pencil-alt"></i>
	</button>

	<BModal 
		v-model="userDialogView" 
		centered 
		title="Zmeň vlastníka položky" 
		hide-footer
	>
		<div class="alert alert-info form-info-box">
				<div class="row">
					<div class="col-2 text-center ">
						<i class="fas fa-question-circle fa-2x align-middle"></i>
					</div>
					<div class="col-10">
						<p>Tu môžete zmeniť vlastníctvo tohoto článku na iného užívateľa. Po zmene už ale nebudete mať práva vlastníka článku 
								(napr. editácia článku, nastavení, ...)! </p>
						<p><strong>&nbsp;&nbsp;Preto si tento krok dobre rozmyslite!!!</strong></p>
					</div>
				</div>
			</div>
			<form @submit="onSubmit" @reset="onReset">
				<fieldset class="mb-3">
					<legend>Nový vlastník:</legend>
					<div v-for="u in users" :key="u.id">
						<input 
							type="radio" :id="u.id" name="userChange" :value="u.id"
							v-model="id_user_main"
						/>
						<label :for="u.id" class="ms-2">{{ u.name }}</label>
					</div>
				</fieldset>
				<button class="btn btn-success user-change-submit mr-2" type="submit">Ulož</button>
				<button class="btn btn-secondary user-change-reset" type="reset" >Cancel</button>
			</form>
	</BModal>
</template>