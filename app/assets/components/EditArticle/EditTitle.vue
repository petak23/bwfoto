<script>
/** 
 * Component EditTitle
 * Posledná zmena(last change): 15.12.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 * 
 */
import EditTexts from "./EditTexts.vue"
import EditMenu from "./EditMenu.vue"
import UserChange from "./UserChange.vue"
import MainService from "../../front/js/vue/services/MainService.js"

export default {
	props: {
		edit_enabled: {
			type: Boolean,
			default: false
		},
		link: String,
		link_to_admin: String,
	},
	components: {
		EditTexts,
		EditMenu,
		UserChange,
	},
	data() {
		return {
			show: true,
			art_title: {
				menu_name: '',
				h1part2: '',
				view_name: '',
				template: 0
			},
			templates: null,
			article_hlavicka: 0,
		}
	},
	methods: {
		saveErr() {
			this.$root.$emit('flash_message', [{
				'message': 'Došlo k chybe a položka sa neuložila.',
				'type': 'danger',
				'heading': 'Oopps ...',
				'timeout': 10000,
			}])
		},
		onSubmit(event) {
			event.preventDefault()
			// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
			if (event.submitter.classList.contains("main-submit")) {
				let vm = this
				let data = {
							menu_name: this.art_title.menu_name,
							h1part2: this.art_title.h1part2,
							view_name: this.art_title.view_name,
							template: this.art_title.template,
						}
				MainService.postH1Save(this.$store.state.article.id, { article: data })
					.then(function (response) {
						//console.log(response.data.result)
						if (response.data.result == "OK") {
							vm.$root.$emit('flash_message', [{'message':'Položka v nadpise bola uložená.', 
																								'type':'success',
																								'heading': 'Uložené ...',
																								'timeout': 5000,
																							}])
							let	td = response.data
							delete td.result
							vm.$store.commit('SET_INIT_ARTICLE', td)
							vm.$root.$emit("reload-main-menu", [])
						} else {
							vm.saveErr()
						}
						setTimeout(() => {
							vm.$bvModal.hide("modal-edit-title-form")
						}, 500)
					})
					.catch(function (error) {
						vm.saveErr()
						console.log(error)
					});
			}
		},
		onReset(event) {
			event.preventDefault()
			if (event.explicitOriginalTarget.classList.contains("main-reset")) {
				this.$bvModal.hide("modal-edit-title-form")
				this.getArtTitle()
			}
		},
		getArtTitle() {
			this.art_title.menu_name = this.$store.state.article.menu_name
			this.art_title.h1part2 = this.$store.state.article.h1part2
			this.art_title.view_name = this.$store.state.article.view_name
			this.art_title.template = this.$store.state.article.template
		}
	},
	watch: {
		'$store.state.article.view_name': function () {
			this.getArtTitle()
		}
	},
	mounted() {
		if (this.edit_enabled) {
			// Načítanie údajov priamo z DB
			MainService.getForFormTemplate()
				.then(response => {
					this.templates = response.data
				})
				.catch((error) => {
					console.log(error);
				});
		}
	
		MainService.getFromUdaj("clanok_hlavicka")
			.then(response => {
				this.article_hlavicka = response.data.result
			})
			.catch((error) => {
				console.error(error);
			});
	},
}
</script>

<template>
	<span>
	<h1 class="title-article" v-if="$store.state.article.view_name">
		{{ $store.state.article.view_name }}
		<small v-if="$store.state.article.h1part2 != null">
			{{ $store.state.article.h1part2 }}
		</small>
		<edit-menu
			:edit_enabled="edit_enabled"
			:link_to_admin="link_to_admin"
		>
		</edit-menu>

		<b-modal id="modal-edit-title-form" centered
			v-if="edit_enabled" 
			:title="$store.state.texts.base_edit_title" 
			header-bg-variant="dark"
			header-text-variant="light"
			body-bg-variant="dark"
			body-text-variant="light"
			footer-bg-variant="dark"
			footer-text-variant="light" 
			:hide-footer="true" 
		>
			<b-form @submit="onSubmit" @reset="onReset" v-if="show">
				<b-form-group
					id="input-group-1"
					label="Názov zobrazený v nadpise:"
					label-for="view_name"
				>
					<b-form-input
						id="view_name"
						v-model="art_title.view_name"
						type="text"
						required
					></b-form-input>
				</b-form-group>
				<b-form-group
					id="input-group-2"
					label="Názov zobrazený v menu:"
					label-for="menu_name"
				>
					<b-form-input
						id="menu_name"
						v-model="art_title.menu_name"
						type="text"
						description="Ak necháte pole prázdne použije sa rovnaký ako pre nadpis."
					></b-form-input>
				</b-form-group>
				<b-form-group
					id="input-group-2"
					label="Podnatpis:"
					label-for="h1part2"
				>
					<b-form-input
						id="h1part2"
						v-model="art_title.h1part2"
						type="text"
					></b-form-input>
				</b-form-group>
				<b-form-group
					id="input-group-3"
					label="Použitá šablóna:"
					label-for="template"
				>
					<b-form-select 
						id="template"
						v-model="art_title.template" :options="templates">
					</b-form-select>
				</b-form-group>
				<b-button type="submit" variant="success" class="main-submit mr-2">Ulož</b-button>
				<b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>
			</b-form>
		</b-modal>
		
		<edit-texts
			v-if="edit_enabled"
			:link="link"
		>
		</edit-texts>
	</h1>
	<div>
		<small v-if="article_hlavicka & 1 || edit_enabled" class="title-info">
			{{ $store.state.texts.base_last_change }}{{ art_title.modified }}
		</small>
		<small v-if="$store.state.article.datum_platnosti != null" class="title-info">
			{{ $store.state.texts.base_platnost_do }}{{ art_title.datum_platnosti }}
		</small>
		<small v-if="article_hlavicka & 2 || edit_enabled" class="title-info">
			{{ $store.state.texts.base_zadal }}{{ $store.state.article.owner }}
			<user-change></user-change>
		</small>
	</div>
	</span>
</template>

<style scoped>
	.title-info {
		border-right: 1px solid #ddd;
		margin-right: .5ex;
		padding-right: .25ex;
	}
	.title-info:last-child {
		border-right: 0;
	}
</style>