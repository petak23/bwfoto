<script>
/** 
 * Component Menucardorder
 * Posledná zmena(last change): 14.03.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 */

import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  props: {
    basepath: {
      type: String,
      required: true
    },
    id_hlavne_menu: { 
      type: String,
      required: true,
    },
  },
  data() {
    return {
      items: [],
    }
  },
  methods: {
    // Zmena id
    /*changebig: function(id) {
      this.id = id
    },
    modalchangebig (id) {
      this.id = id;
      this.$bvModal.show("modal-multi-1")
    },
    // Zmena id na predošlé
    before: function() {
      this.id = this.id <= 0 ? (this.myatt.length - 1) : this.id - 1;
    },  
    // Zmena id na  nasledujúce
    after: function() {
      this.id = this.id >= (this.myatt.length - 1) ? 0 : this.id + 1;
    }, 
    closeme: function() {
      this.$bvModal.hide("modal-multi-2");
    },
    keyPush(event) {
      switch (event.key) {
        case "ArrowLeft":
          this.before();
          break;
        case "ArrowRight":
          this.after();
          break;
      }
    },
    // Generovanie url pre lazyloading obrázky
    getImageUrl(text) {
      return this.basepath + text
    },*/
  },
  created() {
    /*if (parseInt(this.first_id) > 0) { // Ak mám first_id tak k nemu nájdem položku v myatt
      Object.keys(this.myatt).forEach(ma => { 
        if (this.myatt[ma].id == this.first_id) {
          this.id = ma;
        }
      });
    }*/
  },
  computed: {
    // Parsovanie JSON-u  na array
    /*myatt() {
      return JSON.parse(this.attachments)
    },*/
  },
  mounted () {
    var odkaz = this.basepath + '/api/menu/getsubmenu/' + this.id_hlavne_menu
    this.items = []
    axios.get(odkaz)
              .then(response => {
                //console.log(response.data);
                //console.log(this.items)
                this.items = Object.values(response.data)
              })
              .catch((error) => {
                console.log(odkaz);
                console.log(error);
              });
  },

};
</script>

<template>
  
  <dnd-zone
    :transition-duration="0.3"
    handle-class="handle"
  >
    <dnd-container
      :dnd-model="items"
      dnd-id="grid-example"
      class="row"
      dense
    >
      <dnd-item
        v-for="image in items"
        :key="image.id"
        :dnd-id="image.id"
        :dnd-model="image"
      >
        <div class="col-12 col-sm-6 col-md-4 col-xxl-3 album position-relative">
          <i 
            class="fas fa-grip-vertical handle position-absolute"
            style="top: 0; left: 0"
          ></i>
          <a :href="image.link" :title="image.name">
            <img 
              :src="basepath + '/files/menu/' + image.avatar"
              class="img-responsive img-square"
              :alt="image.name"/>
            <!--i n:if="isset($node->node_class)" class="{$node->node_class}"> </i-->
            <h3>{{ image.name }}</h3>
          </a>
          <div class="caption">
            <p v-if="image.anotacia" class="popis">
              {{ image.anotacia }} 
              <a :href="image.link" title="more">»»»</a>
            </p>
          </div>
        </div>

      </dnd-item>
    </dnd-container>
  </dnd-zone>
</template>

<style lang="scss" scoped>


</style>