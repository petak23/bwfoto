<script setup>
/**
 * Komponenta pre vypísanie zoznamu obľúbených produktov.
 * Posledna zmena 21.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 * 
 */

import ProductsLikeItem from "../components/ProductsLike/ProductsLikeItem.vue";
import { ref } from 'vue'
import Session from '../plugins/session.js'
import { useMainStore } from '../store/main.js'
const store = useMainStore()

import { RouterLink } from "vue-router";

const liked = ref([]) // Array of Object {id_article: xx, id_product: xx, name: "xx", source: "xx.jpeg" }

const getFromSession = () => {
	liked.value = []
	for (const [key, value] of Object.entries(Session.allStorage())) {
		if (key.startsWith("like")) {
			liked.value.push(JSON.parse(value))
		}
	}
	store.productsLikeItem = liked.value
}

watch(() => store.productsLikeItem, (newValue, oldValue) => {
	liked.value = store.productsLikeItem
})

onMounted(() => {
	getFromSession()
})
</script>

<template>
	<div>
		<p class="m-2 text-right" v-if="liked.length">
			<RouterLink to="clanky/produkty" class="text-white">
				<i class="fa-solid fa-rotate-left ml-2"></i>
				Návrat do časti produktov
			</RouterLink> 
		</p>
		<div 
			class="card mb-3 bg-dark" 
			v-if="liked.length"
			v-for="i in liked"
			:key="i.id_product"
		>
			<products-like-item	
				:like-item="i" 
				v-on:product-like-update-items="getFromSession"
			/>
		</div>
		
		<div v-if="!liked.length" class="alert alert-warning" role="alert">
			<h4 class="alert-heading">Ups...</h4>
			  <p>Je nám to ľúto, ale žiaden obľúbený produkt sa nenašiel...</p>
				<hr>
				<p class="mb-0">
					Prosím, prejdite do časti 
					<RouterLink to="clanky/produkty" class="alert-link">produktov</RouterLink> a nejaké zvoľte. 
				</p>
		</div>
	</div>
</template>