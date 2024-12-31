<script setup>
/**
 * Komponenta pre vypísanie navigácie nákupu.
 * Posledna zmena 22.12.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

const getToPage = (id) => {
	storeB.view_part = id
}
</script>

<template>
	<div class="d-flex justify-content-between mb-3">
		<div 
			class="flex-fill mx-1"
			v-for="i in storeB.navigation"
			:key="i.id"
		>
			<button 
				@click="getToPage(i.id)"	
				class="w-100 btn btn-sm nav-button"
				:class="[
					i.id == storeB.view_part ? 'btn-success disabled' : (i.enabled ? 'btn-secondary' : 'btn-outline-secondary disabled'),
				]"
				:disabled="i.enabled ? false : true"
			>
				{{ i.key }}
			</button>
		</div>
	</div>
</template>

<style scoped>
.nav-button:disabled {
	cursor: not-allowed;
	opacity: .5;
}
</style>