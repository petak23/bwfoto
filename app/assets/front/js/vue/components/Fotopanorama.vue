<script>
/** 
 * Component Fotopanorama
 * Posledná zmena(last change): 11.03.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.8
 */
export default {
  props: {
    attachments: {
      type: String
    },
    basepath: String,
    border_a: String,
    border_b: String,
    border_c: String,
    text_before: String,
    text_after: String,
    first_id: String,
  },
  data() {
    return {
      id: 0,
    }
  },
  methods: {
    // Zmena id
    changebig: function(id) {
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
    },
  },
  created() {
    if (parseInt(this.first_id) > 0) { // Ak mám first_id tak k nemu nájdem položku v myatt
      Object.keys(this.myatt).forEach(ma => { 
        if (this.myatt[ma].id == this.first_id) {
          this.id = ma;
        }
      });
    }
  },
  computed: {
    // Parsovanie JSON-u  na array
    myatt() {
      return JSON.parse(this.attachments)
    },
  },
  mounted () {
    // Naviazanie na sledovanie stláčania klávesnice
    document.addEventListener("keydown", this.keyPush);
  },

};
</script>

<template>
<div class="main-win">
  <div class="row">
    <h4 class="col-12 bigimg-name">
      {{ myatt[id].name }}
    </h4>
  </div>
  <div class="row">
    <div class="col-12 thumbpanorama" id="imgDetail" ref="imgDetail">
      <div v-for="(im, index) in myatt" :key="im.id">
        <a  v-if="im.type == 'menu'" 
            :href="im.web_name" 
            :title="im.name">
          <b-img-lazy
            :src="getImageUrl(im.main_file)"
            :alt="im.name" class="img-fluid podclanok">
          ></b-img-lazy>
          <h4 class="h4-podclanok">{{ im.name }}</h4>
        </a>
        <video v-else-if="im.type == 'attachments3'"
              class="video-priloha" 
              :src="basepath + im.main_file" 
              :poster="basepath + im.thumb_file"
              type="video/mp4" controls="controls" preload="none">
        </video>
        <button v-else-if="im.type == 'attachments1'" 
                :title="im.name">
          <b-img-lazy
            :src="getImageUrl(im.thumb_file)" 
            :alt="im.name" 
            class="img-fluid a3">
          ></b-img-lazy>
          <br><h6>{{ im.name }}</h6>
        </button>
        <button v-else-if="(im.type == 'attachments2' || im.type == 'product')"
                @click.prevent="modalchangebig(index)" 
                type="button" 
                class="btn btn-link">
          <b-img-lazy
            :src="getImageUrl(im.thumb_file)" 
            :alt="im.name" 
            class="img-fluid a12">
          ></b-img-lazy>
        </button>
      </div>
    </div>
  </div>

  <b-modal  id="modal-multi-1" centered size="xl" 
            :title="myatt[id].name" ok-only
            modal-class="lightbox-img"
            ref="modal1fo">
    <div class="modal-content">
      <div class="modal-body my-img-content">
        <div class="border-a" :style="border_a">
          <div class="border-b" :style="border_b">
            <img :src="basepath + myatt[id].main_file" 
                  :alt="myatt[id].name" 
                  id="big-main-img"
                  class="border-c" 
                  :style="border_c" />
          </div>
        </div>
        <div class="text-center description" v-if="myatt[id].description != null">
          {{ myatt[id].description }}
        </div>
      </div>
      <div class="arrows-overlay">
        <div class="arrows-l"
            @click="before">
          <a href="#" class="text-light"   
              :title="text_before">&#10094;
          </a>
        </div>
        <div class="arrows-r flex-row-reverse"
            @click="after">
          <a href="#" class="text-light"
              :title="text_after">&#10095;
          </a>
        </div>
      </div>
    </div>
  </b-modal>
</div>
</template>

<style lang="scss" scoped>
.thumbpanorama {
	display: grid;
	grid-template-columns: repeat(1, 1fr);
	grid-gap: 0.5rem;
	overflow: auto;
  max-height: 80vh;
  grid-auto-rows: 7rem;

  > div{
    position: relative;
    background-color: rgba(44,44,44,1.00);
    padding: 1rem;
  }
  img{
    position: absolute;
    max-width: 90%; 
    max-height: 90%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: solid 3px #ddd;
    color: transparent;
  }	
  img.podclanok {
    opacity: .5;
  }
  .h4-podclanok {
    position: absolute;
    max-width: 90%; max-height: 90%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #ddd;
    text-align: center;
  }
}
.btn:focus {
  box-shadow: none;
}
</style>