<script setup>
/**
 * Komponenta pre vypísanie nákupného košíka v hlavnej ponuke.
 * Posledna zmena 05.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */

import { watch, onMounted } from 'vue'

import { useMainStore } from '../../store/main.js'
const store = useMainStore()
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

import { RouterLink } from 'vue-router'

import { BDropdown, BDropdownItem, BDropdownDivider } from 'bootstrap-vue-next';

watch(() => storeB.basketItem, () => {
	storeB.getFinalPrice()
})

onMounted(() => {
	storeB.getProductsFromSession()
	storeB.getNavFromSession()
	storeB.getAddressFromSession()
	storeB.getShipingFromSession()
})

</script>

<template>
	<BDropdown variant="success" size="sm" toggle-class="bf-nt py-2 px-2 ms-2" :disabled="storeB.basketItem.length == 0">
		<template #button-content>
			<i class="fa-solid fa-basket-shopping fa-lg"></i>
			<span 
				class="mx-2 badge rounded-pill bg-warning text-dark border"
				v-if="storeB.basketItem.length > 0">
				{{ storeB.basketItem.length }}
			</span> 
		</template>
		<BDropdownItem 
			v-for="i in storeB.basketItem"
			:key="i.id_product"
		>
			<template #default>
				<div class="d-flex justify-content-between">
					<RouterLink
						:to="'/clanky/' + i.url_name + '/' + i.id_product"
					>
						<img :src="store.baseUrl + '/' + i.product.main_file" class="rounded float-start pe-1" :alt="i.product.name" />
					</RouterLink>
					<RouterLink 
						:to="'/clanky/' + i.url_name + '/' + i.id_product"
					>
						{{ i.product.name }}
						<br />{{ i.product.properties.final_price }} €
					</RouterLink>
					<button 
						v-if="storeB.view_part == 1"
						class="btn btn-light btn-sm" 
						@click.prevent="storeB.delOneProduct(i.id_product)"
					>
						<i class="fa-regular fa-trash-can text-danger"></i>
					</button>
				</div>
			</template>
		</BDropdownItem>
		<BDropdownDivider />
		<BDropdownItem to="/basket" class="show-basket mt-2">
			Zobraz košík
		</BDropdownItem>
	</BDropdown>
</template>

<style scoped>
	.dropdown button {
		padding: .4em;
	}
	.dropdown-menu {
		min-width: 22rem;
	}
	.dropdown-menu a {
		font-size: 80%;
	}
	.dropdown-menu img {
		max-width: 4rem;
	}
	.show-basket {
		background-color: rgba(120, 233, 116, 0.41);
	}
</style>