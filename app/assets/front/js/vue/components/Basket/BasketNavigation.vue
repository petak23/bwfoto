<script setup>
/**
 * Komponenta pre vypísanie navigácie nákupu.
 * Posledna zmena 15.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */
import { ref, watch, onMounted } from "vue"
import { useBasketStore } from '../../store/basket.js'
const storeB = useBasketStore()

const items = ref([
	{ id: 1, key: "Obsah košíka", enabled: true },
	{ id: 2, key: "Adresa", enabled: false },
	{ id: 3, key: "Doprava a platba", enabled: false },
	{ id: 4, key: "Sumár", enabled: false },
	{ id: 5, key: "Ukončenie", enabled: false},
])

const emit = defineEmits(['basketViewPart'])

const getToPage = (id) => {
	emit('basketViewPart', id)
}

onMounted(() => {
	/** @TODO !!! */
	/*this.$root.$on("basket-nav-update", data => {  
			/ * formát prichádzajúcich dát: 
				 { id: x, enabled: true|false, view_part: y, disable_another: true|false } * /
			if (data.disable_another != undefined && data.disable_another) {
				for (let i = 0; i < this.items.length; i++) {
					this.items[i].enabled = false;
				}
			}
			if (parseInt(data.id) > 0 && parseInt(data.id) <= items.value.length) { // ošetrenie hraníc
				items.value[data.id - 1].enabled = data.enabled == true // ošetrenie, že to bude bool
			}
			if (this.$session.has('basket-nav')) this.$session.remove('basket-nav')
			this.$session.set('basket-nav', JSON.stringify(this.items))
			if (data.view_part != undefined) this.$root.$emit('basket-view-part', [{view_part: data.view_part}])
		})

		if (this.$session.has('basket-nav')) this.items = JSON.parse(this.$session.get('basket-nav'))
		this.$root.$emit('basket-update', [])*/
})
</script>

<template>
	<div class="d-flex justify-content-between mb-3">
		<div 
			class="flex-fill mx-1"
			v-for="i in items"
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