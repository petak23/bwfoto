<script setup>
/**
 * Komponenta pre formulár na reset hesla.
 * Posledna zmena 03.10.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
import { onMounted, ref, watch } from 'vue'
import MainService from '../../services/MainService'

import { useRouter } from 'vue-router'
const router = useRouter()

//import { useMainStore } from '../../store/main'
import { useFlashStore } from '../../store/flash'
//const store = useMainStore()
const storeF = useFlashStore()

import { useForm, useIsFormValid, useField } from 'vee-validate';
import * as Yup from 'yup'
import { BFormInput } from 'bootstrap-vue-next'

const props = defineProps({
	id: Number,
	new_password_key: String,
})

const schema = Yup.object().shape({
	password: Yup.string().required('Heslo musí byť zadané!').min(5, 'Heslo musí mať aspoň 5 znakov'),
	passwordConfirmation: Yup.string().required().oneOf([Yup.ref('password')], 'Heslá sa nezhodujú!!!'),
})

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
});

const { meta: metap } = useField('password')
const { meta: metapc } = useField('passwordConfirmation')

const [password] = defineField('password')
const [passwordConfirmation] = defineField('passwordConfirmation')

const error_message = ref(null) 	// inak objekt {status: xxx, error: "Správa o chybe..."}
const show_form = ref(false)

const onSubmit = handleSubmit((values) => {
	error_message.value = null
  MainService.postResetPassword(values)
	.then(response => {
		if (response.data.status == 200) {
			console.log(response.data.params);
			storeF.showMessage(response.data.message, 'success', 'Reset hesla bol úspešný', 60000)
			router.push('/login') // Presmerovanie...
		} else {
			console.error(response.data)
			error_message.value = response.data.message
		}
	})
	.catch(error => {
		console.error(error)
		error_message.value = response.data.message
	})
})
	
const isResetFormValid = useIsFormValid()

const submit_enabled = ref(true) // - dočasne inak false

const test_user = () => {
	MainService.getUserNpk(props.id, props.new_password_key)
	.then(response => {
		console.log(response.data);
		
		if (response.data.status == "200") {
			show_form.value = true
		} else {
			show_form.value = false
			error_message.value = "Overovacie kľúče nesúhlasia!"
		}
	})
	.catch(error => {
		console.error(error)
		error_message.value = response.data.message
	})
}

onMounted(() => {
	test_user()
})
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
	<form @submit="onSubmit" @reset="onReset" class="sign-in-form" v-if="show_form">
		<div class="form-row">
			<div class="form-group">
				<BFormInput 
					id="input-password"
					v-model="password"
					aria-describedby="password-help"
					type="password"
					:state="!metap.dirty ? null : metap.valid" 
					placeholder="Zadaj nové heslo"
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
					placeholder="Potvrď nové heslo"
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
					:class="submit_enabled && isResetFormValid ? '' : 'disabled'"
					:disabled="!(submit_enabled && isResetFormValid)"
				>
					Resetovať
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