<script>
/**
 * Komponenta pre vypísanie a spracovanie produktov.
 * Posledna zmena 21.06.2022
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
import ProductsGrid from '../Products/ProductsGrid.vue'
import MultipleUpload from '../Uploader/MultipleUpload.vue'

export default {
  components: { 
    MultipleUpload,
    ProductsGrid,
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
      products_selected: 0,
      items_count: 0,
      items_per_page: [
        { value: 10, text: "10"}, 
        { value: 20, text: "20"}, 
        { value: 50, text: "50"},
        { value: 0, text: "Všetky"},
      ],
      items_per_page_selected: 10,
      items_per_page_selected_old: 10,
      currentPage: 1,
    };
  },
  methods: {
    deleteProducts() {
      this.$root.$emit('products_delete')
    },
    changeItemsPerPage() {
      let odkaz = this.basePath + this.baseApiPath + "changeperpage"
      // Výpočet novej aktuálnej stránky
      let first_id = this.items_per_page_selected_old * (this.currentPage - 1)
      this.currentPage = first_id > 0 ? Math.ceil(first_id / this.items_per_page_selected) : 1
      let vm = this
      axios.post(odkaz, {
          'items_per_page': this.items_per_page_selected,
        })
        .then(function (response) {
          console.log(response.data)
          vm.$root.$emit('products_currentPage', vm.currentPage)
        })
        .catch(function (error) {
          console.log(odkaz)
          console.log(error)
        });
    },
  },
  computed: {
    pages() {
      return Math.ceil(this.items_count / this.items_per_page_selected)
    }
  },
  created() {
    this.admin_links = JSON.parse(this.adminLinks);

    this.$root.$on('products_selected', products_selected => {
			this.products_selected = products_selected
		})
    this.$root.$on('products_count', items_count => {
			this.items_count = items_count
		})
  },
  
}
</script>
<template>
  <div class="card card-info">
    <div class="card-header">
      <b-button 
        v-if="admin_links.elink"
        v-b-modal.myModalAddMultiProductsUpload variant="primary"
        size="sm"
      >
        <i class="fas fa-copy"></i> Pridaj produkt(y)
      </b-button>
      <b-button class="ml-2" 
        variant="danger" 
        v-if="products_selected > 0"
        size="sm"
        @click="deleteProducts"
      >
        <i class="fa-solid fa-trash-can"></i>
      </b-button>
    </div>
    <div class="card-body">
      <products-grid
        :base-path="basePath"
        :id_hlavne_menu="id_hlavne_menu"
        :edit-enabled="admin_links.elink"
      />

      <multiple-upload 
        v-if="admin_links.elink"
        api-url="api/products" 
        :base-path="basePath"
        :id_hlavne_menu="id_hlavne_menu"
        id-of-modal-uplad="myModalAddMultiProductsUpload"
        title="Pridanie viacerích produktov k položke"
        item-emit-name="products_add"
      />
    </div>
    <div class="card-footer">
      <div class="d-flex justify-content-between">
        <div class="px-2">Počet produktov: {{ items_count }}</div>
        <b-pagination
          v-if="pages > 1"
          v-model="currentPage"
          :total-rows="items_count"
          :per-page="items_per_page_selected"
          aria-controls="my-products"
          size="sm"
          class="bg-secondary text-white my-0"
        >
        </b-pagination>
        <form class="px-2 form-inline" v-if="items_count > 10">
          <label class="my-0 mr-2" for="itemsPerPage">Položiek na stránku:</label>
          <b-form-select 
            v-model="items_per_page_selected"
            :options="items_per_page"
            id="itemsPerPage"
            size="sm"
            @change="changeItemsPerPage">
          </b-form-select>
        </form>
      </div>
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