<script setup>
/** 
 * Component Slider
 * Posledná zmena(last change): 06.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */
import { ref, watch } from 'vue'
import MainService from '../services/MainService';
import { useMainStore } from '../store/main.js'
const store = useMainStore()

const props = defineProps({
	filesPath: { // Adresár k súborom
		type: String,
		required: true
	},
})

const items = ref(null)

const getSlider = () => {
	MainService.getSlider()
		.then(response => {
			//console.log(response.data)
			items.value = response.data
			if (items.value !== null) { 
				let show = findIn()
				if (show != null) {
					// TODO - toto
					//this.$root.$refs.myslider.style.backgroundImage = 'url(' + this.filesPath + show.subor + ')'
				}
			}
		})
		.catch((error) => {
			console.error(error);
		});
}

/*
 * Najdenie poloziek slidera */
const findIn = () => {
	let vysledok = false
	let _v = null
	// Nájdi priamo daný klúč
	let sl_item = items.value.find(el => parseInt(el.zobrazenie) == store.main_menu_active)
	if (sl_item == undefined) { // Ak nieje ...
		let vysa = [];
		items.value.forEach((item, index) => {
			let s = item.zobrazenie
			if (s === null) {
				vysledok = true
			} else {
				_v = item.zobrazenie.indexOf(",") !== -1 ? item.zobrazenie.split(",") : item.zobrazenie
				vysledok = false
				if (Array.isArray(_v)) {
					_v.forEach((z) => {
						vysledok = zisti(parseInt(z)) ? true : vysledok
					})
				} else {
					vysledok = zisti(parseInt(_v));
				}
			}
			if (vysledok) {
				vysa.push(index);
			}
		})
		sl_item = vysa.length ? items.value[vysa[0]] : null
	}
	return sl_item
}

/* Pre vyhodnotenie zobrazenia položky z*/
const zisti = (z) => {
	return z == null ? true
		: (z == 0 ? true
			: (z > 0 && (store.main_menu_open.find(el => el == parseInt(z) !== undefined) )))
}

watch(() => store.main_menu_active, () => {
	if (store.main_menu_active !== undefined) {
		getSlider()
	}
})
</script>
<template>
</template>