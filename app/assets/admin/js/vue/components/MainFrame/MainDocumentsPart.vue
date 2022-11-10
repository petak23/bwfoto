<script>
/**
 * Komponenta pre vypísanie kariet dokumentov a produktov
<<<<<<< HEAD
 * Posledna zmena 15.06.2022
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
=======
 * @version    1.0.1
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
 */

import DocumentsMain from "../Documents/DocumentsMain.vue";
import ProductsMain from "../Products/ProductsMain.vue";

export default {
  components: {
    DocumentsMain,
    ProductsMain,
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
    adminLinks: {
      type: String,
      required: true,
<<<<<<< HEAD
=======
    },
    activeTab: {
      type: String,
      default: 'prilohy-tab',
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
    }
  },
  data() {
    return {
      documents_count: 0,
      products_count: 0,
<<<<<<< HEAD
      active_tab: 'prilohy-tab',
    }
  },
  created () {
    this.$root.$on('documents_count', documents_count => {
			this.documents_count = documents_count
		})
    this.$root.$on('products_count', products_count => {
			this.products_count = products_count
=======
    }
  },
  created () {
    this.$root.$on('items_count', data => {
      if (data.id == "products") { // Len ak je to určené pre mňa...
			  this.products_count = data.length
      }
      if (data.id == "documents") { // Len ak je to určené pre mňa...
			  this.documents_count = data.length
      }
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
		})
  },
}
</script>

<template>
  <b-tabs content-class="mt-3">
    <b-tab 
<<<<<<< HEAD
      :active="active_tab == 'prilohy-tab'">
=======
      :active="activeTab == 'prilohy-tab'">
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
      <template #title>
        Prílohy<span v-if="documents_count > 0"> ({{ documents_count }})</span>
      </template>
      <documents-main 
        :base-path="basePath"
<<<<<<< HEAD
=======
        base-api-path="/api/documents/"
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
        :id_hlavne_menu="id_hlavne_menu"
        :admin-links="adminLinks"
      />
    </b-tab>
    <b-tab
<<<<<<< HEAD
      :active="active_tab == 'products-tab'">
=======
      :active="activeTab == 'products-tab'">
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
      <template #title>
        Produkty<span v-if="products_count > 0"> ({{ products_count }})</span>
      </template>
      <products-main 
        :base-path="basePath"
<<<<<<< HEAD
=======
        base-api-path="/api/products/"
>>>>>>> 0300db6e5c2fe474a2c7f3db310def98c43d64e0
        :id_hlavne_menu="id_hlavne_menu"
        :admin-links="adminLinks"
      />
    </b-tab>
  </b-tabs>
</template>

<style>

</style>