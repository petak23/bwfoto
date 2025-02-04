<script setup>
import { ref, watch } from 'vue';
import { BFormInput, BButton, BCard, BCollapse, BFormText } from 'bootstrap-vue-next'
import { useBasketStore } from '../../../store/basket.js'
import AddressForm from './AddressForm.vue';
const storeB = useBasketStore()

const formVisibility = ref(false)

const isFormValid = ref(false)

const emit = defineEmits(['formFirmValid'])

watch(() => storeB.basketAddress.firm, () => {
	//console.log(isFormValid)
	
	emit('formFirmValid', isFormValid.value)
})

const saveIncomingData = (data) => { 
	isFormValid.value = data.formValid
	storeB.basketAddress.firm = {...storeB.basketAddress.firm, ...data.values}
	//console.log(data)
	
	emit('formFirmValid', isFormValid.value)
}

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
				<AddressForm 
					:values="{
						street: storeB.basketAddress.firm.street,
						town: storeB.basketAddress.firm.town,
						psc: storeB.basketAddress.firm.psc,
						country: storeB.basketAddress.firm.country
					}"
					:formVisibility="formVisibility"
					@saveData="saveIncomingData" 
				/>
			</BCard>
		</BCollapse>
	</BCard>



	
</template>

<style lang="scss" scoped>

</style>