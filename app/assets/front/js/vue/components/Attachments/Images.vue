<script setup>
/**
 * Component Images.
 * Posledna zmena 30.10.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.6
 * 
 */
import { ref, watch } from 'vue'
import MainService from '../../services/MainService'
import { BModal, BImg } from 'bootstrap-vue-next'

import { useMainStore } from '../../store/main'
const store = useMainStore()

const images = ref(null) 
const id = ref(0)

const imageDialogView = ref(false)

const getAttachments = () => {
	MainService.getVisibleAttachments(store.article.id_hlavne_menu)
		.then(response => {
			//console.log(response.data.length)
			images.value = response.data.length > 0 ? response.data : null
		})
		.catch((error) => {
			console.error(error);
		});
}

// Generovanie url pre lazyloading obrázky
const getImageUrl = (text) => {
	return /*store.udaje_webu.config.prilohy_dir + */ text
}

/*const changebig = (new_id) => {
	id = new_id
}*/

/*const keyPush = (event) => {
	if (uroven <= 1) {
		switch (event.key) {
			case "ArrowLeft":
				before();
				break;
			case "ArrowRight":
				after();
				break;
		}
	}
}*/

const modalchangebig = (new_id) => {
	id.value = new_id;
	// TODO !!!
	//this.$bvModal.show("modal-images-attachments")
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
	id.value = id <= 0 ? (images.length - 1) : id - 1;
}
// Zmena id na  nasledujúce
const after = () => {
	id = id >= (images.length - 1) ? 0 : id + 1;
}

watch(() => store.article, () => {
	if (store.article != null) getAttachments()
})
</script>

<template>
	<div v-if="images != null" class="row attachments">
		<div class="col-12">
			<hr />
			<h4>{{ store.texts.clanky_h3_prilohy_images }}:</h4>
		</div>
		<div class="col-12 thumbgrid">
			<div v-for="(im, index) in images" :key="im.id">
				<a  @click.prevent="modalchangebig(index)"
						href="#"
						:title="im.name"
						class="thumb-a" 
						:class="index == id ? ', selected' : null">
						<BImg 
							:src="getImageUrl(im.thumb_file)"
							:alt="im.name" class="img-fluid"
							lazy 
						/>
				</a>
			</div>
			<BModal
				v-model:visible="imageDialogView" 
				centered 
				:title="images[id].name" 
				class="lightbox-img"
				size="xl"
				hide-footer
			>
				<div class="modal-content">
					<div class="modal-body my-img-content">

						<img :src="getImageUrl(images[id].main_file)" 
									:alt="images[id].name" 
									id="big-main-img"
									/>
						<div class="text-center description" v-if="images[id].description != null">
							{{ images[id].description }}
						</div>
					</div>
					<div class="arrows-overlay">
						<div class="arrows-l"
								@click="before">
							<a href="#" class="text-light"   
									:title="store.texts.galery_arrows_before">&#10094;
							</a>
						</div>
						<!--div class="go-to-hight"
								v-touch="{
									left: () => swipe('Left'),
									right: () => swipe('Right'),
									up: () => swipe('Up'),
									down: () => swipe('Down')
								}">
						</!--div-->
						<div class="arrows-r flex-row-reverse"
								@click="after">
							<a href="#" class="text-light"
									:title="store.texts.galery_arrows_after">&#10095;
							</a>
						</div>
					</div>
				</div>
			</BModal>
		</div>
	</div>
</template>

<style scoped>
.thumbgrid {
	max-height: inherit;
	overflow: inherit;
}
</style>