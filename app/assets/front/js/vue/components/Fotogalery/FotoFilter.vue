<script setup>
/** 
 * Component FotoFilter
 * Posledná zmena(last change): 14.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */
import { ref, onMounted } from 'vue'

import { BDropdown, BDropdownItem } from 'bootstrap-vue-next';

const props = defineProps({
	val: {
		type: Number,
		default: 1
	},
})

const choice = ref(1)
const data = ref([{id: 1, txt: "Všetky"}, {id: 2, txt: "Len na sklade"}])

const emit = defineEmits(['filter-change'])

const change = (id) => {
	choice.value = id
	emit('filter-change', choice.value)
}

onMounted(() => {
	choice.value = props.val
})
</script>

<template>
	<BDropdown text="Dropdown Button" class="me-2" variant="outline-secondary" toggle-class="px-1 py-2 me-1" end>
		<template #button-content>
			<i class="fa-solid fa-filter me-2"></i><span class="me-1">{{ data[choice - 1].txt }}</span>
		</template>
		<BDropdownItem 
			v-for="i in data"
			:key="i.id"
			@click="change(i.id)"
		>
			<i 
				class="fa-regular"
				:class="choice == i.id ? 'fa-square-check' : 'fa-square'"
			></i> {{ i.txt }}
		</BDropdownItem>
	</BDropdown>

	<!--div-- class="btn-group">
		<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<i class="fa-solid fa-filter mr-2"></i>{{ data[choice - 1].txt }}
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			<button 
				v-for="i in data"
				class="dropdown-item" 
				type="button" 
				@click="change(i.id)"
				:key="i.id"
			>
				<i 
					class="fa-regular"
					:class="choice == i.id ? 'fa-square-check' : 'fa-square'"
				></i> {{ i.txt }}
			</button>
		</div>
	</!--div-->
</template>

<style scoped>
button.disabled {
	color: #888 !important; 
}
</style>