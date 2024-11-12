<script setup>
/**
 * Komponenta pre registračný formulár.
 * Posledna zmena 26.09.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
import { ref, watch } from 'vue'
import MainService from '../../services/MainService'

import { useRouter } from 'vue-router'
const router = useRouter()

import { useMainStore } from '../../store/main'
import { useFlashStore } from '../../store/flash'
const store = useMainStore()
const storeF = useFlashStore()

import { useForm, useIsFormValid, useField } from 'vee-validate';
import * as Yup from 'yup'
import { BFormInput } from 'bootstrap-vue-next'

const schema = Yup.object().shape({
	name: Yup.string().required('Meno a priezvisko musí byť zadané!').min(5, 'Meno a priezvisko musí mať aspoň 5 znakov'),
	email: Yup.string().required('Email musíte zadať!').email('Email nie je zadaný správne!').label('Emailová adresa'),
	password: Yup.string().required('Heslo musí byť zadané!').min(5, 'Heslo musí mať aspoň 5 znakov'),
	passwordConfirmation: Yup.string().required().oneOf([Yup.ref('password')], 'Heslá sa nezhodujú!!!'),
})

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
});

const { meta: metan } = useField('name')
const { meta: metae } = useField('email')
const { meta: metap } = useField('password')
const { meta: metapc } = useField('passwordConfirmation')

const [name] = defineField('name')
const [email] = defineField('email')
const [password] = defineField('password')
const [passwordConfirmation] = defineField('passwordConfirmation')

const error_message = ref(null) 	// inak objekt {status: xxx, error: "Správa o chybe..."}

const onSubmit = handleSubmit((values) => {
	error_message.value = null
  MainService.postRegistration(values)
	.then(response => {
		if (response.data.status == 200) {
			console.log(response.data.params);
			storeF.showMessage(response.data.message, 'success', 'Registrácia bola úspešná', 60000)
			router.push('/') // Presmerovanie...
		} else {
			console.error(response.data)
			error_message.value = response.data.message
		}
	})
	.catch(error => {
		console.error(error)
		error_message.value = response.data.message
	});
});
	
const isRegisterFormValid = useIsFormValid()

const submit_enabled = ref(true) // - dočasne inak false

const test_email = ref(null)

watch(isRegisterFormValid, (newValidTest) => {
	if (isRegisterFormValid) testEmail() // Testuj len keď je validný email
	else test_email.value = null
})

const testEmail = async () => {
	let test = false
	if (email.value != undefined && email.value.length > 5) {
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


</script>

<template>
	<div class="form-row" v-if="error_message != null">
		<div class="form-group">
			<div class="alert alert-danger"> 
				<h4 class="alert-heading">Chyba!!!</h4>
				<p>{{ error_message.error }}</p>
			</div>
		</div>
	</div>
	<form @submit="onSubmit" @reset="onReset" class="sign-in-form">
		<div class="form-row">
			<div class="form-group">
				<BFormInput 
					id="registerInputEmail" aria-describedby="emailHelp" required
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
				<small v-if="test_email != null && test_email && isRegisterFormValid" class="form-text bg-warning px-2 text-dark">
					Vašu e-mailovú adresu sme už našli v databáze!<br />
					Prosím, skúste sa prihlásiť, alebo pokračovať cez zabudnuté heslo.
				</small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<BFormInput 
					id="input-name"
					v-model="name"
					aria-describedby="name-help"
					type="text"
					:state="!metan.dirty ? null : metan.valid" 
					placeholder="Zadajte meno a priezvisko"
				/>
				<small id="name-help" class="text-danger">
					{{ errors.name }}
				</small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<BFormInput 
					id="input-password"
					v-model="password"
					aria-describedby="password-help"
					type="password"
					:state="!metap.dirty ? null : metap.valid" 
					placeholder="Zadaj heslo"
				/>
				<small id="password-help" class="text-danger">
					{{ errors.password }}
				</small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<BFormInput 
					id="input-passwordConfirmation"
					v-model="passwordConfirmation"
					aria-describedby="passwordConfirmation-help"
					type="password"
					:state="!metapc.dirty ? null : metapc.valid" 
					placeholder="Potvrď heslo"
				/>
				<small id="passwordConfirmation-help" class="text-danger">
					{{ errors.passwordConfirmation }}
				</small>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group my-3">
				<button 
					type="submit"
					class="btn btn-success mt-2 send-button"
					:class="submit_enabled && isRegisterFormValid ? '' : 'disabled'"
					:disabled="!(submit_enabled && isRegisterFormValid)"
				>
					Registrovať
				</button>
			</div>
		</div>
	</form>
</template>

<style scoped>
.sign-in-form {
	display: inline-block;
}
.send-button:disabled {
	cursor: not-allowed;
	opacity: .5;
}

.form-control:disabled {
	cursor: not-allowed;
	opacity: .5;
}
</style>