<script setup>
/**
 * Hlavná časť pre prácu s nákupom.
 * Posledna zmena 04.02.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
import { ref, watch, onMounted } from "vue"

import { useBasketStore } from '../store/basket.js'
const storeB = useBasketStore()

import BasketNavigation from "../components/Basket/BasketNavigation.vue"//v3 	// Hlavá navigácie priebehu nákupu
import BasketList from "../components/Basket/BasketList.vue"	 //v3						// Prvý krok: prehľad košíka
import BasketAdress from "../components/Basket/BasketAdress.vue"					// Druhý krok: zadanie adresy
import BasketShipping from "../components/Basket/BasketShipping.vue"//v3			// Tretí krok: doprava a platba
import BasketSumar from "../components/Basket/BasketSumar.vue" //v3						// Štvrtý krok: sumarizácia nákupu
import BasketFinal from "../components/Basket/BasketFinal.vue" //v3						// Piaty krok: správa o ukončení nákupu

const props = defineProps({
	view_part: {
		type: String,
		default: "1",
	},
})

const info = ref(null)

const test_part = (p) => {
	let x = parseInt(p)
	return x > 0 && x < 6 ? x : 1
}

const setPart = (newPart) => {
	storeB.view_part = test_part(newPart)
}

watch(() => props.view_part, (newValue) => {
	setPart(newValue)
})

onMounted(() => {
	setPart(props.view_part)
})


</script>

<template>
	<div>
		<basket-navigation 
			@basket-view-part="setPart"
		/>
		
		<!-- Prvý krok: prehľad košíka -->
		<basket-list 
			v-if="storeB.view_part == 1"
			@basket-view-part="setPart"
		/>
		
		<!-- Druhý krok: zadanie adresy -->
		<basket-adress 
			v-else-if="storeB.view_part == 2"
		/>
	
		<!-- Tretí krok: doprava a platba -->
		<basket-shipping
			v-else-if="storeB.view_part == 3"
		/>
		
		<!-- Štvrtý krok: sumarizácia nákupu -->
		<basket-sumar 
			v-else-if="storeB.view_part == 4"
			@basket-final="(i) => info = i"
		/>
		
		<!-- Piaty krok: správa o ukončení nákupu -->
		<basket-final 
			v-else-if="storeB.view_part == 5" 
			:info="info"
		/>

		<div v-else>
			
		</div>
	</div>
</template>