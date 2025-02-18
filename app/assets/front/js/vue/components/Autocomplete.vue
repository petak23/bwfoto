<script setup>
/** 
 * Component Autocomplete
 * Posledná zmena(last change): 18.02.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 * 
 * Inšpirácia z: https://blog.nette.org/cs/vue-js-v-nette
 */

import { ref, onMounted, onUnmounted } from 'vue'
import MainService from '../services/MainService'
import { RouterLink } from 'vue-router'
import { useMainStore } from '../store/main.js'
const store = useMainStore()

const props = defineProps({
	inputname: { // Názov poliíčka inputu
		type: String,
		default: "searchStr"
	},
})

const searchquery = ref('')
const results = ref([])
const isOpen = ref(false)
const isSearching = ref(true)
const arrowCounter = ref(-1)

const emit = defineEmits(['autocomplete-start'])

const autoComplete = () => {
	emit('autocomplete-start')
	results.value = []
	if (searchquery.value.length > 0) {
		isOpen.value = true
		isSearching.value = true
	}
	if (searchquery.value.length > 2) {
		MainService.getSearch(props.inputname, searchquery.value)
			.then(response => {
				//console.log(response);
				results.value = [];
				response.data.forEach(cl => results.value.push(cl))
				isSearching.value = false 
			})
			.catch((error) => {
				console.error(error)
			});
	}
}

/*const setLink = (result) => {
	if (result.type == 1) {
		return "/clanky/" + result.id;
	} else if (result.type == 2) {
		return "/clanky/" + result.id + '/' + result.id_dokument;
	}
}*/

const onArrowDown = () => {
  if (arrowCounter.value < results.value.length - 1) {
    arrowCounter.value++
  }
}

const onArrowUp = () => {
  if (arrowCounter.value > 0) {
    arrowCounter--
  }
}

const setResult = (item) => {
		isOpen.value = false;
		searchquery.value = item[choice.value];
		//product_id = item.id;
		//this.$emit('selected', item);
}

const onEnter = () => {
  setResult(results.value[arrowCounter.value])
  arrowCounter.value = -1
}

/*const onAClick = () => {
	return true;
}*/

const elementRef = ref(null);

const handleClickOutside = (evt) => {
	//console.log(evt);
	
	if (elementRef.value && !elementRef.value.contains(evt.target)) {
		isOpen.value = false;
		arrowCounter.value = -1;
	}
};

onMounted(() => {
	document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
	document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
	<div id="autocomplete" class="autocomplete" ref="elementRef">
		<form autocomplete="off" class="my-2 my-lg-0" @submit.prevent><!--required for disable google chrome auto fill-->
			<input  type="search" 
							:placeholder="store.texts.autocomplete_placeholder"
							:name="inputname"
							class="form-control mr-sm-2"
							aria-label="Search"
							v-model="searchquery"
							@input="autoComplete"
							@keydown.down="onArrowDown"
							@keydown.up="onArrowUp"
							@keydown.enter="onEnter"
			>
			<div class="autocomplete-result" v-show="isOpen">
				<ul class="list-group">
					<li class="list-group-item text-secondary" v-show="isSearching">
						<span v-show="searchquery.length > 2">
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
							{{ store.texts.autocomplete_searching }}
						</span>
						<span v-show="searchquery.length < 3">{{ store.texts.autocomplete_min_char }}</span>
					</li>
					<li class="list-group-item"
							v-for="(result, i) in results"
							:key="i"
							:class="{ 'is-active': i === arrowCounter }"
					>
						<RouterLink :to="result.vue_link" :title="result.name"> 
							{{ result.name }} 
							<div class="small" v-if="result.description != ''"><span v-html="result.description"></span></div>
						</RouterLink>
					</li>
					<li class="list-group-item text-warning" v-show="!isSearching && searchquery.length > 2 && results.length == 0">
						<span>{{ store.texts.autocomplete_not_found }}</span>
					</li>
				</ul>
			</div>
		</form>
	</div>
</template>