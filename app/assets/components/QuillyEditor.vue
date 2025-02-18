<script setup>
/** 
 * Component QuillyEditor
 * Posledná zmena(last change): 18.02.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
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

const editor = ref()
// Quill instance
let quill = null

import { ClassAttributor } from "parchment";

const AlignClass = new ClassAttributor("align", "text", {
  scope: Quill.import("parchment").Scope.BLOCK,
});

// Registrujeme triedu
Quill.register(AlignClass, true);

const options = {
  theme: 'snow', // If you need Quill theme
  modules: {
    toolbar: [
      [{ size: [] }],
      ['bold', 'italic', 'underline', 'strike'],
			[
				{ align: "" }, 
				{ align: "center" }, 
				{ align: "end" }, // Použijeme "end" namiesto "right"
				{ align: "justify" }
			],
      [{ color: [] }, { background: [] }],
      [{ header: '3' }, { header: '4' }, { header: '5' }, 'blockquote'],
      [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
			['link'],
      ['clean']
    ]
  },
  placeholder: 'Zadaj text ...',
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

const updateText = () => {
	emit('saveText', toRaw(textin.value))     
}

const debouncedChangedText = debounce(updateText, 500);

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
		:is-semantic-html-model="true"
		@update:modelValue="debouncedChangedText(value)"
		style="height: 320px"
	/>
</template>