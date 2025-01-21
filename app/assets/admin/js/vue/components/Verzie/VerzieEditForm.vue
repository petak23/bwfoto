<script>
/**
 * Komponenta pre formulár na zadanie/editáciu verzií.
 * Posledna zmena 24.11.2022
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */

import EditTexts from '../../../../../components/EditArticle/EditTexts.vue';
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  /*components: {
    Tiptap,
  },*/
  props: {
    id: {
      type: String,
      required: true
    },
    basePath: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      form: {
        id: 0,
        id_user_main: 0,
        number: "",
        text:"",
        modified: ""
      },
      editor: null,
      back_link: '/administration/verzie/',
    }
  },
  /*beforeDestroy() {
    this.editor.destroy()
  },*/
  methods: {
    onSubmit(event) {
      event.preventDefault()
      // Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
      if (event.submitter.classList.contains("main-submit")) {
        let to_save = [this.form.number, this.form.text]
        let odkaz = this.basePath + '/api/verzie/save/' + this.id
        /* this.selected.forEach(function(item) {
          to_del.push(item.id)
        })*/
        let vm = this
        axios.post(odkaz, {
            to_save,
          })
          .then(function (response) {
            //console.log(response)
            // https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
            window.location.href = vm.basePath + vm.back_link;
          
            /*vm.$root.$emit('flash_message', 
                              [{ 'message': 'Uložené', 
                                'type':'success',
                                'heading': 'Uloženie'
                                }])*/
          })
          .catch(function (error) {
            console.log(odkaz)
            console.log(error)
            vm.$root.$emit('flash_message', 
                              [{ 'message': 'Pri vymazávaní došlo k chybe',
                                'type':'danger',
                                'heading': 'Chyba'
                                }])
          });
      }
    },
    onReset(event) {
      event.preventDefault()
      if (event.explicitOriginalTarget.classList.contains("main-reset")) {
        window.location.href = this.basePath + this.back_link;
      }
    },
  },
  mounted() {
    if (this.id !== "0") { // Len pri editácii
      // Načítanie údajov priamo z DB
      let odkaz = this.basePath + '/api/verzie/getversion/' + this.id
      axios.get(odkaz)
            .then(response => {
              //console.log(response.data)
              //this.dataSet(this.data_origin)
              this.form.id = response.data.id
              this.form.id_user_main = response.data.id_user_main
              this.form.number = response.data.cislo
              this.form.text = response.data.text
              this.form.modified = response.data.modified
            })
            .catch((error) => {
              console.log(odkaz);
              console.log(error);
            });
    }
  },
  created() {
    this.$root.$on('tiptap_input', data => {
	components: {
		Tiptap,
	},
	props: {
		id: {
			type: String,
			required: true
		},
		basePath: {
			type: String,
			required: true
		},
	},
	data() {
		return {
			form: {
				id: 0,
				id_user_main: 0,
				number: "",
				text:"",
				modified: ""
			},
			editor: null,
			back_link: '/administration/verzie/',
		}
	},
	beforeDestroy() {
		this.editor.destroy()
	},
	methods: {
		onSubmit(event) {
			event.preventDefault()
			// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
			if (event.submitter.classList.contains("main-submit")) {
				let to_save = [this.form.number, this.form.text]
				let odkaz = this.basePath + '/api/verzie/save/' + this.id
				/* this.selected.forEach(function(item) {
					to_del.push(item.id)
				})*/
				let vm = this
				axios.post(odkaz, {
						to_save,
					})
					.then(function (response) {
						//console.log(response)
						// https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
						window.location.href = vm.basePath + vm.back_link;
					
						/*vm.$root.$emit('flash_message', 
															[{ 'message': 'Uložené', 
																'type':'success',
																'heading': 'Uloženie'
																}])*/
					})
					.catch(function (error) {
						console.log(odkaz)
						console.log(error)
						vm.$root.$emit('flash_message', 
															[{ 'message': 'Pri vymazávaní došlo k chybe',
																'type':'danger',
																'heading': 'Chyba'
																}])
					});
			}
		},
		onReset(event) {
			event.preventDefault()
			if (event.explicitOriginalTarget.classList.contains("main-reset")) {
				window.location.href = this.basePath + this.back_link;
			}
		},
	},
	mounted() {
		if (this.id !== "0") { // Len pri editácii
			// Načítanie údajov priamo z DB
			let odkaz = this.basePath + '/api/verzie/getversion/' + this.id
			axios.get(odkaz)
						.then(response => {
							//console.log(response.data)
							//this.dataSet(this.data_origin)
							this.form.id = response.data.id
							this.form.id_user_main = response.data.id_user_main
							this.form.number = response.data.cislo
							this.form.text = response.data.text
							this.form.modified = response.data.modified
						})
						.catch((error) => {
							console.log(odkaz);
							console.log(error);
						});
		}
	},
	created() {
		this.$root.$on('tiptap_input', data => {
			//console.log(data)
			this.form.text = data
		})
	}
}
</script>

<template>
	<b-form @submit="onSubmit" @reset="onReset" class="my-tip-tap">
		<b-form-group
			id="input-number-gr"
			label="Číslo verzie:"
			label-for="input-number"
		>
			<b-form-input
				id="input-number"
				size="sm"
				v-model="form.number"
				type="text"
				required
				autofocus
				>
			</b-form-input>
		</b-form-group>
		<b-form-group
			id="input-text-gr"
			label="Popis verzie:"
			label-for="input-text"
		>
			<tiptap 
				:value="form.text"/>

			<edit-texts
				:editArticleTextsDialogView="editArticleTextsDialogView"
				@saveText="editArticleTextsDialogView = false"
			/>
		</b-form-group>
		<input type="hidden" :value="form.id">
		<b-button type="submit" variant="success" class="main-submit">Ulož</b-button>
		<b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>
	</b-form>

</template>
