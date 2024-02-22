<script>
	import BasketItem from "../components/Basket/BasketItem.vue";

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
					if (key.startsWith("basket")) {
						let data = JSON.parse(value)
						this.product.push(data)
						this.sum_price += data.product.properties.final_price
					}
				}
			},
			my_basket() {
				this.product = []
				this.getFromSession()
			}
		},
		mounted() {
			this.$session.start()

			this.getFromSession()

			this.$root.$on("basket-update", this.my_basket);
		}
	}
</script>

<template>
	<div>
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
		<div class="text-right">
			Výsledná cena: <b>{{ sum_price }} €</b>
		</div>
		
		<div v-if="!product.length" class="alert alert-warning" role="alert">
			<h4 class="alert-heading">Ups...</h4>
			<p>Je nám to ľúto, ale Váš košík je prázdny...</p>
			<hr>
			<p class="mb-0">
				Prosím, prejdite do časti 
				<a :href="$store.state.basePath + '/clanky/produkty'" class="alert-link">produktov</a> a nejaké zvolte. 
			</p>
		</div>
	</div>
</template>