<script setup>
/** 
 * Component QuillyEditor
 * Posledná zmena(last change): 21.01.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 * 
 */
import { ref, watch, onMounted, toRaw } from 'vue'
import Quill from 'quill' // Full build
// https://github.com/alekswebnet/vue-quilly
//import Quill from 'quill/core' // Core build
import { QuillyEditor } from 'vue-quilly'
import 'quill/dist/quill.core.css' // Required
import 'quill/dist/quill.snow.css' // For snow theme (optional)
import '../front/css/quill.bwfoto.css' // Vlastné zmeny v téme

import { debounce } from 'lodash'

const debouncedChangedText = debounce(updateText, 500);

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

const props = defineProps({
	textToEdit: { // Text na editáciu do editora
		type: String,
		default: "",
	}
})

const textin = ref('') // Text na editáciu v editore

const emit = defineEmits(['saveText'])

const updateText = (value) => {
	emit('saveText', toRaw(textin.value))     
}

/* https://dev.to/anjolaogunmefun/using-vuequill-editor-in-vue-js3-1cpd */
const onEditorReady = (e) => {
	e.container.querySelector('.ql-blank').innerHTML = props.textToEdit
}

watch(() => props.textToEdit, () => {
	textin.value = props.textToEdit
})

onMounted(() => {
	textin.value = props.textToEdit
	quill = editor.value && editor.value.initialize(Quill);
})
</script>

<template>
	<QuillyEditor
		ref="editor"
		v-model="textin"
		:options="options"
		@update:modelValue="debouncedChangedText(value)"
		@ready="onEditorReady($event)"
		style="height: 320px"
	/>
</template>