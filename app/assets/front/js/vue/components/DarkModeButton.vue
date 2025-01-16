<script setup>
import { ref, onMounted } from "vue";

// Reaktívna premenná na ukladanie stavu tmavého režimu
const isDarkMode = ref(false);

// Funkcia na prepínanie tmavého a svetlého režimu
const toggleDarkMode = () => {
	isDarkMode.value = !isDarkMode.value;
	updateBodyClass();
};

// Funkcia na aktualizáciu tried na body elemente
const updateBodyClass = () => {
	if (isDarkMode.value) {
		document.body.classList.add("dark-mode")
		document.body.setAttribute('data-bs-theme', "dark")
		document.body.classList.remove("light-mode")
	} else {
		document.body.classList.add("light-mode")
		document.body.setAttribute('data-bs-theme', "light")
		document.body.classList.remove("dark-mode")
	}
};

// Nastavenie režimu podľa aktuálneho času
const setModeBasedOnTime = () => {
	const currentTime = new Date().getHours();
	isDarkMode.value = currentTime < 6 || currentTime >= 18; // Tmavý režim od 18:00 do 6:00
	updateBodyClass();
};

// Inicializácia režimu pri načítaní komponenty
onMounted(() => {
	setModeBasedOnTime();
});
</script>

<template>
	<button class="btn dark-mode-button" @click="toggleDarkMode"
		:class="isDarkMode ? 'btn-light' : 'btn-dark'"
		:title="isDarkMode ? 'Prepni na svetlý mód.' : 'Prepni na tmavý mód.'">
		<i class="fa-solid fa-lg" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
	</button>
</template>

<style scoped>
.dark-mode-button {
	position: fixed;
	bottom: 1rem;
	left: 1rem;
	width: 3rem;
	height: 3rem;
	border-radius: 50%;
}
</style>
