<script>
/**
 * Komponenta pre vypísanie jedného obľúbeného produktu.
 * Posledna zmena 10.09.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 * 
 * @description https://www.npmjs.com/package/vue-session
 */

import MainService from "../../services/MainService.js";

export default {
	props: {
		likeItem: {
			type: Object,
			default: {},
		}
	},
	data() {
		return {
			product: null,
			in_basket: false,
		}
	},
	methods: {
		getProductsInfo() {
			MainService.getProduct(this.likeItem.id_product)
				.then(response => {
					this.product = response.data
					this.my_in_basket()
				})
				.catch((error) => {
					console.error(error)
				})
		},
		delMe() {
			this.$session.remove('like-' + this.likeItem.id_product)
			this.$emit('product-like-update-items');
		},
		basketInsert() {
			console.log(this.product)
			this.$root.$emit("basket-insert", [{
				id_product: this.product.id,
				product: this.product,
				id_article: this.product.id_hlavne_menu,
			}])
		},
		my_in_basket() {
			this.product.in_basket = this.$session.has('basket-item-' + this.product.id)
			this.in_basket = this.product.in_basket
		},
	},
	computed: {
		button_basket_title() {
			let t = this.in_basket ? 'Produkt už je v košíku.' : 'Vlož do košíka.'
			return this.product != null && this.product.id_products_status > 1 ? this.product.products_status : t
		},
		button_basket_class() {
			let t = this.in_basket ? 'btn-outline-secondary disabled' : 'btn-success'
			return this.product != null && this.product.id_products_status > 1 ? 'btn-outline-secondary disabled' : t
		},
		button_basket_disabled() {
			return this.product != null && this.product.id_products_status > 1 ? true : this.in_basket
		},
		filePath() {
			return this.$store.state.app_settings != null ? this.$store.state.app_settings.basePath + '/' : '' 
		}
	},
	watch: {
		likeItem: function (newLikeItem) {
			this.getProductsInfo()
		}
	},
	mounted () {
		this.getProductsInfo()
		this.$root.$on("basket-update", this.my_in_basket)
	},
}
</script>


<template>
	<div class="row no-gutters pt-2 pl-2">
		<div class="col-md-4">
			<a :href="filePath + 'clanky/' + likeItem.id_article + '/?first_id=' + likeItem.id_product">
				<img :src="filePath + likeItem.source" :alt="likeItem.name" class="w-100"> 
			</a>
		</div>
		<div class="col-md-8">
			<div class="card-body row">
				<div class="col-12 col-md-6">
					<h5 class="card-title">
						<a 
							:href="filePath + 'clanky/' + likeItem.id_article + '/?first_id=' + likeItem.id_product"
							class="text-white"
						>
							{{ likeItem.name }}
						</a>
					</h5>
				</div>
				<div class="col-10 col-md-4">
					<h6 v-if="product != null && product.final_price > 0">
						<b>Cena: {{ product.final_price.toFixed(2) }} €</b>
					</h6>
				</div>
				<div class="col-2 col-md-2 text-right">
					<button 
						type="button"
						class="btn btn-outline-danger btn-sm"
						@click.prevent="delMe()"
						title="Odstráň foto zo zoznamu obľúbených položiek."
					>
						<i class="fa-regular fa-trash-can"></i>
					</button>
					<button 
							v-if="product != null"
							:title="button_basket_title" 
							type="button"
							class="btn btn-sm"
							:class="button_basket_class"
							@click.prevent="basketInsert()"
							:disabled="button_basket_disabled"
						>
							<i class="fa-solid fa-basket-shopping" v-if="product.id_products_status == 1"></i>
							<span v-else>{{ product.products_status }}</span>
						</button>
				</div>
				<div class="col-12">
					<h6 v-if="product != null && product.description != null">Popis:</h6>
					<p class="card-text" v-if="product != null && product.description != null">
						<small>{{ product.description }}</small><br />
					</p>
					<h6 v-if="product != null && product.props.length">Vlastnosti:</h6>
					<p class="card-text" v-if="product != null && product.props.length">
						<table class="table table-dark table-striped table-sm">
							<tr v-for="pp in product.props">
								<td>{{ pp.category }}</td>
								<td>{{ pp.name }}</td>
							</tr>
						</table>
					</p>
				</div>
			</div>
		</div>
	</div>
</template>