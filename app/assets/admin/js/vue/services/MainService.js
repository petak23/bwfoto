import axios from 'axios'

const baseUrl = document.getElementById('app').dataset.baseUrl + "/api/"

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

	// ---- menu -----
	getMenuFront() {
		return apiClient.get('menu/getmenu/0/front')
	},
	getSubmenuFront(main_menu_active) {
		return apiClient.get('menu/getsubmenu/' + main_menu_active + '/front')
	},
	getOneMainMenuArticle(id_hlavne_menu){
		return apiClient.get('menu/getonehlavnemenuarticle/' + id_hlavne_menu)
	},
	getOneMenuArticle(id_hlavne_menu_lang) {
		return apiClient.get('menu/getonemenuarticle/' + id_hlavne_menu_lang)
	},
	getFotoCollageSettings(id_hlavne_menu) {
		return apiClient.get('menu/getfotocollagesettings/' + id_hlavne_menu)
	},
	getForFormTemplate() {
		return apiClient.get('menu/getforformtemplate')
	},
	postSaveFotoCollageSettings(id_hlavne_menu, data) {
		return apiClient.post('menu/savefotocollagesettings/' + id_hlavne_menu, data)
	},
	postSaveOrderSubmenu(id_hlavne_menu, data) {
		return apiClient.post('menu/saveordersubmenu/' + id_hlavne_menu, data)
	},
	postH1Save(id, data) {
		return apiClient.post('menu/h1save/' + id, data)
	},
	postTextSave(id, data) {
		return apiClient.post('menu/textssave/' + id, data)
	},
	postSaveMainMenuField(id_hlavne_menu, data) {
		return apiClient.post('menu/savemainmenufield/' + id_hlavne_menu, data)
	},
	postSaveBorder(id, data) {
		return apiClient.post('menu/saveborder/' + id, data)
	},

	// ---- verzie ----
	getVersion(id) {
		return apiClient.get('verzie/getversion/' + id)
	},
	postSaveVerzie(id, number, text) {
		return apiClient.post('verzie/save/' + id, [number, text])
	},

	// ---- slider ----
	getSliderDelete(id) {
		return apiClient.get('slider/delete/' + id)
	},
	getSliderAll() {
		return apiClient.get('slider/getall')
	},
	postSliderSaveOrderVerzie(items) {
		return apiClient.post('slider/saveorder', {	items: items })
	},
	postSliderUpdate(id, data) {
		return apiClient.post('slider/update/' + id, data)
	},
	
	
}