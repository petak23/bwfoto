<script setup>
/** 
 * Component BWfoto_Tree_Main
 * Posledná zmena(last change): 09.07.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 * 
 */
//import { ref, watch, onMounted } from 'vue'
import { RouterLink } from 'vue-router'

import { useMainStore } from '../../store/main'
const store = useMainStore()

const props = defineProps({
	ulClass: {
		type: String,
		default: "navbar-nav mr-md-2 flex-grow-1 justify-content-end"
	}
}) 

/*const menu_part = ref(null)
const single_menu = ref([])

/ **
 * @param {array} items main_menu items
 * @param {array} mmo main_menu_open 
 * @param {int} level 
 * /
const getItem = (items, mmo, level) => {
	//let self = this
	items.map(function (i) {
		if (i.id == mmo[level]) {
			single_menu.value = i
			if (typeof (i.children) != undefined && level < (mmo.length - 1)) {
				level++
				getItem(i.children, mmo, level)
				level--
			}
		}
	})
}

const getSingleMenu = () => {
	single_menu.value = []
	getItem(store.main_menu, store.main_menu_open, 0)
}

watch(store.main_menu_open, (newMainMenuOpen) => {
	getSingleMenu()
})

watch(store.main_menu_loadet, (newMainMenuLoadet) => {
	getSingleMenu()
}) 

onMounted(() => {
	getSingleMenu()
})*/
</script>

<template>
	<div class="list-group mt-3" :class="props.ulClass" v-if="store.sub_menu != null">
			<RouterLink  
				v-for="item in store.sub_menu" :key="item.id"
				:to="item.vue_link" 
				:title="item.name"
				class="list-group-item list-group-item-action bg-transparent"
			>
				{{ item.name }}
				<br v-if="item.tooltip != null" />
				<small v-if="item.tooltip != null">{{ item.tooltip }}</small>
			</RouterLink>
		
	</div>
	<!--ul :class="props.ulClass" v-if="single_menu != null">
		<li class="nav-item" v-for="item in single_menu.children" :key="item.id">
			<a  
				:href="item.link" 
				:title="item.name"
				class="nav-link btn btn-outline-secondary"
					
			>< !--:class="item.getItemClass('active')"-- >
				{{ item.name }}
				<br v-if="item.tooltip != null" />
				<small v-if="item.tooltip != null">{{ item.tooltip }}</small>
			</a>
		</li>
	</ul-->
</template>