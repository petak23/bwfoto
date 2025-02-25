import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import MainService from '../services/MainService'

export const useMainStore = defineStore('main', () => {

	const baseUrl = ref(document.getElementById('app').dataset.baseUrl)

	const apiPath = computed(() => baseUrl.value + "api/") // Cesta k API

	const user = ref(null)
	const user_permission = ref(null) 

	const udaje_webu = ref(null)

	const admin_menu = ref(null)

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
		baseUrl, apiPath, user, user_permission, udaje_webu, 
		admin_menu,
		checkUserPermission, getActualUser
	}
})