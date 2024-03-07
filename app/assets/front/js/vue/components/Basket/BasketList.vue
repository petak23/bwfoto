<script>
/**
 * Komponenta pre vypísanie položiek nákupného košíka.
 * Posledna zmena 07.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
	import BasketItem from "./BasketItem.vue";

	export default {
		components: {
			BasketItem,
		},
		data() {
			return {
				product: [], // Array of Object {id_article: xx, id_product: xx, product: Object }
				sum_price: 0, // Sumárna cena za produkty 
			}
		},
		methods: {
			getFromSession() {
				this.product = []
				this.sum_price = 0
				for (const [key, value] of Object.entries(this.$session.getAll())) {
					if (key.startsWith("basket-item")) {
						let data = JSON.parse(value)
						this.product.push(data)
						this.sum_price += data.product.properties.final_price
					}
				}
			},
			my_basket() {
				this.product = []
				this.getFromSession()
			},
			getToPage(id) {
				this.$root.$emit('basket-view-part', [{ view_part: id }])
			},
		},
		mounted() {
			this.$session.start()

			this.getFromSession()

			if (this.product.length) {
				this.$root.$emit('basket-nav-update', { id: 2, enabled: true })
			}

			this.$root.$on("basket-update", this.my_basket);
		}
	}
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
			<!--a-- :href="$store.state.basePath + '/homepage/basket/2'" class="btn btn-success mt-2">
				Ďaľší krok v objednávke <i class="ml-1 fa-solid fa-arrow-right"></i>
			</!--a-->
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
				<a :href="$store.state.basePath + '/clanky/produkty'" class="alert-link">produktov</a> a nejaké zvoľte. 
			</p>
		</div>
	</div>
</template>