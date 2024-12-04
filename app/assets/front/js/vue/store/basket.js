import { ref } from 'vue'
import { defineStore } from 'pinia'
import Session from '../plugins/session'

export const useBasketStore = defineStore('basket', () => {

	const view_part = ref(1)
	const basketItem = ref([])

	const basketNav = ref([
		{ id: 1, key: "Obsah košíka", enabled: true },
		{ id: 2, key: "Adresa", enabled: false },
		{ id: 3, key: "Doprava a platba", enabled: false },
		{ id: 4, key: "Sumár", enabled: false },
		{ id: 5, key: "Ukončenie", enabled: false},
	])

	/** Ulož položku 
	 *  data = {
	 *		id_product: item.id,
	 *		product: item,
	 *		id_article: store.article.id_hlavne_menu,
	 *		url_name: store.article.url_name, 
	 *	}
	 */
	 const saveProduct = (data) => {
		if (basketItem.value.length == 1 && basketItem.value[0].id_product == data.id_product) {
			delAllProducts()
		} else {	
			// Ak je v poli položka s id_propdukt == data.id_product tak ju vylúč
			basketItem.value = basketItem.value.filter((likeItem) => (likeItem.id_product !== data.id_product))
			
			// Pridaj novú položku
			basketItem.value.push(data)
	
			Session.clearStorage('basket-items')
			Session.saveStorage('basket-items', basketItem.value)
		}
	}

	/** Vymazanie všetkých obľúbených položiek */
	const delAllProducts = () => { 
		basketItem.value = []
		Session.clearStorage('basket-items')
	}
	
	/** Vymazanie jednej obľúbenej položky */
	const delOneProduct = (id) => { 
		if (basketItem.value.length == 1 && basketItem.value[0].id_product == id) {
			delAllProducts()
		} else {
			// Ak je v poli položka s id_propdukt == id tak ju vylúč
			basketItem.value = basketItem.value.filter((likeItem) => (likeItem != null && likeItem.id_product !== id))
			Session.clearStorage('basket-items')
			Session.saveStorage('basket-items', basketItem.value)
		}
	}

	const getProductsFromSession = () => {
		if (Session.has('basket-items')) {
			basketItem.value = JSON.parse(Session.getStorage('basket-items'))
		} else {
			basketItem.value = []
		}
	}

	const getProductFromBasket = (id_product) => {
		let out = null
		basketItem.value.forEach((item) => {
			if (item.id_product == id_product) {
				out = item
			}
		})
		return out
	}

	const basketNavUpdate = (data) => {
		/* formát prichádzajúcich dát: { id: x, enabled: true|false, view_part: y, disable_another: true|false } */
		if (data.disable_another != undefined && data.disable_another) {
			for (let i = 0; i < basketNav.value.length; i++) {
				basketNav.value[i].enabled = false;
			}
		}
		if (parseInt(data.id) > 0 && parseInt(data.id) <= basketNav.value.length) { // ošetrenie hraníc
			basketNav.value[data.id - 1].enabled = data.enabled == true // ošetrenie, že to bude bool
		}
		if (Session.has('basket-nav')) Session.clearStorage('basket-nav')
		Session.saveStorage('basket-nav', basketNav.value)
		if (data.view_part != undefined) view_part.value = data.view_part
	}
 
	return {
		view_part, basketItem,
		saveProduct, delAllProducts, delOneProduct, getProductsFromSession, getProductFromBasket,
		basketNavUpdate
	}
})
