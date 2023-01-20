<script>
/** 
 * Component EditTexts
 * Posledná zmena(last change): 11.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.6
 * 
 */
import Tiptap from "../Tiptap/tiptap-editor.vue"
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  components: {
    Tiptap,
  },
  props: {
    basePath: String,
    link: String,
    article: Object,
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
      if (event.submitter.classList.contains("main-submit")) {
        let odkaz = this.basePath + '/api/menu/textssave/' + this.article.id
        let vm = this
        let data = {
              texts: this.textin
            }
        axios.post(odkaz, data)
          .then(function (response) {
            //console.log(response.data)
            vm.$root.$emit('flash_message', [{'message':'Text bol uložený.', 
                                              'type':'success',
                                              'heading': 'Podarilo sa...'
                                            }])
            setTimeout(() => {
              vm.$bvModal.hide("editArticleTextsModal")
              vm.$root.$emit("texts-save", data)
            }, 500)
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
        this.$bvModal.hide("editArticleTextsModal")
        this.textin = this.article.text_c
      }
    },
  },
  watch: {
    article: function (newArticle) {
      this.textin = this.article.text_c
    }
  },
  created: function () {
    this.$root.$on('tiptap_input', data => {
      this.textin = data
		})
  },
}
</script>

<template>
  <b-modal 
      id="editArticleTextsModal" 
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
        <tiptap :value="textin"/>
      </b-form-group>
        
      <b-button type="submit" variant="success" class="main-submit">Ulož</b-button>
      <b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>

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