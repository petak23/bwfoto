<script setup>
/**
 * Komponenta pre užívateľkú ponuku - neprihlásený užívateľ.
 * Posledna zmena 21.08.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
import { RouterLink } from 'vue-router'
import { useMainStore } from '../../store/main'
const store = useMainStore()

/**
 * TODO udaje_webu - regLink - už nie je potrebný !!!
 */

</script>

<template>
	<div>
		<RouterLink
			to="/login"
			:title="store.texts.log_in"
			class="btn btn-success"
		> 
			<i class="fa-solid fa-arrow-right-to-bracket me-2"></i>{{ store.texts.log_in }}
		</RouterLink>
		<RouterLink
			v-if="parseInt(store.udaje_webu.registracia_enabled) == 1 && store.udaje_webu.regLink != null"
			to="/registration"
			class="btn btn-info"
			:title="store.texts.register"
		>
			<i class="fa-solid fa-user-plus me-2"></i>{{ store.texts.register }}
		</RouterLink>
	</div>
	
</template>