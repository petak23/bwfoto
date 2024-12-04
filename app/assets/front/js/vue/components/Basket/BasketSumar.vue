<script setup>
/**
 * Komponenta pre vypísanie sumárnych údajov o nákupe.
 * Posledna zmena 04.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.7
 */
import { ref, computed, onMounted } from 'vue'
import MainService from '../../services/MainService.js'
import Session from '../../plugins/session.js'
import { BAvatar } from 'bootstrap-vue-next'
import { RouterLink } from 'vue-router'

import { useMainStore } from '../../store/main.js'
import { useFlashStore } from '../../store/flash'
const store = useMainStore()
const storeF = useFlashStore()
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

const product = ref([])
const adress = ref(null)
const shipping = ref(null)
const final_price = ref(0)
const dph = ref(0)
const op = ref(false)  // Súhlas s obchodnými podmienkami
const su = ref(false)	// Súhlas na spracovanie údajov

const getFromSession = () => {
	product.value = []
	final_price.value = 0
	for (const [key, value] of Object.entries(Session.allStorage())) {
		if (key.startsWith("basket-item")) {
			let data = JSON.parse(value)
			product.value.push(data)
			final_price.value += parseFloat(data.product.properties.final_price)
		}
	}

	if (final_price.value > 0 && Session.has('basket-adress') && Session.has('basket-shipping')) 
	{
		adress.value = JSON.parse(Session.get("basket-adress"))
		shipping.value = JSON.parse(Session.get("basket-shipping"))
		final_price.value += parseFloat(shipping.value.shipping.price) + parseFloat(shipping.value.payment.price)
		final_price.value = final_price.value.toFixed(2)
		dph.value = parseFloat(final_price.value * 0.2).toFixed(2)
	}
}

const emit = defineEmits(['basket-final'])

const onSubmit = async (e) =>{
	e.preventDefault()
	let vm = this
	let data = {
		product: product.value,
		adress: adress.value,
		shipping: shipping.value,
		final_price: final_price.value,
		dph: dph.value,
	}
	await MainService.postSaveNakup(data)
	.then(response => {
		if (parseInt(response.data.status) == 200) {
			Session.clearStorage('basket-adress')
			Session.clearStorage('basket-shipping')
			for (const [key, value] of Object.entries(Session.allStorage())) {
				if (key.startsWith("basket-item")) {
					let v = JSON.parse(value)
					Session.clearStorage('basket-item-' + v.id_product)
				}
			}
			storeB.basketNavUpdate({ id: 5, enabled: true, view_part: 5, disable_another: true })

			emit('basket-final', { message: response.data.message, type: 'success', heading: 'Ukončenie nákupu', })
		} else {
			console.error(response.data)
			storeF.showMessage(response.data.message, 'danger', 'Chyba posielania', 10000)
		}
	})
	.catch(error => {
		console.error(error)
	})
}

// TODO validate
const isFormValid = computed(() => {
	return su.value
	//return Object.keys(fields).every(key => this.fields[key].valid);
})

onMounted(() => {
	getFromSession()
})
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
					<BAvatar variant="info" :src="store.baseUrl + '/' + i.product.main_file"></BAvatar>
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
							<RouterLink to="/clanky/obchodne-podmienky">
								obchodnými podmienkami
							</RouterLink>
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