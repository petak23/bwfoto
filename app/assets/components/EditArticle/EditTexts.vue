<script>
/** 
 * Component EditTexts
 * Posledná zmena(last change): 07.12.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.4
 * 
 */
import Tiptap from "../Tiptap/tiptap-editor.vue"
import axios from 'axios'
//import _ from 'lodash'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  components: {
    Tiptap,
  },
  props: {
    basePath: String,
    link: String,
    article_id: String, // hlavne_menu_lang.id
  },
  data() {
    return {
      textin: '',
      show: true,
      editor: null,
      article: {},
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
        let odkaz = this.basePath + '/api/menu/textssave/' + this.id_hlavne_menu
        let vm = this
        axios.post(odkaz, {
            texts: this.article.text_c,
          })
          .then(function (response) {
            //vm.textin = response.data
            //console.log(response.data)
            // https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
            window.location.href = vm.link;
          })
          .catch(function (error) {
            console.log(odkaz)
            console.log(error)
          });      
      }
    },
    onCancel(event) {
      event.preventDefault()
      if (event.explicitOriginalTarget.classList.contains("main-reset")) {
        window.location.href = this.link;
      }
    },
  },
  watch: {},
  created: function () {
    //this.textin = this.articleText
    if (this.article_id !== "0") { // Len pri editácii
      // Načítanie údajov priamo z DB
      let odkaz = this.basePath + '/api/menu/getonemenuarticle/' + this.article_id
      axios.get(odkaz)
            .then(response => {
              this.article = response.data
              console.log(this.article)
            })
            .catch((error) => {
              console.log(odkaz);
              console.log(error);
            });
    }
    this.$root.$on('tiptap_input', data => {
			//console.log(data)
      this.textin = data
		})
  },
}
</script>

<template>
  <div>
    <b-form @submit="onSubmit" @reset="onCancel" v-if="show">
      <b-form-group
        id="input-text-gr"
        label=""
        label-for="input-text"
      >
        <tiptap :value="article.text_c"/>
      </b-form-group>
        
      <b-button type="submit" variant="success" class="main-submit">Ulož</b-button>
      <b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>

    </b-form>
  </div>
</template>

<style>
.editor__header {
  background-color: #aaa;
  border-radius: .5ex;
  padding-left: .5ex;
}
</style>