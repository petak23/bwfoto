<script setup>
import { ref, computed, watch } from 'vue';
import { BFormInput, BButton, BCard, BCollapse, BFormText } from 'bootstrap-vue-next'
import { useBasketStore } from '../../../store/basket.js'
const storeB = useBasketStore()

import countryCodes from "../../../plugins/country.js"
const country = countryCodes

const formVisibility = ref(false)

const errors = ref({
	psc: null,
})

const fieldsValid = ref({
	psc: true,
})

const isFormValid = computed(() => {
	return (Object.keys(errors.value).every(key => errors.value[key] == null)) 
			&& (Object.keys(fieldsValid.value).every(key => fieldsValid.value[key]))
})

const emit = defineEmits(['isFormValid'])

const validatePsc = () => {
	if (storeB.basketAddress.firm.psc == null) {
		errors.value.psc = null
		fieldsValid.value.psc = null
	} else if (storeB.basketAddress.firm.psc.length != 5) {
		errors.value.password = "PSČ musí mať 5 znakov!"
		fieldsValid.value.password = false
	} else {
		errors.value.password = null
		fieldsValid.value.password = true
	}
}

// TODO skús watch na celý firm ???
watch(() => storeB.basketAddress.firm.psc, () => {
	if (storeB.basketAddress.firm.psc != null && storeB.basketAddress.firm.psc == 0) storeB.basketAddress.firm.psc = null
	validatePsc()
	emit('isFormValid', isFormValid)
})

watch(formVisibility, () => {
	if (formVisibility.value) {
		validatePsc()
	} else {
		fieldsValid.value.psc = true
	}
})

</script>

<template>
	<BCard bg-variant="secondary">
		<BButton
			:class="formVisibility ? null : 'collapsed'"
			:aria-expanded="formVisibility ? 'true' : 'false'"
			aria-controls="collapse-firm"
			@click="formVisibility = !formVisibility"
			variant="primary"
		>
			Dodávka na firmu
		</BButton>
		
		<BCollapse id="collapse-firm" v-model="formVisibility" class="mt-2">
			<BCard bg-variant="secondary">
				<div class="row">
					<div class="col-12">
						<label for="inputFirmName">Firma:</label>
						<input type="text" 
							class="form-control" id="inputFirmName"
							v-model="storeB.basketAddress.firm.name"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<label for="inputFirmIco">IČO:</label>
						<input type="text" 
							class="form-control" id="inputFirmIco"
							v-model="storeB.basketAddress.firm.ico"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<label for="inputFirmDic">DIČ:</label>
						<input type="text" 
							class="form-control" id="inputFirmDic"
							v-model="storeB.basketAddress.firm.dic"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<label for="inputFirmIcdph">IČ DPH:</label>
						<input type="text" 
							class="form-control" id="inputFirmIcdph"
							v-model="storeB.basketAddress.firm.icdph"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<label for="inputFirmAdress">Ulica a číslo:</label>
						<input type="text"
							class="form-control" id="inputFirmAdress"
							v-model="storeB.basketAddress.firm.street"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-4">
						<label for="inputFirmCity">Mesto:</label>
						<input type="text" 
							class="form-control" id="inputFirmCity"
							v-model="storeB.basketAddress.firm.town"
						>
					</div>
					<div class="col-12 col-md-4">
						<label for="inputFirmPsc">PSČ(bez medzery):</label>
						<input type="text" 
							class="form-control" id="inputFirmPsc" 
							:state="fieldsValid.psc"
							v-model="storeB.basketAddress.firm.psc"
						>
						<small class="form-text bg-danger text-white px-2" v-if="errors.psc != null">{{ errors.psc }}</small>
					</div>
					<div class="col-12 col-md-4">
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
			</BCard>
		</BCollapse>
	</BCard>



	
</template>

<style lang="scss" scoped>

</style>