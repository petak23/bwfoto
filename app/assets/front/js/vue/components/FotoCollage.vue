<script setup>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 29.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.2.6
 * Z kniznica pouzite súbory a upravene: https://github.com/seanghay/vue-photo-collage
 */
import { ref, computed, watch, onMounted, onUnmounted } from "vue"
import { BButton, BModal } from 'bootstrap-vue-next'
import PhotoCollageWrapper from "./vue-photo-collage/PhotoCollageWrapper.vue"
import EditSchemaRow from "./vue-photo-collage/EditSchemaRow.vue"
import MainService from '../services/MainService.js'
import { useMainStore } from '../store/main.js'
const store = useMainStore()
import { useFlashStore } from '../store/flash'
const storeF = useFlashStore()

const props = defineProps({
	filesPath: { // Adresár k súborom bez basePath
		type: String,
		default: "",
	}, 
	maxrandompercwidth: { // Percento, o ktoré sa môže meniť naviac šírka fotky
		type: Number,
		default: 25,
	},
})

const id = ref(0)
const attachments = ref([])
const collage = ref({ // Objekt pre koláž
	width: "",
	height: [],
	layout: [],
	photos: [],
	borderRadius: ".2rem",
	showNumOfRemainingPhotos: false,
	maxRandomPercWidth: 20,
	widerPhotoId: [], // poradie fotky v riadku, ktorá má byť širšia 1,2,... 
										// ak je zadané 0 generuje sa náhodne
										// ak je zadané -1 všetky fotky budú rovnaké
	maxPhotosToShow: 0, // Max. počet fotiek na zobrazenie pre dan schému
	uniqeRows: 0,			// Počet jedinečných riadkov (pred opakovaním)
})
const image = ref({
	name: "",
	main_file: "",
	description: null,
	id_collage: 0,
})
const schstr = ref("")			// Schéma v textovej podobe
const schstr_old = ref("")	// Záloha textovej podoby schémy
/* Formát schémy: 
*[
*	{
*		// Max. šírka koláže pre ktorú platí
*		max_width: 320,  
*		// Počet fotiek v jednotlivých riadkoch
*		schema: [2, 1, 3, 4, 4, 3, 4, 4], 
*		// Výška jednotlivých riadkov v px
*		height: [85, 60, 85, 60, 70, 95, 70, 60],
*		// Veľkosť medzery pod daným riadkom
*		padding: [2, 5, 0, 0, 0, 5, 0, 0],
*		// Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku:
*		// Ak je zadané číslo väčšie ako 0 (1,2,...) tak tá konkrétna bude širšia, 
*		// ak je zadané 0 generuje sa náhodne,
*		// ak je zadané -1 všetky fotky v riadku budú rovnaké.
*		widerPhotoId: [-1, 2, 0, 1, -1, 0, 2, 1], 
*	},
*	...
*],*/
const sch = ref([])				// Aktuálna schéma
const edit_enabled = ref(false)

const imgDetail = ref(null)
const viewModalImg = ref(false)
const viewModalEdit = ref(false)

const itemClickHandler = (data, i) => {
	// click event
	MainService.getDocument(data.id_foto)
					.then(response => {
						image.value = response.data
						image.value.id_collage = data.id
						viewModalImg.value = true
					})
					.catch((error) => {
						console.error(error)
					})
}
const matchHeight = () => {
	if (imgDetail.value != undefined || imgDetail.value != null) { // Musí byť inak vyhadzuje chybu pri načítaní...
		computeLayout(imgDetail.value.clientWidth)
	}
}
const computeLayout = (client_width) => {
	let res = { 
		max_width: 0,  
		schema: [],  
		height: [],
		padding: [],  
		layout: [],
		widerPhotoId: [],
		maxPhotosToShow: 0
	};
	sch.value.forEach(x => {
		if (client_width < x.max_width && res.max_width == 0) {
			res = x
		}
	})
	res.layout = [] // Musí ostať inak nefunguje !?!
	collage.value.photos = attachments.value
	let i = collage.value.photos.length
	let r = 0 // riadok
	do {
		// Zisti počet foto v riadku. Ak by bolo 0 tak nahraď to 1
		let fr = res.schema[r] > 0 ? res.schema[r] : 1;
		res.layout.push( fr )

		r = r + 1 >= res.schema.length ? 0 : r + 1 
		i -= fr
	}
	while (i > 0);
	collage.value.width = client_width + 'px';
	collage.value.height = res.height.map(x => parseInt(x)) // Aby som z textových položiek urobil čísla
	collage.value.padding = res.padding.map(x => parseInt(x))
	collage.value.layout = res.layout.map(x => parseInt(x))
	collage.value.maxRandomPercWidth = props.maxrandompercwidth
	collage.value.widerPhotoId = res.widerPhotoId.map(x => parseInt(x))
	// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/Reduce
	collage.value.maxPhotosToShow = res.schema.map(x => parseInt(x)).reduce((a, b) => a + b, 0)
	collage.value.uniqeRows = res.schema.length
}
const loadSchema = () => {
	MainService.getFotoCollageSettings(store.article.id_hlavne_menu)
		.then(response => {
			sch.value = response.data
			schstr.value = JSON.stringify(sch.value, null, 2)
			schstr_old.value = schstr.value
			loadPictures()
		})
		.catch((error) => {
			console.error(error);
		});
}
const loadPictures = () => {
	MainService.getFotoCollage(store.article.id)
		.then(response => {
			attachments.value = response.data
			computeLayout(imgDetail.value.clientWidth)
		})
		.catch((error) => {
			console.error(error);
		});
}
const SchemaChanged = (data) => {
	//let vm = this
	sch.value[data[0].id_part] = data[0].data
	let id_part = data[0].id_part

	MainService.postSaveFotoCollageSettings(store.article.id_hlavne_menu, { data: sch.value })
		.then(function (response) {
			storeF.showMessage('Schéma bola uložená.', 'success', 'Podarilo sa...', 5000)
			setTimeout(() => {
				sch.value = response.data
				computeLayout(imgDetail.value.clientWidth)
			}, 100)
		})
		.catch((error) => {
			console.error(error)
		})
}
const keyPush = (event) => {
	switch (event.key) {
		case "ArrowLeft":
			before()
			break
		case "ArrowRight":
			after()
			break
	}
}
const swipe = (direction) => {
	if (direction == 'Left' || direction == 'Up') {
		before()
	} else if (direction == 'Right' || direction == 'Down') {
		after()
	}
}
// Zmena id na predošlé
const before = () => {
	id.value = id.value <= 0 ? (attachments.value.length - 1) : id.value - 1
	let idb = image.value.id_collage <= 0 ? (attachments.value.length - 1) : image.value.id_collage - 1
	itemClickHandler({
		id: idb,
		id_foto: attachments.value[idb].id_foto
	}, 1)
}
// Zmena id na  nasledujúce
const after = () => {
	id.value = id.value >= (attachments.value.length - 1) ? 0 : id.value + 1
	let ida = image.value.id_collage >= (attachments.value.length - 1) ? 0 : image.value.id_collage + 1
	itemClickHandler({
		id: ida,
		id_foto: attachments.value[ida].id_foto
	}, 1)
}

const filesDir = computed(() => {
	return store.baseUrl + '/' + props.filesPath
})

watch(() => store.main_menu_changed, () => {
	if (!store.main_menu_changed) {
		/* Nčítanie schémy fotokoláže */
		loadSchema()
	}
})

watch(() => store.user, () => {
	edit_enabled.value = store.checkUserPermission('Front:Clanky', 'edit')
})

onMounted(() => {
	window.addEventListener("resize", matchHeight);

	/* Naviazanie na sledovanie zmeny veľkosti stránky */
	matchHeight();

	/* Naviazanie na sledovanie stláčania klávesnice */
	document.addEventListener("keydown", keyPush);

	/* Nčítanie schémy fotokoláže */
	loadSchema();
})

onUnmounted(() => {
	window.removeEventListener("resize", matchHeight);
})
</script>

<template>
	<section id="webThumbnails" class="row" v-if="filesDir.length > 0">
		<div class="col-12 vue-fotogalery">
			<ul class="nav justify-content-end mb-1" v-if="edit_enabled">
				<li class="nav-item">
					<a type="button" class="btn btn-sm btn-dark disabled" disabled>
						Fotiek: {{ attachments.length }}, max. v schéme: {{ collage.maxPhotosToShow }},
						riadkov: {{ collage.uniqeRows }}
					</a>
				</li>
				<li class="nav-item">
					<BButton 
						variant="outline-warning"
						size="sm"
						@click="viewModalEdit = true"
						title="Editácia schémy">
						<i class="fas fa-pen"></i>
					</BButton>
				</li>
			</ul>
			<BModal 
				id="edit-collage"
				v-if="edit_enabled"
				centered 
				size="xl" 
				title="Editácia schémy fotokoláže" 
				ok-only 
				header-bg-variant="dark"
				header-text-variant="light"
				body-bg-variant="dark"
				body-text-variant="light"
				footer-bg-variant="dark"
				footer-text-variant="light" 
				v-model="viewModalEdit"
				:hide-footer="true"
			>
				<div
					class="accordion"
					role="tablist"
					v-for="(s, index) in sch"
					:key="index"
				>
					<edit-schema-row
						:row="s"
						:id_part="index"
						@schema-changed="SchemaChanged"
					>
					</edit-schema-row>
				</div>
			</BModal>

			<div ref="imgDetail" id="imgDetail"> 
				<photo-collage-wrapper 
					gapSize="6px"
					@itemClick="itemClickHandler"
					v-bind="collage">
				</photo-collage-wrapper>
			</div>
			
			<BModal  centered size="xl" 
								:title="image.name" ok-only
								modal-class="lightbox-img"
								v-model="viewModalImg"
								header-close-class="btn btn-dark text-light me-3"
								header-close-variant="dark"
			>
				<div class="modal-content">
					<div class="modal-body my-img-content">  
						<img :src="filesDir + image.main_file" 
									:alt="image.name" 
									id="big-main-img"
									class="" />
						<div class="text-center description" v-if="image.description != null">
							{{ image.description }}
						</div>
					</div>
					<div class="arrows-overlay">
						<div class="arrows-l" @click="before">
							<button class="btn btn-dark text-light" :title="store.texts.galery_arrows_before">&#10094;</button>
						</div>
						<div class="go-to-hight" 
							v-touch="{
								left: () => swipe('Left'),
								right: () => swipe('Right'),
								up: () => swipe('Up'),
								down: () => swipe('Down')
							}"
						></div>
						<div class="arrows-r flex-row-reverse" @click="after">
							<button class="btn btn-dark text-light" :title="store.texts.galery_arrows_after">&#10095;</button>
						</div>
					</div>
				</div>
			</BModal>
		</div>
	</section>
</template>

<style lang="scss" scoped>
	img {
		max-width: 80vw;
		height: 80vh;
	}
</style>