<script setup>
/**
 * Komponenta pre prihlasovací formulár.
 * Posledna zmena 14.11.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
import { ref } from 'vue'
import MainService from '../../services/MainService'
import ForgotPasswdForm from './ForgotPasswdForm.vue'

import { useRouter } from 'vue-router'
const router = useRouter()

import { useMainStore } from '../../store/main'
import { useFlashStore } from '../../store/flash'
const store = useMainStore()
const storeF = useFlashStore()

import { useForm, useIsFormValid, useField } from 'vee-validate';
import * as Yup from 'yup'
import { BFormInput, BModal } from 'bootstrap-vue-next'

const schema = Yup.object().shape({
	email: Yup.string().required('Email musíte zadať!').email('Email nie je zadaný správne!').label('Emailová adresa'),
	password: Yup.string().required('Heslo musí byť zadané!').min(5, 'Heslo musí mať aspoň 5 zankov'),
})

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
});

const { meta: metae } = useField('email')
const { meta: metap } = useField('password')

const [email] = defineField('email');
const [password] = defineField('password');

const error_message = ref(null) 	// inak objekt {status: xxx, error: "Správa o chybe..."}

const onSubmit = handleSubmit((values) => {
	error_message.value = null
  MainService.postSignIn(values)
	.then(response => {
		if (response.data.status == 200 && response.data.user != undefined && response.data.user != null) {
			store.user = response.data.user
			store.user_permission = response.data.user.permission
			storeF.showMessage('Úspešne ste sa prihlásili.', 'success', 'Prihlásenie', 5000)
			router.push('/') // Presmerovanie...
		} else {
			console.error(response.data)
			error_message.value = response.data
		}
	})
	.catch(error => {
		console.error(error)
		error_message.value = response.data
	});
});
	
const isSignInFormValid = useIsFormValid()

const submit_enabled = ref(true) // - dočasne inak false

const forgotDialogView = ref(false)

const	onReset = async (event) => {
	event.preventDefault()
	// Reset our form values
	form.email = ''
	form.password = ''
	form.remember = false
	// Trick to reset/clear native browser form validation state
	submit_enabled.value = false
	
	await nextTick()
	submit_enabled.value = true
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
					id="input-email"
					v-model="email" 
					aria-describedby="email-help"
					:state="!metae.dirty ? null : metae.valid" 
					type="email"
					placeholder="Zadaj email"
					autofocus
				/>
				<small id="email-help" class="text-danger">{{ errors.email }}</small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<BFormInput 
					id="input-passwordemail"
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
		<!-- b-form-group id="input-group-4" v-slot="{ ariaDescribedby }">
			<b-form-checkbox-group
				v-model="form.remember"
				id="checkboxes-4"
				:aria-describedby="ariaDescribedby"
			>
				<b-form-checkbox :value="true">Pamätať si prihlásenie</b-form-checkbox>
			</b-form-checkbox-group>
		</ -->

		<div class="form-row">
			<div class="form-group my-3">
				<button 
					type="submit"
					class="btn btn-success mt-2 send-button"
					:class="submit_enabled && isSignInFormValid ? '' : 'disabled'"
					:disabled="!(submit_enabled && isSignInFormValid)"
				>
					Prihlásiť
				</button>
				<button 
					@click.prevent="forgotDialogView = true"
					class="btn btn-link mt-2"
				>
					Zabudnuté heslo
				</button>
				<BModal
					v-model="forgotDialogView" 
					title="Obnovenie zabudnutého hesla"
					centered 
					size="xl" 
					hide-footer 
				>
					<forgot-passwd-form 
						@forgot-passwd-close="() => {forgotDialogView = false}"
					/>
				</BModal>
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