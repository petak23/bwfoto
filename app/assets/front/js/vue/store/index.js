import Vue from "vue";
import Vuex from "vuex";
import MainService from "../services/MainService";

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		basePath: "", 	// Základná cesta
 		main_menu: null,
		main_menu_open: [],
		main_menu_active: null,

		app_settings: null,
		article: {
			id_user_main: 0,
		},
		//admin_menu: [],
		user: null,
		texts_to_load: ['base_edit_title', 'base_edit_texts', 'base_to_admin', 
										'base_last_change', 'base_platnost_do', 'base_zadal',
										'galery_arrows_before', 'galery_arrows_after',
										'autocomplete_placeholder', 'autocomplete_searching',
										'autocomplete_min_char', 'autocomplete_not_found',
										'clanky_h3_prilohy_images', 'clanky_h3_prilohy_audios',
										'clanky_h3_prilohy_others',
										'clanky_dokument_download', 'clanky_dokument_view',
										'base_link_to_admin_log_in', 'base_AdminLink_name',
										'log_in', 'log_out', 'register'
									],
		texts: {},

		basket: {
			view_part: 1,
		}
	},
	mutations: {
		SET_INIT_APP_SETTINGS (state, app_settings) {
			state.app_settings = app_settings
		},
		SET_INIT_BASE_PATH (state, basePath) {
			state.basePath = basePath
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
		SET_INIT_USER (state, user) {
			state.user = user
		},
		/*SET_INIT_ADMIN_MENU (state, admin_menu) {
			state.admin_menu = admin_menu
		}*/
		SET_INIT_TEXTS (state, texts) {
			state.texts = texts
		},
		UPDATE_ARTICLE_FIELD (state, field, data) {
			state.article[field] = data
		},
		UPDATE_BASKET_VIEW_PART (state, view_part) {
			state.basket.view_part = view_part
		}
	},
	actions: {
		changeUserMainId ({ commit, state }, id_user_main) {
			let vm = this
			let data = {
							id_user_main: id_user_main
						}
			MainService.postSaveMainMenuField(state.article.id_hlavne_menu, {
						data: data,
						id_hlavne_menu_lang: state.article.id
					})
				.then(function (response) {
					commit('SET_INIT_ARTICLE', response.data)
				})
		},
		changeMainMenu ({ commit, state }, main_menu) {
			state.main_menu = []
			commit('SET_INIT_MAIN_MENU', main_menu)
		},

		// check_perm = { 'resource': 'some:resource', 'action': 'some_action_or_null' }
		/* checkPermission({ state }, check_perm) { 
			let ok = false
			state.user.permission.forEach(function check(item) { 
				if (item.resource == check_perm.resource) {
					ok = (item.action == null || (item.action.isArray && item.action.includes(check_perm.action)))
				}
			})
			return new Promise((resolve, reject) => { 
				if (ok) resolve() 
			}) 
		}*/
	},
	getters: {
		id_user_main: state => {
			return state.article.id_user_main;
		}
	}
});
