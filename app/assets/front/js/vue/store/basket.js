import { ref, toRaw } from 'vue'
import { defineStore } from 'pinia'
import Session from '../plugins/session'

export const useBasketStore = defineStore('basket', () => {

/** ---------------- BASKET ------------- */
	const basketItem = ref([]) // Array of Object {id_article: xx, id_product: xx, product: Object }
	const final_price = ref(0) // Sumárna cena za produkty 

	const getFinalPrice = () => {
		final_price.value = 0
		basketItem.value.forEach((item) => {
			final_price.value += parseFloat(item.product.properties.final_price)
		})
		return final_price.value
	}

	/** Ulož produkt
	 *  data = { id_product: item.id, product: item, id_article: store.article.id_hlavne_menu,
	 *		url_name: store.article.url_name, 
	 *	}
	 */
	const saveProduct = (data) => {
		if (basketItem.value.length == 1 && basketItem.value[0].id_product == data.id_product) {
			delAllProducts('basket-items')
		} else {	
			// Ak je v poli položka s id_propdukt == data.id_product tak ju vylúč
			basketItem.value = basketItem.value.filter((likeItem) => (likeItem.id_product !== data.id_product))
			
			// Pridaj novú položku
			basketItem.value.push(data)
	
			Session.clearStorage('basket-items')
			Session.saveStorage('basket-items', toRaw(basketItem.value))
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
			Session.saveStorage('basket-items', toRaw(basketItem.value))
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





/** ---------------- NAV ------------- */

	const view_part = ref(1)

	const nav_dafault = [
		{ id: 1, key: "Obsah košíka", enabled: true },
		{ id: 2, key: "Adresa", enabled: false },
		{ id: 3, key: "Doprava a platba", enabled: false },
		{ id: 4, key: "Sumár", enabled: false },
		{ id: 5, key: "Ukončenie", enabled: false},
	]

	const navigation = ref(nav_dafault)

	const getNavFromSession = () => {
		if (Session.has('basket-nav')) {
			let pom = JSON.parse(Session.getStorage('basket-nav'))
			navigation.value = pom.nav
			view_part.value = pom.view_part
		} else {
			view_part.value = 1
			navigation.value = nav_dafault
		}
	}

	const saveNav = () => {
		if (Session.has('basket-nav')) Session.clearStorage('basket-nav')
		Session.saveStorage('basket-nav', {nav: toRaw(navigation.value), view_part: toRaw(view_part.value)})	
	}

	const navigationUpdate = (data) => {
		/* formát prichádzajúcich dát: { id: x, enabled: true|false, view_part: y, disable_another: true|false } */
		if (data.disable_another != undefined && data.disable_another) {
			for (let i = 0; i < navigation.value.length; i++) {
				navigation.value[i].enabled = false;
			}
		}
		if (data.id != undefined && parseInt(data.id) > 0 && parseInt(data.id) <= navigation.value.length) { // ošetrenie hraníc
			navigation.value[data.id - 1].enabled = data.enabled == true // ošetrenie, že to bude bool
		}
		saveNav()
		if (data.view_part != undefined) view_part.value = data.view_part
	}





/** ---------------- ADDRESS ------------- */

	const address_default = {
		name: "",
		email: "",
		password: "",
		street: "",
		town: "",
		country: "",
		psc: "",
		phone: "+421",
		adress2: {
			street: "",
			town: "",
			country: "",
			psc: ""
		},
		firm: {
			name: "",
			ico: "",
			dic: "",
			icdph: "",
			street: "",
			town: "",
			country: "",
			psc: ""
		}
	}

	const basketAddress = ref(address_default)
	
	const getAddressFromSession = () => {
		if (Session.has('basket-address')) {
			basketAddress.value = JSON.parse(Session.getStorage('basket-address'))
		} else {
			basketAddress.value = address_default
		}
	}

	const saveAddress = (data = null) => {
		if (data != null) basketAddress.value = data
		if (Session.has('basket-address')) Session.clearStorage('basket-address')
		Session.saveStorage('basket-address', toRaw(basketAddress.value))	
	}

	

	
/** ---------------- SHIPPING ------------- */

	const basketShipping = ref([])

	const getShipingFromSession = () => {
		if (Session.has('basket-shipping')) {
			basketShipping.value = JSON.parse(Session.getStorage('basket-shipping'))
		} else {
			basketShipping.value = []
		}
	}
	
	const saveShipping = () => {
		if (Session.has('basket-shipping')) Session.clearStorage('basket-shipping')
		Session.saveStorage('basket-shipping', toRaw(basketShipping.value))	
	}
	
 
	return {
		view_part, basketItem, final_price, navigation, basketAddress, basketShipping,
		saveProduct, delAllProducts, delOneProduct, 
		getProductsFromSession, getProductFromBasket, navigationUpdate, getFinalPrice,
		getNavFromSession,
		getAddressFromSession, saveAddress,
		getShipingFromSession, saveShipping,
	}
})
