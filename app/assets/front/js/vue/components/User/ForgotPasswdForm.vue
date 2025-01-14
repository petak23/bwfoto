<script setup>
/**
 * Komponenta pre formulár pri strate hesla.
 * Posledna zmena 14.01.2025
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
import { ref, watch, onMounted } from 'vue'
import MainService from '../../services/MainService.js'

import { useFlashStore } from '../../../../../components/FlashMessages/store/flash' 
const storeF = useFlashStore()

const props = defineProps({
	email: {	// prednastavenie emailovej adresy 
		type: String,
		default: null
	}
})

import { useForm, useIsFormValid, useField } from 'vee-validate';
import * as Yup from 'yup'
import { BFormInput } from 'bootstrap-vue-next'

const schema = Yup.object().shape({
	email: Yup.string().required('Email musíte zadať!').email('Email nie je zadaný správne!').label('Emailová adresa'),
})

const { defineField, errors } = useForm({
  validationSchema: schema,
});

const [email] = defineField('email');
const { meta: metae } = useField('email')

const test_email = ref(null)

const isForgotPassWdFormValid = useIsFormValid()

//const inputEmailRef = ref(null)

watch(() => props.email, (newEmail) => {
	email.value = newEmail
})

watch(isForgotPassWdFormValid, (newValidTest) => {
	if (isForgotPassWdFormValid) testEmail() // Testuj len keď je validný email
	else test_email.value = null
})

const emit = defineEmits('forgotPasswdClose')

const onSubmit = async (event) => {
	event.preventDefault()
	await MainService.postForgottenPassword(email.value)
	.then(response => {
		if (response.data.status == 200) {
			storeF.showMessage(response.data.message, 'success', 'Zabudnuté heslo', 10000)
			console.log(response.data.params)
			
		} else {
			storeF.showMessage(response.data.message, 'danger', 'Zabudnuté heslo', 10000)
		}
		emit('forgotPasswdClose')
	})
	.catch(error => {
		console.error(error)
	});
}

const testEmail = async () => {
	let test = false
	if (isForgotPassWdFormValid && email.value != undefined && email.value.length > 5) {
		await MainService.testUserEmail(email.value)
			.then(response => {
				test = response.data.status == 200
				test_email.value = test ? 1 : 0
			})
			.catch((error) => {
				console.error(error);
			})
	}
	return test
}

onMounted(() => {
	if (props.email != null) {
		email.value = props.email
	} else {
		email.value = null
	}
	/*setTimeout(() => {
		inputEmailRef.value.focus()	
	}, 500);*/
	
})
</script>

<template>
	<form @submit="onSubmit">
		<div class="form-row">
			<div class="form-group">
				<label for="forgotInputEmail">E-mail:</label>
				<BFormInput 
					id="forgotInputEmail" aria-describedby="emailHelp" required
					v-model="email" 
					:state="!metae.dirty ? null : metae.valid" 
					type="email"
					placeholder="Zadaj email"
					@blur="testEmail"
					autofocus
				/>
				<small id="email-help" 
					class="form-text bg-danger text-white px-2"
					v-if="errors.email"
				>
					{{ errors.email }}<br />
				</small>
				<small id="emailHelp" class="form-text text-muted">
					Zadajte, prosím, e-mailovú adresu, na ktorú zašleme inštrukcie na obnovenie hesla!<br />
				</small>
				<small v-if="test_email != null && !test_email && isForgotPassWdFormValid" class="form-text bg-warning px-2 text-dark">
					Vašu e-mailovú adresu sme nenašli v databáze!<br />
					Prosím, skúste sa zaregistrovať.
				</small>
			</div>
		</div>
		<button 
			type="submit"
			class="btn btn-success mt-2 send-button"
			:class="isForgotPassWdFormValid && test_email ? '' : 'disabled'"
			:disabled="!(isForgotPassWdFormValid && test_email)"
		>
			Odošli
		</button>
	</form>
</template>