<script>
/**
 * Komponenta pre zadanie a editáciu kontaktných údajov.
 * Posledna zmena 07.05.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */
	import countryCodes from "../../plugins/country.js"
	import MainService from '../../services/MainService.js'

	export default {
		data() {
			return {
				country: countryCodes,
				confirmation: "",
				f_data: {
					name: "",
					email: "",
					password: "",
					street: "",
					town: "",
					country: "",
					psc: "",
					phone: "+421",
					adress2: {
						street: "",
						town: "",
						country: "",
						psc: ""
					},
					firm: {
						name: "",
						ico: "",
						dic: "",
						icdph: "",
						street: "",
						town: "",
						country: "",
						psc: ""
					},
				},
				test_email: 0, // 0: nevalidný; 1: validný a nenájdený; 2: validný a nájdený; 3: najdený a prihlásený
			}
		},
		methods: {
			onSubmit() {
				if (this.$session.has('basket-adress')) this.$session.remove('basket-adress')
				this.$session.set('basket-adress', JSON.stringify(this.f_data))
				// Nasleduje emit do basketNavigation a odtiaľ na zmenu view
				this.$root.$emit('basket-nav-update', { id: 3, enabled: true, view_part: 3 })
				
			},
			getFromSession() {
				if (this.$session.has('basket-adress')) {
					this.f_data = JSON.parse(this.$session.get("basket-adress"))
				}
			},
			async testEmail() {
				if (this.$store.state.user == null) { // Testovanie má zmysel len pre neprihláseného
					await MainService.testUserEmail(this.f_data.email)
						.then(response => {
							//console.log(response.data);
							this.test_email = response.data.status == 200 ? 2 : (this.isEmailVAlid ? 1 : 0)
							console.log('testEmail: ' + this.test_email);
						})
						.catch((error) => {
							console.error(error);
						})
				}
			}
		},
		watch: {
			isEmailVAlid() {
				if (this.$store.state.user == null) {
					if (this.isEmailVAlid) this.testEmail() // Testuj len keď je validný email
					else this.test_email = 0
				}
			}
		},
		computed: {
			isFormValid() {
				return Object.keys(this.fields).every(key => this.fields[key].valid);
			},
			isEmailVAlid() {
				return this.fields['basketInputEmail'] !== undefined ? this.fields['basketInputEmail'].valid : false
			}
		},
		created () {
			this.getFromSession()
			if (this.$store.state.user != null) { // Mám prihláseného užívateľa
				this.f_data.name = this.$store.state.user.name
				this.f_data.email = this.$store.state.user.email
				this.test_email = 3
				MainService.getActualUserProfile(this.$store.state.id)
					.then(response => {
						this.f_data.phone = response.data.phone
						this.f_data.street = response.data.street
						this.f_data.town = response.data.town
						this.f_data.psc = response.data.psc
						this.f_data.country = response.data.country
						if (response.data.adress2 != null) this.f_data.adress2 = JSON.parse(response.data.adress2)
						if (response.data.firm != null) this.f_data.firm = JSON.parse(response.data.firm)
					})
					.catch((error) => {
						console.error(error);
					})
				console.log('created: ' + this.test_email);
			}
		},
	}
</script>

<template>
	<div>
		<h1>Fakturačné údaje</h1>
		<form
			@submit="onSubmit()"
		>
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="basketInputEmail">E-mail:</label>
					<input 
						type="email" class="form-control" 
						name="basketInputEmail"
						id="basketInputEmail" aria-describedby="emailHelp" required
						v-validate="'required|email'" 
						data-vv-as="e-mail"
						v-model="f_data.email"
						:disabled="$store.state.user != null"
						:class="$store.state.user != null ? 'disabled' : ''"
						@blur="testEmail"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputEmail') }}</small>
					<small id="emailHelp" class="form-text text-muted">
						E-mailovú adresu nezdieľame s nikým iným!
					</small>
					<small v-if="test_email == 2 && $store.state.user == null" class="form-text alert alert-warning px-2">
						Vašu e-mailovú adresu sme našli v databáze. 
						Prosím, najprv sa prihláste a potom pokračujte v nákupe.
					</small>
				</div>
				<div class="form-group col-md-6" v-if="test_email % 2 == 1">
					<label for="basketInputName">Meno a priezvisko:</label>
					<input 
						type="text" class="form-control" 
						name="basketInputName"
						id="basketInputName" aria-describedby="nameHelp" required
						v-validate="'required|alpha_spaces'" 
						data-vv-as="Meno a priezvisko"
						v-model="f_data.name"
						:disabled="$store.state.user != null"
						:class="$store.state.user != null ? 'disabled' : ''"
					/>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputName') }}</small>
					<small id="nameHelp" class="form-text text-muted">Zadajte, prosím, meno v tvare: Janko Mrkvička.</small>
				</div>
			</div>
			<div v-if="$store.state.user == null && test_email == 1">
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseReg" aria-expanded="false" aria-controls="collapseReg">
					Registrácia
				</button>
				<small id="emailHelp" class="form-text text-muted">Ak sa zaregistrujete, tak pri najbližšom nákupe už nemusíte zadávať údaje nanovo.</small>
			</div>
			<div class="collapse form-row" id="collapseReg" v-if="$store.state.user == null && test_email == 1">
				<div class="form-group col-md-6">
					<label for="password1">Heslo</label>
					<input 
						type="password" 
						class="form-control"
						id="password1"
						name="password1"
						v-validate="'min:5'"
						v-model="f_data.password"
						data-vv-as="heslo"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('password1') }}</small>
					<small id="emailHelp" class="form-text text-muted">
						Zadajte dvakrát rovnaké heslo!
					</small>
				</div>
				<div class="form-group col-md-6">
					<label for="password2">Over heslo</label>
					<input 
						v-model="confirmation"
						type="password" 
						name="password_confirmation" 
						class="form-control"
						id="password2"
						v-validate="{ min:5, confirmed: f_data.password }"
						data-vv-as="overené heslo"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('password_confirmation') }}</small>
				</div>
			</div>
			<div class="form-group" v-if="test_email % 2 == 1">
				<label for="basketInputAdress1">Ulica a číslo domu:</label>
				<input type="text" class="form-control" 
					name="basketInputAdress1"
					id="basketInputAdress1" required
					v-validate="'required'"
					data-vv-as="Adresa"
					v-model="f_data.street"
				>
				<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputAdress1') }}</small>
			</div>
			<div class="form-row" v-if="test_email % 2 == 1">
				<div class="form-group col-md-4">
					<label for="inputCity">Mesto:</label>
					<input type="text" class="form-control" 
						name="inputCity"
						id="inputCity" required
						v-validate="'required'"
						data-vv-as="Mesto"
						v-model="f_data.town"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('inputCity') }}</small>
				</div>
				<div class="form-group col-md-4">
					<label for="inputPsc">PSČ(bez medzery):</label>
					<input type="text" class="form-control" 
						name="inputPsc"
						id="inputPsc" required
						v-validate="'required|numeric|length:5'"
						data-vv-as="PSČ"
						v-model="f_data.psc"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('inputPsc') }}</small>
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Štát:</label>
					<select id="inputState" class="form-control" required
						name="inputState"
						v-validate="'required'"
						data-vv-as="Štát" 
						v-model="f_data.country"
					>
						<option selected disabled>Vyber...</option>
						<option v-for="c in country" :key="c.code" :value="c.code">{{ c.name }}</option>
					</select>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('inputState') }}</small>
				</div>
			</div>
			<div class="form-group" v-if="test_email % 2 == 1">
				<label for="basketInputTel">Telefón(bez medzier):</label>
				<input type="text" class="form-control" 
					name="basketInputTel"
					id="basketInputTel"
					value="+421" required
					v-validate="'required|min:13'"
					data-vv-as="Telefón"
					v-model="f_data.phone"
				>
				<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputTel') }}</small>
			</div>

			<div v-if="test_email % 2 == 1">
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseFirm" aria-expanded="false" aria-controls="collapseFirm">
					Dodávka na firmu
				</button>
			</div>
			<div class="collapse" id="collapseFirm" v-if="test_email % 2 == 1">
				<div class="form-group">
					<label for="inputFirmName">Firma:</label>
					<input type="text" 
						class="form-control" id="inputFirmName"
						v-model="f_data.firm.name"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmIco">IČO:</label>
					<input type="text" 
						class="form-control" id="inputFirmIco"
						v-model="f_data.firm.ico"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmDic">DIČ:</label>
					<input type="text" 
						class="form-control" id="inputFirmDic"
						v-model="f_data.firm.dic"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmIcdph">IČ DPH:</label>
					<input type="text" 
						class="form-control" id="inputFirmIcdph"
						v-model="f_data.firm.icdph"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmAdress">Ulica a číslo domu:</label>
					<input type="text"
						class="form-control" id="inputFirmAdress"
						v-model="f_data.firm.street"
					>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label for="inputFirmCity">Mesto:</label>
						<input type="text" 
							class="form-control" id="inputFirmCity"
							v-model="f_data.firm.town"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputFirmPsc">PSČ(bez medzery):</label>
						<input type="text" 
							class="form-control" id="inputFirmPsc" 
							v-validate="'numeric|length:5'"
							data-vv-as="PSČ firmy"
							v-model="f_data.firm.psc"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputFirmState">Štát:</label>
						<select id="inputFirmState" 
							class="form-control" 
							v-model="f_data.firm.country"
						>
							<option selected disabled>Vyber...</option>
							<option v-for="c in country" :key="c.code" value="c.code">{{ c.name }}</option>
						</select>
					</div>
				</div>
			</div>
			
			<div v-if="test_email % 2 == 1">
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseAdress2" aria-expanded="false" aria-controls="collapseFirm">
					Iná dodacia adresa
				</button>
			</div>
			<div class="collapse" id="collapseAdress2" v-if="test_email % 2 == 1">
				<div class="form-group">
					<label for="inputAdress2">Ulica a číslo domu:</label>
					<input type="text" 
						class="form-control" id="inputAdress2"
						v-model="f_data.adress2.street"
					>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label for="inputCity2">Mesto:</label>
						<input type="text" 
							class="form-control" id="inputCity2"
							v-model="f_data.adress2.town"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputPsc2">PSČ(bez medzery):</label>
						<input type="text" 
							class="form-control" id="inputPsc2"
							v-validate="'numeric|length:5'"
							data-vv-as="PSČ inej dodacej adresy"
							v-model="f_data.adress2.psc"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputState2">Štát:</label>
						<select id="inputState2" 
							class="form-control"
							v-model="f_data.adress2.country"
						>
							<option selected disabled>Vyber...</option>
							<option v-for="c in country" :key="c.code" :value="c.code">{{ c.name }}</option>
						</select>
					</div>
				</div>
			</div>

			<button 
				v-if="test_email != 2"
				type="submit"
				class="btn btn-success mt-2 send-button"
				:class="isFormValid ? '' : 'disabled'"
				:disabled="!isFormValid"
			>
				Pokračuj v objednávke na zhrnutie <i class="ml-1 fa-solid fa-arrow-right"></i>
			</button>


		</form>
		<a :href="$store.state.logInLink" v-if="test_email == 2" class="btn btn-success mt-2">
			{{ $store.state.texts.log_in }}
		</a>
	</div>
</template>

<style scoped>
.send-button:disabled {
	cursor: not-allowed;
	opacity: .5;
}

.form-control:disabled {
	cursor: not-allowed;
	opacity: .5;
}
</style>