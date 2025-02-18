<script setup>
/** 
 * Component EditTexts
 * Posledná zmena(last change): 18.02.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.7
 * 
 */
import { ref, watch, onMounted, toRaw } from 'vue'
import { BModal } from 'bootstrap-vue-next'
import QuillyEditor from '../QuillyEditor.vue'

import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()

const props = defineProps({
	button_prefix: { // Prefix classu odosielacích tlačidiel
		type: String,
		default: "main"
	},
	editArticleTextsDialogView: { // Zobrazenie modálneho okna editora
		type: Boolean,
		default: false,
	},
	article: {
		type: Object,
		required: true
	},
})

const articleTextToEdit = ref('')
const editArticleTextsDialogViewModal = ref(false)

const emit = defineEmits(['reloadArticle'])

const onSubmit = (event) => {
	event.preventDefault()
	// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
	if (event.submitter.classList.contains(props.button_prefix + "-submit")) {
		emit('reloadArticle', { text_c: toRaw(articleTextToEdit.value)})
	}
	editArticleTextsDialogViewModal.value = false
}

const onSaveText = (text) => {
	if (text != undefined) articleTextToEdit.value = text
}

const onCancel = (event) => {
	event.preventDefault()
	if (event.explicitOriginalTarget.classList.contains(props.button_prefix + "-reset")) {
		articleTextToEdit.value = props.article.text_c
	}
	editArticleTextsDialogViewModal.value = false
}

watch(() => props.article, () => {
	articleTextToEdit.value = props.article.text_c
})

watch(() => props.editArticleTextsDialogView, (newEditArticleTextsDialogView) => {
	editArticleTextsDialogViewModal.value = newEditArticleTextsDialogView
})

onMounted(() => {
	articleTextToEdit.value = props.article.text_c
})
</script>

<template>
	<BModal 
		v-model="editArticleTextsDialogViewModal" 
		centered 
		:title="store.texts.base_edit_texts" 
		hide-footer
		body-bg-variant="dark"
		header-bg-variant="dark"
		hide-header-close
		fullscreen
	>
		<div v-html="articleTextToEdit"></div>
		<form @submit="onSubmit" @reset="onCancel">
			<div id="input-text-gr" role="group" class="form-group mb-2 qu-editor">
				<div>
					<QuillyEditor
						:text-to-edit="articleTextToEdit"
						@saveText="onSaveText"
					/>

				</div>
			</div>
			<button 
				type="submit"
				class="btn btn-success me-2" 
				:class="props.button_prefix + '-submit'"
			>
				Ulož
			</button>
			<button
				type="reset" 
				class="btn btn-secondary"
				:class="props.button_prefix + '-reset'"
			>
				Cancel
			</button>
		</form>
	</BModal>
</template>