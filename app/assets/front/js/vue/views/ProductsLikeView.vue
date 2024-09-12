<script>
/**
 * Komponenta pre vypísanie zoznamu obľúbených produktov.
 * Posledna zmena 12.09.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 * 
 * @description https://www.npmjs.com/package/vue-session
 */

	import ProductsLikeItem from "../components/ProductsLike/ProductsLikeItem.vue";

	export default {
		components: {
			ProductsLikeItem,
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
				this.$store.commit("UPDATE_PRODUCTS_LIKE_ITEMS", this.liked)
			},
			my_liked() {
				this.liked = []
				this.getFromSession()
			}
		},
		computed: {
			filePath() {
				return this.$store.state.app_settings != null ? this.$store.state.app_settings.basePath + '/' : '' 
			}
		},
		mounted() {
			this.$session.start()

			this.getFromSession()
		}
	}
</script>

<template>
	<div>
		<p class="m-2 text-right" v-if="liked.length">
			<a :href="filePath + 'clanky/produkty'" class="text-white">
				<i class="fa-solid fa-rotate-left ml-2"></i>
				Návrat do časti produktov
			</a> 
		</p>
		<div 
			class="card mb-3 bg-dark" 
			v-if="liked.length"
			v-for="i in liked"
			:key="i.id_product"
		>
			<products-like-item	
				:like-item="i" 
				v-on:product-like-update-items="my_liked"
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