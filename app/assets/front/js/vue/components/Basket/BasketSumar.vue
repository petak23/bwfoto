<script>
/**
 * Komponenta pre vypísanie sumárnych údajov o nákupe.
 * Posledna zmena 20.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
import MainService from '../../services/MainService.js'

export default {
	data() {
		return {
			product: [],
			adress: null,
			shipping: null,
			final_price: 0,
			dph: 0,
			op: false,  // Súhlas s obchodnými podmienkami
			su: false,	// Súhlas na spracovanie údajov
		}
	},
	methods: {
		getFromSession() {
			this.product = []
			this.final_price = 0
			for (const [key, value] of Object.entries(this.$session.getAll())) {
				if (key.startsWith("basket-item")) {
					let data = JSON.parse(value)
					this.product.push(data)
					this.final_price += parseFloat(data.product.properties.final_price)
				}
			}

			if (this.final_price > 0 && this.$session.has('basket-adress') && this.$session.has('basket-shipping')) 
			{
				this.adress = JSON.parse(this.$session.get("basket-adress"))
				this.shipping = JSON.parse(this.$session.get("basket-shipping"))
				this.final_price += parseFloat(this.shipping.shipping.price) + parseFloat(this.shipping.payment.price)
				this.final_price = this.final_price.toFixed(2)
				this.dph = parseFloat(this.final_price * 0.2).toFixed(2)
			}
		},
		async onSubmit(e) {
			e.preventDefault()
			let vm = this
			let data = {
				product: this.product,
				adress: this.adress,
				shipping: this.shipping,
				final_price: this.final_price,
				dph: this.dph,
			}
			await MainService.postSaveNakup(data)
			.then(response => {
				if (parseInt(response.data.status) == 200) {
					this.$session.remove('basket-adress')
					this.$session.remove('basket-shipping')
					for (const [key, value] of Object.entries(this.$session.getAll())) {
						if (key.startsWith("basket-item")) {
							let v = JSON.parse(value)
							this.$session.remove('basket-item-' + v.id_product)
						}
					}
					this.$root.$emit('basket-nav-update', { id: 5, enabled: true, view_part: 5, disable_another: true })

						vm.$root.$emit('flash_message', [{
							'message': response.data.message,
							'type': 'success',
							'heading': 'Ukončenie nákupu',
							'timeout': 50000,
						}])
					
					// https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
					// Tvrdé presmerovanie po prihlásení.
					//window.location.href = this.$store.state.basePath;
				} else {
					console.error(response.data)
					vm.$root.$emit('flash_message', [{
						'message': response.data.message,
						'type': 'danger',
						'heading': 'Chyba posielania',
						'timeout': 10000,
					}])
				}
			})
			.catch(error => {
				console.error(error)
			})
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
		<h1>Sumarizácia nákupu</h1>
		<div class="row">
			<div class="col-md-6">
				<h5>Produkty:</h5>
				<div
					v-if="product.length > 0"
					v-for="i in product"
					:key="i.id_product" 
					class="d-flex justify-content-between"
				>
					<b-avatar variant="info" :src="$store.state.basePath + '/' + i.product.main_file"></b-avatar>
					{{ i.product.name }}
					<b class="ms-2">{{ i.product.properties.final_price }} €</b>
				</div>
				<div class="d-flex justify-content-between border-top pt-2 mt-2">
					<h6>Dodávka:</h6>
					<span>{{ shipping.shipping.name }}</span>
					<b>{{ shipping.shipping.price }} €</b>
				</div>
				<div class="d-flex justify-content-between">
					<h6>Platba:</h6>
					<span>{{ shipping.payment.name }}</span>
					<b>{{ shipping.payment.price }} €</b>
				</div>
				<div class="d-flex justify-content-between border-top pt-2">
					<h6>Konečná cena:</h6>
					<b>{{ final_price }} €</b><br />
					<small>DPH: {{ dph }} €</small>
				</div>
			</div>
			<div class="col-md-6">
				<h5>Adresa:</h5>
				<p>
					{{ adress.name }}<br />
					{{ adress.email }}<br />
					{{ adress.street }}<br />
					{{ adress.town }}	{{ adress.psc }}<br />
					{{ adress.country }}<br />
					{{ adress.phone }}
				</p>
				<h5 v-if="adress.firm.name.length" class="border-top pt-2">Firma:</h5>
				<p v-if="adress.firm.name.length">
					{{ adress.firm.name }}<br />
					{{ adress.firm.ico }}<br />
					{{ adress.firm.dic }}<br />
					{{ adress.firm.icdph }}<br />
					{{ adress.firm.street }}<br />
					{{ adress.firm.town }}<br />
					{{ adress.firm.psc }}<br />
					{{ adress.firm.country }}<br />
				</p>
				<h5 v-if="adress.adress2.street.length" class="border-top pt-2">Iná dodacia adresa:</h5>
				<p v-if="adress.adress2.street.length">
					{{ adress.adress2.street }}<br />
					{{ adress.adress2.town }}<br />
					{{ adress.adress2.psc }}<br />
					{{ adress.adress2.country }}<br />
				</p>
			</div>
			<div class="col-12 mb-1" v-if="shipping.notice != null">
				Poznámka: {{ shipping.notice }}
			</div>
			<div class="col-12">
				<form
					@submit="onSubmit"
				>
					<div class="form-check">
						<input class="form-check-input" 
							type="checkbox" value="" id="opCheck"
							v-validate="'required'"
							data-vv-as="Obchodné podmienky"
							v-model="op"
						>
						<label class="form-check-label" for="opCheck">
							Súhlasím s 
							<a :href="$store.state.basePath + '/clanky/obchodne-podmienky'">
								obchodnými podmienkami
							</a>
							.
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" 
							type="checkbox" value="" id="suCheck"
							v-validate="'required'"
							data-vv-as="spracovanie osobných údajov"
							v-model="su"
						>
						<label class="form-check-label" for="suCheck">
							Súhlasím so spracovaním osobných údajov pre potreby nákupu. 
						</label>
					</div>
					<button 
						type="submit"
						class="btn btn-success mt-2 send-button"
						:class="isFormValid ? '' : 'disabled'"
						:disabled="!isFormValid"
					>
						Objednávka s povinnosťou platby
					</button>
				</form>
			</div>
		</div>
	</div>
</template>