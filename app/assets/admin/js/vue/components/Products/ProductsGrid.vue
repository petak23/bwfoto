<script>
/**
 * Komponenta pre vypísanie a spracovanie produktov.
 * Posledna zmena 29.05.2022
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */

import axios from "axios";
import textCell from '../Grid/TextCell.vue'

//for Tracy Debug Bar
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

export default {
  components: {
    textCell,
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
    editEnabled: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      fields: [
          {
            key: 'thumb_file',
            label: 'Obrázok'
          },
          {
            key: 'name',
            label: 'Názov'
          },
          {
            key: 'description',
            label: 'Popis',
          },
          {
            key: 'action',
            label: 'Akcie',
          },
        ],
      items: [],
      id_p: 1,
      loading: 0,     // Načítanie údajov 0 - nič, 1 - načítavanie, 2 - chyba načítania
      error_msg: '',  // Chybová hláška
      items_per_page: [
        { value: 10, text: "10"}, 
        { value: 20, text: "20"}, 
        { value: 50, text: "50"},
        { value: 0, text: "Všetky"},
      ],
      items_per_page_selected: 10,
      page: 1,
    };
  },
  methods: {
    deleteProduct(id) {
      if (window.confirm('Naozaj chceš vymazať?')) {
        let odkaz = this.basePath + "/api/products/delete/" + id;
        axios
          .get(odkaz)
          .then((response) => {
            if (response.data.data == "OK") {
              this.items = this.items.filter((item) => item.id !== id);
              this.$root.$emit('flash_message', [{'message':'Položka bola úspešne vymazaná.', 
                                                  'type':'success',
                                                  'heading': 'Podarilo sa...'
                                                  }])
            }
          })
          .catch((error) => {
            console.log(odkaz);
            console.log(error);
          });
      }
    },
    openmodal(index) {
      this.id_p = index;
      this.$bvModal.show("modal-multi-product");
    },
    closeme: function () {
      this.$bvModal.hide("modal-multi-product");
    },
    imgUrl() {
      return this.items[this.id_p] === undefined
        ? ""
        : this.basePath + "/" + this.items[this.id_p].main_file;
    },
    imgName() {
      return this.items[this.id_p] === undefined
        ? ""
        : this.items[this.id_p].name;
    },
    loadItems() { // Načítanie údajov priamo z DB
      this.loading = 1
      let odkaz =
        this.basePath + "/api/products/getproducts/" + this.id_hlavne_menu;
      this.items = [];
      axios
        .get(odkaz)
        .then((response) => {
          this.items = Object.values(response.data);
          this.loading = 0
        })
        .catch((error) => {
          this.error_msg = 'Nepodarilo sa načítať údaje do tabuľky produktov. <br/>Možná príčina: ' + error
          this.loading = 2
          this.$root.$emit('flash_message', 
                           [{ 'message': this.error_msg, 
                              'type':'danger',
                              'heading': 'Chyba'
                              }])
          
          console.log(odkaz);
          console.log(error);
        });
    },
    changeItemsPerPage() {
      let odkaz = this.basePath + "/api/products/changeperpage"
      let vm = this
      axios.post(odkaz, {
          'items_per_page': this.items_per_page_selected,
        })
        .then(function (response) {
          //vm.preview = response.data
          //console.log(response.data)
          //vm.$root.$emit('flash_message', 
          //                 [{ 'message': 'Uloženie v poriadku', 
          //                    'type':'success',
          //                    'heading': 'Uložené'
          //                    }])
        })
        .catch(function (error) {
          console.log(odkaz)
          console.log(error)
          //vm.$root.$emit('flash_message', 
          //                 [{ 'message': 'Pri uklasaní došlo k chybe',
          //                    'type':'danger',
          //                    'heading': 'Chyba'
          //                    }])
        });
    }
  },
  computed: {
    pages() {
      return Math.ceil(this.items.length / this.items_per_page_selected)
    }
  },
  created() {
    // Načítanie údajov priamo z DB
    this.loadItems()
    this.$root.$on('products_add', data => {
			this.items.push(...data)
		})
  },
};
</script>

<template>
  <div>
    <table class="table table-bordered table-striped" v-if="loading == 0">
      <caption class="bg-secondary text-white py-1">
        <div class="d-flex justify-content-between">
          <div class="px-2">Počet produktov: {{ items.length }}</div>
          <b-pagination
            v-if="pages > 1"
            v-model="page"
            :total-rows="items.length"
            :per-page="items_per_page_selected"
            aria-controls="my-table"
            size="sm"
            class="bg-secondary text-white my-0"
          >
          </b-pagination>
          <form class="px-2 form-inline" v-if="items.length > 10">
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
      </caption>
      <thead class="thead-light">
        <tr>
          <th>Obrázok</th>
          <th>Názov</th>
          <th>Popis</th>
          <th class="action-col" v-if="editEnabled">Akcia</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="item.id">
          <td>
            <img
              :src="basePath + '/' + item.thumb_file"
              :alt="item.name"
              class="img-thumbnail"
              @click="openmodal(index)"
            />
          </td>
          <td>
          <text-cell
            :value="item.name"
            :apiLink="basePath + '/api/products/update/'"
            colName="name"
            :id="item.id"
          ></text-cell>
          </td>
          <td>
          <text-cell
            :value="item.description"
            :apiLink="basePath + '/api/products/update/'"
            colName="description"
            :id="item.id"
          ></text-cell>
          </td>
          <td class="action-col" v-if="editEnabled">
            <button type="button" class="btn btn-info btn-sm" title="Edit">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button
              type="button"
              class="btn btn-danger btn-sm"
              title="Zmaž"
              @click="deleteProduct(item.id)"
            >
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <b-table
      id="my-products"
      :items="items"
      :per-page="items_per_page_selected"
      :current-page="pages"
      :fields="fields"
      small
    >
      <template #cell(thumb_file)="data">
        <img
          :src="basePath + '/' + data.item.thumb_file"
          :alt="data.item.name"
          class="img-thumbnail"
          @click="openmodal(index)"
        />
      </template>
      <template #cell(name)="data">
        <text-cell
          :value="data.item.name"
          :apiLink="basePath + '/api/products/update/'"
          colName="name"
          :id="data.item.id"
        ></text-cell>
      </template>
    </b-table>

    <div class="alert alert-danger" v-if="loading == 2" v-html="error_msg"></div>
    <div class="d-flex align-items-center" v-if="loading == 1">
      <strong>Nahrávam...</strong>
      <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
    </div>
    <b-modal
      id="modal-multi-product"
      centered
      size="xl"
      ok-only
      hide-header
      hide-footer
      dialog-class="product-dialog"
    >
      <img :src="imgUrl()" :alt="imgName()" @click="closeme" />
    </b-modal>
  </div>
</template>

<style lang="scss" scoped>
.action-col {
  min-width: 40px;
}
button {
  margin-left: 0.1em;
}
#modal-multi-product {
  .product-dialog {
    max-width: 95vw !important;
  }

  .modal-body img {
    max-width: 100%;
  }
}
</style>
