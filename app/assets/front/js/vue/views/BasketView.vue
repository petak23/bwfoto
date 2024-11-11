<script setup>
/**
 * Hlavná časť pre prácu s nákupom.
 * Posledna zmena 11.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */

	import BasketNavigation from "../components/Basket/BasketNavigation.vue"
	import BasketList from "../components/Basket/BasketList.vue"
	import BasketAdress from "../components/Basket/BasketAdress.vue"
	import BasketShipping from "../components/Basket/BasketShipping.vue"
	import BasketSumar from "../components/Basket/BasketSumar.vue"
	import BasketFinal from "../components/Basket/BasketFinal.vue" //v3

	export default {
		components: {
			BasketNavigation, // Hlavá navigácie priebehu nákupu
			BasketList, 			// Prvý krok: prehľad košíka
			BasketAdress, 		// Druhý krok: zadanie adresy
			BasketShipping,		// Tretí krok: doprava a platba
			BasketSumar,			// Štvrtý krok: sumarizácia nákupu
			BasketFinal,			// Piaty krok: správa o ukončení nákupu
		},
		props: {
			view_part: {
				type: String,
				default: "1",
			},
		},
		data() {
			return {
				info: null,
			}
		},
		methods: {
			test_part(p) {
				let x = parseInt(p)
				return x > 0 && x < 6 ? x : 1
			}
		},
		watch: {
			view_part(newValue) {
				this.$store.commit('UPDATE_BASKET_VIEW_PART', this.test_part(newValue))
			}
		},
		mounted () {
			this.$store.commit('UPDATE_BASKET_VIEW_PART', this.test_part(this.view_part))

			this.$root.$on('basket-view-part', item => {
				this.$store.commit('UPDATE_BASKET_VIEW_PART', this.test_part(item[0].view_part))
			});
			this.$root.$on('basket-final', item => {
				this.info = item
			})
		},
	}
</script>

<template>
	<div>
		<basket-navigation />
		<!-- Prvý krok: prehľad košíka -->
		<basket-list 
			v-if="$store.state.basket.view_part == 1"
		>
		</basket-list>
		<!-- Druhý krok: zadanie adresy -->
		<basket-adress 
			v-else-if="$store.state.basket.view_part == 2"
		>
		</basket-adress>
		<!-- Tretí krok: doprava a platba -->
		<basket-shipping
				v-else-if="$store.state.basket.view_part == 3"
		>
		</basket-shipping>
		<!-- Štvrtý krok: sumarizácia nákupu -->
		<basket-sumar 
			v-else-if="$store.state.basket.view_part == 4"
		>
		</basket-sumar>
		<!-- Piaty krok: správa o ukončení nákupu -->
		<basket-final 
			v-else-if="$store.state.basket.view_part == 5" 
			:info="info"
		/>
		<div v-else>
			
		</div>
	</div>
</template>