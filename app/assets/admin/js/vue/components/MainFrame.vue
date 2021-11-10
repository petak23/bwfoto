<script>
/**
 * Komponenta pre základné rozloženie.
 * Posledna zmena 10.11.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */

//import draggable from 'vuedraggable'
//import vuetify from '@/admin/js/vue/plugins/vuetify'
import axios from 'axios'
import adminmenu from './MainFrame/AdminMenu.vue'
import lastlogin from './MainFrame/LastLogin.vue'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  export default {
    components: { 
      adminmenu,
      lastlogin,
    },
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
            cols="4"
          >
            <lastlogin
              :basepath="basepath"
              :user="user"
            ></lastlogin>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>