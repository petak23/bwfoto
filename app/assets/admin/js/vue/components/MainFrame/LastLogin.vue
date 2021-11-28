<script>
/**
 * Komponenta pre vypísanie posledných prihlásení.
 * Posledna zmena 09.11.2021
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
    user: {
      type: String,
      required: true
    },
    basepath: {
      type: String,
      required: true
    },
    rows: {
      default: 25,
    }
  },
  data() {
    return {
      items: [],
      loading: false,
    };
  },
  computed: {
    useritems() {
      return JSON.parse(this.user)
    },
    count() {
      return Object.keys(this.items).length
    },
  },
  watch: {},
  methods: {
    activeClass(id) {
      return id == this.useritems.id ? 'selected' : 'not-selected'
    },
    rowsClass(i) {
      return (i % 2  == 0) ? "even" : "odd"
    },
    deletelogs() {
      this.loading = true
      this.odkaz = this.basepath + '/api/user/deleteall'
      axios.get(this.odkaz)
                .then(response => {
                  if (response.data.result == 0) {
                    this.items = []
                  }
                })
                .catch((error) => {
                  console.log(this.odkaz);
                  console.log(error);
                });
    },
  },
  mounted() {
    // Načítanie údajov priamo z DB
    this.odkaz = this.basepath + '/api/user/getlastlogin/' + this.rows
    this.items = []
    axios.get(this.odkaz)
              .then(response => {
                this.items = Object.values(response.data)
              })
              .catch((error) => {
                console.log(this.odkaz);
                console.log(error);
              });
  }}
</script>

<template>
  <v-card
    elevation="5"
    outlined
    class="last-login"
  > 
    <v-app-bar
    
    color="rgba(245, 222, 179, 1)"
    >

      <v-toolbar-title>Posledných {{ rows }} prihlásení</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon @click="deletelogs" :loading="loading" v-if="count">
        <v-icon>mdi-delete</v-icon>
      </v-btn>
    </v-app-bar>
    <v-list-item three-line class="list-item s">
      <v-list-item-content>
        <v-list-item-title class="text-h5 mb-1" v-if="!count">
          Bez záznamu
        </v-list-item-title>
        <v-simple-table dense v-if="count">
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">
                  Name
                </th>
                <th class="text-left">
                  Date
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in items" :key="index" :class="[ activeClass(item.id_user_main), rowsClass(index) ]">
                <td>{{ item.name }}</td>
                <td>{{ item.log_in_datetime }}</td>
              </tr>
            </tbody>
          </template>
        </v-simple-table> 
        
      </v-list-item-content>
    </v-list-item>
  </v-card>
</template>

<style lang="scss" scoped>
  .last-login {
    max-height: 20rem;
    overflow: auto;

    td {
      font-size: 80% !important;
    }

    .selected td {
      font-weight: bold;
    }

    .odd {
      background-color: rgba(245, 222, 179, 0.35);
    }
    /*.list-item {
      overflow: auto;
      height: 100%;
    }*/
  }

  @media (min-width: 576px){ 
    .last-login {
      max-height: 20rem;
    }
  }
  @media (min-width: 768px){
    .last-login {
      max-height: 19rem;
    }
  }
  @media (min-width: 992px) {
    .last-login {
      max-height: 23rem;
    }
  }
  @media (min-width: 1200px) {
    .last-login {
      max-height: 22rem;
    }
  }
</style>