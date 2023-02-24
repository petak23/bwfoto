import Vue from "vue";
import Vuex from "vuex";
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		apiPath: "", // Cesta k API

		main_menu: [],
		main_menu_open: [],
		main_menu_active: 0,

		article: {
			id_user_main: 0,
		},
		//admin_menu: [],
		//user: {},
		texts_to_load: ['base_edit_title', 'base_edit_texts', 'base_to_admin', 
										'base_last_change', 'base_platnost_do', 'base_zadal',
										'galery_arrows_before', 'galery_arrows_after'
									 ],
		texts: {},
	},
	mutations: {
		SET_INIT_API_PATH (state, apiPath) {
			state.apiPath = apiPath
		},
		SET_INIT_MAIN_MENU (state, main_menu) {
			state.main_menu = main_menu
		},
		SET_INIT_MAIN_MENU_OPEN (state, main_menu_open) {
			state.main_menu_open = main_menu_open
		},
		SET_PUSH_MAIN_MENU_OPEN (state, push_id) {
			state.main_menu_open.push(push_id)
		},
		SET_REVERSE_MAIN_MENU_OPEN (state) {
			state.main_menu_open.reverse()
		},
		SET_MAIN_MENU_ACTIVE (state, main_menu_active) {
			state.main_menu_active = main_menu_active
		},
		SET_INIT_ARTICLE(state, article) {
			state.article = article
		},
		/*SET_INIT_USER (state, user) {
			state.user = user
		},
		SET_INIT_ADMIN_MENU (state, admin_menu) {
			state.admin_menu = admin_menu
		}*/
		SET_INIT_TEXTS (state, texts) {
			state.texts = texts
		},
		UPDATE_ARTICLE_FIELD (state, field, data) {
			state.article[field] = data
		}
	},
	actions: {
		changeUserMainId ({ commit, state }, id_user_main) {
			let odkaz = state.apiPath + 'menu/savemainmenufield/' + state.article.id_hlavne_menu
			let vm = this
			let data = {
							id_user_main: id_user_main
						}
			axios.post(odkaz, {
						data: data,
						id_hlavne_menu_lang: state.article.id
					})
				.then(function (response) {
					//console.log(response.data.result)
					commit('SET_INIT_ARTICLE', response.data)
				})
		}
	},
	getters: {
		id_user_main: state => {
			return state.article.id_user_main;
		}
	}
});
