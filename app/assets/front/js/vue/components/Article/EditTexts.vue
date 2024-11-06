<script>
/** 
 * Component EditTexts
 * Posledná zmena(last change): 07.12.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */
//import Tiptap from "../../../../../components/Tiptap/tiptap-editor.vue"//"../Tiptap/tiptap-editor.vue"
import MainService from '../../services/MainService.js'

export default {
	/*components: {
		Tiptap,
	},*/
	props: {
		link: String,
		button_prefix: { // Prefix classu odosielacích tlačidiel
			type: String,
			default: "main"
		},
		article: {
			type: Object,
			required: true,
		},
		modal_name: { // Názov modlneho okna
			type: String,
			default: 'EditTextModal',
		}

	},
	data() {
		return {
			textin: '',
			show: true,
			editor: null,
		}
	},
	beforeDestroy() {
		this.editor.destroy()
	},
	methods: {
		onSubmit(event) {
			event.preventDefault()
			// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
			if (event.submitter.classList.contains(this.button_prefix + "-submit")) {
				let vm = this
				MainService.postTextSave(this.article.id, { texts: this.textin })
					.then(function (response) {
						vm.$root.$emit('flash_message', [{'message':'Text bol uložený.', 
																							'type':'success',
																							'heading': 'Podarilo sa...'
																						}])
						vm.$root.$emit("reload-article-" + vm.article.id, [response.data])
						setTimeout(() => {
							vm.$bvModal.hide(vm.modal_name)
						}, 300)
					})
					.catch(function (error) {
						console.log(error)
					});      
			}
		},
		onCancel(event) {
			event.preventDefault()
			if (event.explicitOriginalTarget.classList.contains(this.button_prefix + "-reset")) {
				this.$bvModal.hide(this.modal_name)
				this.textin = this.article.text_c
			}
		},
	},
	watch: {
		article: function (newArticle) {
			this.textin = this.article.text_c
		},
	},
	mounted () {
		this.textin = this.article.text_c
		this.$root.$on('tip-tap-input' + this.article.id, data => {
			this.textin = data
		})
	},
}
</script>

<template>
	<b-modal 
			:id="modal_name" 
			title="Editácia textu článku"
			header-bg-variant="dark"
			header-text-variant="light"
			body-bg-variant="dark"
			body-text-variant="light"
			footer-bg-variant="dark"
			footer-text-variant="light" 
			:hide-footer="true" 
		>
		<b-form @submit="onSubmit" @reset="onCancel" v-if="show">
			<b-form-group
				id="input-text-gr"
				label=""
				label-for="input-text"
			>
				<tiptap 
					:value="textin"
					:root-emit-name="'tip-tap-input' + article.id"
				/>
			</b-form-group>
				
			<b-button 
				type="submit" variant="success"
				class="mr-2" 
				:class="button_prefix + '-submit'"
			>
				Ulož
			</b-button>
			<b-button
				type="reset" variant="secondary"
				:class="button_prefix + '-reset'"
			>
				Cancel
			</b-button>

		</b-form>
	</b-modal>
</template>

<style>
.editor__header {
	background-color: #aaa;
	border-radius: .5ex;
	padding-left: .5ex;
}
</style>