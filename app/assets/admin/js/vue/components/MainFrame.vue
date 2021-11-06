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
import adminmenu from './MainFrame/AdminMenu.vue'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  export default {
    components: { adminmenu },
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
      },
      name_first() {
        return (this.useritems.meno.substring(0, 1) + this.useritems.priezvisko.substring(0, 1)).toUpperCase()
      },
      menuitems() {
        return this.convert(this.menu)
      }
    },
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
      this.odkaz = this.basepath + '/api/menu/getmenu'
      this.menu = []
      axios.get(this.odkaz)
                .then(response => {            
                  this.menu = response.data
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
    <v-system-bar 
      app
      window
      height="48px"
      >
      <v-avatar
        color="grey darken-1"
        size="32"
      > {{ name_first }}
      </v-avatar>
      <span class="ml-2">{{ useritems.email }}</span>
      <v-spacer></v-spacer>

      <a href=":Front:Homepage:" title="Zmeny">
        <v-icon>fas fa-eye</v-icon> Skontroluj zmeny na webe...
      </a>
      <v-icon>mdi-square</v-icon>

      <v-icon>mdi-circle</v-icon>

      <v-icon>mdi-triangle</v-icon>
    </v-system-bar>

    <v-navigation-drawer
      v-model="drawer"
      app
      class="pt-2"
    >
      <v-sheet
        color="grey lighten-4"
        class="py-4"
      >
      
        <adminmenu
          :basepath="basepath">
        </adminmenu>  

      </v-sheet>

      <v-divider></v-divider>
      <v-treeview 
        :items="menuitems">
      </v-treeview>
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