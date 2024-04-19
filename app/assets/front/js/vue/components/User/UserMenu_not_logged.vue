<script>
/**
 * Komponenta pre užívateľkú ponuku - neprihlásený užívateľ.
 * Posledna zmena 19.04.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
//export default {}
</script>

<template>
	<div>
		<a 
			:href="$store.state.logInLink"
			:title="$store.state.texts.base_link_to_admin_log_in" 
		> <!-- role="button" aria-label="{$mu->nazov|webalize}" class="" -->
			<i class="fas fa-sign-in-alt pe-2"></i>
			{{ $store.state.texts.base_link_to_admin_log_in }}
		</a>
		<a
			v-if="$store.state.regLink != null"
			:href="$store.state.regLink"
			class="ml-2 pl-2 border-left border-secondary"
		>
			Registrácia
		</a>
	</div>
</template>