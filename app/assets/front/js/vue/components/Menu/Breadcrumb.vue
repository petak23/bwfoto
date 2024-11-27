<script setup>
/**
 * Komponenta pre navigáciu "odrobinky".
 * Posledna zmena 27.11.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */
import { ref, onMounted } from 'vue'
import { BBreadcrumb, BBreadcrumbItem, BDropdown, BDropdownItem }	from 'bootstrap-vue-next'
import { useMainStore } from '../../store/main'
const store = useMainStore()

const submenu = ref([])

/**
 * @param {array} items main_menu items
 * @param {array} mmo main_menu_open 
 * @param {int} level 
 */
const getItem = (items, mmo, level) => {
	items.map((i) => {
		if (i.id == mmo[level]) {
			submenu.value.push(i)
			if (i.children != undefined && level < (mmo.length - 1)) {
				level++
				getItem(i.children, mmo, level)
				level--
			}
		}
	})
}

const getBreadcrumb = () => {
	submenu.value = []
	getItem(store.main_menu, store.main_menu_open, 0)
}

watch(() => store.main_menu_loadet, () => { // Sleduje, či došlo k zmene hl. menu
	if (parseInt(store.main_menu_active) != 0) {
		getBreadcrumb()
	}
})

onMounted(() => {
	if (parseInt(store.main_menu_active) != 0) {
		getBreadcrumb()
	}
})
</script>

<template>
	<div class="row">
		<BBreadcrumb 
			v-if="submenu !== null && submenu.length > 1"
			class="col breadcrumb-main"
		>
			<BBreadcrumbItem
				v-for="(ia, index) in submenu"
				:key="index"
				:to="ia.vue_link"
				:active="index == (submenu.length - 1)"
			>
				<BDropdown 
					v-if="typeof (ia.children) !== 'undefined' && ia.children.length && index != (submenu.length - 1)"
					split
					size="sm"
					split-variant="link"
					variant="link"
					:text="ia.name"
					:split-href="index != (submenu.length - 1) ? ia.link : null"
					class="m-0"
				> 
					<BDropdownItem 
						v-for="dit in ia.children"
						:key="dit.id"
						:to="dit.vue_link"
					>
						{{ dit.name }}
					</BDropdownItem>
				</BDropdown>
				<span v-else>{{ ia.name }}</span>
			</BBreadcrumbItem>
		</BBreadcrumb>
	</div>

</template>