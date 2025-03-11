<script setup>
/**
 * Komponenta pre vypísanie textového políčka gridu.
 * Posledna zmena 11.03.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
import { ref, watch, onMounted, nextTick, useTemplateRef } from 'vue'

const props = defineProps({
	tag: {	// Tag akým má byť obalená komponenta
    type: String,
    default: 'div'
  },
	value: {	// Hodnota prvku
		type: String,
		default: '',
	},
	// TODO @deprecadet
	id: {	// Id editovaného prvku z DB 
		type: Number,
		default: 0,
	},
	// TODO DEPRECADET
	index: {
		type: Number,
		default: 0,
	}
})

const my_value = ref('')
const editing = ref(false)
const text_area = useTemplateRef('text_area')

const emit = defineEmits(['saveData'])

const updateItem = () => {
	editing.value = false
	// TODO delete deprecadet props id, index...
	emit('saveData', {id: props.id, text:my_value.value, index: props.index })
}
const edit = async () => {
	editing.value = true
	
	// https://forum.vuejs.org/t/setting-focus-to-textarea-not-working/17891/5
	// https://vuejs.org/guide/essentials/template-refs.html#template-refs
	await nextTick()
	text_area.value.focus()

}
watch(() => props.value, () => {
	my_value.value = props.value
})

onMounted(() => {
	my_value.value = props.value
})
</script>

<template>
	<component :is="props.tag" 
		class="text-col"
		@click="edit"
	>
		<div v-if="!editing">{{ my_value }}</div>
		<textarea 
			ref="text_area"
			v-model="my_value"
			v-if="editing"
			@blur="updateItem">
		</textarea>
	</component>
</template>


<style>
.text-col{
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
}
textarea {
	max-width: 100%;
	height: 100%;
}
</style>