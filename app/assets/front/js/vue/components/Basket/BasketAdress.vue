<script>
/**
 * Komponenta pre zadanie a editáciu kontaktných údajov.
 * Posledna zmena 08.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
	import countryCodes from "../../plugins/country.js"

	export default {
		data() {
			return {
				country: countryCodes,
				f_data: {
					name: "",
					email: "",
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
				}
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
		},
		computed: {
			isFormValid() {
				return Object.keys(this.fields).every(key => this.fields[key].valid);
			}
		},
		created () {
			this.getFromSession()
		},
	}
</script>

<template>
	<div>
		<h1>Fakturačné údaje <small>V: {{ isFormValid }}</small></h1>
		<form
			@submit="onSubmit()"
		>
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="basketInputName">Meno a priezvisko:</label>
					<input 
						type="text" class="form-control" 
						name="basketInputName"
						id="basketInputName" aria-describedby="nameHelp" required
						v-validate="'required|alpha_spaces'" 
						data-vv-as="Meno a priezvisko"
						v-model="f_data.name"
					/>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputName') }}</small>
					<small id="nameHelp" class="form-text text-muted">Zadajte, prosím, meno v tvare: Janko Mrkvička.</small>
				</div>
				<div class="form-group col-md-6">
					<label for="basketInputEmail">E-mail:</label>
					<input 
						type="email" class="form-control" 
						name="basketInputEmail"
						id="basketInputEmail" aria-describedby="emailHelp" required
						v-validate="'required|email'" 
						data-vv-as="e-mail"
						v-model="f_data.email"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputEmail') }}</small>
					<small id="emailHelp" class="form-text text-muted">
						E-mailovú adresu nezdieľame s nikým iným!
					</small>
				</div>
			</div>
			<div class="form-group">
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
			<div class="form-row">
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
			<div class="form-group">
				<label for="basketInputTel">Telefón(bez medzier):</label>
				<input type="text" class="form-control" 
					name="basketInputTel"
					id="basketInputTel"
					value="+421" required
					v-validate="'required|min:13'"
					data-vv-as="Telefon"
					v-model="f_data.phone"
				>
				<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputTel') }}</small>
			</div>

			<div>
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseFirm" aria-expanded="false" aria-controls="collapseFirm">
					Dodávka na firmu
				</button>
			</div>
			<div class="collapse" id="collapseFirm">
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
			
			<div>
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseAdress2" aria-expanded="false" aria-controls="collapseFirm">
					Iná dodacia adresa
				</button>
			</div>
			<div class="collapse" id="collapseAdress2">
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
							<option v-for="c in country" :key="c.code" value="c.code">{{ c.name }}</option>
						</select>
					</div>
				</div>
			</div>

			<button 
				type="submit"
				class="btn btn-success mt-2 send-button"
				:class="isFormValid ? '' : 'disabled'"
				:disabled="!isFormValid"
			>
				Pokračuj v objednávke na zhrnutie <i class="ml-1 fa-solid fa-arrow-right"></i>
			</button>


		</form>
	</div>
</template>

<style scoped>
.send-button:disabled {
  cursor: not-allowed;
	opacity: .5;
}
</style>