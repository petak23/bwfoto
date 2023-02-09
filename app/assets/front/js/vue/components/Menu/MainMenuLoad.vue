<script>
/**
 * Komponenta pre načítanie hl. menu.
 * Posledna zmena 07.02.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */

import axios from 'axios'


//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
	props: {
		basePath: {
			type: String,
			required: true
		},
		main_menu_active: { //Aktuálna aktívna polozka
			type: String,
			default: 0,
		},
	},
	data: () => ({
		in_path: false,
	}),
	computed: {},
	methods: {
		convert(itemsObject) {
			return Object.values(itemsObject).map(item => ({
				...item,
				children: item.children ? this.convert(item.children) : undefined,
			}));
		},
		getpath(item) {
			var self = this
			item.map(function(i) {
				if (self.in_path == false) {
					if (i.id == self.main_menu_active) {
						self.$store.commit('SET_PUSH_MAIN_MENU_OPEN', i.id)
						self.in_path = true
					} else if (typeof i.children !== 'undefined' && i.children.length > 0) {
						self.getpath(i.children)
						if (self.in_path) {
							self.$store.commit('SET_PUSH_MAIN_MENU_OPEN', i.id)
						}
					}
				}
			})
		},
		getMenu() {
			let odkaz = this.basePath + '/api/menu/getmenu/0/Front'
			axios.get(odkaz)
				.then(response => {
					this.$store.commit('SET_INIT_MAIN_MENU', this.convert(response.data))
					this.$store.commit('SET_INIT_MAIN_MENU_OPEN', [])
					this.getpath(this.$store.state.main_menu)
					this.in_path = false
					this.$store.commit('SET_REVERSE_MAIN_MENU_OPEN')
					this.$root.$emit("main-menu-loadet", [])
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		},
	},
	watch: {
		// Zapísanie aktívnej položky menu
		main_menu_active: function (newMainMenuActive) {
			this.$store.commit('SET_MAIN_MENU_ACTIVE', parseInt(this.main_menu_active))
		}
	},
	mounted() {
		// Zapísanie aktívnej položky menu
		this.$store.commit('SET_MAIN_MENU_ACTIVE', parseInt(this.main_menu_active))
		// Načítanie údajov priamo z DB
		this.getMenu()
	},
}
</script>

<template></template>