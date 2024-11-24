<script setup>
/**
 * Komponenta pre vypísanie obľúbených produktov.
 * Posledna zmena 24.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 * 
 */
import { watch, onMounted } from 'vue'
import Session from '../../plugins/session'
import { RouterLink } from 'vue-router';
import { BButton, BDropdown, BDropdownDivider, BDropdownItem } from 'bootstrap-vue-next';
import { useMainStore } from '../../store/main.js'
const store = useMainStore()

const delAll = () => { // Vymazanie všetkých obľúbených položiek
	store.productsLikeItem = []
	Session.clearStorage('like-items')
}

const delOne = (id) => { // Vymazanie jednej obľúbenej položky
	// Ak je v poli položka s id_propdukt == id tak ju vylúč
	store.productsLikeItem = store.productsLikeItem.map((likeItem) => {
		if (likeItem != null && likeItem.id_product !== id) {
			return likeItem
		}
	})

	Session.clearStorage('like-items')
	Session.saveStorage('like-items', store.productsLikeItem)
}

onMounted(() => {
	if (Session.has('like-items')) {
		store.productsLikeItem = JSON.parse(Session.getStorage('like-items'))
	} else {
		store.productsLikeItem = []
	}
})
</script>

<template>
	<div class="liked" v-if="store.productsLikeItem.length > 0">
		<BDropdown class="me-2" toggle-class="btn-lg btn-warning rounded-pill p-2">
			<template #button-content>
				<i class="fa-regular fa-heart my-heart"></i>
				<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning border border-light">
					{{ store.productsLikeItem.length }}
				</span>
			</template>
			<BDropdownItem 
				v-for="i in store.productsLikeItem"
				link-class="dropdown-item-text d-flex justify-content-between mx-2"
			>
				<RouterLink 
					v-if="i != null"
					:to="'/clanky/' + i.id_article + '/?first_id=' + i.id_product">
					<img :src="store.baseUrl + '/' + i.source" 
					class="rounded float-start pe-1" :alt="i.name"
				/>
					{{ i.name }}
				</RouterLink>
				<BButton v-if="i != null" variant="light" @click.prevent="delOne(i.id_product)">
					<i class="fa-regular fa-trash-can text-danger"></i>
				</BButton>
			</BDropdownItem>
			
			<BDropdownDivider />
			<BDropdownItem >
				<button class="dropdown-item px-2 del-item" @click.prevent="delAll">Vymaž všetky obľúbené foto</button>
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
	left: 4rem;
	bottom: 1rem;
  max-width: 50vw;
  z-index: 2000;
	border-width: .25rem;
}

.dropdown-menu {
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