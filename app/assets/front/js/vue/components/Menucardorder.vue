<script>
/** 
 * Component Menucardorder
 * Posledná zmena(last change): 04.12.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.5
 */
import MainService from '../services/MainService.js'

export default {
	props: {
		filesPath: { // Adresár k súborom bez basePath
			type: String,
			required: true
		},
	},
	data() {
		return {
			items: [],
			mainProps: {
				center: true,
				fluidGrow: true,
				blank: true,
				blankColor: '#bbb',
			},
			edit_enabled: false,
		}
	},
	methods: {
		moveArticle: function(ai) {
			let from = ai.from.index
			let to = ai.to.index
			let out = []
			for (let i = 0; i < this.items.length; i++) {
				out.push(this.items[i].id)
			}
			// https://www.codegrepper.com/code-examples/javascript/change+index+order+in+array+javascript
			let element = out[from];
			out.splice(from, 1);
			out.splice(to, 0, element);
			MainService.postSaveOrderSubmenu(this.$store.state.article.id_hlavne_menu, { items: out })
				.then(function (response) {
					//console.log(response.data);
					if (response.data.result == 'OK') {
						//this.items = newItem
						console.log("OK")
					}
				})
				.catch(function (error) {
					console.log(error);
				});
		},
		getSubmenu: function() {
			this.items = []
			MainService.getSubmenuFront(this.$store.state.main_menu_active)
				.then(response => {
					this.items = Object.values(response.data)
				})
				.catch((error) => {
					console.log(error);
				});
		},
	},
	mounted () {
		if (this.$store.state.main_menu_active > 0) this.getSubmenu()
	},
	computed: {
		filesDir() {
			return document.getElementById('vueapp').dataset.baseUrl + '/' + this.filesPath
		},
	},
	watch: {
		'$store.state.main_menu_active': function () {
			this.getSubmenu()
		},
		'$store.state.user': function () {
			let vm = this
			let data = {
				resource: 'Api:Menu',
				action: 'saveordersubmenu	',
			}
			MainService.postIsAllowed(this.$store.state.user.id, data)
				.then(function (response) {
					vm.edit_enabled = response.data.result == 1
				})
				.catch(function (error) {
					console.log(error);
				});
		}
	},
};
</script>

<template>
	<section id="webAlbums" class="row">
		<div class="col-12 menu-section">
			<dnd-zone
				:transition-duration="0.3"
				handle-class="handle"
				v-on:move="moveArticle"
			>
				<dnd-container
					:dnd-model="items"
					dnd-id="grid-example"
					class="row"
					dense
				>
					<dnd-item
						v-for="image in items"
						:key="image.id"
						:dnd-id="image.id"
						:dnd-model="image"
					>
						<div class="col-12 col-sm-6 col-md-4 col-xxl-3 album position-relative">
							<i 
								v-if="edit_enabled"
								class="fas fa-grip-vertical handle position-absolute"
								style="top: 0; left: 0"
							></i>
							<a :href="image.link" :title="image.name">
								<b-img-lazy 
									v-if="image.avatar != null"
									v-bind="mainProps" 
									:src="filesDir + image.avatar" 
									class="img-responsive img-square"
									:alt="image.name">
								</b-img-lazy>
								<i v-if="image.node_class != null" :class="image.node_class"> </i>
								<h3>{{ image.name }}</h3>
							</a>
							<div class="caption">
								<p v-if="image.anotacia" class="popis">
									{{ image.anotacia }} 
									<a :href="image.link" title="more">»»»</a>
								</p>
							</div>
						</div>

					</dnd-item>
				</dnd-container>
			</dnd-zone>
		</div>
	</section>
</template>