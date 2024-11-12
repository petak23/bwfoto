<script setup>
/** 
 * Component BWfoto_Fixed_Homepage
 * Posledná zmena(last change): 12.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 */
import { ref, computed, watch } from 'vue'

import { useMainStore } from '../../store/main'
const store = useMainStore()

import part_small from "./BWfoto_Fixed_Homepage/part_small.vue"
import figureMy from "./BWfoto_Fixed_Homepage/figureMy.vue"

const props = defineProps({
	part: { // id_hlavne_menu_cast
		type: Number,
		default: 1,
	},
	ulClass: {
		type: String,
		default: "navbar-nav mr-md-2 flex-grow-1 justify-content-end"
	}
})

const menu_part = ref(null)

const menu = computed(() => {
	const steps = [0, 3, 1, 4, 2, 5]
	let out = []
	let tmp = { f: null, s: null}
	let menu_s = []
	if (menu_part.value != null) {
		steps.forEach(i => {
			menu_s.push(menu_part.value.children[i])
		})
		menu_s.forEach((item, index) => {
			if (index % 2 == 0) {
				tmp.f = item
			} else {
				tmp.s = item
				out.push(tmp)
				tmp = { f: null, s: null }
			}
		})
	}
	return out 
})

watch(() => store.main_menu, () => {
	store.main_menu.map(item => {
		if (item.id == -1*props.part) menu_part.value = item
	})
})
</script>

<template>
	<div>

	<!-- kategorie webu lg+ -->
	<section id="webParts" class="container pb-3" v-if="menu_part != null">
		<div class="row w-100" v-if="menu != undefined">
			<div
				v-for="(node, index) in menu"
				:key="index"
				class="col d-flex" 
				:class="index != 1 ? 'flex-column justify-content-between' : 'flex-column-reverse justify-content-around'"
				
			>
				<figureMy
					v-if="node.f != undefined"
					:item="node.f"
					:avatarDir="store.udaje_webu.config.dir_to_menu"
					:if_part="index != 1"
				>
				</figureMy>
				<part_small
					v-if="node.s != undefined"
					:item="node.s"
					main_class="d-flex flex-column justify-content-center"
					:bolder="node.s.id == 5"
				>
				</part_small>
			</div>
		</div>
	</section>

	<!-- kategorie webu md- -->
	<section id="webParts-md" class="container d-lg-none pb-3" v-if="menu_part != null">
		<div class="row w-100">
			<div class="col-md d-flex flex-column justify-content-between">
				<figureMy
					:item="menu_part.children[0]"
					:avatarDir="store.udaje_webu.config.dir_to_menu"
					:if_part="true"
				>
				</figureMy>
				<figureMy
					:item="menu_part.children[1]"
					:avatarDir="store.udaje_webu.config.dir_to_menu"
				>
				</figureMy>
			</div>
			<div class="col-md d-flex flex-column justify-content-between">
				<figureMy
					:item="menu_part.children[2]"
					:avatarDir="store.udaje_webu.config.dir_to_menu"
					:if_part="true"
				>
				</figureMy>
				<figure class="d-flex flex-column justify-content-start justify-content-md-between">
					<part_small	:item="menu_part.children[3]"></part_small>
					<part_small
						:item="menu_part.children[4]"
						:bolder="true"
					></part_small>
					<part_small	:item="menu_part.children[5]"></part_small>
				</figure>
			</div>
		</div>
	</section>
	</div>
</template>