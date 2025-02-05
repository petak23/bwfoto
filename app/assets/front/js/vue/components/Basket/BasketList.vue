<script setup>
/**
 * Komponenta pre vypísanie položiek nákupného košíka.
 * Posledna zmena 05.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
import { onMounted } from 'vue'
import BasketItem from "./BasketItem.vue"
import { RouterLink } from "vue-router"
import { useMainStore } from '../../store/main.js'
const store = useMainStore()
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

const getToPage = (id) => {
	storeB.navigationUpdate({ view_part: id })
}

onMounted(() => {
	if (storeB.basketItem.length) {
		storeB.navigationUpdate({ id: 2, enabled: true })
	}
})
</script>

<template>
	<h1>Obsah košíka</h1>
	<div v-if="storeB.basketItem.length">
		<div 
			class="card mb-3 bg-dark" 
			v-for="i in storeB.basketItem"
			:key="i.id_product"
		>
			<basket-item
				:basket-item="i"
				:file-path="store.baseUrl + '/'"
				@basket-item-del="(id) => storeB.delOneProduct(id)"
			/>
		</div>
		<div class="text-right" v-if="storeB.basketItem.length">
			Výsledná cena: <b>{{ storeB.final_price }} €</b><br />
			<button
				@click="getToPage(2)"
				class="btn btn-success mt-2">
				Ďaľší krok v objednávke <i class="ml-1 fa-solid fa-arrow-right"></i>
			</button>
		</div>
	</div>
	<div v-else class="alert alert-warning" role="alert">
		<h4 class="alert-heading">Ups...</h4>
		<p>Je nám to ľúto, ale Váš košík je prázdny...</p>
		<hr>
		<p class="mb-0">
			Prosím, prejdite do časti 
			<RouterLink to="/clanky/produkty" class="alert-link">produktov</RouterLink> a nejaké zvoľte. 
		</p>
	</div>
</template>