<script>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 14.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.8
 * Z kniznica pouzite súbory a upravene: https://github.com/seanghay/vue-photo-collage
 */
import PhotoCollageWrapper from "./vue-photo-collage/PhotoCollageWrapper.vue";
import axios from 'axios'

export default {
	components: {
		PhotoCollageWrapper,
	},
	props: {
		article_id: {
			type: String,
			required: true,
		},
		basePath: {
			type: String,
			required: true,
		},
		maxrandompercwidth: { // Percento, o ktoré sa môže meniť naviac šírka fotky
			type: String,
			default: 20,
		},
		myschema: String,
		edit_enabled: {
			type: String,
			default: '0'
		}
	},
	data() {
		return {
			id: 0,
			attachments: [],

			collage: { // Objekt pre koláž
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
			},
			image: {
				name: "",
				main_file: "",
				description: null,
				id_collage: 0,
			},
			id_sch: 0,
			schstr: "",
			schstr_old: "",
			text_before: 'Pred',
			text_after: 'Po',
			sch: [
				/*{
					// Max. šírka koláže pre ktorú platí
					max_width: 320,  
					// Počet fotiek v jednotlivých riadkoch
					schema: [2, 1, 3, 4, 4, 3, 4, 4], 
					// Výška jednotlivých riadkov v px
					height: [85, 60, 85, 60, 70, 95, 70, 60],
					// Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku:
					// Ak je zadané číslo väčšie ako 0 (1,2,...) tak tá konkrétna bude širšia, 
					// ak je zadané 0 generuje sa náhodne,
					// ak je zadané -1 všetky fotky v riadku budú rovnaké.
					widerPhotoId: [-1, 2, 0, 1, -1, 0, 2, 1], 
				},
				{
					max_width: 700,
					schema: [4, 3, 5, 4, 3, 4, 5, 4],
					height: [130, 175, 105, 120, 175, 130, 105, 120],
					widerPhotoId: [-1, 0, 2, 0, -1, 2, 3, 1],
				},
				{
					max_width: 1300,
					schema: [6, 7, 8, 7, 6, 8, 7, 6],
					height: [225, 170, 135, 170, 225, 135, 170, 225],
					widerPhotoId: [2, -1, 0, 2, -1, 1, 2, 1],
				},
				{
					max_width: 10000,
					schema: [6, 7, 8, 7, 6, 8, 7, 6],
					height: [318, 240, 190, 240, 318, 190, 240, 318],
					widerPhotoId: [3, 0, -1, 2, 2, -1, 3, 4],
				},*/
			],
			// Koniec sch -----
		}
	},
	methods: {
		itemClickHandler(data, i) {
			// click event
			let odkaz = this.basePath + '/api/documents/document/' + data.id_foto
			axios.get(odkaz)
							.then(response => {
								this.image = response.data
								this.image.id_collage = data.id
								this.$bvModal.show("modal-multi-1")
							})
							.catch((error) => {
								console.log(odkaz)
								console.log(error)
							})
		},
		matchHeight () {
			this.computeLayout(this.$refs.imgDetail.clientWidth)
		},
		computeLayout(client_width) {
			let res = { 
				max_width: 0,  
				schema: [],  
				height: [],  
				layout: [],
				widerPhotoId: []
			};
			this.sch.forEach(x => {
				if (client_width < x.max_width && res.max_width == 0) {
					res = x
				}
			})
			res.layout = [] // Musí ostať inak nefunguje !?!
			this.collage.photos = this.attachments
			let i = this.collage.photos.length
			let r = 0 // riadok
			do {
				// Zisti počet foto v riadku. Ak by bolo 0 tak nahraď to 1
				let fr = res.schema[r] > 0 ? res.schema[r] : 1;
				res.layout.push( fr )

				r = r + 1 >= res.schema.length ? 0 : r + 1 
				i -= fr
			}
			while (i > 0);
			this.collage.width = client_width + 'px';
			this.collage.height = res.height
			this.collage.layout = res.layout
			this.collage.maxRandomPercWidth = parseInt(this.maxrandompercwidth)
			this.collage.widerPhotoId = res.widerPhotoId
		},
		loadSchema() {
			let odkaz = this.basePath + '/api/menu/getonemenuarticlesp/fotocollage-settings'
			axios.get(odkaz)
				.then(response => {
					//this.article = response.data
					this.sch = JSON.parse(response.data.text_c)
					this.id_sch = JSON.parse(response.data.id)
					this.schstr = JSON.stringify(this.sch, null, 2)
					this.schstr_old = this.schstr
					//console.log(this.sch)
					this.loadPictures()
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		},
		loadPictures() {
			// Načítanie údajov priamo z DB
			let odkaz = this.basePath + '/api/documents/getfotocollage/' + this.article_id
			axios.get(odkaz)
				.then(response => {
					this.attachments = response.data
					//console.log(this.attachments)
					this.computeLayout(this.$refs.imgDetail.clientWidth)

				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		},
		onSubmitSch(event) {
			event.preventDefault()
			try {
				this.sch = JSON.parse(this.schstr)
				this.computeLayout(this.$refs.imgDetail.clientWidth)
				// Aby sa formulár odoslal, len ak je stačené tlačítko s class="sch-submit"
				if (event.submitter.classList.contains("sch-submit")) {
					let odkaz = this.basePath + '/api/menu/textssave/' + this.id_sch
					let vm = this
					let data = {
						texts: this.schstr
					}
					axios.post(odkaz, data)
						.then(function (response) {
							//console.log(response.data)
							vm.$root.$emit('flash_message', [{
								'message': 'Schéma bola uložená.',
								'type': 'success',
								'heading': 'Podarilo sa...'
							}])
							setTimeout(() => {
								vm.$bvModal.hide("edit-collage")
							}, 500)
						})
						.catch(function (error) {
							console.log(odkaz)
							console.log(error)
						});
				}
			}
			catch (e) {
				alert("Chybné zadanie schémy...");
			}
			
		},
		onResetSch(event) {
			event.preventDefault()
			this.schstr = this.schstr_old
			this.$bvModal.hide("edit-collage")
		},
		keyPush(event) {
			if (this.uroven <= 1) {
				switch (event.key) {
					case "ArrowLeft":
						this.before();
						break;
					case "ArrowRight":
						this.after();
						break;
				}
			}
		},
		// Zmena id na predošlé
		before() {
			let id = this.image.id_collage <= 0 ? (this.attachments.length - 1) : this.image.id_collage - 1
			this.itemClickHandler({
				id: id,
				id_foto: this.attachments[id].id_foto
			}, 1)
		},
		// Zmena id na  nasledujúce
		after() {
			this.id = this.id >= (this.attachments.length - 1) ? 0 : this.id + 1;
			let id = this.image.id_collage >= (this.attachments.length - 1) ? 0 : this.image.id_collage + 1
			this.itemClickHandler({
				id: id,
				id_foto: this.attachments[id].id_foto
			}, 1)
		}, 
	},
	created() {
		window.addEventListener("resize", this.matchHeight);
	},
	destroyed() {
		window.removeEventListener("resize", this.matchHeight);
	},
	computed: {},
	mounted () {
		this.loadSchema();

		/* Naviazanie na sledovanie zmeny veľkosti stránky */
		this.matchHeight();
	},

};
</script>

<template>
	<span>
		<ul class="nav justify-content-end" v-if="edit_enabled == '1'">
			<li class="nav-item">
				<b-button 
					variant="outline-warning"
					size="sm"
					v-b-modal.edit-collage
					title="Editácia schémy">
					<i class="fas fa-pen"></i>
				</b-button>
			</li>
		</ul>
		<b-modal 
			id="edit-collage"
			v-if="edit_enabled == '1'"
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
			ref="modal1fo"
			:hide-footer="true"
		>
			<b-form @submit="onSubmitSch" @reset="onResetSch">
				<b-form-group id="input-group-1" label="Schéma:" label-for="schema">
					<b-form-textarea 
						id="schema" 
						v-model="schstr"
						rows="15"> <!-- cols="45" -->
					</b-form-textarea>
				</b-form-group>
				<b-button type="submit" variant="success" class="sch-submit">Ulož</b-button>&nbsp;
				<b-button type="reset" variant="secondary" class="sch-reset">Cancel</b-button>
			</b-form>
		</b-modal>

		<div ref="imgDetail" id="imgDetail"> 
			<photo-collage-wrapper 
				gapSize="6px"
				@itemClick="itemClickHandler"
				v-bind="collage">
			</photo-collage-wrapper>
		</div>
		
		<b-modal  id="modal-multi-1" centered size="xl" 
							:title="image.name" ok-only
							modal-class="lightbox-img"
							ref="modal1fo">
			<div class="modal-content">
				<div class="modal-body my-img-content">  
					<img :src="basePath + '/' + image.main_file" 
								:alt="image.name" 
								id="big-main-img"
								class="" />
					<div class="text-center description" v-if="image.description != null">
						{{ image.description }}
					</div>
				</div>
				<div class="arrows-overlay">
					<div class="arrows-l" @click="before">
						<a href="#" class="text-light" :title="text_before">&#10094;
						</a>
					</div>
					<div class="go-to-hight" v-touch="{
											left: () => swipe('Left'),
											right: () => swipe('Right'),
											up: () => swipe('Up'),
											down: () => swipe('Down')
										}">
					</div>
					<div class="arrows-r flex-row-reverse" @click="after">
						<a href="#" class="text-light" :title="text_after">&#10095;
						</a>
					</div>
				</div>
			</div>
		</b-modal>
	</span>
</template>

<style lang="scss" scoped>
	img {
		max-width: 80vw;
		height: 80vh;
	}
</style>