<script setup>
/**
 * Komponenta pre zadanie a editáciu kontaktných údajov.
 * Posledna zmena 12.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 * @example https://medium.com/swlh/vue3-using-ref-or-reactive-88d47c8f6944
 */

import { ref, computed, watch, onMounted } from 'vue';

import countryCodes from "../../plugins/country.js"
const country = countryCodes

import MainService from '../../services/MainService.js'

import { useMainStore } from '../../store/main'
const store = useMainStore()
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

import { BFormInput } from 'bootstrap-vue-next'

// Reactive state
const confirmation = ref("")

const test_email = ref(0) // 0: nevalidný; 1: validný a nenájdený; 2: validný a nájdený; 3: najdený a prihlasený

import { Form, useForm, useIsFormValid} from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as Yup from 'yup'

const schema = toTypedSchema(Yup.object().shape({
	basketEmail: Yup.string().email('Email nie je zadaný správne!').required('Email musíte zadať!'),//.label('Emailová adresa'),
	//basketInputName: Yup.string().required('Heslo musí byť zadané!').min(5, 'Heslo musí mať aspoň 5 zankov'),
}))

const { values, defineField, errors } = useForm({
  validationSchema: schema,
});

const [basketEmail, basketEmailAttrs, basketEmailMeta] = defineField('basketEmail')

const { meta: metae } = useField('basketEmail')
const { meta: metap } = useField('password')


//const [password] = defineField('password');


const emailRules = Yup
  .string()
  .required('Email je povinný')
  .email('Zadajte platný email');

const nameRules = Yup
  .string()
  .required('Meno a priezvisko sú povinné')
  .matches(/^\S+\s+\S+$/, 'Meno musí obsahovať meno a priezvisko oddelené medzerou');


const error_message = ref(null)

const isAddressFormValid = useIsFormValid()

const onSubmit = (values) => {
	console.log(values);
	
	// Ulož data do storu a session
	storeB.saveAddress()
	// Nasleduje zmena menu odtiaľ na zmenu view
	storeB.navigationUpdate({ id: 3, enabled: true, view_part: 3 })
}

const getFromSession = () => {
	storeB.getAddressFromSession()
}

const testEmail = async () => {
	if (store.user == null) { // Testovanie má zmysel len pre neprihláseného
		await MainService.testUserEmail(storeB.basketAddress.email)
			.then(response => {
				//console.log(response.data); // TODO isEmailVAlid...
				test_email.value = response.data.status == 200 ? 2 : 0//(metae.valid ? 1 : 0)
				console.log('testEmail: ' + test_email.value);
			})
			.catch((error) => {
				console.error(error);
			})
	}
}

// TODO ...
const isFormValid = computed(() => {
	return false//Object.keys(fields).every(key => fields[key].valid);
})

// TODO
watch(() => metae.valid, () => {
	if (store.user == null) {
		if (metae.valid) testEmail() // Testuj len keď je validný email
		else test_email.value = 0
	}
})

onMounted(() => {
	getFromSession()
	if (store.user != null) { // Mám prihláseného užívateľa
		storeB.basketAddress.name = store.user.name
		storeB.basketAddress.email = store.user.email
		test_email.value = 3
		MainService.getActualUserProfile(store.id)
			.then(response => {
				storeB.basketAddress.phone = response.data.phone
				storeB.basketAddress.street = response.data.street
				storeB.basketAddress.town = response.data.town
				storeB.basketAddress.psc = response.data.psc
				storeB.basketAddress.country = response.data.country
				if (response.data.adress2 != null) storeB.basketAddress.adress2 = JSON.parse(response.data.adress2)
				if (response.data.firm != null) storeB.basketAddress.firm = JSON.parse(response.data.firm)
			})
			.catch((error) => {
				console.error(error);
			})
		console.log('created: ' + test_email.value);
	}
})

</script>

<template>
	<div>
		<h1>Fakturačné údaje</h1>
		<Form
			@submit="onSubmit"
			:validation-schema="schema"
		>
			<div class="form-row">
				<div class="form-group col-md-6">
					<!--<div class="form-row">
						<div class="form-group">
							<BFormInput 
								id="input-email"
								v-model="storeB.basketAddress.email" 
								aria-describedby="email-help"
								:state="!metae.dirty ? null : metae.valid"
								:disabled="store.user != null"
								:class="store.user != null ? 'disabled' : ''"
								@blur="testEmail" 
								type="email"
								title="E-mail:"
								placeholder="Zadaj email"
								autofocus
							/>
							<small id="email-help" class="text-danger">{{ errors.email }}</small>
							
							<small class="form-text bg-danger text-white px-2">{{ errors.email }}</small>
							<small id="emailHelp" class="form-text text-white">
								E-mailovú adresu nezdieľame s nikým iným!
							</small>
							<small v-if="test_email == 2 && store.user == null" class="form-text alert alert-warning px-2">
								Vašu e-mailovú adresu sme našli v databáze. 
								Prosím, najprv sa prihláste a potom pokračujte v nákupe.
							</small>
						</div>
					</div>-->
					
					<!--label for="email">E-mail:</label-->
				<!--
					<Field
						name="email"
						type="email"
						v-model="storeB.basketAddress.email"
						label="E-mail:"
						:rules="emailRules"
						placeholder="Zadajte váš1 email"
						:disabled="store.user != null"
						class="form-control"
						:class="store.user != null ? 'disabled' : ''"
						@blur="testEmail"
						autofocus
					/>
					<ErrorMessage name="email" class="form-text bg-danger text-white px-2" />
				-->

					<label for="basketInputEmail">E-mail:</label>
					<input 
						type="email" class="form-control" 
						name="basketInputEmail"
						id="basketInputEmail" aria-describedby="emailHelp" required 
						v-bind="basketEmailAttrs"
						v-model="storeB.basketAddress.email"
						:disabled="store.user != null"
						:class="store.user != null ? 'disabled' : ''"
						@blur="testEmail"
						autofocus
					>
					<small class="form-text bg-danger text-white px-2"><pre>{{ errors }}</pre></small>
					<small id="emailHelp" class="form-text text-light">
						E-mailovú adresu nezdieľame s nikým iným!
					</small>
					<small v-if="test_email == 2 && store.user == null" class="form-text alert alert-warning px-2">
						Vašu e-mailovú adresu sme našli v databáze. 
						Prosím, najprv sa prihláste a potom pokračujte v nákupe.
					</small>
				</div>


				<!--<div class="form-group col-md-6" v-if="test_email % 2 == 1">
					<label for="basketInputName">Meno a priezvisko:</label>
					<input 
						type="text" class="form-control" 
						name="basketInputName"
						id="basketInputName" aria-describedby="nameHelp" required
						v-validate="'required|alpha_spaces'" 
						data-vv-as="Meno a priezvisko"
						v-model="storeB.basketAddress.name"
						:disabled="store.user != null"
						:class="store.user != null ? 'disabled' : ''"
					/>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('basketInputName') }}</small>
					<small id="nameHelp" class="form-text text-muted">Zadajte, prosím, meno v tvare: Janko Mrkvička.</small>
				</div>
			</div>
			<div v-if="store.user == null && test_email == 1">
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseReg" aria-expanded="false" aria-controls="collapseReg">
					Registrácia
				</button>
				<small id="emailHelp" class="form-text text-muted">Ak sa zaregistrujete, tak pri najbližšom nákupe už nemusíte zadávať údaje nanovo.</small>
			</div>
			<div class="collapse form-row" id="collapseReg" v-if="store.user == null && test_email == 1">
				<div class="form-group col-md-6">
					<label for="password1">Heslo</label>
					<input 
						type="password" 
						class="form-control"
						id="password1"
						name="password1"
						v-validate="'min:5'"
						v-model="storeB.basketAddress.password"
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
						v-validate="{ min:5, confirmed: storeB.basketAddress.password }"
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
					v-model="storeB.basketAddress.street"
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
						v-model="storeB.basketAddress.town"
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
						v-model="storeB.basketAddress.psc"
					>
					<small class="form-text bg-danger text-white px-2">{{ errors.first('inputPsc') }}</small>
				</div>
				<div class="form-group col-md-4">
					<label for="inputState">Štát:</label>
					<select id="inputState" class="form-control" required
						name="inputState"
						v-validate="'required'"
						data-vv-as="Štát" 
						v-model="storeB.basketAddress.country"
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
					v-model="storeB.basketAddress.phone"
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
						v-model="storeB.basketAddress.firm.name"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmIco">IČO:</label>
					<input type="text" 
						class="form-control" id="inputFirmIco"
						v-model="storeB.basketAddress.firm.ico"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmDic">DIČ:</label>
					<input type="text" 
						class="form-control" id="inputFirmDic"
						v-model="storeB.basketAddress.firm.dic"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmIcdph">IČ DPH:</label>
					<input type="text" 
						class="form-control" id="inputFirmIcdph"
						v-model="storeB.basketAddress.firm.icdph"
					>
				</div>
				<div class="form-group">
					<label for="inputFirmAdress">Ulica a číslo domu:</label>
					<input type="text"
						class="form-control" id="inputFirmAdress"
						v-model="storeB.basketAddress.firm.street"
					>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label for="inputFirmCity">Mesto:</label>
						<input type="text" 
							class="form-control" id="inputFirmCity"
							v-model="storeB.basketAddress.firm.town"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputFirmPsc">PSČ(bez medzery):</label>
						<input type="text" 
							class="form-control" id="inputFirmPsc" 
							v-validate="'numeric|length:5'"
							data-vv-as="PSČ firmy"
							v-model="storeB.basketAddress.firm.psc"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputFirmState">Štát:</label>
						<select id="inputFirmState" 
							class="form-control" 
							v-model="storeB.basketAddress.firm.country"
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
						v-model="storeB.basketAddress.adress2.street"
					>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label for="inputCity2">Mesto:</label>
						<input type="text" 
							class="form-control" id="inputCity2"
							v-model="storeB.basketAddress.adress2.town"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputPsc2">PSČ(bez medzery):</label>
						<input type="text" 
							class="form-control" id="inputPsc2"
							v-validate="'numeric|length:5'"
							data-vv-as="PSČ inej dodacej adresy"
							v-model="storeB.basketAddress.adress2.psc"
						>
					</div>
					<div class="form-group col-md-4">
						<label for="inputState2">Štát:</label>
						<select id="inputState2" 
							class="form-control"
							v-model="storeB.basketAddress.adress2.country"
						>
							<option selected disabled>Vyber...</option>
							<option v-for="c in country" :key="c.code" :value="c.code">{{ c.name }}</option>
						</select>
					</div>
				</div>-->
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


		</Form>
		<a :href="store.logInLink" v-if="test_email == 2" class="btn btn-success mt-2">
			{{ store.texts.log_in }}
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