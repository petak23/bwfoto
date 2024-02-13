<script>
/**
 * Komponenta pre načítanie hl. menu a textov prekladov.
 * Posledna zmena 17.01.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */

import MainService from '../../services/MainService.js'

export default {
	props: {
		main_menu_active: { //Aktuálna aktívna polozka
			type: String,
			default: 0,
		},
		id_hlavne_menu_lang: {
			type: String,
			default: 0,
		},
		id_user_main: {
			type: String,
			default: 0,
		}
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
			MainService.getMenuFront()
				.then(response => {
					this.$store.dispatch('changeMainMenu', this.convert(response.data))
					//this.$store.commit('SET_INIT_MAIN_MENU', this.convert(response.data))
					this.$store.commit('SET_INIT_MAIN_MENU_OPEN', [])
					this.getpath(this.$store.state.main_menu)
					this.in_path = false
					this.$store.commit('SET_REVERSE_MAIN_MENU_OPEN')
					this.$root.$emit("main-menu-loadet", [])
				})
				.catch((error) => {
					console.log(error);
				});
		},

		// Načítanie prekladov textov
		getTexts() {
			let vm = this
			let data = { texts: this.$store.state.texts_to_load }
			MainService.postGetTexts(data)
				.then(function (response) {
					//console.log(response.data)
					vm.$store.commit('SET_INIT_TEXTS', response.data)
					vm.$root.$emit("main-texts-loadet", [])
				})
				.catch(function (error) {
					console.log(error)
				});
		},
		// Načítanie aktuálneho článku
		getArticle() {
			MainService.getOneMenuArticle(this.id_hlavne_menu_lang)
				.then(response => {
					this.$store.commit('SET_INIT_ARTICLE', response.data)
				})
				.catch((error) => {
					console.log(error);
				});
		},
		// Načítanie aktuálneho užívateľa
		getUser() {
			MainService.getActualUserInfo(this.id_user_main)
				.then(response => {
					this.$store.commit('SET_INIT_USER', response.data.user)
					if (typeof(this.$store.state.user.id) != 'undefined') {
						this.$root.$emit("user-loadet", [])
					}
				})
				.catch((error) => {
					console.error(error);
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
		const basePath = document.getElementById('vueapp').dataset.baseUrl

		// Zapísanie aktívnej položky menu
		this.$store.commit('SET_MAIN_MENU_ACTIVE', parseInt(this.main_menu_active))

		MainService.getFromSettings()
			.then(response => {
				this.$store.commit('SET_INIT_APP_SETTINGS', response.data.data)
			})
			.catch((error) => {
				console.log(error);
			});
		
		// Načítanie údajov priamo z DB
		this.getMenu()

		// Načítanie prekladov textov
		this.getTexts()

		// Načítanie aktuálneho článku
		this.getArticle()

		// Načítanie užívateľa
		if (parseInt(this.id_user_main) > 0) this.getUser()

		this.$store.commit('SET_INIT_BASE_PATH', basePath)

		this.$root.$on('reload-main-menu', data => {
			this.getMenu()
		})
	},
}
</script>

<template></template>