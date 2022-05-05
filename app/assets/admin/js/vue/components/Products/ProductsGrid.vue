<script>
/**
 * Komponenta pre vypísanie a spracovanie produktov.
 * Posledna zmena 04.05.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
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
  },
  data() {
    return {
      items: [],
    };
  },
  methods: {
    deleteProduct(id) {
      let odkaz = this.basePath + '/api/products/delete/' + id
      axios.get(odkaz)
                .then(response => {
                  if (response.data.data == 'OK') {

                    this.items = []
                  }
                })
                .catch((error) => {
                  console.log(odkaz);
                  console.log(error);
                });
    },
  },
  mounted() {
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
      <tr v-for="item in items" :key="item.id">
        <td>
          <img  :src="basePath + '/' + item.thumb_file" 
                :alt="item.name"
                class="img-thumbnail"
          />
        </td>
        <td>{{item.name}}</td>
        <td>{{item.description}}</td>
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
</template>

<style lang="scss" scoped>
.action-col {
  min-width: 40px;
}
button {
  margin-left: 0.1em;
}
</style>