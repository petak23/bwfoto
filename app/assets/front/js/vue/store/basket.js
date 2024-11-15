import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useFlashStore = defineStore('basket', () => {

	const view_part = ref(1)

	/** Vloží správu na koniec */
	/*function showMessage(message, type = "info", heading = null, timeout = null) {
		flashMessages.value.push({
			message: message, 
			type: type,
			heading: heading,
			timeout: timeout
		})
	}*/

	/** Vráti prvú správu a zároveň ju aj vymaže */
	/*function getFirst () {
		return flashMessages.value.shift()
	}*/
 
	return {
		view_part
	}
})
