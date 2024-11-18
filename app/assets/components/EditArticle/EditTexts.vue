<script setup>
/** 
 * Component EditTexts
 * Posledná zmena(last change): 18.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.3
 * 
 */
import { ref, watch, onMounted } from 'vue'
import MainService from '../../front/js/vue/services/MainService'
import { BModal } from 'bootstrap-vue-next'
import Quill from 'quill' // Full build
// https://github.com/alekswebnet/vue-quilly
//import Quill from 'quill/core' // Core build
import { QuillyEditor } from 'vue-quilly'
import 'quill/dist/quill.core.css' // Required
import 'quill/dist/quill.snow.css' // For snow theme (optional)
import '../../front/css/quill.bwfoto.css' // Vlastné zmeny v téme

const editor = ref()
// Quill instance
let quill = null

const options = {
  theme: 'snow', // If you need Quill theme
  modules: {
    toolbar: true,
  },
  //placeholder: 'Compose an epic...',
  readOnly: false
}

import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()
import { useFlashStore } from '../../front/js/vue/store/flash'
const storeF = useFlashStore()

const props = defineProps({
	button_prefix: { // Prefix classu odosielacích tlačidiel
		type: String,
		default: "main"
	},
	editArticleTextsDialogView: {
		type: Boolean,
		default: false,
	}
})

const textin = ref('')
const show = ref(true)
const editArticleTextsDialogViewModal = ref(false)

const emit = defineEmits(['reloadArticle'])

const onSubmit = (event) => {
	event.preventDefault()
	// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
	if (event.submitter.classList.contains(props.button_prefix + "-submit")) {
		MainService.postTextSave(store.article.id, { texts: textin.value })
			.then(function (response) {
				storeF.showMessage('Text bol uložený.', 'success', 'Podarilo sa...', 10000)
				let	td = response.data
				delete td.result
				store.article = td
				setTimeout(() => {
					emit('reloadArticle', [td]) // Info o úroveň vyššie o znovunačítaní informácií o položke
				}, 100)
			})
			.catch(function (error) {
				console.error(error)
			});      
	}
	editArticleTextsDialogViewModal.value = false
}

const onCancel = (event) => {
	event.preventDefault()
	if (event.explicitOriginalTarget.classList.contains(props.button_prefix + "-reset")) {
		textin.value = store.article.text_c
		setTimeout(() => {
			emit('reloadArticle', store.article) // Info o úroveň vyššie o znovunačítaní informácií o položke
		}, 100)
	}
	editArticleTextsDialogViewModal.value = false
}

/* https://dev.to/anjolaogunmefun/using-vuequill-editor-in-vue-js3-1cpd */
const onEditorReady = (e) => {
	e.container.querySelector('.ql-blank').innerHTML = 
		(store.article != null ? store.article.text_c : "Prázdno ...")
}

watch(() => store.article, () => {
	textin.value = store.article.text_c
})

watch(() => props.editArticleTextsDialogView, (newEditArticleTextsDialogView) => {
	editArticleTextsDialogViewModal.value = newEditArticleTextsDialogView
})

onMounted(() => {
	textin.value = store.article != null ? store.article.text_c : ""
	quill = editor.value && editor.value.initialize(Quill);
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
		<form @submit="onSubmit" @reset="onCancel" v-if="show">
			<div id="input-text-gr" role="group" class="form-group mb-2 qu-editor">
				<div>
					<QuillyEditor
						ref="editor"
						v-model="textin"
						:options="options"
						@update:modelValue="(value) => console.log('HTML model updated:', value)"
						@ready="onEditorReady($event)/*(quill) => console.log('ready', quill)*/"
						style="height: 320px"
					/>

				</div>
			</div>
			<button 
				type="submit"
				class="btn btn-success mr-2" 
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