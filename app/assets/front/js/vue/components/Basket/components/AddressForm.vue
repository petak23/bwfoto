<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { BInput } from 'bootstrap-vue-next'
import countryCodes from "../../../plugins/country.js"
import { useBasketStore } from '../../../store/basket.js'
const storeB = useBasketStore()

const country = countryCodes

const formVisibility = ref(false)
const wasFirstRun = ref(false)

const props = defineProps({
	values: { // Hodnota daného poľa
		type: Object,
		default: { // Objekt pre null hodnoty
			street: null,
			town: null,
			psc: null,
			country: null
		}
	},
	require: { // Voľba, či je dané pole povinné
		type: Object,
		default: {
			street: false,
			town: false,
			psc: false,
			country: false		
		}
	},
	formVisibility: {
		type: Boolean,
		default: true
	} 
})

const valuesFields = ref({ // Dynamické hodnoty prvkov
		street: null,
		town: null,
		psc: null,
		country: null
	}) 

const requireField = ref({ // Povinné polia
	street: false,
	town: false,
	psc: false,
	country: false		
})  

const errors = ref({ // Chyby pri poliach
	street: null,
	town: null,
	psc: null,
	country: null
})

const fieldsValid = ref({ // Validnosť poľa
	street: null,
	town: null,
	psc: null,
	country: null
}) 

const isFormValid = computed(() => {
	return (Object.keys(errors.value).every(key => errors.value[key] == null)) 
			&& (Object.keys(fieldsValid.value).every(key => fieldsValid.value[key] || fieldsValid.value[key] == null))
})

const emit = defineEmits(['saveData'])

const sendData = (mes) => {
	//let data = {formValid: isFormValid.value, values: valuesFields}
	//console.log(data, mes)
	
	emit('saveData', {formValid: isFormValid.value, values: valuesFields.value})
}

// ------- STREET ------
const validateStreet = () => {
	if (valuesFields.value.street == null) {
		errors.value.street = requireField.value.street ? "Ulica a číslo musia byť zadané!" : null
		fieldsValid.value.street = requireField.value.street ? false : null
	} else {
		errors.value.street = null
		fieldsValid.value.street = true
	}
	return fieldsValid.value.street
}

watch(() => valuesFields.value.street, (newValue, oldValue) => {
	if (newValue != null && newValue.length == 0) newValue = null // Ak je prázdny reťazec zmeň na null
	if (oldValue != null && oldValue.length == 0) oldValue = null // Ak je prázdny reťazec zmeň na null
	valuesFields.value.street = newValue													// Zapíš hodnotu
	validateStreet()																							// Validuj
	if (newValue != oldValue && wasFirstRun.value) {							// Ak sa hodnoty nerovnajú a nie je to prvý raz
		sendData('watch-street')																		// Odošli data
	}
})

// ------- TOWN ------
const validateTown = () => {
	if (valuesFields.value.town == null) {
		errors.value.town = requireField.value.town ? "Mesto musí byť zadané!" : null
		fieldsValid.value.town = requireField.value.town ? false : null
	} else {
		errors.value.town = null
		fieldsValid.value.town = true
	}
	return fieldsValid.value.street
}

watch(() => valuesFields.value.town, (newValue, oldValue) => {
	if (newValue != null && newValue.length == 0) newValue = null // Ak je prázdny reťazec zmeň na null
	if (oldValue != null && oldValue.length == 0) oldValue = null // Ak je prázdny reťazec zmeň na null
	valuesFields.value.town = newValue
	validateTown()
	if (newValue != oldValue && wasFirstRun.value) {
		sendData('watch-town')
	}
})

// ------- PSČ ------
const validatePsc = () => {
	if (valuesFields.value.psc == null) {
		errors.value.psc = requireField.value.psc ? "PSČ musi byť zadané!" : null
		fieldsValid.value.psc = requireField.value.psc ? false : null
	} else if (valuesFields.value.psc.length != 5) {
		errors.value.psc = "PSČ musí mať 5 znakov!"
		fieldsValid.value.psc = false
	} else {
		errors.value.psc = null
		fieldsValid.value.psc = true
	}
	return fieldsValid.value.psc
}

watch(() => valuesFields.value.psc, (newValue, oldValue) => {
	if (newValue != null && newValue.length == 0) newValue = null // Ak je prázdny reťazec zmeň na null
	if (oldValue != null && oldValue.length == 0) oldValue = null // Ak je prázdny reťazec zmeň na null
	valuesFields.value.psc = newValue
	validatePsc()
	if (newValue != oldValue && wasFirstRun.value) {
		sendData('watch-psc')
	}
})

// ------- COUNTRY ------
const validateCountry = () => {
	if (valuesFields.value.country == null) {
		errors.value.country = requireField.value.country ? "Štát musí byť zadaný!" : null
		fieldsValid.value.country = requireField.value.country ? false : null
	} else {
		errors.value.country = null
		fieldsValid.value.country = true
	}
	return fieldsValid.value.country
}

watch(() => valuesFields.value.country, (newValue, oldValue) => {
	if (newValue != null && newValue.length == 0) newValue = null // Ak je prázdny reťazec zmeň na null
	if (oldValue != null && oldValue.length == 0) oldValue = null // Ak je prázdny reťazec zmeň na null
	valuesFields.value.country = newValue
	validateCountry()
	if (newValue != oldValue && wasFirstRun.value) {
		sendData('watch-country')
	}
})

// -------- Koniec validácií ---------

const testForm = () => {
	// Je formulár je viditeľný alebo je sapoň jedno pole povinné?
	if (props.formVisibility || Object.values(requireField.value).some(pole => pole === true)){
		validateStreet()
		validateTown()
		validatePsc()
		validateCountry()
	} else {
		fieldsValid.value = { 
			street: null,
			town: null,
			psc: null,
			country: null
		}
	}
}

watch(() => props.formVisibility, () => {
	testForm()
})

watch(() => props.require, (newValue) => {
	requireField.value = {...requireField.value, ...newValue} // Zlúčenie nastavení
})

watch(() => props.values, (newValue) => {
	valuesFields.value = {...valuesFields.value, ...newValue} // Zlúčenie nastavení
})

onMounted(() => {
	// https://stackoverflow.com/questions/171251/how-can-i-merge-properties-of-two-javascript-objects
	// Zlúčenie nastavení
	requireField.value = {...requireField.value, ...props.require}
	valuesFields.value = {...valuesFields.value, ...props.values}
	formVisibility.value = props.formVisibility
	testForm()
	
	sendData('onMounted')
	wasFirstRun.value = true // Pre blokovanie zbytočných emitov
})
</script>

<template>
	<div class="row">
		<div class="col-12">
			<label for="inputAdress" class="text-light">Ulica a číslo domu:</label>
			<BInput type="text" 
				id="inputAdress"
				v-model="valuesFields.street"
				:state="fieldsValid.street"
				:required="requireField.street"
				debounce="500"
			/>
			<small class="text-bg-danger px-2 py-1" v-if="errors.street != null">{{ errors.street }}</small>
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-md-4">
			<label for="inputCity" class="text-light">Mesto:</label>
			<BInput type="text" 
				id="inputCity"
				v-model="valuesFields.town"
				:state="fieldsValid.town"
				:required="requireField.town"
				debounce="500"
			/>
			<small class="text-bg-danger px-2 py-1" v-if="errors.town != null">{{ errors.town }}</small>
		</div>
		<div class="col-12 col-md-4">
			<label for="inputPsc" class="text-light">PSČ(bez medzery):</label>
			<BInput type="text" 
				id="inputPsc"
				:state="fieldsValid.psc"
				v-model="valuesFields.psc"
				:required="requireField.psc"
				debounce="500"
			/>
			<small class="text-bg-danger px-2 py-1" v-if="errors.psc != null">{{ errors.psc }}</small>
		</div>
		<div class="col-12 col-md-4">
			<label for="inputState2" class="text-light">Štát:</label>
			<select id="inputState2" 
				class="form-control"
				v-model="valuesFields.country"
				:state="fieldsValid.country"
				:required="requireField.country"
			>
				<option selected disabled>Vyber...</option>
				<option v-for="c in country" :key="c.code" :value="c.code">{{ c.name }}</option>
			</select>
		</div>
	</div>
</template>

<style lang="scss" scoped>

</style>