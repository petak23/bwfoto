<script>
/**
 * Komponenta pre užívateľkú ponuku - neprihlásený užívateľ.
 * Posledna zmena 10.10.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
export default {
	props: {
		regLink: {
			type: String,
			default: "0",
		},
		logInLink: {
			type: String,
			required: true,
		}
	}
}
</script>

<template>
	<div>
		<a 
			:href="logInLink" 
			:title="$store.state.texts.base_link_to_admin_log_in" 
		> <!-- role="button" aria-label="{$mu->nazov|webalize}" class="" -->
			<i class="fas fa-sign-in-alt pe-2"></i>
			{{ $store.state.texts.base_link_to_admin_log_in }}
		</a>
		<a
			v-if="regLink != '0'"
			:href="regLink"
			class="ml-2 pl-2 border-left border-secondary"
		>
			Registrácia
		</a>
	</div>
</template>

<style lang="sass" scoped>

</style>