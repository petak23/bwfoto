<script setup>
/**
 * Komponenta pre navigáciu "odrobinky".
 * Posledna zmena 28.11.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
import { ref, watch, onMounted } from 'vue'
import { BBreadcrumb, BBreadcrumbItem, BDropdown, BDropdownItem, BLink }	from 'bootstrap-vue-next'
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
		if (i.id == mmo[level].id) {
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
	if (store.main_menu_active != 0) {
		submenu.value = []
		getItem(store.main_menu, store.main_menu_open, 0)
	}
}

watch(() => store.main_menu_active, () => { // Sleduje, či došlo k zmene hl. menu
	getBreadcrumb()
})

watch(() => store.main_menu_loadet, () => {
	getBreadcrumb()
})

onMounted(() => {
	getBreadcrumb()
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
				:active="index == (submenu.length - 1)"
			>
				<BDropdown 
					v-if="typeof (ia.children) !== 'undefined' && ia.children.length && index != (submenu.length - 1)"
					split
					size="sm"
					split-variant="link"
					variant="link"
					:split-to="ia.vue_link"
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
				<BLink v-else :to="ia.vue_link">{{ ia.name }}</BLink>
			</BBreadcrumbItem>
		</BBreadcrumb>
	</div>

</template>