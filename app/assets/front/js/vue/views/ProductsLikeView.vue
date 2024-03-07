<script>
	import ProductsLikeItem from "../components/ProductsLike/ProductsLikeItem.vue";

	export default {
		components: {
			ProductsLikeItem,
		},
		props: {
			filePath: {
				type: String,
				required: true,
			},
		},
		data() {
			return {
				liked: [], // Array of Object {id_article: xx, id_product: xx, name: "xx", source: "xx.jpeg" }
			}
		},
		methods: {
			getFromSession() {
				this.liked = []
				for (const [key, value] of Object.entries(this.$session.getAll())) {
					if (key.startsWith("like")) {
						this.liked.push(JSON.parse(value))
					}
				}
				console.log(this.liked)
			},
			my_liked() {
				this.liked = []
				this.getFromSession()
			}
		},
		mounted() {
			this.$session.start()

			this.getFromSession()

			this.$root.$on("product-like-update", this.my_liked);
		}
	}
</script>

<template>
	<div>
		<div 
			class="card mb-3 bg-dark" 
			v-if="liked.length"
			v-for="i in liked"
			:key="i.id_product"
		>
			<products-like-item
				:like-item="i"
				:file-path="filePath"
			/>
		</div>
		
		<div v-if="!liked.length" class="alert alert-warning" role="alert">
			<h4 class="alert-heading">Ups...</h4>
			  <p>Je nám to ľúto, ale žiaden obľúbený produkt sa nenašiel...</p>
				<hr>
				<p class="mb-0">
					Prosím, prejdite do časti <a :href="filePath + 'clanky/produkty'" class="alert-link">produktov</a> a nejaké zvoľte. 
				</p>
	  	
		</div>

	</div>
</template>


<style scoped>

</style>