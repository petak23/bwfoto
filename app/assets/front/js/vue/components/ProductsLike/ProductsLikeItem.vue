<script>
import MainService from "../../services/MainService.js";

export default {
	props: {
		likeItem: {
			type: Object,
			default: {},
		},
		filePath: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			product: null
		}
	},
	methods: {
		getProductsInfo() {
			MainService.getProduct(this.likeItem.id_product)
				.then(response => {
					this.product = response.data
					console.log(this.product)
					console.log(this.likeItem)
				})
				.catch((error) => {
					console.error(error)
				})
		},
		delMe() {
			this.$session.remove('like-' + this.likeItem.id_product)
			this.$root.$emit("product-like-update", [])
		}
	},
	watch: {
		likeItem: function (newLikeItem) {
			this.getProductsInfo()
		},
	},
	mounted () {
		//console.log(this.likeItem);
		this.getProductsInfo()
	},
}
</script>


<template>
	<div class="row no-gutters pt-2 pl-2">
		<div class="col-md-4">
			<img :src="filePath + likeItem.source" :alt="likeItem.name" class="w-100"> 
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
						<b>Cena: {{ product.final_price }} €</b>
					</h6>
				</div>
				<div class="col-2 col-md-2 text-right">
					<button type="button" class="btn btn-outline-danger btn-sm" @click.prevent="delMe()">
						<i class="fa-regular fa-trash-can"></i>
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



<style lang="scss" scoped>

</style>