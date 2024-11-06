<script setup>
import { watch, onMounted } from 'vue'
import { useMainStore } from '../store/main'
import MainService from '../services/MainService';

const store = useMainStore()

const getMySettings = () => {
	MainService.getMySettings()
		.then(response => {
			//console.log(response.data)
			store.udaje_webu = response.data
		})
		.catch((error) => {
			console.log(error);
		})
}

// Načítanie prekladov textov
const getTexts = () => {
	let data = { texts: store.texts_to_load }
	MainService.postGetTexts(data)
		.then(function (response) {
			store.texts = response.data
			store.texts_loadet = true
		})
		.catch(function (error) {
			console.error(error)
		});
}

watch(() => store.user, () => {
	getMySettings()
})

onMounted(() => {
	// Načítanie nastavení
	getMySettings()

	// Načítanie prekladov textov
	getTexts()

	// Načítanie aktuálneho užívateľa
	store.getActualUser()
})
</script>

<template></template>