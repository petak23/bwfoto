<script>
/**
 * Komponenta pre vypísanie a spracovanie príloh.
<<<<<<< HEAD
 * Posledna zmena 09.06.2022
=======
 * Posledna zmena 24.06.2022
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
<<<<<<< HEAD
 * @version    1.0.0
 */
import DocumentsGrid from '../Documents/DocumentsGrid.vue'
import MultipleUpload from '../Uploader/MultipleUpload.vue'
=======
 * @version    1.0.2
 */
import DocumentsGrid from '../Documents/DocumentsGrid.vue'
import MultipleUpload from '../Uploader/MultipleUpload.vue'
import GridFooter from "../Grid/GridFooter.vue";
import axios from "axios";

//for Tracy Debug Bar
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0

export default {
  components: { 
    MultipleUpload,
    DocumentsGrid,
<<<<<<< HEAD
=======
    GridFooter,
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
  },
  props: {
    id_hlavne_menu: {
      type: String,
      required: true,
    },
    basePath: {
      type: String,
      required: true,
    },
<<<<<<< HEAD
=======
    baseApiPath: {  // Základná časť cesty k API s lomítkom na začiatku a na konci
      type: String,
      required: true,
    },
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
    adminLinks: { // Oprávnenia pre administratívne úkony
      type: String,
      required: true,
    },
<<<<<<< HEAD
=======
    currentLang: { // Skratka aktuálneho jazyka
      type: String,
      default: 'sk',
    },
    id: {
      type: String,
      default: 'documents',
    }
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
  },
  data() {
    return {
      admin_links: {},
<<<<<<< HEAD
    };
  },
  created() {
    this.admin_links = JSON.parse(this.adminLinks);
=======
      items_selected: 0, // Počet označených položiek
      items_count: 0,    // Celkový počet položiek
      items_per_page_selected: 10,  // Počet položiek na stránku
      currentPage: 1,    // Aktuálne zobrazená stránka
      language_texts: {  // Texty pre jazykové mutácie
        sk: {
          add_items: "Pridaj prílohu(y)",
          add_more_items: "Pridanie viacerích dokumetov k položke",
        },
      },
    };
  },
  methods: {
    deleteItems() {
      this.$root.$emit('documents_delete')
    },
    trans(key) {  // Preklad textov
      // help: https://stackoverflow.com/questions/6921803/how-to-access-object-using-dynamic-key
      if (this.language_texts[this.currentLang].hasOwnProperty(key)) {
        return this.language_texts[this.currentLang][key]
      } else {
        return key
      }
    }
  },
  created() {
    this.admin_links = JSON.parse(this.adminLinks);
    
    // Reaguje na zmenu počtu označených položiek 
    this.$root.$on('items_selected', data => {
      if (data.id == this.id) { // Len ak je to určené pre mňa...
			  this.items_selected = data.length
      }
		})

    // Reaguje na zmenu počtu položiek
    this.$root.$on('items_count', data => {
      if (data.id == this.id) { // Len ak je to určené pre mňa...
        console.log(data, this.id)
			  this.items_count = data.length
      }
		})

    // Reaguje na zmenu počtu položiek na stránku v gride
    this.$root.$on('changed_items_per_page', data => { 
      if (data.id == this.id) { // Len ak je to určené pre mňa...
        this.items_per_page_selected = data.items_per_page_selected
        this.currentPage = data.currentPage
      }
    })

    // Reaguje na zmenu aktuálnej stránky
    this.$root.$on('current_page', data => { 
      if (data.id == this.id) { // Len ak je to určené pre mňa...
        this.currentPage = data.currentPage
      }
    })

>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
  },
  
}
</script>
<template>
  <div class="card card-info">
    <div class="card-header">
<<<<<<< HEAD
      <b-button v-if="admin_links.elink" v-b-modal.myModalAddMultiDocumentsUpload variant="primary">
        <i class="fas fa-copy"></i> Pridaj prílohu(y)
=======
      <b-button 
        v-if="admin_links.elink" 
        v-b-modal.myModalAddMultiDocumentsUpload variant="primary"
        size="sm"
      >
        <i class="fas fa-copy"></i> {{ trans('add_items') }}
      </b-button>
      <b-button class="ml-2" 
        variant="danger" 
        v-if="items_selected > 0"
        size="sm"
        @click="deleteItems"
      >
        <i class="fa-solid fa-trash-can"></i>
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
      </b-button>
    </div>
    <div class="card-body">
      <documents-grid
        :base-path="basePath"
<<<<<<< HEAD
        :id_hlavne_menu="id_hlavne_menu"
        :edit-enabled="admin_links.elink"
=======
        :base-api-path="baseApiPath"
        :id_hlavne_menu="id_hlavne_menu"
        :edit-enabled="admin_links.elink"
        :items-per-page-selected = "items_per_page_selected"
        :id="id"
        :current-page="currentPage"
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
      />

      <multiple-upload 
        v-if="admin_links.elink"
<<<<<<< HEAD
        api-url="api/documents" 
        :base-path="basePath"
        :id_hlavne_menu="id_hlavne_menu"
        id-of-modal-uplad="myModalAddMultiDocumentsUpload"
        title="Pridanie viacerích príloh k položke"
        item-emit-name="documents_add"
=======
        :base-path="basePath"
        :base-api-path="baseApiPath"
        :id_hlavne_menu="id_hlavne_menu"
        id-of-modal-uplad="myModalAddMultiDocumentsUpload"
        title="trans('add_more_items')"
        :item-emit-name="id + '_add'"
      />
    </div>
    <div class="card-footer">
      <grid-footer
        :base-path="basePath"
        :base-api-path="baseApiPath"
        :id="id"
        :items_count="items_count"
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
      />
    </div>
  </div>
</template>

<style scoped>
.card-body {
  padding-top: 0;
  padding-left: 0;
  padding-right: 0; 
}
<<<<<<< HEAD
=======
.card-header {
  padding-top: .25rem;
  padding-bottom: .25rem;
}
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
</style>