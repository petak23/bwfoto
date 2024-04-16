import axios from 'axios'

const baseUrl = document.getElementById('vueapp').dataset.baseUrl + "/api/"

//axios.defaults.withCredentials = true;

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const apiClient = axios.create({
	baseURL: baseUrl,
	withCredentials: false, // This is the default
	headers: {
		Accept: 'application/json',
		'Content-Type': 'application/json'
	},
	timeout: 10000,
})

export default {
	// ---- nakup ----
	getAllNakupStatus(id_user_main) {
		return apiClient.get('products/getnakupstatus')
	},
	changeNakupStatus(id_nakup, change_to) {
		return apiClient.post('products/changenakupstatus', { id_nakup: id_nakup, change_to: change_to })
	},

	// ---- user ----
	getUsersForSpravca() {
		return apiClient.get('user/userchangeformusers/4')
	},

	// ---- udaje ----
	postSaveUdaj(key, val) {
		return apiClient.post('udaje/saveudaj', { key: key, val: val })
	},
}