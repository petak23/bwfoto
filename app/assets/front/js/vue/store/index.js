import Vue from "vue";
import Vuex from "vuex";
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    main_menu: [],
    main_menu_open: [],
    main_menu_active: 0,
    //admin_menu: [],
    //user: {},
  },
  mutations: {
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
    /*SET_INIT_USER (state, user) {
      state.user = user
    },
    SET_INIT_ADMIN_MENU (state, admin_menu) {
      state.admin_menu = admin_menu
    }*/
  },
  actions: {
    loadMenu ({ commit }) {
      axios
        .get('Your API link', {
          //headers: {
          //  'Ocp-Apim-Subscription-Key': 'your key',
          //}
        })
        .then(response => response.data)
        .then(menu => {
          console.log(menu);
          commit('SET_INIT_MENU', menu)
        })
    }
  },
  modules: {},
});
