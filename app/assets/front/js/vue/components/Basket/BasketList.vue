<script setup>
/**
 * Komponenta pre vypísanie položiek nákupného košíka.
 * Posledna zmena 26.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
import { ref, onMounted } from 'vue'
import BasketItem from "./BasketItem.vue"
import Session from "../../plugins/session.js"
import { RouterLink } from "vue-router";

const product = ref([]) // Array of Object {id_article: xx, id_product: xx, product: Object }
const sum_price = ref(0) // Sumárna cena za produkty 

const emit = defineEmits(['basket-nav-update', 'basket-view-part'])

const getFromSession = () => {
	product.value = []
	sum_price.value = 0
	for (const [key, value] of Object.entries(Session.allStorage())) {
		if (key.startsWith("basket-item")) {
			let data = JSON.parse(value)
			product.value.push(data)
			sum_price.value += parseFloat(data.product.properties.final_price)
		}
	}
}
const my_basket = () => {
	product.value = []
	getFromSession()
	if (product.value.length == 0) { // Pre prípad, že košík bude prázdny
		emit('basket-nav-update', { id: 0, enabled: false, disable_another: true })	
	}
}
const getToPage = (id) => {
	emit('basket-view-part', [{ view_part: id }])
}

onMounted(() => {
	//this.$session.start()

	getFromSession()

	if (product.value.length) {
		emit('basket-nav-update', { id: 2, enabled: true })
	}

	//this.$root.$on("basket-update", my_basket);
})
</script>

<template>
	<div>
		<h1>Obsah košíka</h1>
		<div 
			class="card mb-3 bg-dark" 
			v-if="product.length"
			v-for="i in product"
			:key="i.id_product"
		>
			<basket-item
				:basket-item="i"
				:file-path="$store.state.basePath + '/'"
			/>
		</div>
		<div class="text-right" v-if="product.length">
			Výsledná cena: <b>{{ sum_price }} €</b><br />
			<button
				@click="getToPage(2)"
				class="btn btn-success mt-2">
				Ďaľší krok v objednávke <i class="ml-1 fa-solid fa-arrow-right"></i>
			</button>
		</div>
		
		<div v-if="!product.length" class="alert alert-warning" role="alert">
			<h4 class="alert-heading">Ups...</h4>
			<p>Je nám to ľúto, ale Váš košík je prázdny...</p>
			<hr>
			<p class="mb-0">
				Prosím, prejdite do časti 
				<RouterLink to="/clanky/produkty" class="alert-link">produktov</RouterLink> a nejaké zvoľte. 
			</p>
		</div>
	</div>
</template>