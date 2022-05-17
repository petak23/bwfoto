<script>
/**
 * Komponenta pre vypísanie a spracovanie produktov.
 * Posledna zmena 06.05.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */

import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  components: {},
  props: {
    id_hlavne_menu: {
      type: String,
      required: true
    },
    basePath: {
      type: String,
      required: true
    },
    adminLinks: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      items: [],
      admin_links: [],
      id_p: 1,
    };
  },
  methods: {
    deleteProduct(id) {
      let odkaz = this.basePath + '/api/products/delete/' + id
      axios.get(odkaz)
                .then(response => {
                  if (response.data.data == 'OK') {
			            	this.items = this.items.filter(item => item.id !== id)
                  }
                })
                .catch((error) => {
                  console.log(odkaz);
                  console.log(error);
                });
    },
    openmodal(index) {
      this.id_p = index
      this.$bvModal.show("modal-multi-product")
    },
    closeme: function() {
      this.$bvModal.hide("modal-multi-product");
    },
    imgUrl() {
      return (this.items[this.id_p] === undefined) ? '' : this.basePath + '/' + this.items[this.id_p].main_file
    },
    imgName() {
      return (this.items[this.id_p] === undefined) ? '' : this.items[this.id_p].name
    }
  },
  created() {
    this.admin_links = JSON.parse(this.adminLinks)
    // Načítanie údajov priamo z DB
    let odkaz = this.basePath + '/api/products/getproducts/' + this.id_hlavne_menu
    this.items = []
    axios.get(odkaz)
              .then(response => {
                this.items = Object.values(response.data)
              })
              .catch((error) => {
                console.log(odkaz);
                console.log(error);
              });
  },
}
</script>

<template>
  <div>
    <table class="table table-striped">
      <thead class="thead-light">
        <tr>
          <th>Obrázok</th>
          <th>Názov</th>
          <th>Popis</th>
          <th class="action-col">Akcia</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="item.id">
          <td>
            <img  :src="basePath + '/' + item.thumb_file" 
                  :alt="item.name"
                  class="img-thumbnail"
                  @click="openmodal(index)"
            />
          </td>
          <td><span>{{item.name}}</span></td>
          <td><span>{{item.description}}</span></td>
          <td class="action-col">
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
    <b-modal id="modal-multi-product" 
              centered size="xl" ok-only hide-header hide-footer 
              dialog-class="product-dialog">
      <img :src="imgUrl()" :alt="imgName()" @click="closeme">
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