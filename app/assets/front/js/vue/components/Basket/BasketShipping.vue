<script>
/**
 * Komponenta pre zadanie možností o doprave a platbe.
 * Posledna zmena 08.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
export default {
	data() {
		return {
			f_data: {
				shipping: { val: 1, name: "", price: 0 },
				payment: { val: 1, name: "", price: 0 },
				notice: null,
			},
			shipping: [
				{id: 1, name: "Kuriér XYZ", price: 5 },
				{id: 2, name: "Slovenská pošta", price: 3.5 },
				{id: 3, name: "Osobný odber(Spišské Bystré)", price: 0 },
			],
			payment: [
				{id: 1, name: "Prevodom na účet", price: 0 },
				{id: 2, name: "Dobierka", price: 2 }
			]
		}
	},
	methods: {
		onSubmit() {
			if (this.$session.has('basket-shipping')) this.$session.remove('basket-shipping')
			this.f_data.shipping.price = this.shipping[this.f_data.shipping.val - 1].price
			this.f_data.shipping.name = this.shipping[this.f_data.shipping.val - 1].name
			this.f_data.payment.price = this.payment[this.f_data.payment.val - 1].price
			this.f_data.payment.name = this.payment[this.f_data.payment.val - 1].name
			this.$session.set('basket-shipping', JSON.stringify(this.f_data))
			// Nasleduje emit do basketNavigation a odtiaľ na zmenu view
			this.$root.$emit('basket-nav-update', { id: 4, enabled: true, view_part: 4 })
		},
		getFromSession() {
			if (this.$session.has('basket-shipping')) {
				this.f_data = JSON.parse(this.$session.get("basket-shipping"))			
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
					<textarea 
						v-model="f_data.notice"
						
						data-vv-as="Telefón"
					>
					</textarea>
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