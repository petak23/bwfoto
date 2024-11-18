<script setup>
/**
 * Komponenta pre vypísanie nákupného košíka v hlavnej ponuke.
 * Posledna zmena 18.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */

import { ref, onMounted } from 'vue'
import Session from "../../plugins/session.js"

import { useMainStore } from '../../store/main.js'
const store = useMainStore()

import { RouterLink } from 'vue-router'

import { BDropdown, BDropdownItem, BDropdownDivider } from 'bootstrap-vue-next';

const items = ref([])

const getFromSession = () => {
	//let spom = Session.getStorage()
	items.value = []
	for (const [key, value] of Object.entries(Session.allStorage())) {
		if (key.startsWith("basket-item")) {
			items.value.push(JSON.parse(value))
		}
	}
	if (items.value.length == 0) {
		Session.clearStorage('basket-adress')
		Session.clearStorage('basket-shipping')
		Session.clearStorage('basket-nav')	
	}
}

const emit = defineEmits('basket-update')

const delOne = (id) => {
	Session.clearStorage('basket-item-' + id)
	emit("basket-update")
	getFromSession()
}

onMounted(() => {
	//this.$session.start()

	getFromSession()

	/*this.$root.$on("basket-insert", item => {
		Session.saveStorage('basket-item-' + item[0].id_product, JSON.stringify(item[0]))
		emit("basket-update")
	});
	this.$root.$on("basket-update", this.getFromSession);*/
})

</script>

<template>
	<BDropdown variant="success" size="sm" toggle-class="bf-nt py-1 px-2 ms-2" :disabled="items.length == 0">
		<template #button-content>
			<i class="fa-solid fa-basket-shopping"></i>
			<span 
				class="mx-2 badge badge-pill badge-warning"
				v-if="items.length > 0">
				{{ items.length }}
			</span> 
		</template>
		<BDropdownItem 
			v-for="i in items"
			:key="i.id_product"
		>
			<template #default>
				<div class="d-flex justify-content-between">
					<RouterLink
						:to="'/clanky/' + i.id_article + '/?first_id=' + i.id_product"
					>
						<img :src="store.baseUrl + '/' + i.product.main_file" class="rounded float-start pe-1" :alt="i.product.name" />
					</RouterLink>
					<RouterLink 
						:to="'/clanky/' + i.id_article + '/?first_id=' + i.id_product"
					>
						{{ i.product.name }}
						<br />{{ i.product.properties.final_price }} €
					</RouterLink>
					<button 
						v-if="store.basket.view_part == 1"
						class="btn btn-light btn-sm" 
						@click.prevent="delOne(i.id_product)"
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