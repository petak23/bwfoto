<script setup>
/**
 * Komponenta pre načítanie hl. menu a textov prekladov.
 * Posledna zmena 14.01.2025
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.0
 */

import { ref, watch, onMounted } from 'vue'
import MainService from '../../services/MainService.js'

import { useMainStore } from '../../store/main'
const store = useMainStore()
import { useFlashStore } from '../../../../../components/FlashMessages/store/flash'
const storeF = useFlashStore()

const props = defineProps({
	id_hlavne_menu_lang: {
		type: Number,
		default: 0,
	},
	
})

const in_path = ref(false)

const convert = (itemsObject) => {
	return Object.values(itemsObject).map(item => ({
		...item,
		children: item.children ? convert(item.children) : undefined,
	}));
}

const getpath = (item) => {
	item.map((i) => {
		if (in_path.value == false) {
			if (i.id == store.main_menu_active) {
				store.push_main_menu_open(i)
				in_path.value = true
			} else if (i.children !== undefined && i.children.length > 0) {
				getpath(i.children)
				if (in_path.value) { store.push_main_menu_open(i) }
			}
		}
	})
}

const changeActiveItemOfMenu = () => {
	store.main_menu_open = []
	if (store.main_menu_active > 1) {
		getpath(store.main_menu)
		in_path.value = false
		store.main_menu_open.reverse()
	}
}

const getMenu = () => {
	MainService.getMenuFront()
		.then(response => {
			store.changeMainMenu(convert(response.data))
			changeActiveItemOfMenu()
			store.main_menu_loadet = true
			if (store.user != null && store.user.user_role == 'admin')
				setTimeout(() => storeF.showMessage('Menu sa načítalo.', 'success', 'Menu ...', 2000), 100)
		})
		.catch((error) => {
			console.error(error);
		});
}

// Načítanie aktuálneho článku
const getArticle = () => {
	if (store.main_menu_active != 0) {
		MainService.getOneMenuArticle(store.main_menu_active)
			.then(response => {
				store.article = response.data
			})
			.catch((error) => {
				console.error(error);
			});
	} else {
		store.article = null
	}
}

watch(() => props.id_hlavne_menu_lang, (newIdHlavneMenuLang) => {
	//console.log(newIdHlavneMenuLang)
	store.main_menu_active = newIdHlavneMenuLang
	store.main_menu_changed = true
	//getArticle()
	//changeActiveItemOfMenu()
	//store.getSubmenu(store.main_menu_active)
})

watch(() => store.user, () => {
	getMenu()
	changeActiveItemOfMenu()
})

watch(() => store.main_menu_changed, () => { // Sleduje, či došlo k zmene hl. menu
	if (store.main_menu_changed) {
		getMenu()
		getArticle()
		changeActiveItemOfMenu()
		store.getSubmenu(store.main_menu_active)
		store.main_menu_changed = false
	}
})


onMounted(() => {
	// Zapísanie aktívnej položky menu
	store.main_menu_active = props.id_hlavne_menu_lang
	
	// Načítanie údajov priamo z DB
	getMenu()

	// Načítanie aktuálneho článku
	getArticle()
})
</script>

<template></template>