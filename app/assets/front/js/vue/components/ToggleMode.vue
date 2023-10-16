<script>
import { FontAwesomeIcon } from "./../plugins/fontawesome.js"; // Uistite sa, že importujete správne

export default {
	components: {
		FontAwesomeIcon,
	},
	data() {
		return {
			isDarkMode: false,
		};
	},
	methods: {
		toggleDarkMode() {
			this.isDarkMode = !this.isDarkMode;
			// Emitujte udalosť 'darkModeChanged' s aktuálnym stavom isDarkMode
			this.$root.$emit("darkModeChanged", this.isDarkMode);
		},
	},
};
</script>

<template>
	<a class="dark-mode-button" @click="toggleDarkMode" 
		:title="isDarkMode ? 'Prepni na svetlý mód.' : 'Prepni na tmavý mód.'">
		<font-awesome-icon :icon="isDarkMode ? 'sun' : 'moon'" />
	</a>
</template>


<style scoped>
.dark-mode-button {
	position: fixed;
	bottom: 1rem;
	left: 1rem;
	width: 2rem;
	height: 2rem;
	background-color: #333;
	border-radius: 50%;
	display: flex;
	justify-content: center;
	align-items: center;
	cursor: pointer;
}
a {
	color: #ccc;
}
</style>
