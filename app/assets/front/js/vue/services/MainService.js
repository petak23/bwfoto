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
	// ---- user ----
	getActualUserInfo(id_user_main) {
		return apiClient.get('user/getactualuserinfo/' + id_user_main)
	},
	getUserChangeFormUsers(min_ur_reg = 5) {
		return apiClient.get('user/userchangeformusers/' + min_ur_reg)
	},
	postIsAllowed(id_user, data) {
		return apiClient.post('user/isallowed/' + id_user, data)
	},
	getActualUserProfile() {
		return apiClient.get('user/getactualuserprofile')
	},
	getUserInfo(id_user_main) {
		return apiClient.get('user/getuserinfo/' + id_user_main)
	},
	testUserEmail(email) {
		return apiClient.post('user/testuseremail', {email: email})
	},
	postForgottenPassword(email) {
		return apiClient.post('user/forgottenpassword', {email: email})
	},

	// ---- sign ----
	postSignIn(data) {
		return apiClient.post('sign/in', data)
	},

	// ---- menu ----
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

	// ---- documents ----
	getFotogalery(id_hlavne_menu, filter = 1) {
		return apiClient.get('documents/getfotogalery/' + id_hlavne_menu + (filter > 1 ? "?filter=" + filter : ""))
	},
	getFotoCollage(id) {
		return apiClient.get('documents/getfotocollage/' + id)
	},
	getDocument(id_document) {
		return apiClient.get('documents/document/' + id_document)
	},
	getVisibleAttachments(id_hlavne_menu, group = '') {
		return apiClient.get('documents/getvisibleattachments/' + parseInt(id_hlavne_menu) + (group.length ? '?group=' + group : ''))
	},
	

	// ---- slider ----
	getSlider(id = 1) {
		return apiClient.get('slider/getall/' + id)
	},

	// ---- lang ----
	getActLangs() {
		return apiClient.get('lang/getakclangs')
	},
	postGetTexts(data) {
		return apiClient.post('lang/gettexts', data)
	},

	// ---- search ----
	getSearch(inputname, searchquery) {
		return apiClient.get('search', {params: {[inputname]: searchquery}})
	},

	// ---- udaje ----
	getFromUdaj(name) {
		return apiClient.get('udaje/getfromudaj/' + name)
	},
	getFromSettings(name = "") {
		return apiClient.get('udaje/getfromsettings' + (name.length ? '/'+name : ''))
	},

	// ---- products ----
	getProductPropsCategories() {
		return apiClient.get('products/productpropscategories')
	},
	getProduct(id) {
		return apiClient.get('products/product/' + id)
	},
	postSaveProductProps(data) {
		return apiClient.post('products/saveproductprops', data)
	},
	postSaveNakup(data) {
		return apiClient.post('products/nakup', data)
	},


	
	// --- from vuemeteo...
	getMySettings() {
		return apiClient.get('homepage/myappsettings')
	},
	getDevices() {
		return apiClient.get('devices')
	},
	getDevice(id_device) {
		return apiClient.get('device/' + id_device)
	},
	getUnits() {
		return apiClient.get('units'/*, { headers: {
			Accept: 'application/json',
			'Content-Type': 'application/json',
      "Cache-Control": "no-cache",
      //"Content-Type": "application/x-www-form-urlencoded",
    }}*/)
	},
	getMyUserData() {
		return apiClient.get('user')
	},
	/*getEvents(perPage, page) {
		return apiClient.get('/events?_limit=' + perPage + '&_page=' + page)
	},*/
	/*postLogIn(event) {
		return apiClient.post('/events', event)
	},*/
	postLogIn(data) {
		return apiClient.post('/login', data)
	}
}
