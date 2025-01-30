<script setup>
import { ref, computed, watch } from 'vue';
import { BButton, BCard, BCollapse } from 'bootstrap-vue-next'
import AddressForm from './AddressForm.vue';
import { useBasketStore } from '../../../store/basket.js'
const storeB = useBasketStore()

const formVisibility = ref(false)

const isFormValid = ref(false)

const emit = defineEmits(['formOtherValid'])

watch(() => storeB.basketAddress.adress2, () => {
	console.log(isFormValid)
	
	emit('formOtherValid', isFormValid)
})

const saveIncomingData = (data) => { 
	isFormValid = data.formValid
	storeB.basketAddress.adress2 = {...storeB.basketAddress.adress2, ...data.values}
	console.log(data)
	
	emit('formOtherValid', isFormValid)
}

</script>

<template>
	<BCard bg-variant="secondary">
		<BButton
			:class="formVisibility ? null : 'collapsed'"
			:aria-expanded="formVisibility ? 'true' : 'false'"
			aria-controls="collapse-other"
			@click="formVisibility = !formVisibility"
			variant="primary"
		>
			Iná dodacia adresa
		</BButton>
		
		<BCollapse id="collapse-other" v-model="formVisibility" class="mt-2">
			<BCard bg-variant="secondary">
				<AddressForm 
					:values="{
						street: storeB.basketAddress.adress2.street,
						town: storeB.basketAddress.adress2.town,
						psc: storeB.basketAddress.adress2.psc,
						country: storeB.basketAddress.adress2.country
					}"
					:require="{street: true}"
					:formVisibility="formVisibility"
					@saveData="saveIncomingData" 
				/>

			</BCard>
		</BCollapse>
	</BCard>
</template>

<style lang="scss" scoped>

</style>