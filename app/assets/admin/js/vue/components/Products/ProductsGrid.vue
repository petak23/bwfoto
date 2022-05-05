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
  <table class="table">
    <tr>
      <th>Obrázok</th>
      <th>Názov</th>
      <th>Popis</th>
      <th>Akcia</th>
    </tr>
    <tr v-for="item in items" :key="item.id">
      <td>
        <img  :src="basePath + '/' + item.thumb_file" 
              :alt="item.name"
              class="img-thumbnail"
        />
      </td>
      <td>{{item.name}}</td>
      <td>{{item.description}}</td>
      <td>
        <a href="#" title="Edit">Edit</a>
        <a href="#" title="Zmaž">Zmaž</a>
      </td>
    </tr>
  </table>
</template>

<style>

</style>