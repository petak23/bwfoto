<script setup>
/**
 * Komponenta pre zadanie možností o doprave a platbe.
 * Posledna zmena 04.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */

import { ref, onMounted, computed } from 'vue'
import Session from "../../plugins/session.js"
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

const f_data = ref({
	shipping: { val: 1, name: "", price: 0 },
	payment: { val: 1, name: "", price: 0 },
	notice: null,
})
const shipping = ref([
	{id: 1, name: "Kuriér XYZ", price: 5 },
	{id: 2, name: "Slovenská pošta", price: 3.5 },
	{id: 3, name: "Osobný odber(Spišské Bystré)", price: 0 },
])
const	payment = ref([
	{id: 1, name: "Prevodom na účet", price: 0 },
	{id: 2, name: "Dobierka", price: 2 }
])

const onSubmit = () => {
	if (Session.getStorage('basket-shipping')) Session.clearStorage('basket-shipping')
	f_data.value.shipping.price = shipping.value[f_data.value.shipping.val - 1].price
	f_data.value.shipping.name = shipping.value[f_data.value.shipping.val - 1].name
	f_data.value.payment.price = payment.value[f_data.value.payment.val - 1].price
	f_data.value.payment.name = payment.value[f_data.value.payment.val - 1].name
	Session.saveStorage('basket-shipping', JSON.stringify(f_data.value))
	// Nasleduje zmena menu a odtiaľ na zmenu view
	storeB.navigationUpdate({ id: 4, enabled: true, view_part: 4 })
}

const getFromSession = () => {
	if (Session.getStorage('basket-shipping')) {
		f_data.value = JSON.parse(Session.getStorage("basket-shipping"))			
	}
}

const isFormValid = computed(() => {
	return Object.keys(fields).every(key => fields[key].valid);
})

onMounted(() => {
	getFromSession()
})
</script>

<template>
	<div>
		<h1>Doprava a platba</h1>
		<form
			@submit="onSubmit()"
		>
			<div class="form-row">
				<div class="form-group col-md-6">
					<h6>Spôsob doručenia:</h6>
					<div 
						class="form-check"
						v-for="s in shipping"
						:key="s.id"
					>
						<input 
							class="form-check-input" type="radio" 
							name="shippingRadios" :id="'shippingRadios'+s.id"
							:value="s.id" 
							v-model="f_data.shipping.val"
						>
						<label class="form-check-label" :for="'shippingRadios'+s.id">
							{{ s.name }} (+ {{ s.price }} €)
						</label>
					</div>
				</div>
				<div class="form-group col-md-6">
					<h6>Platba:</h6>
					<div 
						class="form-check"
						v-for="p in payment"
						:key="p.id"
					>
						<input 
							class="form-check-input" type="radio" 
							name="paymentRadios" :id="'paymentRadios'+p.id"
							:value="p.id" 
							v-model="f_data.payment.val"
						>
						<label class="form-check-label" :for="'paymentRadios'+p.id">
							{{ p.name }} (+ {{ p.price }} €)
						</label>
					</div>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-6">
					<h6>Poznámka:</h6><!-- v-validate="{ regex: /<[^>]*>/g }" -->
					<textarea v-model="f_data.notice"></textarea>
				</div>
			</div>
		
			<button 
				type="submit"
				class="btn btn-success mt-2 send-button"
				:class="isFormValid ? '' : 'disabled'"
				:disabled="!isFormValid"
			>
				Pokračuj na zhrnutie <i class="ml-1 fa-solid fa-arrow-right"></i>
			</button>
		</form>
	</div>
</template>

<style scoped>
	textarea {
		border: #999;
		background-color: #ccc;
		width: 100%;
	}
</style>