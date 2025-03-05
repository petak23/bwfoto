<script setup>
/**
 * Komponenta pre formulár na zadanie/editáciu verzií.
 * Posledna zmena 05.03.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.6
 */
import { ref, onMounted, toRaw, watch } from 'vue'
import QuillyEditor from '../../../../../components/QuillyEditor.vue'
import { BForm, BFormGroup, BFormInput, BButton } from 'bootstrap-vue-next'

const props = defineProps({
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

const emit = defineEmits(['saveVersion'])

const onSaveText = (text) => {
	if (text != undefined) form.value.text = text
}

const onSubmit = (event) => {
	event.preventDefault()
	// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
	if (event.submitter.classList.contains("main-submit")) {
		emit('saveVersion', toRaw(form.value))
	}
}

const onReset = (event) => {
	event.preventDefault()
	if (event.explicitOriginalTarget.classList.contains("main-reset")) {
		emit('saveVersion', null)
	}
}

watch(() => props.verzia, () => {
	if (props.verzia != null) {
		form.value.id = props.verzia.id
		form.value.cislo = props.verzia.cislo
		form.value.text = props.verzia.text
	}
})

onMounted(() => {
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
		<b-button type="submit" variant="success" class="main-submit me-1">Ulož</b-button>
		<b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>
	</b-form>

</template>
