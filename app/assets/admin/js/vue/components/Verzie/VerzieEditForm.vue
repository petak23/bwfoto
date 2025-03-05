<script setup>
/**
 * Komponenta pre formulár na zadanie/editáciu verzií.
 * Posledna zmena 27.01.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
import { ref, onMounted, toRaw } from 'vue'
import QuillyEditor from '../../../../../components/QuillyEditor.vue'
//import MainService from '../../services/MainService'
import { BForm, BFormGroup, BFormInput, BButton } from 'bootstrap-vue-next'
//import { useFlashStore } from '../../../../../components/FlashMessages/store/flash'
//const storeF = useFlashStore()

const props = defineProps({
	/*id: {
		type: Number,
		default: 0
	},
	basePath: {
		type: String,
		required: true
	},*/
	verzia: {
		type: Object,
		default: null,
	}
})

const form = ref({
	id: 0,
	cislo: "",
	text:"",
})
//const back_link = ref('/administration/verzie/')

const emit = defineEmits(['saveVersion'])

const onSaveText = (text) => {
	if (text != undefined) form.value.text = text
	//emit('saveVersion', toRaw(form.value))
}

const onSubmit = (event) => {
	event.preventDefault()
	// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
	if (event.submitter.classList.contains("main-submit")) {
		emit('saveVersion', toRaw(form.value))
		/*MainService.postSaveVerzie(props.id, form.value.number, form.value.text)
			.then(function (response) {
				//console.log(response)
				// https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
				//window.location.href = vm.basePath + vm.back_link;
				storeF.showMessage('Verzia bola uložená.', 'success', 'Podarilo sa...', 5000)
			})
			.catch(function (error) {
				console.error(error)
				storeF.showMessage('Pri ukladaní došlo k chybe... Skúste neskôr znovu.', 'danger', 'Chyba', 50000)
			});*/
	}
}

const onReset = (event) => {
	event.preventDefault()
	if (event.explicitOriginalTarget.classList.contains("main-reset")) {
		emit('saveVersion', null)
		//window.location.href = props.basePath + back_link.value
	}
}

onMounted(() => {
	/*if (props.id !== 0) { // Len pri editácii
		// Načítanie údajov priamo z DB
		MainService.getVersion(props.id)
			.then(response => {
				//console.log(response.data)
				form.value.id = response.data.id
				form.value.id_user_main = response.data.id_user_main
				form.value.number = response.data.cislo
				form.value.text = response.data.text
				form.value.modified = response.data.modified
			})
			.catch((error) => {
				console.error(error)
			})
		}*/
	if (props.verzia != null) {
		form.value.id = props.verzia.id
		form.value.cislo = props.verzia.cislo
		form.value.text = props.verzia.text
	}
})

</script>

<template>
	<b-form @submit="onSubmit" @reset="onReset" class="my-tip-tap">
		<b-form-group
			id="input-number-gr"
			label="Číslo verzie:"
			label-for="input-number"
		>
			<b-form-input
				id="input-number"
				size="sm"
				v-model="form.cislo"
				type="text"
				required
				autofocus
				>
			</b-form-input>
		</b-form-group>
		<b-form-group
			id="input-text-gr"
			label="Popis verzie:"
			label-for="input-text"
		>
			<QuillyEditor
				:text-to-edit="form.text"
				@saveText="onSaveText"
			/>
		</b-form-group>
		<input type="hidden" :value="form.id">
		<b-button type="submit" variant="success" class="main-submit">Ulož</b-button>
		<b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>
	</b-form>

</template>
