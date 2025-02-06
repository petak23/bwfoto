<script setup>
/**
 * Komponenta pre vypísanie textového políčka gridu.
 * Posledna zmena 09.06.2022
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */
import { ref, watch, onMounted, nextTick, useTemplateRef } from 'vue'

const props = defineProps({
	value: {
		type: String,
		default: '',
		//required: true,
	},
	id: {
		type: Number,
		required: true,
	}
})

const my_value = ref('')
const editing = ref(false)
//const edit_name = ref('')
const text_area = useTemplateRef('text_area')

const emit = defineEmits(['saveData'])

const updateItem = () => {
	editing.value = false
	emit('saveData', {id: props.id, text:my_value.value })
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
	<div 
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
	</div>
</template>


<style>
.text-col{
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	/*border: 2px solid red;*/
}
textarea {
	max-width: 100%;
	height: 100%;
}
</style>