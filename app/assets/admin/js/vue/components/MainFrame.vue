<script>
/**
 * Komponenta pre základné rozloženie.
 * Posledna zmena 04.11.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */

//import draggable from 'vuedraggable'
//import vuetify from '@/admin/js/vue/plugins/vuetify'
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  export default {
    props: {
      user: {
        type: String,
        required: true
      },
      basepath: {
        type: String,
        required: true
      },
    },
    data: () => ({
      cards: ['Today', 'Yesterday'],
      drawer: null,
      menu: {},
      odkaz: "",
    }),
    computed: {
      // Parsovanie JSON-u  na array
      useritems() {
        return JSON.parse(this.user)
      }
    },
    mounted() {
      // Načítanie údajov priamo z DB
      this.odkaz = this.basepath + '/api/menu/getmenu'
      this.menu = []
      axios.get(this.odkaz)
                .then(response => {
                  //console.log(response.data);
                  //console.log(this.items)
                  var menu = Object.entries(response.data)
                  this.menu = menu.forEach
                  var po = []
                  Object.keys(menu).forEach(function(key){

                    po.push(menu[key])

                  });
                  this.menu = po
                    console.log(this.menu);
                })
                .catch((error) => {
                  console.log(this.odkaz);
                  console.log(error);
                });
    }
  }
</script>

<template>
  <v-app id="inspire">
    <v-system-bar app>
      <v-spacer></v-spacer>

      <v-icon>mdi-square</v-icon>

      <v-icon>mdi-circle</v-icon>

      <v-icon>mdi-triangle</v-icon>
    </v-system-bar>

    <v-navigation-drawer
      v-model="drawer"
      app
    >
      <v-sheet
        color="grey lighten-4"
        class="pa-4"
      >
        <v-avatar
          class="mb-4"
          color="grey darken-1"
          size="64"
        >
          <img
            :src="basepath + '/' + useritems.avatar"
            :alt="useritems.meno"
          >
        </v-avatar>

        <div>{{ useritems.email }}</div>
      </v-sheet>

      <v-divider></v-divider>
      <v-treeview :items="menu"></v-treeview>
    </v-navigation-drawer>

    <v-main>
      <v-container
        class="py-8 px-6"
        fluid
      >
        <v-row>
          <v-col
            v-for="card in cards"
            :key="card"
            cols="12"
          >
            <v-card>
              <v-subheader>{{ card }}</v-subheader>

              <v-list two-line>
                <template v-for="n in 6">
                  <v-list-item

                    :key="n"
                  >
                    <v-list-item-avatar color="grey darken-1">
                    </v-list-item-avatar>

                    <v-list-item-content>
                      <v-list-item-title>Message {{ n }}</v-list-item-title>

                      <v-list-item-subtitle>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil repellendus distinctio similique
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>

                  <v-divider
                    v-if="n !== 6"
                    :key="`divider-${n}`"
                    inset
                  ></v-divider>
                </template>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>