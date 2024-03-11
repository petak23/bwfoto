<script>
/**
 * Komponenta pre užívateľkú ponuku - prihlásený užívateľ.
 * Posledna zmena 11.03.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
export default {
	props: {
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
			name: "",
			texts: {},
		}
	},
	methods: {
		load_data() {
			this.name = this.$store.state.user.name
			this.texts.admin_text = this.$store.state.texts.base_AdminLink_name
			this.texts.log_out = this.$store.state.texts.log_out
		}
	},
	created() {
		this.load_data();
		// Reaguje na načítanie hl. menu
		this.$root.$on('user-loadet', data => {
			this.load_data();
		})
		this.$root.$on('main-texts-loadet', data => {
			this.load_data();
		})
		
	}
}
</script>

<template>
	<div class="btn-group user-lang-menu" role="group" aria-label="User-lang-menu">
		<a 
			:href="userLogLink" 
			:title="name" 
			role="button" 
			class="btn btn-outline-info btn-sm" 
		>
			{{ name }}
		</a>
		<a 
			v-if="adminLink != null"
			:href="adminLink" 
			:title="texts.admin_text" 
			role="button" 
			class="btn btn-outline-info btn-sm" 
		>
			{{ texts.admin_text }}
		</a>
		<a 
			v-if="adminerLink != null"
			:href="adminerLink" 
			role="button" 
			target="_blank"
			class="btn btn-outline-secondary btn-sm" 
		>
			<i class="fas fa-database"></i>
		</a>
		<a 
			:href="logOutLink" 
			:title="texts.log_out" 
			role="button" 
			class="btn btn-outline-warning btn-sm" 
		>
			<i class="fas fa-sign-out-alt pr-1"></i>
			{{ texts.log_out }}
		</a>
	</div>
</template>

<style lang="sass" scoped>

</style>