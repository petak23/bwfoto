<script>
/**
 * Komponenta pre užívateľkú ponuku - neprihlásený užívateľ.
 * Posledna zmena 24.04.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
export default {
	props: {
		buttonClass: {
			type: String,
			default: ""
		},
		view_registration_button: {
			type: Boolean,
			default: false
		}
	},
}
</script>

<template>
	<div>
		<a 
			:href="$store.state.logInLink"
			:title="$store.state.texts.log_in"
			:class="buttonClass" 
		> 
			<i class="fa-solid fa-arrow-right-to-bracket"></i>
		</a>
		<a
			v-if="view_registration_button && $store.state.regLink != null"
			:href="$store.state.regLink"
			:class="buttonClass"
			:title="$store.state.texts.register"
		>
			<i class="fa-solid fa-user-plus"></i>
		</a>
	</div>
</template>