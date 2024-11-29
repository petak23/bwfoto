<script setup>
/** 
 * Component FotoPanorama
 * Posledná zmena(last change): 28.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.7
 */
import { ref, computed, watch, onMounted } from 'vue'
import MainService from '../services/MainService.js'

import { BModal, BImg } from 'bootstrap-vue-next'

import { useMainStore } from '../store/main'
const store = useMainStore()

const props = defineProps({
	first_id: { // Ak je nastavené tak sa zobrazí obrázok ako prvý
		type: Number,
		default: 0,
	}
})

const id = ref(0)
const attachments = ref([{ // Musí byť nejaký nultý objekt inak je chyba...
	description: null,
	id: 0,
	main_file: "",
	name: "",
	thumb_file: "",
	type: "",
	web_name: ""
}])

const viewModalFoto = ref(false)

// Zmena id
/*const changebig = (idb) => {
	id.value = idb
}*/
const modalchangebig = (idm) => {
	id.value = idm
	viewModalFoto.value = true
}
// Zmena id na predošlé
const before = () => {
	id.value = id.value <= 0 ? (attachments.value.length - 1) : id.value - 1
}
// Zmena id na  nasledujúce
const after = () => {
	id.value = id.value >= (attachments.value.length - 1) ? 0 : id.value + 1
}
/*const closeme = () => {
	viewModalFoto.value = false
}*/
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
// Generovanie url pre lazyloading obrázky
const getImageUrl = (text) => {
	return store.baseUrl + "/" + text
}
const border_compute = (border) => {
	let pom = border != null && border.length > 2 ? border.split("|") : ['', '0'];
	return "border: " + pom[1] + "px solid " + (pom[0].length > 2 ? (pom[0]) : "inherit")
}
const getAttachments = () => {
	MainService.getFotogalery(store.main_menu_active)
		.then(response => {
			attachments.value = response.data
			if (parseInt(props.first_id) > 0) { // Ak mám first_id tak k nemu nájdem položku v attachments
				getFirstId(props.first_id)
			}
		})
		.catch((error) => {
			console.error(error)
		});
}
const getFirstId = (idf) => {
	Object.keys(attachments.value).forEach(ma => {
		if (attachments[ma].id === idf) {
			id.value = ma
		}
	})
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

watch(() => store.main_menu_active, () => {
	getAttachments()
})

onMounted(() => {
	if (parseInt(props.first_id) > 0) { // Ak mám first_id tak k nemu nájdem položku v attachments
		Object.keys(attachments.value).forEach(ma => { 
			if (attachments.value[ma].id == props.first_id) {
				id.value = ma
			}
		});
	}

	// Naviazanie na sledovanie stláčania klávesnice
	document.addEventListener("keydown", keyPush);
	
	getAttachments()
})
</script>

<template>
	<section id="webThumbnails" class="row">
		<div class="col-12 main-win" v-if="attachments.length > 0">
			<div class="row">
				<h4 class="col-12 bigimg-name">
					{{ attachments[id].name }}
				</h4>
			</div>
			<div class="row">
				<div class="col-12 thumbpanorama" id="imgDetail" ref="imgDetail">
					<div v-for="(im, index) in attachments" :key="im.id">
						<a  v-if="im.type == 'menu'" 
								:href="im.web_name" 
								:title="im.name">
							<BImg
								:src="getImageUrl(im.main_file)"
								:alt="im.name" class="img-fluid podclanok" lazy
							/>
							<h4 class="h4-podclanok">{{ im.name }}</h4>
						</a>
						<video v-else-if="im.type == 'attachments3'"
									class="video-priloha" 
									:src="filesDir + im.main_file" 
									:poster="filesDir + im.thumb_file"
									type="video/mp4" controls="controls" preload="none">
						</video>
						<button v-else-if="im.type == 'attachments1'" 
										:title="im.name">
							<BImg
								:src="getImageUrl(im.thumb_file)" 
								:alt="im.name" 
								class="img-fluid a3" lazy
							/>
							<br><h6>{{ im.name }}</h6>
						</button>
						<button v-else-if="(im.type == 'attachments2' || im.type == 'product')"
										@click.prevent="modalchangebig(index)" 
										type="button" 
										class="btn btn-link">
							<BImg
								:src="getImageUrl(im.thumb_file)" 
								:alt="im.name" 
								class="img-fluid a12" lazy
							/>
						</button>
					</div>
				</div>
			</div>

			<BModal  id="modal-multi-1" centered size="xl" 
								:title="attachments[id].name" ok-only
								modal-class="lightbox-img"
								v-model="viewModalFoto"
								header-close-class="btn btn-dark"
			>
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
						<div class="arrows-l" @click="before">
							<button class="btn btn-dark text-light" :title="store.texts.galery_arrows_before">&#10094;</button>
						</div>
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
.thumbpanorama {
	display: grid;
	grid-template-columns: repeat(1, 1fr);
	grid-gap: 0.5rem;
	overflow: auto;
	max-height: 80vh;
	grid-auto-rows: 7rem;

	> div{
		position: relative;
		background-color: rgba(44,44,44,1.00);
		padding: 1rem;
	}
	img{
		position: absolute;
		max-width: 90%; 
		max-height: 90%;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		border: solid 3px #ddd;
		color: transparent;
	}	
	img.podclanok {
		opacity: .5;
	}
	.h4-podclanok {
		position: absolute;
		max-width: 90%; max-height: 90%;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		color: #ddd;
		text-align: center;
	}
}
.btn:focus {
	box-shadow: none;
}
</style>