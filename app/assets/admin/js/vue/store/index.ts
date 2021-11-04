import Vue from "vue";
import Vuex from "vuex";
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    main_menu: {},
    user: {},
  },
  mutations: {
    SET_INIT_MENU (state, menu) {
      state.main_menu = menu
    },
    SET_INIT_USER (state, user) {
      state.user = user
    }
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
