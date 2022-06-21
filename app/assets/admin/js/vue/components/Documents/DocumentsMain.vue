<script>
/**
 * Komponenta pre vypísanie a spracovanie príloh.
 * Posledna zmena 21.06.2022
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
import DocumentsGrid from '../Documents/DocumentsGrid.vue'
import MultipleUpload from '../Uploader/MultipleUpload.vue'

export default {
  components: { 
    MultipleUpload,
    DocumentsGrid,
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
    adminLinks: { // Oprávnenia pre administratívne úkony
      type: String,
      required: true,
    },
  },
  data() {
    return {
      admin_links: {},
      documents_selected: 0,
    };
  },
  methods: {
    deleteDocuments() {
      this.$root.$emit('documents_delete')
    },
  },
  created() {
    this.admin_links = JSON.parse(this.adminLinks);
    
    this.$root.$on('documents_selected', documents_selected => {
			this.documents_selected = documents_selected
		})
  },
  
}
</script>
<template>
  <div class="card card-info">
    <div class="card-header">
      <b-button 
        v-if="admin_links.elink" 
        v-b-modal.myModalAddMultiDocumentsUpload variant="primary"
        size="sm"
      >
        <i class="fas fa-copy"></i> Pridaj prílohu(y)
      </b-button>
      <b-button class="ml-2" 
        variant="danger" 
        v-if="documents_selected > 0"
        size="sm"
        @click="deleteDocuments"
      >
        <i class="fa-solid fa-trash-can"></i>
      </b-button>
    </div>
    <div class="card-body">
      <documents-grid
        :base-path="basePath"
        :id_hlavne_menu="id_hlavne_menu"
        :edit-enabled="admin_links.elink"
      />

      <multiple-upload 
        v-if="admin_links.elink"
        api-url="api/documents" 
        :base-path="basePath"
        :id_hlavne_menu="id_hlavne_menu"
        id-of-modal-uplad="myModalAddMultiDocumentsUpload"
        title="Pridanie viacerích príloh k položke"
        item-emit-name="documents_add"
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
.card-header {
  padding-top: .25rem;
  padding-bottom: .25rem;
}
</style>