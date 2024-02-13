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

export default {
	data() {
		return {
			form: {
				email: '',
				password: '',
				remember: false,
			},
			submit_enabled: true  // - dočasne inak false
		}
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

		},
	}
}
</script>

<template>
	<b-form @submit="onSubmit" @reset="onReset" class="sign-in-form">
		<b-form-group
			id="input-group-1"
			label=""
			label-for="input-1"
		>
			<b-form-input
				id="input-1"
				v-model="form.email"
				type="email"
				placeholder="Zadaj email"
				required
			></b-form-input>
		</b-form-group>

		<b-form-group id="input-group-2" label="" label-for="input-2">
			<b-form-input
				id="input-2"
				v-model="form.password"
				type="password"
				placeholder="Zadaj heslo"
				required
			></b-form-input>
		</b-form-group>

		<!-- b-form-group id="input-group-4" v-slot="{ ariaDescribedby }">
			<b-form-checkbox-group
				v-model="form.remember"
				id="checkboxes-4"
				:aria-describedby="ariaDescribedby"
			>
				<b-form-checkbox :value="true">Pamätať si prihlásenie</b-form-checkbox>
			</b-form-checkbox-group>
		</ -->

		<b-button 
			type="submit" 
			variant="success"
			:disabled="!submit_enabled"
		>Prihlásiť</b-button>

		<b-button @onClick="forgottenPassword" variant="link">Zabudnuté heslo</b-button>
	</b-form>

	<!--form action="/~petak23/bwfoto/login" method="post">
		<div class="sign-in-form">
			<div class="form-group row justify-content-center required">
				<div class="d-none"><label for="frm-signInForm-email" class="required">Email:</label></div>
				<div class="col-12">
					<input type="email" name="email" placeholder="Email:" id="frm-signInForm-email" required="required" data-nette-rules="[{&quot;op&quot;:&quot;:filled&quot;,&quot;msg&quot;:&quot;Uveďte, prosím, užívateľský email.&quot;},{&quot;op&quot;:&quot;:email&quot;,&quot;msg&quot;:&quot;Please enter a valid email address.&quot;},{&quot;op&quot;:&quot;:email&quot;,&quot;msg&quot;:&quot;Musíte zadať platnú e-mailovú adresu!&quot;}]" class="form-control text">
				</div>
			</div>
			<div class="form-group row justify-content-center required">
				<div class="d-none"><label for="frm-signInForm-password" class="required">Heslo:</label></div>
				<div class="col-12">
					<input type="password" name="password" placeholder="Heslo:" id="frm-signInForm-password" required="required" data-nette-rules="[{&quot;op&quot;:&quot;:filled&quot;,&quot;msg&quot;:&quot;Uveďte, prosím, heslo.&quot;},{&quot;op&quot;:&quot;:minLength&quot;,&quot;msg&quot;:&quot;Heslo musí mať aspoň 3 znaky&quot;,&quot;arg&quot;:3}]" class="form-control text">
				</div>
			</div>
			<div class="form-group row justify-content-center">
				<div class="d-none"></div>
				<div class="col-12">
					<div class="form-check">
						<label for="frm-signInForm-remember" class="form-check-label">
							<input type="checkbox" name="remember" id="frm-signInForm-remember" class="form-check-input"> Pamätať si ma
						</label>
					</div>
				</div>
			</div>
			<div class="form-group row justify-content-center">
				<div class="d-none"></div>
				<div class="col-12">
					<input type="submit" name="login" value="Prihlásiť sa..." class="btn btn-success button">
					<input type="submit" name="forgottenPassword" value="Zabudnuté heslo?" formnovalidate="formnovalidate" class="btn btn-link button">
				</div>
			</div>
		</div>
		<input type="hidden" name="_token_" value="tnqykqi6a7eibylziyx/v+PmjgDD2f6yJHINY=">
		<input type="hidden" name="_do" value="signInForm-submit">
	</form -->
</template>



<style scoped>
.sign-in-form {
	display: inline-block;
}
</style>