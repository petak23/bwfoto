<script setup>
/** 
 * Component BWfoto_Tree_Main
 * Posledná zmena(last change): 05.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 */
import { ref, watch } from 'vue'

import { useMainStore } from '../../store/main'
const store = useMainStore()

const props = defineProps({
	part: {
		type: String,
		default: "1",
	},
	ulClass: {
		type: String,
		default: "navbar-nav mr-md-2 flex-grow-1 justify-content-end"
	}
})

const menu_part = ref(null)

watch(() => store.main_menu, () => {
	store.main_menu.map(item => {
		if (item.id == -1*parseInt(props.part)) menu_part.value = item
	})
})
</script>

<template>
	<ul :class="props.ulClass" v-if="menu_part != null">
		<li class="nav-item"  v-for="item in menu_part.children" :key="item.id">
			<a  :href="item.link" 
					:title="item.name"
					class="nav-link"
					
			>
				{{ item.name }}
				<br v-if="item.tooltip != null" />
				<small v-if="item.tooltip != null">{{ item.tooltip }}</small>
			</a>
		</li>
	</ul>
</template>