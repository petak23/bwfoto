import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import MainService from '../services/MainService'

export const useMainStore = defineStore('main', () => {

	const baseUrl = ref(document.getElementById('app').dataset.baseUrl)

	const apiPath = computed(() => baseUrl.value + "api/") // Cesta k API

	const appName = ref("")  // Meno aplikácie
		
	const minYear = ref(2000)

	const user = ref(null)
	const user_permission = ref(null) 

	const main_menu = ref([])

	const main_menu_open = ref([])
	const main_menu_active = ref(null)
	const main_menu_loadet = ref(false) // Príznak, či je hl. menu načítané
	const main_menu_changed = ref(false) // Príznak, že sa hl. menu zmenilo
	const sub_menu = ref(null)
	
	const article = ref({
			id_user_main: 0,
	})

	const	texts_to_load = ref(['base_edit_title', 'base_edit_texts', 'base_to_admin', 
										'base_last_change', 'base_platnost_do', 'base_zadal',
										'galery_arrows_before', 'galery_arrows_after',
										'autocomplete_placeholder', 'autocomplete_searching',
										'autocomplete_min_char', 'autocomplete_not_found',
										'clanky_h3_prilohy_images', 'clanky_h3_prilohy_audios',
										'clanky_h3_prilohy_others', 'base_AdminLink_name', 'base_component_news_h4',
										'clanky_dokument_download', 'clanky_dokument_view', 'log_in', 'register', 'log_out'
									])
	const texts = ref({})
	const texts_loadet = ref(false) // Príznak načítania textov

	const udaje_webu = ref([])

	const productsLikeItem = ref([])

	function push_main_menu_open (push_id) {
		main_menu_open.value.push(push_id)
  }

	function changeMainMenu (in_main_menu) {
		main_menu.value = in_main_menu
	}

	/** 
	 * Funkcia na hľadanie v hlavnom mene podľa špecifického názvu(web name)
	 * @var item Object item of main_menu
	 * @var spec_nazov String Šecifický názov - webname
	 * @returns int id_hlavne_menu_lang */
	function searchMenuSpecNazov(item, spec_nazov) {
		let out = 0
		if (Array.isArray(item)) { // Prechádzanie jednotlivých častí menu
			item.forEach((i) => {
				if (out == 0) {
					out = searchMenuSpecNazov(i, spec_nazov)
				}
			})
		} else { // Prechádzanie jednotlivých položiek časti
			if (item.vue_link == ("/clanky/" + spec_nazov)) {
				out = item.id
			} else {
				if (item.children !== undefined) {
					item.children.forEach((i) => {
						if (out == 0) {
							out = searchMenuSpecNazov(i, spec_nazov)
						}
					})
				}
			}
		}
		return parseInt(out)
	}

	const getSubmenu = async (id_submenu) => {
		sub_menu.value = []
		await MainService.getSubmenuFront(id_submenu)
			.then(response => {
				sub_menu.value = Object.values(response.data)
			})
			.catch((error) => {
				console.error(error);
			})
	}	

	const checkUserPermission = (resource, action = null) => {
		let edit_enabled = false
		if (user.value != null && user.value.id != undefined) {
			user_permission.value.forEach(function check(item) {
				if (item.resource == resource) {
					let p = false
					if (item.action == null) {
						p = true
					} else if (Array.isArray(item.action) && item.action.includes(action)) {
						p = true
					}
					edit_enabled = p
				}
			}, this)
		}

		return edit_enabled
	}

	const getActualUser = () => {
		MainService.getMyUserData()
			.then(response => {
				if (response.data.status == 200) {	// Prihlásený v poriadku
					user.value = response.data.user
					user_permission.value = response.data.user.permission
				} else if (response.data.status == 401) { // Neprihlásený užívateľ
					user.value = null
					user_permission.value = response.data.user.permission
				} else {
					user.value = null
					user_permission.value = null
				}
			})
			.catch((error) => {
				console.log(error)
			})
	}

  return {
		baseUrl, apiPath, appName, minYear, user, user_permission,
		main_menu, main_menu_open, main_menu_active, main_menu_loadet, main_menu_changed, sub_menu,
		article, texts, texts_to_load, texts_loadet, udaje_webu,
		productsLikeItem,
		push_main_menu_open, changeMainMenu, searchMenuSpecNazov, getSubmenu,
		checkUserPermission, getActualUser
	}
})
