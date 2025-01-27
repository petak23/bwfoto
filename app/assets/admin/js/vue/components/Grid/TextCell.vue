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
import MainService from '../../services/MainService'

const props = defineProps({
	value: {
		type: String,
		default: '',
		//required: true,
	},
	apiLink: {
		type: String,
		required: true,
	},
	colName: {
		type: String,
		required: true,
	},
	id: {
		type: Number,
		required: true,
	}
})

const my_value = ref('')
const editing = ref(false)
//const edit_name = ref('')
const input = useTemplateRef('text_area')

const updateItem = () => {
	editing.value = false
	let odkaz = props.apiLink + props.id
	let vm = this
	// TODO axios, storeF
	axios.post(odkaz, {
			[props.colName]: my_value.value,
		})
		.then(function (response) {
			//vm.preview = response.data
			//console.log(response.data)
			vm.$root.$emit('flash_message', 
												[{ 'message': 'Uloženie v poriadku', 
													'type':'success',
													'heading': 'Uložené'
													}])
			vm.my_value
		})
		.catch(function (error) {
			console.log(odkaz)
			console.log(error)
			vm.$root.$emit('flash_message', 
												[{ 'message': 'Pri uklasaní došlo k chybe',
													'type':'danger',
													'heading': 'Chyba'
													}])
		});      
	
}
const edit = async () => {
	editing.value = true
	
	// https://forum.vuejs.org/t/setting-focus-to-textarea-not-working/17891/5
	// https://vuejs.org/guide/essentials/template-refs.html#template-refs
	await nextTick()
	text_area.value.focus()

}

watch(() => props.value, (newValue, oldValue) => {
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