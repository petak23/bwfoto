/** https://dev.to/grahammorby/persist-data-with-vue-3-38pc */
export default {
	// Sets an item with a Key to local storage
	//const saveStorage = function(key, data) {
	saveStorage(key, data) {
		localStorage.setItem(key, JSON.stringify(data));
	},

	// Looks for a local storage item and returns if present
	//const getStorage = function(key, item) {
	getStorage(key, item) {
		if( localStorage.getItem(key) && item) {
			const data = JSON.parse(localStorage.getItem(key))
			return data[item]
		}
		else if(localStorage.getItem(key)) {
			return localStorage.getItem(key)
		} else {
			return false
		}
	},

	// est či existuje daný kľúč
	has(key) {
		return localStorage.getItem(key) ? true : false
	},

	// Get all storage items
	// https://stackoverflow.com/questions/17745292/how-to-retrieve-all-localstorage-items-without-knowing-the-keys-in-advance
	allStorage() {
		var archive = {}, // Notice change here
				keys = Object.keys(localStorage),
				i = keys.length;

		while ( i-- ) {
				archive[ keys[i] ] = localStorage.getItem( keys[i] );
		}
		return archive;

		//??? Otestuj alternatívu ...
		// return JSON.stringify(localStorage);
	},

	// Clear a single item or the whole local storage
	//const clearStorage = function(key='false') {
	clearStorage(key='false') {
		if(key) {
				localStorage.removeItem(key);
		} else {
				localStorage.clear();
		}
	}
	// Exports all avaliable functions on the script
	//export {getStorage, saveStorage, clearStorage}
}