<script>
/**
 * Hlavná časť pre prácu s nákupom.
 * Posledna zmena 04.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */

	import BasketNavigation from "../components/Basket/BasketNavigation.vue"
	import BasketList from "../components/Basket/BasketList.vue"
	import BasketAdress from "../components/Basket/BasketAdress.vue"
	import BasketShipping from "../components/Basket/BasketShipping.vue"
	import BasketSumar from "../components/Basket/BasketSumar.vue"

	export default {
		components: {
			BasketNavigation, // Hlavá navigácie priebehu nákupu
			BasketList, 			// Prvý krok: prehľad košíka
			BasketAdress, 		// Druhý krok: zadanie adresy
			BasketShipping,		// Tretí krok: doprava a platba
			BasketSumar,			// Štvrtý krok: sumarizácia nákupu
		},
		props: {
			view_part: {
				type: String,
				default: "1",
			},
		},
		data() {
			return {
				part: 1,
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
				this.part = this.test_part(newValue)
			}
		},
		mounted () {
			this.part = this.test_part(this.view_part)

			this.$root.$on('basket-view-part', item => {
				this.part = this.test_part(item[0].view_part)
			});
			//this.$session.remove('adress-basket')
		},
	}
</script>

<template>
	<div>
		<basket-navigation 
			:view_part="part"
		/>
		<!-- Prvý krok: prehľad košíka -->
		<basket-list 
			v-if="part == 1"
		>
		</basket-list>
		<!-- Druhý krok: zadanie adresy -->
		<basket-adress 
			v-else-if="part == 2"
		>
		</basket-adress>
		<!-- Tretí krok: doprava a platba -->
		<basket-shipping
				v-else-if="part == 3"
		>
		</basket-shipping>
		<!-- Štvrtý krok: sumarizácia nákupu -->
		<basket-sumar 
			v-else-if="part == 4"
		>
		</basket-sumar>
		<div v-else>
			{{ part }}
		</div>
	</div>
</template>