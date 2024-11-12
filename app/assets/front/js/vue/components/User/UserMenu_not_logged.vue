<script setup>
/**
 * Komponenta pre užívateľkú ponuku - neprihlásený užívateľ.
 * Posledna zmena 12.11.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.6
 */
import { RouterLink } from 'vue-router'
import { useMainStore } from '../../store/main'
const store = useMainStore()

const props = defineProps({
	viewTexts: { // Zobrazenie textov tlačítok
		type: Boolean,
		default: false
	}
})

</script>

<template>
	<RouterLink
		to="/login"
		:title="store.texts.log_in"
		class="btn btn-light ms-2"
	> 
		<i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
		<span v-if="props.viewTexts">{{ store.texts.log_in }}</span>
	</RouterLink>
	<RouterLink
		v-if="parseInt(store.udaje_webu.registracia_enabled) == 1"
		to="/registration"
		class="btn btn-light ms-2"
		:title="store.texts.register"
	>
		<i class="fa-solid fa-user-plus me-2"></i>
		<span v-if="props.viewTexts">{{ store.texts.register }}</span>
	</RouterLink>
</template>