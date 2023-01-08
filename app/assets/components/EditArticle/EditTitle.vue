<script>
/** 
 * Component EditTitle
 * Posledná zmena(last change): 08.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 * 
 */
import EditTexts from "./EditTexts.vue";
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  props: {
    basePath: String,
    article_id: String, // hlavne_menu_lang.id
    title: String,
    title_text: String,
    title_admin: String,
    title_last_change: String,
    title_platnost_do: String,
    title_zadal: String,
    edit_enabled: String,
    link: String,
    link_to_admin: String,
    article_hlavicka: String,
  },
  components: {
    EditTexts,
  },
  data() {
    return {
      show: true,
      article: {},
    }
  },
  methods: {
    onSubmit(event) {
      event.preventDefault()
      // Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
      if (event.submitter.classList.contains("main-submit")) {
        let odkaz = this.basePath + '/api/menu/h1save/' + this.article_id
        let vm = this
        axios.post(odkaz, {
            article: this.article,
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
    onReset(event) {
      event.preventDefault()
      if (event.explicitOriginalTarget.classList.contains("main-reset")) {
        window.location.href = this.link;
      }
    }
  },
  mounted() {
    if (this.article_id !== "0") { // Len pri editácii
      // Načítanie údajov priamo z DB
      let odkaz = this.basePath + '/api/menu/getonemenuarticle/' + this.article_id
      axios.get(odkaz)
            .then(response => {
              this.article = response.data
              //console.log(this.article)
            })
            .catch((error) => {
              console.log(odkaz);
              console.log(error);
            });
    }
  },

}
</script>

<template>
  <span>
  <h1 class="title-article">
    {{ article.view_name }}
    <small v-if="article.h1part2 != null">
      {{ article.h1part2 }}
    </small>
    <div v-if="edit_enabled == '1'"
        class="btn-group btn-group-sm editable" 
        role="group" 
        aria-label="Button group with nested dropdown"
    >
      <b-button
        variant="outline-warning"
        size="sm"
        v-b-modal.modal-1
        :title="title"
      >
        <i class="fas fa-pen"></i>
      </b-button>
      <b-button 
        variant="outline-warning"
        size="sm"
        :title="title_text + '- pokusná funkcionalita'"
        v-b-modal.editArticleTextsModal
      >
        <i class="fa-solid fa-file-lines"></i>
      </b-button>
      <a 
        class="btn btn-sm btn-outline-warning"
        :href="link_to_admin"
        :title="title_admin"
      >
        <i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i>
      </a>
    </div>

    <b-modal id="modal-1" centered 
      :title="title" 
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
            v-model="article.view_name"
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
            v-model="article.menu_name"
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
            v-model="article.h1part2"
            type="text"
          ></b-form-input>
        </b-form-group>
        <b-button type="submit" variant="success" class="main-submit">Ulož</b-button>&nbsp;
        <b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>
      </b-form>
    </b-modal>
    

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
      <edit-texts
          :id_hlavne_menu="article.id_hlavne_menu"
          :base-path="basePath"
          :link="link"
          :article-text="article.text_c"
          :article_id="article.id"
        >
      </edit-texts>
    </b-modal>
  </h1>
  <div>
    <small v-if="article_hlavicka & 1" class="title-info">
      {{ title_last_change }}{{ article.modified }}
    </small>
    <small v-if="article.datum_platnosti != null" class="title-info">
      {{ title_platnost_do }}{{ article.datum_platnosti }}
    </small>
    <small v-if="article_hlavicka & 2" class="title-info">
      {{ title_zadal }}{{ article.owner }}
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