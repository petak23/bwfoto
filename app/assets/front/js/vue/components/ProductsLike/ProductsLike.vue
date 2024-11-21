<script setup>
/**
 * Komponenta pre vypísanie obľúbených produktov.
 * Posledna zmena 21.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 * 
 */
import { onMounted } from 'vue'
import Session from '../../plugins/session'
import { RouterLink } from 'vue-router';
import { BButton, BDropdown, BDropdownDivider, BDropdownItem } from 'bootstrap-vue-next';
import { useMainStore } from '../../store/main.js'
const store = useMainStore()

const getFromSession = () => {
	store.productsLikeItem = []
	for (const [key, value] of Object.entries(Session.allStorage())) {
		if (key.startsWith("like")) {
			store.productsLikeItem.push(JSON.parse(value))
		}
	}
}

const delAll = () => { // Vymazanie všetkých obľúbených položiek
	store.productsLikeItem.forEach(function(i) {
		Session.clearStorage('like-' + i.id_product)	
	})
	getFromSession()
}

const delOne = (id) => { // Vymazanie jednej obľúbenej položky
	Session.clearStorage('like-' + id)
	getFromSession()
}

onMounted(() => {
	getFromSession()
})
</script>

<template>
	<div class="liked" v-if="store.productsLikeItem != null && store.productsLikeItem.length > 0">
		<BDropdown class="me-2" toggle-class="btn-lg btn-warning rounded-pill">
			<template #button-content>
				<i class="fa-regular fa-heart my-heart"></i>
				<span class="badge badge-pill badge-warning">
					{{ store.productsLikeItem.length }}
				</span>
			</template>
			<BDropdownItem 
				v-for="i in store.productsLikeItem"
				:key="i.id_product"
				link-class="dropdown-item-text d-flex justify-content-between mx-2"
			>
				<RouterLink :to="'/clanky/' + i.id_article + '/?first_id=' + i.id_product">
					<img :src="store.baseUrl + '/' + i.source" class="rounded float-start pe-1" :alt="i.name" />
					{{ i.name }}
				</RouterLink>
				<BButton variant="light" @click.prevent="delOne(i.id_product)">
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

		<!--div class="btn-group dropup">
			<button 
				type="button" 
				class="btn btn-lg btn-warning dropdown-toggle rounded-pill" 
				data-toggle="dropdown" aria-expanded="false"
			>
				<i class="fa-regular fa-heart my-heart"></i>
				<span class="badge badge-pill badge-warning">
					{{ liked.length }}
				</span>
			</button>
			<ul class="dropdown-menu">
				<li
					v-for="i in liked"
					:key="i.id_product" 
					class="dropdown-item-text d-flex justify-content-between mx-2"
				>
					<RouterLink :to="'/clanky/' + i.id_article + '/?first_id=' + i.id_product">
						<img :src="store.baseUrl + '/' + i.source" class="rounded float-start pe-1" :alt="i.name" />
						{{ i.name }}
					</RouterLink>
					<BButton variant="light" @click.prevent="delOne(i.id_product)">
						<i class="fa-regular fa-trash-can text-danger"></i>
					</BButton>
				</li>
				<li><hr class="dropdown-divider"></li>
				<li></li>
				<li>
					<RouterLink class="dropdown-item px-2 mt-2 all-item" 
						to="/productlike">Zobraz obľúbené foto</RouterLink>
				</li>
			</ul>
		</div-->
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

/*.my-heart {
	margin-left: 1ex;
	margin-bottom: 1ex;
	margin-top: 1ex;
}*/
.badge {
	position: absolute !important;
	top: -10px !important;
	right: -10px;
}
.dropdown-menu {
	min-width: 22rem;
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