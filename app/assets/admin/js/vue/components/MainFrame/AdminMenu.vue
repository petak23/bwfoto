<script>
/**
 * Komponenta pre administračné menu.
 * Posledna zmena 06.11.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */


import vuetify from '@/admin/js/vue/plugins/vuetify'
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  vuetify,
  components: {},
  props: {
    basepath: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      items: [],
      odkaz: ""
    };
  },
  computed: {
    menuitems() {
      return this.convert(this.items)
    }
  },
  watch: {},
  methods: {
    convert(itemsObject) {
      return Object.values(itemsObject).map(item => ({
        ...item,
        children: item.children ? this.convert(item.children) : undefined,
      }));
    }
  },
  mounted() {
    // Načítanie údajov priamo z DB
    this.odkaz = this.basepath + '/api/menu/getadminmenu'
    this.items = []
    axios.get(this.odkaz)
              .then(response => {
                this.items = Object.values(response.data)
              })
              .catch((error) => {
                console.log(this.odkaz)
                console.log(error)
              })
  }
}
</script>

<template>
  <v-treeview 
    :items="menuitems">
  </v-treeview>
</template>

<style>
</style>