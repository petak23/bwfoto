<script setup>
/**
 * Komponenta pre zadanie a editáciu kontaktných údajov.
 * Posledna zmena 13.01.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.6
 * @example https://medium.com/swlh/vue3-using-ref-or-reactive-88d47c8f6944
 */
import { ref, computed, onMounted, watch } from 'vue'
import { useForm, useField, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { debounce } from 'lodash'
import { RouterLink } from "vue-router"
import { BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BCard, BCollapse } from 'bootstrap-vue-next'
import MainService from '../../services/MainService.js'

import { useMainStore } from '../../store/main'
const store = useMainStore()
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

import countryCodes from "../../plugins/country.js"
const country = countryCodes

const isLoading = ref(false)
const apiError = ref(false) // Premenná pre chyby API
const emailState = ref(null)
const nameState = ref(null)
const oldValue = ref("")
const password1State = ref(null)
const password2State = ref(null)

const registrationVisibility = ref(false)

const test_email = ref(0) // 0: nevalidný; 1: validný a nenájdený; 2: validný a nájdený; 3: najdený a prihlasený

const validationSchema = yup.object().shape({
	email: yup.string().email('Zadajte platný email').required('Email je povinný'),
	basketInputName: yup.string().required('Meno a priezvisko sú povinné')
		.matches(/^\S+\s+\S+$/, 'Meno musí obsahovať meno a priezvisko oddelené medzerou'),
	password: yup.string().min(6, "Heslo musí mať aspoň 6 znakov!"),
});

const { handleSubmit, meta, errors, values } = useForm({
	validationSchema,
	initialValues: {
		email: storeB.basketEmail,
		basketInputName: storeB.basketInputName,
		password: null,
		passwordConfirm: yup
      .string()
      .required()
      .min(6)
      .oneOf([yup.ref('password')], "Oba zadané heslá sa musia zhodovať!"),
	}
})

const { value: email, errorMessage, meta: metaE } = useField('email')
const { value: basketInputName, errorMessage: nameError, meta: metaM } = useField('basketInputName');
const { value: password, errorMessage: psswdError } = useField('password1')

const validateEmailExistence = async () => {
	if (email.value.length == 0) {
		test_email.value = 0
		emailState.value = null
	} else if (!metaE.valid) { // Email nie je validný
		test_email.value = 0
		emailState.value = false
	} else {								// Email JE validný
		emailState.value = true
		if (oldValue.value != email.value) { // Došlo k zmene v zadaní
			isLoading.value = true
			apiError.value = false // Resetovanie chyby pred novým pokus
			await MainService.testUserEmail(email.value)
				.then(response => {
					console.log(response.data.status, store.user)
					const sta = response.data.status
					test_email.value = (sta === 200) ? 
						(store.user == null ? 2 : 3) : 1
					oldValue.value = email.value	
				})
				.catch((error) => {
					console.error('Chyba pri kontrole emailu:', error)
					apiError.value = true // Nastavenie chyby
					emailState.value = false
					test_email.value = 0;
				})
				isLoading.value = false	
		}
	}
}

const validateNameExistence = async () => {
	console.log(basketInputName.value)
	if (basketInputName.value.length == 0) { 
		nameState = null
	} else if (!metaM.valid) { // Meno nie je validné
		nameState.value = false
	} else {								// Meno JE validné
		nameState.value = true
	}
}

const debouncedValidateEmailExistence = debounce(validateEmailExistence, 500);
const debouncedValidateNameExistence = debounce(validateNameExistence, 500);

const isFormValid = computed(() => meta.value.valid);

onMounted(() => {
	if (storeB.basketEmail) {
			email.value = storeB.basketEmail
	}
	storeB.getAddressFromSession()
	if (store.user != null) { // Mám prihláseného užívateľa
		storeB.basketAddress.name = store.user.name
		storeB.basketAddress.email = store.user.email
		email.value = store.user.email
		emailState.value = true
		test_email.value = 3
		basketInputName.value = store.user.name
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

const onSubmit = () => {
	storeB.basketEmail = values.email
	storeB.basketAddress.name = values.basketInputName
	console.log('Odosielané dáta:', values);
	// Ulož data do storu a session
	storeB.saveAddress()
	// Nasleduje zmena menu odtiaľ na zmenu view
	storeB.navigationUpdate({ id: 3, enabled: true, view_part: 3 })
}

const emailValidFeedback = computed(() => {
	return isFormValid && test_email.value === 1 ? "Email je OK." : null
})
const emailInvalidFeedback = computed(() => {
	return apiError.value ? "Vyskytla sa chyba pri overovaní emailu." :
		(errorMessage.value ? errorMessage.value : null)
})
const nameInvalidFeedback = computed(() => {
	return nameError.value ? nameError.value : null
})
const emailCanUse = computed(() => {
	return test_email.value % 2 === 1
})
</script>

<template>
	<h1>Fakturačné údaje</h1>
	<BForm @submit.prevent="onSubmit">
		<BFormGroup 
			label="Email:"  
			:state="emailState"
			description="E-mailovú adresu nezdieľame s nikým iným!"
			:valid-feedback="emailValidFeedback"
			:invalid-feedback="emailInvalidFeedback"
			label-cols-sm="4"
			label-cols-lg="3"
			content-cols-sm
			content-cols-lg="7"
		>
			<template #description>
				<small class="text-light" v-if="store.user == null">
					E-mailovú adresu nezdieľame s nikým iným!
				</small>
			</template>
			<BFormInput
				id="basket-email"
				class="form-control"
				v-model="email"
				type="email"
				placeholder="Zadajte email"
				@blur="validateEmailExistence"
				@input="debouncedValidateEmailExistence"
				:state="emailState"
				:disabled="store.user != null"
				:class="store.user != null ? 'disabled' : ''"
			/>
			<div v-if="isLoading" class="animate-pulse">Kontrolujem email...</div>
			<div v-if="test_email === 2" class="form-text alert alert-warning px-2">
				Vašu e-mailovú adresu sme našli v databáze. 
				Prosím, najprv sa prihláste a potom pokračujte v nákupe. <br />
				<RouterLink to="login" v-if="test_email === 2" class="btn btn-success mt-2">
					{{ store.texts.log_in }}
				</RouterLink>
			</div>
		</BFormGroup>

		<BFormGroup 
			v-if="emailCanUse"
			label="Meno a priezvisko:"  
			:state="nameState"
			:invalid-feedback="nameInvalidFeedback"
			label-cols-sm="4"
			label-cols-lg="3"
			content-cols-sm
			content-cols-lg="7"
		>
			<template #description>
				<small class="text-light" v-if="store.user == null">
					Zadajte, prosím, meno v tvare: Janko Mrkvička.
				</small>
			</template>
			<BFormInput
				id="basketInputName"
				placeholder="Zadajte meno a priezvisko"
				:state="nameState"
				v-model="basketInputName"
				:disabled="store.user != null"
				:class="store.user != null ? 'disabled' : ''"
				@blur="validateNameExistence"
				@input="debouncedValidateNameExistence"
			/>
		</BFormGroup>
		<!--<div>Errors: {{ errors }}</div>
		<pre>Values: {{ values }}</pre>-->

		<BCard v-if="store.user == null && test_email == 1" bg-variant="secondary">
			<BButton
				:class="registrationVisibility ? null : 'collapsed'"
				:aria-expanded="registrationVisibility ? 'true' : 'false'"
				aria-controls="collapse-registration"
				@click="registrationVisibility = !registrationVisibility"
				variant="primary"
			>
				Registrácia
			</BButton>
			<small id="emailHelp" class="ms-2 text-white">
				Ak sa zaregistrujete, tak pri najbližšom nákupe už nemusíte zadávať údaje nanovo.
			</small>
			<BCollapse id="collapse-registration" v-model="registrationVisibility" class="mt-2">
				<BCard bg-variant="secondary">
					<BFormGroup 
						label="Heslo"
						label-cols-sm="4"
						label-cols-lg="3"
						content-cols-sm
						content-cols-lg="7"
					>
						<template #description>
							<small class="text-light">
								Zadajte dvakrát rovnaké heslo!
							</small>
						</template>
						<BFormInput id="input-id" />
					</BFormGroup>

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
						<!--<small class="form-text bg-danger text-light px-2">{{ /*errors.first('password_confirmation')*/ }}</small>-->
					</div>
				
				</BCard>
			</BCollapse>
		</BCard>


		<!--<div v-if="store.user == null && test_email == 1">
			<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseReg" aria-expanded="false" aria-controls="collapseReg">
				Registrácia
			</button><br />
			
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
				<small class="form-text bg-danger text-white px-2">{{ /*errors.first('password1')*/ }}</small>
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
				<small class="form-text bg-danger text-white px-2">{{ /*errors.first('password_confirmation')*/ }}</small>
			</div>
		</div>-->

		<!--<div class="form-group" v-if="emailCanUse">
			<label for="basketInputAdress1">Ulica a číslo domu:</label>
			<input type="text" class="form-control" 
				name="basketInputAdress1"
				id="basketInputAdress1" required
				v-validate="'required'"
				data-vv-as="Adresa"
				v-model="storeB.basketAddress.street"
			>
			<small class="form-text bg-danger text-white px-2">{{ /*errors.first('basketInputAdress1')*/ }}</small>
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
				<small class="form-text bg-danger text-white px-2">{{ /*errors.first('inputCity')*/ }}</small>
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
				<small class="form-text bg-danger text-white px-2">{{ /*errors.first('inputPsc')*/ }}</small>
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
				<small class="form-text bg-danger text-white px-2">{{ /*errors.first('inputState')*/ }}</small>
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
				<small class="form-text bg-danger text-white px-2">{{ /*errors.first('basketInputTel')*/ }}</small>
			</div>-->

			<div v-if="emailCanUse">
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseFirm" aria-expanded="false" aria-controls="collapseFirm">
					Dodávka na firmu
				</button>
			</div>
			<div class="collapse" id="collapseFirm" v-if="emailCanUse">
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
			
			<div v-if="emailCanUse">
				<button class="btn btn-primary my-2" type="button" data-toggle="collapse" data-target="#collapseAdress2" aria-expanded="false" aria-controls="collapseFirm">
					Iná dodacia adresa
				</button>
			</div>
			<div class="collapse" id="collapseAdress2" v-if="emailCanUse">
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
				</div>
			</div>


		<BButton 
			type="submit" :disabled="!isFormValid" 
			variant="success" class="mt-2 send-button"
			:class="isFormValid ? '' : 'disabled'"
		>
			Pokračuj v objednávke na zhrnutie <i class="ml-1 fa-solid fa-arrow-right"></i>
		</BButton>
	</BForm>
	
</template>