<script>
/**
 * Komponenta pre užívateľkú ponuku.
 * Posledna zmena 11.10.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */

import UserMenu_not_logged from "./UserMenu_not_logged";
import UserMenu_logged_in from "./UserMenu_logged_in";

export default {
	components: {
		UserMenu_logged_in,
		UserMenu_not_logged,
	},
	props: {
		regLink: {
			type: String,
			default: "0",
		},
		logInLink: {
			type: String,
			required: true,
		},
		userLogLink: {
			type: String,
			required: true,
		},
		adminLink: {
			type: String,
			default: null,
		},
		adminerLink: {
			type: String,
			default: null,
		},
		logOutLink: {
			type: String,
			required: true,
		}
	},
	data() {
		return {
			logged_in: false,
		}
	},
	created() {
		this.logged_in = this.$store.state.user != null
		// Reaguje na načítanie hl. menu
		this.$root.$on('user-loadet', data => {
			this.logged_in = this.$store.state.user != null
		})
	}
}
</script>

<template>
	<div>
		<user-menu_logged_in 
			v-if="logged_in"
			:user-log-link="userLogLink"
			:admin-link="adminLink"
			:adminer-link="adminerLink"
			:log-out-link="logOutLink"
		></user-menu_logged_in>
		<user-menu_not_logged 
			v-else
			:reg-link="regLink"
			:log-in-link="logInLink"
		></user-menu_not_logged>
	</div>
</template>

<style lang="sass" scoped>

</style>