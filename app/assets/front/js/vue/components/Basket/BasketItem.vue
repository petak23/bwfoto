<script setup>
/**
 * Komponenta pre vypísanie jednej položky nákupného košíka.
 * Posledna zmena 05.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
import { RouterLink } from "vue-router";

const props = defineProps({
	basketItem: {
		type: Object,
		default: {},
	},
	filePath: {
		type: String,
		required: true,
	}
})

const emit = defineEmits("basket-item-del")

const delMe = () => {
	emit("basket-item-del", props.basketItem.id_product)
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
						<RouterLink 
							:to="'/clanky/' + basketItem.url_name + '/' + basketItem.id_product"
							class="text-white"
						>
							{{ basketItem.product.name }}
						</RouterLink>
					</h5>
				</div>
				<div class="col-10 col-md-4">
					<h6 class="text-white" v-if="basketItem.product.properties.final_price > 0">
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
				<div class="col-12 text-white">
					<h6 v-if="basketItem.product.description != null">Popis:</h6>
					<p class="card-text" v-if="basketItem.product.description != null">
						<small>{{ basketItem.product.description }}</small><br />
					</p>
					<h6 v-if="basketItem.product.properties.props.length">Vlastnosti:</h6>
					<div class="card-text" v-if="basketItem.product.properties.props.length">
						<table class="table table-dark table-striped table-sm">
							<tr v-for="pp in basketItem.product.properties.props">
								<td>{{ pp.category }}</td>
								<td>{{ pp.name }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>