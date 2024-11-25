import { ref } from 'vue'
import { defineStore } from 'pinia'
import Session from '../plugins/session'

export const useProductLikeStore = defineStore('productLike', () => {

	const productsLikeItem = ref([])

	/** Ulož položku 
	 *  data = {
	 *		id_product: id,
	 *		id_article: store.article.id_hlavne_menu,
	 *		source: main_file,
	 *		name: name,
	 *		url_name: store.article.url_name, 
	 *	}
	 */
	const saveLiked = (data) => {
		if (productsLikeItem.value.length == 1 && productsLikeItem.value[0].id_product == data.id_product) {
			delAll()
		} else {	
			// Ak je v poli položka s id_propdukt == data.id_product tak ju vylúč
			productsLikeItem.value = productsLikeItem.value.filter((likeItem) => (likeItem.id_product !== data.id_product))
			
			// Pridaj novú položku
			productsLikeItem.value.push(data)
	
			Session.clearStorage('like-items')
			Session.saveStorage('like-items', productsLikeItem.value)
		}
	}

	/** Vymazanie všetkých obľúbených položiek */
	const delAll = () => { 
		productsLikeItem.value = []
		Session.clearStorage('like-items')
	}
	
	/** Vymazanie jednej obľúbenej položky */
	const delOne = (id) => { 
		if (productsLikeItem.value.length == 1 && productsLikeItem.value[0].id_product == id) {
			delAll()
		} else {
			// Ak je v poli položka s id_propdukt == id tak ju vylúč
			productsLikeItem.value = productsLikeItem.value.filter((likeItem) => (likeItem != null && likeItem.id_product !== id))
			Session.clearStorage('like-items')
			Session.saveStorage('like-items', productsLikeItem.value)
		}
	}

	const getFromSession = () => {
		if (Session.has('like-items')) {
			productsLikeItem.value = JSON.parse(Session.getStorage('like-items'))
		} else {
			productsLikeItem.value = []
		}
	}

	return {
		productsLikeItem, 
		delAll, delOne, saveLiked, getFromSession
	}
})
