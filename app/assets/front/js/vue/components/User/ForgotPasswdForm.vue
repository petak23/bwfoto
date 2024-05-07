<script>
/**
 * Komponenta pre formulár pri strate hesla.
 * Posledna zmena 06.05.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */

import MainService from '../../services/MainService.js'

export default {
	props: {
		email: {
			type: String,
			default: '',
		},
	},
	data() {
		return {
			form_email: '',
			submit_enabled: false, // Použité pri resete
			test_email: null,
			email_tested: false,
		}
	},
	watch: {
		email() {
			this.form_email = this.email
		},
		isEmailVAlid() {
			if (this.isEmailVAlid) this.testEmail() // Testuj len keď je validný email
			else this.test_email = null
		}
	},
	computed: {
		isFormValid() {
			return Object.keys(this.fields).every(key => this.fields[key].valid);
		},
		isEmailVAlid() {
			return this.fields['forgotInputEmail'] !== undefined ? this.fields['forgotInputEmail'].valid : false
		}
	},
	methods: {
		async onSubmit(event) {
			event.preventDefault()
			let vm = this
			await MainService.postForgottenPassword(this.form_email)
			.then(response => {
				vm.$root.$emit('flash_message', [{
					'message': response.data.message,
					'type': response.data.status == 200 ? 'success' : 'danger',
					'heading': 'Zabudnuté heslo',
					'timeout': 10000,
				}])
				vm.$root.$emit('forgottenHide', [])
			})
			.catch(error => {
				console.log(error)
				alert(error)
			});
		},
		onReset(event) {
			event.preventDefault()
			// Reset our form values
			this.form_email = ''
			// Trick to reset/clear native browser form validation state
			this.submit_enabled = false
			this.$nextTick(() => {
				this.submit_enabled = true
			})
		},
		forgottenPassword() {

		},
		async testEmail() {
			console.log(this.form_email)
			let test = false
			await MainService.testUserEmail(this.form_email)
				.then(response => {
					test = response.data.status == 200
					//console.log(response.data);
					//console.log(test ? "Našiel som mail..." : "Žiadny mail...")
					this.test_email = test ? 1 : 0
				})
				.catch((error) => {
					console.error(error);
				})

			return test
		}
	}
}
</script>

<template>
	<form @submit="onSubmit" @reset="onReset">
		<div class="form-row">
			<div class="form-group">
				<label for="forgotInputEmail">E-mail:</label>
				<input 
					type="email" class="form-control" 
					name="forgotInputEmail"
					id="forgotInputEmail" aria-describedby="emailHelp" required
					v-validate="'required|email'" 
					data-vv-as="e-mail"
					v-model="form_email"
					@blur="testEmail"
				>
				<small class="form-text bg-danger text-white px-2">{{ errors.first('forgotInputEmail') }}</small>
				<small id="emailHelp" class="form-text text-muted">
					Zadajte, prosím, e-mailovú adresu, na ktorú zašleme inštrukcie na obnovenie hesla!
				</small>
				<small v-if="test_email != null && !test_email" class="form-text bg-warning px-2 text-dark">
					Vašu e-mailovú adresu sme nenašli v databáze!
					Prosím, skúste sa zaregistrovať.
				</small>
			</div>
		</div>
		<button 
			type="submit"
			class="btn btn-success mt-2 send-button"
			:class="isFormValid && test_email ? '' : 'disabled'"
			:disabled="!(isFormValid && test_email)"
		>
			Odošli
		</button>
	</form>
</template>