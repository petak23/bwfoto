<script setup>
/**
 * Komponenta pre vypísanie jedného obľúbeného produktu.
 * Posledna zmena 21.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 * 
 */
import { ref, computed, onMounted } from 'vue'
import MainService from "../../services/MainService.js";
import Session from '../../plugins/session.js'
import { RouterLink } from 'vue-router';

import { useMainStore } from '../../store/main.js'
const store = useMainStore()

const props = defineProps({
	likeItem: {
		type: Object,
		default: {},
	}
})

const product = ref(null)
const in_basket = ref(false)

const emit = defineEmits(['product-like-update-items', "basket-insert"])

const getProductsInfo = () => {
	MainService.getProduct(props.likeItem.id_product)
		.then(response => {
			product.value = response.data
			my_in_basket()
		})
		.catch((error) => {
			console.error(error)
		})
}

const delMe = () => {
	Session.clearStorage('like-' + props.likeItem.id_product)
	emit('product-like-update-items')
}

const basketInsert = () => {
	console.log(this.product)
	emit("basket-insert", [{
		id_product: product.value.id,
		product: product.value,
		id_article: product.value.id_hlavne_menu,
	}])
}

const my_in_basket = () => {
	product.value.in_basket = Session.has('basket-item-' + product.value.id)
	in_basket.value = product.value.in_basket
}

const button_basket_title = computed(() => {
	let t = in_basket.value ? 'Produkt už je v košíku.' : 'Vlož do košíka.'
	return product.value != null && product.value.id_products_status > 1 ? product.value.products_status : t
})

const button_basket_class = computed(() => {
	let t = in_basket.value ? 'btn-outline-secondary disabled' : 'btn-success'
	return product.value != null && product.value.id_products_status > 1 ? 'btn-outline-secondary disabled' : t
})

const button_basket_disabled = computed(() => {
	return product.value != null && product.value.id_products_status > 1 ? true : in_basket.value
})

watch(() => likeItem, () => {
	getProductsInfo()
})

onMounted(() => {
	getProductsInfo()
	// TODO $root.$on 
	//this.$root.$on("basket-update", this.my_in_basket)
})
</script>

<template>
	<div class="row no-gutters pt-2 pl-2">
		<div class="col-md-4">
			<RouterLink :to="'/clanky/' + likeItem.id_article + '/?first_id=' + likeItem.id_product">
				<img :src="store.baseUrl + '/' + likeItem.source" :alt="likeItem.name" class="w-100"> 
			</RouterLink>
		</div>
		<div class="col-md-8">
			<div class="card-body row">
				<div class="col-12 col-md-6">
					<h5 class="card-title">
						<RouterLink 
							:to="'/clanky/' + likeItem.id_article + '/?first_id=' + likeItem.id_product"
							class="text-white"
						>
							{{ likeItem.name }}
						</RouterLink>
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