<script setup>
/** 
 * Component Fotogalery
 * Posledná zmena(last change): 21.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.2.0
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import MainService from '../services/MainService.js'
import ProductsProperties from '../components/ProductsProperties/ProductsProperties.vue'
import FotoFilter from './Fotogalery/FotoFilter.vue'
import Session from '../plugins/session.js'
import { useMainStore } from '../store/main.js'
const store = useMainStore()

import { BModal, BImg } from 'bootstrap-vue-next'

// https://swiperjs.com/vue
// import Swiper core and required modules
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper/modules';

// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';

// Import Swiper styles
import 'swiper/css';

const props = defineProps({
	first_id: { // Ak je nastavené tak sa zobrazí obrázok ako prvý
		type: String,
		default: "0",
	},
	large: {	// TODO zisti potrebnosť...
		type: Boolean,
		default: false,
	}
})

const id = ref(0)
const square = ref(0)
const wid = ref(0)
const imgDetail = ref(null)
const uroven = ref(0) // Premenná sleduje uroveň zobrazenia
//article = ref({})
const attachments = ref([{ // Musí byť nejaký nultý objekt inak je chyba...
	description: null,
	id: 0,
	main_file: "",
	name: "",
	thumb_file: "",
	type: "",
	web_name: "",
	liked: false
}])
const liked = ref(false)
const in_basket = ref(false)
const filter_choice = ref(1) // 1: všetky, 2: Len na sklade

const viewModalFoto1 = ref(false)
const viewModalFoto2 = ref(false)

const emit = defineEmits(["basket-insert"])

// Zmena id
const changebig = (idc) => {
	id.value = idc
	my_liked()
	my_in_basket()
}
const modalchangebig = (idm) => {
	id.value = idm
	viewModalFoto1.value = true
}
const swipe = (direction) => {
	//console.log(direction)
	if (direction == 'Left' || direction == 'Up') {
		before()
	} else if (direction == 'Right' || direction == 'Down') {
		after()
	}
}
// Zmena id na predošlé
const before = () => {
	id.value = id.value <= 0 ? (attachments.value.length - 1) : id.value - 1;
	my_liked()
	my_in_basket()
}  
// Zmena id na  nasledujúce
const after = () => {
	id.value = id.value >= (attachments.value.length - 1) ? 0 : id.value + 1;
	my_liked()
	my_in_basket()
}
const matchHeight = () => {
	if (imgDetail.value) {
		const height = imgDetail.value.clientHeight;
		const width = imgDetail.value.clientWidth;
		const height2 = parseInt(window.innerHeight * 0.8);
		const h = height2 > height ? height2 : height;

		square.value = h > width ? width - 20 : h;
		wid.value = width;
	}
}
const urovenUp = () => { // Funkcia pre zmenu úrovne o +1 na max. 2
	uroven.value += uroven.value < 2 ? 1 : 0
}
const urovenDwn = () => {// Funkcia pre zmenu úrovne o -1 na min. 0
	uroven.value -= uroven.value > 0 ? 1 : 0;
}
const keyPush = (event) => {
	if (uroven.value <= 1) {
		switch (event.key) {
			case "ArrowLeft":
				before()
				break
			case "ArrowRight":
				after()
				break
		}
	}
}
// Generovanie url pre lazyloading obrázky
const getImageUrl = (text) => {
	return store.baseUrl + '/' + text
}
const border_compute = (border) => {
	let pom = border != null && border.length > 2 ? border.split("|") : ['', '0'];
	return "border: " + pom[1] + "px solid " + (pom[0].length > 2 ? (pom[0]) : "inherit")
}
const getAttachments = async () => { 
	await MainService.getFotogalery(store.main_menu_active, filter_choice.value)
		.then(response => {
			attachments.value = response.data
			if (parseInt(props.first_id) > 0) { // Ak mám first_id tak k nemu nájdem položku v attachments
				getFirstId(parseInt(props.first_id))
				my_liked()
				my_in_basket()
				if (wid.value == 0) {
					modalchangebig(id.value)
				}
			}
		})
		.catch((error) => {
			console.error(error);
		})
}
const getFirstId = (idf) => {
	Object.keys(attachments.value).forEach(ma => {
		if (attachments.value[ma].id === idf) {
			id.value = ma
		}
	})
}
/*const productsLikeUpdate = () => {
	store.productsLikeItem = []
	for (const [key, value] of Object.entries(Session.allStorage())) {
		if (key.startsWith("like")) {
			store.productsLikeItem.push(JSON.parse(value))
		}
	}
}*/
const saveLiked = () => {
	const item = attachments.value[id.value]

	// Ak je v poli položka s id_propdukt == item.id tak ju vylúč
	store.productsLikeItem = store.productsLikeItem.map((likeItem) => {
		if (likeItem.id_product !== item.id) {
			return likeItem
		}
	})

	store.productsLikeItem.push({
		id_product: item.id,
		id_article: store.article.id_hlavne_menu,
		source: item.main_file,
		name: item.name,
	})

	Session.clearStorage('like-items')
	Session.saveStorage('like-items', store.productsLikeItem)
	
	//productsLikeUpdate()
}
const my_liked = () => {
	const result = store.productsLikeItem.find(({ id_product }) => id_product == attachments.value[id.value].id)
	attachments.value[id.value].liked = result == undefined
	liked.value = attachments.value[id.value].liked
}
const basketInsert = () => {
	let item = attachments.value[id.value]
	emit("basket-insert", [{
		id_product: item.id,
		product: item,
		id_article: store.article.id_hlavne_menu,
	}])
}
const my_in_basket = () => {
	attachments.value[id.value].in_basket = Session.has('basket-item-' + attachments.value[id.value].id)
	in_basket.value = attachments.value[id.value].in_basket
}
const filterChange = (choice) => {
	filter_choice.value = choice
	getAttachments()
}

const border_a = computed(() => {
	return border_compute(store.article.border_a)
})
const border_b = computed(() => {
	return border_compute(store.article.border_b)
})
const border_c = computed(() => {
	return border_compute(store.article.border_c)
})
const filesDir = computed(() => {
	return store.baseUrl + '/'
})
const aa = computed(() => {
	return typeof attachments.value[id.value] !== 'undefined' ? attachments.value[id.value] : null
})
const button_basket_title = computed(() => {
	let t = in_basket.value ? 'Produkt už je v košíku.' : 'Vlož do košíka.'
	return aa != null && aa.id_products_status > 1 ? aa.products_status : t 
})
const button_basket_class = computed(() => {
	let t = in_basket.value ? 'btn-outline-secondary disabled' : 'btn-success'
	return aa != null && aa.id_products_status > 1 ? 'btn-outline-secondary disabled' : t
})
const button_basket_disabled = computed(() => {
	return aa != null && aa.id_products_status > 1 ? true : in_basket.value
})

watch(() => store.main_menu_active, () => {
	getAttachments()
})
watch(() => store.productsLikeItem, () => {
	my_liked()
})

onMounted(() => {
	// Dynamické správanie pri zmene veľkosti okna
	matchHeight();
  window.addEventListener("resize", matchHeight); 

	/* Naviazanie na sledovanie stláčania klávesnice */
	document.addEventListener("keydown", keyPush);

	// TODO $root.$on ...
	/*this.$root.$on("basket-update", my_in_basket)

	this.$root.$on("product_update_props", getAttachments)*/

	getAttachments()
})

onUnmounted(() => {
	window.removeEventListener("resize", matchHeight);
})
</script>

<template>
	<section id="webThumbnails" class="row">
		<div class="col-12 vue-fotogalery main-win" v-if="attachments.length > 0 && filesDir != ''">
			<div class="row" v-if="wid > 0">
				<h4 class="col-8 d-flex justify-content-between mb-0">
					{{ attachments[id].name }}
					<div 
						class="btn-group" role="group" aria-label="Tlačítka akcie"
						v-if="attachments[id].type == 'product'"
					>
						<button
							:title="liked ? 'Produkt je označený ako obľúbený.': 'Označ obľúbený produkt.'"	
							type="button"
							class="btn align-right"
							:class="liked ? 'btn-warning' : 'btn-outline-warning'"
							@click="saveLiked()"
							>
							<i 
								class="fa-solid"
								:class="liked ? 'fa-heart' : 'fa-thumbs-up'"
							></i>
						</button>
						<button 
							:title="button_basket_title" 
							type="button"
							class="btn"
							:class="button_basket_class"
							@click="basketInsert()"
							:disabled="button_basket_disabled"
						>
							<i class="fa-solid fa-basket-shopping" v-if="aa.id_products_status == 1"></i>
							<span v-else>{{ aa.products_status }}</span>
						</button>
					</div>
				</h4>
				<div class="col-4 d-flex justify-content-end">
					<foto-filter @filter-change="filterChange" />
				</div>
			</div>
			<div class="row">
				<div class="d-none d-sm-flex justify-content-center col-sm-8 detail" ref="imgDetail" id="imgDetail"
						v-if="props.large == false">
					<div id="squarePlace" v-bind:style="{height: square + 'px', width: square + 'px'}">
						<a  v-if="attachments[id].type == 'menu'"
								:href="attachments[id].web_name" 
								:title="attachments[id].name">
							<img  :src="filesDir + attachments[id].main_file" 
										:alt="attachments[id].name" class="img-fluid">
							<h4>{{ attachments[id].name }}</h4>
						</a>
						<video v-if="attachments[id].type == 'attachments3'"
									class="video-priloha" 
									:src="filesDir + attachments[id].main_file" 
									:poster="filesDir + attachments[id].thumb_file"
									type="video/mp4" controls="controls" preload="none">
						</video>
						<a v-else-if="attachments[id].type == 'attachments1'"
								:title="attachments[id].name"
								:href="filesDir + attachments[id].main_file"
								target="_blank"
								class="for-pdf"
										>
							<img :src="filesDir + attachments[id].thumb_file" 
									:alt="attachments[id].name" class="img-fluid">
							<br><h6>{{ attachments[id].name }}</h6>
						</a>  
						<button v-else-if="attachments[id].type == 'attachments2' || attachments[id].type == 'product'"
										@click="viewModalFoto1 = true"
										type="button" class="btn btn-link">
							<img :src="filesDir + attachments[id].main_file" 
									:alt="attachments[id].name" class="img-fluid">
						</button>
					</div>
				</div>
				<div class="col-12 thumbgrid" 
						:class="{'thumbs-large': props.large, 'col-sm-4': !props.large}">
					<div v-for="(im, index) in attachments" :key="im.id">
						<a  v-if="wid > 0" 
								@click.prevent="changebig(index)" href=""
								:title="'Odkaz' + (im.type == 'menu' ? im.view_name : im.name)"
								class="pok thumb-a ajax" 
								:class="index == id ? 'selected' : ''">
							<BImg
								:src="getImageUrl(im.thumb_file)"
								:alt="im.name" class="img-fluid"
								lazy />
						</a>
						<a  v-else-if="wid == 0 && im.type == 'menu'" 
								:href="im.web_name" 
								:title="im.name">
							<BImg
								:src="getImageUrl(im.main_file)"
								:alt="im.name" class="img-fluid podclanok"
								lazy />
							<h4 class="h4-podclanok">{{ im.name }}</h4>
						</a>
						<video v-if="wid == 0 && im.type == 'attachments3'"
									class="video-priloha" 
									:src="filesDir + im.main_file" 
									:poster="filesDir + im.thumb_file"
									type="video/mp4" controls="controls" preload="none">
						</video>
						<button v-else-if="wid == 0 && im.type == 'attachments1'" 
										:title="im.name">
							<BImg
								:src="getImageUrl(im.thumb_file)" 
								:alt="im.name"
								lazy
								class="img-fluid a3"/>
							<br><h6>{{ im.name }}</h6>
						</button>
						<button v-else-if="wid == 0 && (im.type == 'attachments2' || im.type == 'product')"
										@click.prevent="modalchangebig(index)" 
										type="button" 
										class="btn btn-link"
										:class="index == id ? 'selected' : ''"
						>
							<BImg
								:src="getImageUrl(im.thumb_file)" 
								:alt="im.name" 
								lazy
								class="img-fluid a12"/>
						</button>
					</div>
				</div>
			</div> 
			<div class="row d-none d-sm-flex justify-content-left" v-if="wid > 0 ">
				<div class="col-sm-8">
					{{ aa.description }}
					<products-properties :article="aa" />
				</div>
			</div>

			<BModal  
				id="modal-multi-1" 
				ref="modal1fo"
				centered 
				size="xl" 
				ok-only
				modal-class="lightbox-img"
				v-model="viewModalFoto1"
			>
				<template #header>
					<h5 class="modal-title">{{ attachments[id].name}}</h5>
					<button 
							v-if="attachments[id].type == 'product'"
							type="button"
							class="btn align-right"
							:class="liked ? 'btn-success' : 'btn-outline-warning'"				
							@click="saveLiked()"
							>
							<i 
								class="fa-solid"
								:class="liked ? 'fa-heart' : 'fa-thumbs-up'"
							></i>
						</button>
					<button 
						type="button" aria-label="Close" 
						class="btn btn-outline-warning mr-5"
						@click="viewModalFoto1 = false"
					>
						<i class="fa-solid fa-xmark"></i>
					</button>
				</template>
				<div class="modal-content">
					<div class="modal-body my-img-content">
						<div class="border-a" :style="border_a">
							<div class="border-b" :style="border_b">
									<img :src="filesDir + attachments[id].main_file" 
												:alt="attachments[id].name" 
												id="big-main-img"
												class="border-c" 
												:style="border_c" />
								</div>
							</div>
						<div class="text-center description" v-if="attachments[id].description != null">
							{{ attachments[id].description }}
						</div>
					</div>
					<div class="arrows-overlay">
						<div class="arrows-l"
								@click="before">
							<a href="#" class="text-light"   
									:title="store.texts.galery_arrows_before">&#10094;
							</a>
						</div>
						<div class="go-to-hight"
								v-touch="{
									left: () => swipe('Left'),
									right: () => swipe('Right'),
									up: () => swipe('Up'),
									down: () => swipe('Down')
								}"
								@click="viewModalFoto2 = true">
						</div>
						<div class="arrows-r flex-row-reverse"
								@click="after">
							<a href="#" class="text-light"
									:title="store.texts.galery_arrows_after">&#10095;
							</a>
						</div>
					</div>
				</div>
			</BModal>

			<BModal id="modal-multi-2" centered size="xl" ok-only v-model="viewModalFoto2">
				<img 
					:src="filesDir + attachments[id].main_file" 
					:alt="attachments[id].name" @click="viewModalFoto2 = false"
				>
			</BModal>
		</div>
	</section>
</template>
