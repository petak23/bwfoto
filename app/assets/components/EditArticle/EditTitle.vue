<script>
/** 
 * Component EditTitle
 * Posledná zmena(last change): 19.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 */
import EditTexts from "./EditTexts.vue";
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  props: {
    basePath: String,
    title: String,
    title_text: String,
    title_admin: String,
    title_last_change: String,
    title_platnost_do: String,
    title_zadal: String,
    edit_enabled: Number,
    link: String,
    link_to_admin: String,
    article_hlavicka: String,
    article: Object,
  },
  components: {
    EditTexts,
  },
  data() {
    return {
      show: true,
      art_title: {
        menu_name: '',
        h1part2: '',
        view_name: ''
      },
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
        let odkaz = this.basePath + '/api/menu/h1save/' + this.article.id
        let vm = this
        let data = {
              menu_name: this.art_title.menu_name,
              h1part2: this.art_title.h1part2,
              view_name: this.art_title.view_name
            }
        axios.post(odkaz, {
            article: data
          })
          .then(function (response) {
            //vm.textin = response.data
            //console.log(response.data.result)
            if (response.data.result == "OK") {
              vm.$root.$emit('flash_message', [{'message':'Položka v nadpise bola uložená.', 
                                                'type':'success',
                                                'heading': 'Uložené ...',
                                                'timeout': 5000,
                                              }])
            } else {
              vm.saveErr()
            }
            setTimeout(() => {
              vm.$bvModal.hide("modal-1")
              vm.$root.$emit("title-save", data)
            }, 500)
          })
          .catch(function (error) {
            vm.saveErr()
            console.log(odkaz)
            console.log(error)
          });      
      }
    },
    onReset(event) {
      event.preventDefault()
      if (event.explicitOriginalTarget.classList.contains("main-reset")) {
        this.$bvModal.hide("modal-1")
        this.art_title.menu_name = this.article.menu_name
        this.art_title.h1part2 = this.article.h1part2
        this.art_title.view_name = this.article.view_name
      }
    }
  },
  watch: {
    article: function (newArticle) {
      this.art_title.menu_name = this.article.menu_name
      this.art_title.h1part2 = this.article.h1part2
      this.art_title.view_name = this.article.view_name
    }
  },
  mounted() {},
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
        <b-button type="submit" variant="success" class="main-submit">Ulož</b-button>&nbsp;
        <b-button type="reset" variant="secondary" class="main-reset">Cancel</b-button>
      </b-form>
    </b-modal>
    
    <edit-texts
      :base-path="basePath"
      :link="link"
      :article="article"
    >
    </edit-texts>
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