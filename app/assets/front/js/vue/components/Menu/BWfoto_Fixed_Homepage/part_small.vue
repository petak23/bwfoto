<script setup>
/** 
 * Component BWfoto_Fixed_Homepage -> part_small
 * Posledná zmena(last change): 25.09.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

const props = defineProps({
	item: {
		type: Object,
		required: true,
	},
	main_class: {
		type: String,
		default: "h-md-25",
	},
	bolder: {
		type: Boolean,
		default: false
	}
})

const iclass = computed(() => {
	return 'fa-' + props.item.node_class + (props.bolder ? 'bolder' : ''  )
})
</script>

<template>
	<RouterLink
		v-if="item != undefined"
		:to="item.vue_link" 
		:title="item.name" 
		class="part-small"
		:class="main_class">
		<h2 class="h3 text-center">
			<i
				class="fas mr-1"  
				:class="iclass">
			</i>
			{{ item.name }}
		</h2>
	</RouterLink>
</template>