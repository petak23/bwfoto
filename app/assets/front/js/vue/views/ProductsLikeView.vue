<script setup>
/**
 * Komponenta pre vypísanie zoznamu obľúbených produktov.
 * Posledna zmena 25.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 * 
 */

import ProductsLikeItem from "../components/ProductsLike/ProductsLikeItem.vue";
import { onMounted } from 'vue'

import { useProductLikeStore } from '../store/productLike.js'
const storePL = useProductLikeStore()

import { RouterLink } from "vue-router";

onMounted(() => {
	storePL.getFromSession()
})
</script>

<template>
	<p class="m-2 text-right" v-if="storePL.productsLikeItem.length">
		<RouterLink to="clanky/produkty" class="text-white">
			<i class="fa-solid fa-rotate-left ml-2"></i>
			Návrat do časti produktov
		</RouterLink> 
	</p>
	<div 
		class="card mb-3 bg-dark" 
		v-if="storePL.productsLikeItem.length"
		v-for="i in storePL.productsLikeItem"
		:key="i.id_product"
	>
		<products-like-item	
			:like-item="i" 
			v-on:product-like-update-items="getFromSession"
		/>
	</div>
	
	<div v-if="!storePL.productsLikeItem.length" class="alert alert-warning" role="alert">
		<h4 class="alert-heading">Ups...</h4>
			<p>Je nám to ľúto, ale žiaden obľúbený produkt sa nenašiel...</p>
			<hr>
			<p class="mb-0">
				Prosím, prejdite do časti 
				<RouterLink to="clanky/produkty" class="alert-link">produktov</RouterLink> a nejaké zvoľte. 
			</p>
	</div>
</template>