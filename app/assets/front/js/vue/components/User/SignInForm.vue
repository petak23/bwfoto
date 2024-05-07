<script>
/**
 * Komponenta pre prihlasovací formulár.
 * Posledna zmena 17.01.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */

import MainService from '../../services/MainService.js'
import ForgotPasswdForm from './ForgotPasswdForm.vue'

export default {
	components: {
		ForgotPasswdForm,
	},
	data() {
		return {
			form: {
				email: '',
				password: '',
				remember: false,
			},
			submit_enabled: true, // - dočasne inak false
			error_message: null, 	// inak objekt {status: xxx, error: "Správa o chybe..."}
		}
	},
	computed: {
		isFormValid() {
			return Object.keys(this.fields).every(key => this.fields[key].valid);
		},
	},
	methods: {
		async onSubmit(event) {
			event.preventDefault()
			let vm = this
			await MainService.postSignIn(this.form)
			.then(response => {
				if (response.data.status == 200) {
					vm.$store.commit('SET_INIT_USER', response.data.user)
					if (typeof (this.$store.state.user.id) != 'undefined') {
						vm.$root.$emit("user-loadet", [])
						vm.$root.$emit('flash_message', [{
							'message': 'Ǔspešne ste sa prihlásili.',
							'type': 'success',
							'heading': 'Prihlásenie',
							'timeout': 5000,
						}])
					}
					// https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
					// Tvrdé presmerovanie po prihlásení.
					window.location.href = this.$store.state.basePath;
				} else {
					console.error(response.data)
					this.error_message = response.data
				}
			})
			.catch(error => {
				console.log(error)
				alert(error)
			});
		},
		onReset(event) {
			event.preventDefault()
			// Reset our form values
			this.form.email = ''
			this.form.password = ''
			this.form.remember = false
			// Trick to reset/clear native browser form validation state
			this.submit_enabled = false
			this.$nextTick(() => {
				this.submit_enabled = true
			})
		},
		forgottenPassword() {
			//console.log("FPass");
			this.$bvModal.show("modal-forgot-passwd")
		},
	},
	mounted () {
    this.$root.$on('forgottenHide', () => {
      this.$bvModal.hide("modal-forgot-passwd")
    })
	},
}
</script>

<template>
	<form @submit="onSubmit" @reset="onReset" class="sign-in-form">
		<div class="form-row" v-if="error_message != null">
			<div class="form-group">
				<div class="alert alert-danger"> 
					<h4 class="alert-heading">Chyba!!!</h4>
					<p>{{ error_message.error }}</p>
				</div>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<input 
					type="email" class="form-control" 
					name="signInEmail"
					placeholder="Zadaj email"
					id="signInEmail" aria-describedby="emailHelp" 
					required
					v-validate="'required|email'" 
					data-vv-as="e-mail"
					v-model="form.email"
				>
				<small class="form-text bg-danger text-white px-2">
					{{ errors.first('signInEmail') }}
				</small>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group">
				<input 
					type="password" 
					class="form-control"
					id="signInPassdord"
					placeholder="Zadaj heslo"
					name="signInPassdord"
					required
					v-validate="'required|min:5'"
					v-model="form.password"
					data-vv-as="heslo"
				>
				<small class="form-text bg-danger text-white px-2">
					{{ errors.first('signInPassdord') }}
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
			<div class="form-group">
				<button 
					type="submit"
					class="btn btn-success mt-2 send-button"
					:class="submit_enabled && isFormValid ? '' : 'disabled'"
					:disabled="!(submit_enabled && isFormValid)"
				>
					Prihlásiť
				</button>
				<button 
					v-on:click.prevent="forgottenPassword" 
					class="btn btn-link mt-2"
				>
					Zabudnuté heslo
				</button>
				<b-modal 
					id="modal-forgot-passwd" 
					title="Obnovenie zabudnutého hesla"
					centered 
					size="xl" 
					ok-only
					header-bg-variant="dark"
					header-text-variant="light"
					body-bg-variant="dark"
					body-text-variant="white"
					footer-bg-variant="dark"
					footer-text-variant="light"
					hide-footer 
				>
					<forgot-passwd-form></forgot-passwd-form>
				</b-modal>
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