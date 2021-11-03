<script>
/**
 * Komponenta pre zmenu poradia.
 * Posledna zmena 03.11.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 * @link https://sortablejs.github.io/Vue.Draggable/#/transition-example-2
 */

import draggable from 'vuedraggable'
import vuetify from '@/admin/js/vue/plugins/vuetify'
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  vuetify,
  components: {
    draggable
  },
  props: {
    basepath: {
      type: String,
      required: true
    },
    id_hlavne_menu: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      drag: false,
      items: [],
      nextchange: false, // Aby sa pri prvom behu nespustilo ukladanie.
      odkaz: ""
    };
  },
  computed: {
    dragOptions() {
      return {
        animation: 200,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
      };
    },

  },
  watch: {
    items: function (newItem) { // Kedykolvek sa item zmení tak sa spustí
      if (this.nextchange) {
        this.odkaz = this.basepath + '/api/menu/saveordersubmenu/' + this.id_hlavne_menu
        //console.log(newItem)
        var out = []
        newItem.forEach(item => {
          out.push(item.id)
        });
        axios.post(this.odkaz, {
            items: out,
          })
          .then(function (response) {
            console.log(response.data);
            if (response.data.result == 'OK') {
              //this.items = newItem
            }
          })
          .catch(function (error) {
            console.log(error);
          });
      } else {
        this.nextchange = true
      }
    }
  },
  mounted() {
    // Načítanie údajov priamo z DB
    this.odkaz = this.basepath + '/api/menu/getsubmenu/' + this.id_hlavne_menu
    this.items = []
    axios.get(this.odkaz)
              .then(response => {
                console.log(response.data);
                console.log(this.items)
                this.items = Object.values(response.data)
              })
              .catch((error) => {
                console.log(this.odkaz);
                console.log(error);
              });
  }
}
</script>

<template>
    <div class="row">
      <div class="col-12">
        <h3 class="p-2">Zoznam podčlánkov danej časti:</h3>
        <p>Poradie položiek je možné zmeniť chytením a ťahaním na požadované miesto.</p>
      </div>
      <div class="col-6">
        <draggable
          class="list-group"
          tag="ul"
          v-model="items"
          v-bind="dragOptions"
          @start="drag = true"
          @end="drag = false"
        >
          <transition-group type="transition" :name="!drag ? 'flip-list' : null">
            <div
              class="list-group-item"
              v-for="item in items"
              :key="item.order"
            >
              <a  :href="item.link" 
                  :title="item.name">
                {{ item.name }}
                <img  v-if="item.avatar != null"
                      :src="basepath + '/files/menu/' + item.avatar" 
                      :title="item.name" 
                      style="max-height: 5rem;"
                />
              </a> 
            </div>
          </transition-group>
        </draggable>
      </div>
    </div>
</template>

<style>
.button {
  margin-top: 35px;
}

.flip-list-move {
  transition: transform 0.5s;
}

.no-move {
  transition: transform 0s;
}

.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

.list-group {
  min-height: 20px;
}

.list-group-item {
  cursor: move;
}

.list-group-item i {
  cursor: pointer;
}
</style>