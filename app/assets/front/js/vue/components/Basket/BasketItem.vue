<script>
export default {
	props: {
		basketItem: {
			type: Object,
			default: {},
		},
		filePath: {
			type: String,
			required: true,
		},
	},
	methods: {
		delMe() {
			this.$session.remove('basket-' + this.basketItem.id_product)
			this.$root.$emit("basket-update", [])
		}
	},
}
</script>


<template>
	<div class="row no-gutters pt-2 pl-2">
		<div class="col-md-4">
			<img :src="filePath + basketItem.product.main_file" :alt="basketItem.product.name" class="w-100"> 
		</div>
		<div class="col-md-8">
			<div class="card-body row">
				<div class="col-12 col-md-6">
					<h5 class="card-title">
						<a 
							:href="filePath + 'clanky/' + basketItem.id_article + '/?first_id=' + basketItem.id_product"
							class="text-white"
						>
							{{ basketItem.product.name }}
						</a>
					</h5>
				</div>
				<div class="col-10 col-md-4">
					<h6 v-if="basketItem.product.properties.final_price > 0">
						<b>Cena: {{ basketItem.product.properties.final_price }} €</b>
					</h6>
				</div>
				<div class="col-2 col-md-2 text-right">
					<button 
						type="button" 
						title="Odstráň z košíka"
						class="btn btn-outline-danger btn-sm" 
						@click.prevent="delMe()"
					>
						<i class="fa-regular fa-trash-can"></i>
					</button>
				</div>
				<div class="col-12">
					<h6 v-if="basketItem.product.description != null">Popis:</h6>
					<p class="card-text" v-if="basketItem.product.description != null">
						<small>{{ basketItem.product.description }}</small><br />
					</p>
					<h6 v-if="basketItem.product.properties.props.length">Vlastnosti:</h6>
					<p class="card-text" v-if="basketItem.product.properties.props.length">
						<table class="table table-dark table-striped table-sm">
							<tr v-for="pp in basketItem.product.properties.props">
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