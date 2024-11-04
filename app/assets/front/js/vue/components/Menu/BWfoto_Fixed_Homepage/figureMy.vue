<script setup>
/** 
 * Component BWfoto_Fixed_Homepage -> figure
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
		default: "",
	},
	avatarDir: { // Kompletná cesta vrátane basePath
		type: String,
		default: ""
	},
	if_part: {
		type: Boolean,
		default: false
	},
}) 

const avatar = computed(() => {
	return props.avatarDir.length && props.item.avatar != null ? props.avatarDir + props.item.avatar : false
})
</script>

<template>
	<figure 
		class="w-img"
		:class="main_class"
		v-if="item != undefined"
	>
		<RouterLink
			:to="item.vue_link"
			:title="item.name">
			<img 
				v-if="avatar"
				:src="avatar"
				class="img-fluid"
				:alt="item.name"
			>
			<figcaption class="text-center w-100">
				<h2 
					class="py-2"
					:class="if_part ? 'black-curtain m-0 h3' : 'white-curtain text-body'"
				>
					{{ props.item.name }}
				</h2>
			</figcaption>
		</RouterLink>
	</figure>
</template>