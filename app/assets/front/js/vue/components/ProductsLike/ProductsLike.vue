<script setup>
/**
 * Komponenta pre vypísanie obľúbených produktov.
 * Posledna zmena 25.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 * 
 */
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router';
import { BButton, BDropdown, BDropdownDivider, BDropdownItem } from 'bootstrap-vue-next';

import { useMainStore } from '../../store/main.js'
const store = useMainStore()

import { useProductLikeStore } from '../../store/productLike.js'
const storePL = useProductLikeStore()

onMounted(() => {
	storePL.getFromSession()
})
</script>

<template>
	<div class="liked" v-if="storePL.productsLikeItem.length > 0">
		<BDropdown class="me-2" toggle-class="btn-lg btn-warning rounded-pill px-3 py-2 text-dark" variant="warning" menu-class="product-like-menu">
			<template #button-content>
				<i class="fa-regular fa-heart my-heart"></i>
				<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning border border-light">
					{{ storePL.productsLikeItem.length }}
				</span>
			</template>
			<BDropdownItem 
				v-for="i in storePL.productsLikeItem"
				link-class="dropdown-item-text d-flex justify-content-between mx-2"
			>
				<RouterLink 
					v-if="i != null"
					:to="'/clanky/' + i.url_name + '/' + i.id_product"
				>
					<img :src="store.baseUrl + '/' + i.source" class="rounded float-start pe-1" :alt="i.name" />
					<small>{{ i.name }}</small>
				</RouterLink>
				<BButton v-if="i != null" variant="light" @click.prevent="storePL.delOne(i.id_product)">
					<i class="fa-regular fa-trash-can text-danger"></i>
				</BButton>
			</BDropdownItem>
			
			<BDropdownDivider />
			<BDropdownItem >
				<button class="dropdown-item px-2 del-item" @click.prevent="storePL.delAll">Vymaž všetky obľúbené foto</button>
			</BDropdownItem>
			<BDropdownItem to="/productlike" link-class="dropdown-item px-2 mt-2 all-item">
				Zobraz obľúbené foto
			</BDropdownItem>
		</BDropdown>
	</div>
</template>

<style scoped>
.liked {
  font-size: 0.9rem;
	position: fixed;
	left: 5rem;
	bottom: 1rem;
  max-width: 50vw;
  z-index: 2000;
	border-width: .25rem;
}

.liked .product-like-menu {
	min-width: 25rem;
}
.dropdown-item-text img {
	max-width: 4rem;
}
.del-item {
	background-color: rgba(233, 116, 116, 0.41);
}
.all-item {
	background-color: rgba(120, 233, 116, 0.41);
}
</style>