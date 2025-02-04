<script setup>
/**
 * Komponenta pre zadanie a editáciu kontaktných údajov.
 * Posledna zmena 04.02.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.6
 * @example https://medium.com/swlh/vue3-using-ref-or-reactive-88d47c8f6944
 */
import { ref, computed, watch, onMounted } from 'vue'
import { BFormInput, BButton, BCard, BCollapse, BFormText } from 'bootstrap-vue-next'
import MainService from '../../services/MainService.js'

import { useMainStore } from '../../store/main'
const store = useMainStore()
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

import AddressForm from './components/AddressForm.vue'
import AddressFirm from './components/AddressFirm.vue'
import AddressOther from './components/AddressOther.vue'

const registrationVisibility = ref(false) // (Ne)zobrazenie časti pre registráciu.

const test_email = ref(0) // 0: nevalidný; 1: validný a nenájdený; 2: validný a nájdený; 3: najdený a prihlasený
const passwordConfirm = ref(null)

const errors = ref({
	email: null,
	name: null,
	password: null,
	passwordConfirm: null,
	basketInputAdress1: null,
	basketInputTel: null,
})

const fieldsValid = ref({
	email: null,
	name: null,
	password: true,
	passwordConfirm: true,
	basketInputAdress1: true,
	isAddressFormValid: false,
	basketInputTel: true,
	isAddressFirmFormValid: false,
	isAddressOtherFormValid: true,
})

const onSubmit = () => {
	// Ulož data do storu a session
	storeB.saveAddress()
	// Nasleduje zmena menu odtiaľ na zmenu view
	storeB.navigationUpdate({ id: 3, enabled: true, view_part: 3 })
}

const testEmail = async () => {
	if (store.user == null) { // Testovanie má zmysel len pre neprihláseného
		await MainService.testUserEmail(storeB.basketAddress.email)
			.then(response => {
				//console.log(response.data); 
				test_email.value = response.data.status == 200 ? 2 : (fieldsValid.value.email ? 1 : 0)
				//console.log('testEmail: ' + test_email.value)
			})
			.catch((error) => {
				test_email.value = 0
				console.error(error)
			})
	} else {
		test_email.value = 3
	}
}

const validateEmail = () => {
  if (!storeB.basketAddress.email) {
    errors.value.email = "Email nemôže byť prázdny."
		fieldsValid.value.email = null
		test_email.value = 0
    return
  }
	
  // Dôkladnejší regex pre overenie emailu.
  const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

  if (!emailRegex.test(storeB.basketAddress.email)) {
    errors.value.email = "Zadaný email nemá platný formát."
		fieldsValid.value.email = false
		test_email.value = 0
  } else {
    errors.value.email = null
		fieldsValid.value.email = true
		test_email.value = 1
		testEmail()
  }
}

watch(() => storeB.basketAddress.email, () => {
	if (store.user == null) {
		validateEmail()
	}
})

const validateName = () => {
	if (!storeB.basketAddress.name) {
    errors.value.name = "Meno músí byť zadané."
		fieldsValid.value.name = null
    return
  }
	const nameRegex = /^[a-zA-Z\u00C0-\u017F]+\s[a-zA-Z\u00C0-\u017F]+$/; // Regex pre meno a priezvisko (s diakritikou)

	if (!nameRegex.test(storeB.basketAddress.name)) {
    errors.value.name = "Zadané meno nemá platný formát."
		fieldsValid.value.name = false
  } else {
		errors.value.name = null
		fieldsValid.value.name = true
  }
}

watch(() => storeB.basketAddress.name, () => {
	if (store.user == null) {
		validateName()
	}
})

const validatePassword = () => {
	if (storeB.basketAddress.password == null) {
		errors.value.password = null
		fieldsValid.value.password = null
	} else if (storeB.basketAddress.password.length < 5) {
		errors.value.password = "Heslo musí mať aspoň 6 znakov!"
		fieldsValid.value.password = false
	} else {
		errors.value.password = null
		fieldsValid.value.password = true
	}
}

watch(() => storeB.basketAddress.password, () => {
	if (store.user == null) {
		if (storeB.basketAddress.password != null && storeB.basketAddress.password.length == 0) storeB.basketAddress.password = null
		validatePassword()
	}
})

const validatePasswordConfirm = () => {
	if (fieldsValid.value.password) {
		if (storeB.basketAddress.password === passwordConfirm.value) {
			errors.value.passwordConfirm = null
			fieldsValid.value.passwordConfirm = true
		} else {
			errors.value.passwordConfirm = "Zadané heslá sa nezhodujú!"
			fieldsValid.value.passwordConfirm = false
		}
	} else {
		errors.value.passwordConfirm = null
		fieldsValid.value.passwordConfirm = null
	}
}

watch(passwordConfirm, () => {
	if (store.user == null) {
		validatePasswordConfirm()
	}
})

watch(registrationVisibility, () => {
	if (store.user == null) {
		if (registrationVisibility.value) {
			fieldsValid.value.password = null
			fieldsValid.value.passwordConfirm = null
		} else {
			fieldsValid.value.password = true
			fieldsValid.value.passwordConfirm = true
		}
		storeB.basketAddress.password = null
		passwordConfirm.value = null
	}
})

const isFormValid = computed(() => {
	return (Object.keys(errors.value).every(key => errors.value[key] == null)) 
		&& (Object.keys(fieldsValid.value).every(key => fieldsValid.value[key]))
})

const getFromSession = () => {
	storeB.getAddressFromSession()
	if (storeB.basketAddress.name != "") validateName()
	if (storeB.basketAddress.email != "") validateEmail()
}

const saveIncomingData = (data) => { 
	fieldsValid.value.isAddressFormValid = data.formValid
	storeB.basketAddress = {...storeB.basketAddress, ...data.values}
	//console.log('saveIncomingData', data)
}

onMounted(() => {
	getFromSession()
	if (store.user != null) { // Mám prihláseného užívateľa
		storeB.basketAddress.name = store.user.name
		storeB.basketAddress.email = store.user.email
		validateEmail()
		validateName()
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
		//console.log('created: ' + test_email.value);
	}
})

</script>

<template>
	<div>
		<h1>Fakturačné údaje</h1>
		<form
			@submit="onSubmit()"
		>
			<div class="row gx-3">
				<div class="col-12 col-md-6">
					<label for="basketInputEmail">E-mail:</label>
					<BFormInput 
						autofocus
						debounce="500" 
						type="email"
						name="basketInputEmail"
						id="basketInputEmail" required
						v-model="storeB.basketAddress.email"
						:disabled="store.user != null"
						:class="store.user != null ? 'disabled' : ''"
						:state="fieldsValid.email"
					/>
					<small class="form-text bg-danger text-white px-2" v-if="errors.email != null">
						{{ errors.email }}
					</small>
					<small class="form-text text-secondary">
						E-mailovú adresu nezdieľame s nikým iným!
					</small>
					<div v-if="test_email === 2 && store.user == null" class=" alert alert-warning px-2">
						Vašu e-mailovú adresu sme našli v databáze. 
						Prosím, najprv sa prihláste a potom pokračujte v nákupe. <br />
						<RouterLink to="login" v-if="test_email === 2" class="btn btn-success mt-2">
							{{ store.texts.log_in }}
						</RouterLink>
					</div>
				</div>
				<div class="col-12 col-md-6" v-if="test_email % 2 == 1">
					<label for="basketInputName">Meno a priezvisko:</label>
					<BFormInput 
						type="text"
						debounce="500" 
						name="basketInputName"
						id="basketInputName" required
						v-model="storeB.basketAddress.name"
						:disabled="store.user != null"
						:class="store.user != null ? 'disabled' : ''"
						:state="fieldsValid.name"
					/>
					<small class="form-text bg-danger text-white px-2" v-if="errors.name != null">{{ errors.name }}</small>
					<small class="form-text text-secondary">Zadajte, prosím, meno v tvare: Janko Mrkvička.</small>
				</div>
			</div>
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
						<div class="row">
							<div class="col-12 col-md-6">
								<label for="password1">Heslo</label>
								<input 
									type="password" 
									id="password1"
									name="password1"
									v-model="storeB.basketAddress.password"
									:state="fieldsValid.password"
								>
								<small class="form-text bg-danger text-white px-2" v-if="errors.password != null">{{ errors.password }}</small>
								<small id="emailHelp" class="form-text text-secondary">
									Zadajte dvakrát rovnaké heslo!
								</small>
							</div>
							<div class="form-group col-md-6">
								<label for="password2">Over heslo</label>
								<input 
									v-model="passwordConfirm"
									type="password" 
									name="password_confirmation"
									id="password2"
									:state="fieldsValid.passwordConfirm"
								>
								<small class="form-text bg-danger text-white px-2" v-if="errors.passwordConfirm != null">{{ errors.passwordConfirm }}</small>
							</div>
						</div>
					</BCard>
				</BCollapse>
			</BCard>

			<BCard bg-variant="secondary" v-if="test_email % 2 == 1">
				<AddressForm 
					:values="{
						street: storeB.basketAddress.street,
						town: storeB.basketAddress.town,
						psc: storeB.basketAddress.psc,
						country: storeB.basketAddress.country
					}"
					:require="{street: true, town: true, psc: true, country: true}"
					:formVisibility="true"
					@saveData="saveIncomingData" 
				/>
				<div class="row">
					<div class="col-12">
						<label for="basketInputTel">Telefón(bez medzier):</label>
						<input type="text" class="form-control" 
							name="basketInputTel"
							id="basketInputTel"
							value="+421" required
							v-validate="'required|min:13'"
							data-vv-as="Telefón"
							v-model="storeB.basketAddress.phone"
						>
						<small class="form-text bg-danger text-white px-2" v-if="errors.basketInputTel != null">{{ errors.basketInputTel }}</small>
					</div>
				</div>
			</BCard>

			<address-firm 
				v-if="test_email % 2 == 1"
				@formFirmValid="(isf) => fieldsValid.isAddressFirmFormValid = isf" 
			/>
			<address-other 
				v-if="test_email % 2 == 1"
				@formOtherValid="(iso) => fieldsValid.isAddressOtherFormValid = iso" 
			/> 

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