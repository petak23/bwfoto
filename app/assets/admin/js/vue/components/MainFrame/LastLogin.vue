<script setup>
/**
 * Komponenta pre vypísanie posledných prihlásení.
 * Posledna zmena 25.02.2025
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
import { ref, onMounted } from 'vue'
import MainService from '../../services/MainService';

import { useMainStore } from '../../store/main'
const store = useMainStore()

const props = defineProps({
	rows: {
		default: 25,
	}
})

const items = ref(null)
const loading = ref(false)

const activeClass = (id) => {
	return id == store.user.id ? 'selected' : 'not-selected'
}

const rowsClass = (i) => {
	return (i % 2  == 0) ? "even" : "odd"
}

const deletelogs = () => {
	loading.value = true
	MainService.getDellAllLogins()
		.then(response => {
			if (response.data.result == 0) {
				items.value = null
			}
		})
		.catch((error) => {
			console.error(error);
		})
}

onMounted(() => {
	items.value = null
	MainService.getLastLogins(props.rows)
		.then(response => {
			items.value = Object.values(response.data)
		})
		.catch((error) => {
			console.error(error)
		})
})


</script>

<template>
	<div class="card border-info text-center last-login h-100">
		<div class="card-header">
			Posledných {{ props.rows }} prihlásení
			<button @click="deletelogs" :loading="loading" v-if="items != null" class="btn btn-sm btn-outline-danger">
				<i class="fas fa-trash-alt"></i>
			</button>
		</div>
		<div class="card-body">
			<table class="table table-sm table-striped" v-if="items != null">
				<tbody>
					<tr v-for="(item, index) in items" :key="index" :class="[ activeClass(item.id_user_main), rowsClass(index) ]">
						<td>{{ item.name }}</td>
						<td>{{ item.log_in_datetime }}</td>
					</tr>
				</tbody>
			</table>
			<h5 class="card-title" v-else>Bez záznamu</h5>
		</div>
	</div>
</template>

<style lang="scss" scoped>
	.last-login {
		max-height: 20rem;
		overflow: auto;

		td {
			font-size: 80% !important;
		}

		.selected td {
			font-weight: bold;
		}

		.odd {
			background-color: rgba(245, 222, 179, 0.35);
		}
		/*.list-item {
			overflow: auto;
			height: 100%;
		}*/
	}

	@media (min-width: 576px){ 
		.last-login {
			max-height: 20rem;
		}
	}
	@media (min-width: 768px){
		.last-login {
			max-height: 19rem;
		}
	}
	@media (min-width: 992px) {
		.last-login {
			max-height: 23rem;
		}
	}
	@media (min-width: 1200px) {
		.last-login {
			max-height: 22rem;
		}
	}
</style>