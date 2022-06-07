<script>
/**
 * Komponenta pre vypísanie a spracovanie príloh.
 * Posledna zmena 09.06.2022
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
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
    };
  },
  created() {
    this.admin_links = JSON.parse(this.adminLinks);
  },
  
}
</script>
<template>
  <div class="card card-info">
    <div class="card-header">
      <b-button v-if="admin_links.elink" v-b-modal.myModalAddMultiDocumentsUpload variant="primary">
        <i class="fas fa-copy"></i> Pridaj prílohu(y)
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
</style>