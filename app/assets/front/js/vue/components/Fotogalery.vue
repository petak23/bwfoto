<script>
/* 
 * Component Fotogalery
 * Posledná zmena(last change): 27.02.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
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
  },
  data() {
    return {
      id: 0,
      square: 0,
      wid: 0
    }
  },
  methods: {
    // Zmena id
    changebig: function(id) {
      this.id = id
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
    matchHeight () {
      let height = this.$refs.imgDetail.clientHeight;
      let width = this.$refs.imgDetail.clientWidth;
      var height2 = parseInt(window.innerHeight * 0.8);
      var h = height2 > height ? height2 : height;
      this.square = (h>width ? width-20 : h);
      this.wid = width;
    }
  },
  created() {
    window.addEventListener("resize", this.matchHeight);
  },
  destroyed() {
    window.removeEventListener("resize", this.matchHeight);
  },
  computed: {
    // Parsovanie JSON-u  na array
    myatt() {
      return JSON.parse(this.attachments)
    }
  },
  mounted () {
    this.matchHeight()
  },

};
</script>

<template>
<div class="main-win">
  <div class="row" v-if="wid > 0">
    <h4 class="col-12 bigimg-name">
      {{ myatt[id].name }}
    </h4>
  </div>
  <div class="row">
    <div class="d-none d-sm-inline-block col-sm-8 detail" ref="imgDetail" id="imgDetail">
      <div id="squarePlace" v-bind:style="{height: square + 'px', width: square + 'px'}">
        <a  v-if="myatt[id].type == 'menu'"
            :href="myatt[id].web_name" 
            :title="myatt[id].name">
          <img  :src="basepath + myatt[id].main_file" 
                :alt="myatt[id].name" class="img-fluid">
          <h4>{{ myatt[id].name }}</h4>
        </a>
        <video v-if="myatt[id].type == 'attachments3'"
              class="video-priloha" 
              :src="basepath + myatt[id].main_file" 
              :poster="basepath + myatt[id].thumb_file"
              type="video/mp4" controls="controls" preload="none">
        </video>
        <button v-else-if="myatt[id].type == 'attachments1'"
                :title="myatt[id].name">
          <img :src="basepath + myatt[id].thumb_file" 
              :alt="myatt[id].name" class="img-fluid">
          <br><h6>{{ myatt[id].name }}</h6>
        </button>  
        <button v-else-if="myatt[id].type == 'attachments2' || myatt[id].type == 'product'"
                v-b-modal.modal-multi-1
                type="button" class="btn btn-link">
          <img :src="basepath + myatt[id].main_file" 
              :alt="myatt[id].name" class="img-fluid">
        </button>
      </div>
    </div>
    <div class="col-12 col-sm-4 thumbgrid">
      <div v-for="(im, index) in myatt" :key="im.id">
        <a  v-if="wid > 0"
            @click.prevent="changebig(index)" href=""
            :title="'Odkaz' + (im.type == 'menu' ? im.view_name : im.name)" 
            :class="'thumb-a, ajax' + (index == id ? ', selected' : '')">
          <img :src="basepath + im.thumb_file" :alt="im.name" class="img-fluid">
        </a>
        <a  v-else-if="wid == 0 && im.type == 'menu'"
            :href="im.web_name" 
            :title="im.name">
          <img  :src="basepath + im.main_file" 
                :alt="im.name" class="img-fluid podclanok">
          <h4 class="h4-podclanok">{{ im.name }}</h4>
        </a>
        <video v-if="wid == 0 && im.type == 'attachments3'"
              class="video-priloha" 
              :src="basepath + im.main_file" 
              :poster="basepath + im.thumb_file"
              type="video/mp4" controls="controls" preload="none">
        </video>
        <button v-else-if="wid == 0 && im.type == 'attachments1'"
                :title="im.name">
          <img :src="basepath + im.thumb_file" 
              :alt="im.name" class="img-fluid">
          <br><h6>{{ im.name }}</h6>
        </button>
        <button v-else-if="wid == 0 && (im.type == 'attachments2' || im.type == 'product')"
                v-b-modal.modal-multi-1
                type="button" class="btn btn-link">
          <img :src="basepath + im.main_file" 
              :alt="im.name" class="img-fluid">
        </button>
      </div>
    </div>
  </div> 
  <div class="row d-none d-sm-inline-block" v-if="wid > 0">
    <div class="col-12 bigimg-description popis">{{ myatt[id].description }}</div>
  </div>

  <b-modal  id="modal-multi-1" centered size="xl" 
            :title="myatt[id].name" ok-only
            modal-class="lightbox-img">
    <div class="modal-content">
      <div class="modal-body my-img-content">
        <b-button v-b-modal.modal-multi-2>
          <div class="border-a" :style="border_a">
            <div class="border-b" :style="border_b">
              <img :src="basepath + myatt[id].main_file" 
                    :alt="myatt[id].name" 
                    id="big-main-img"
                    class="border-c" 
                    :style="border_c" />
            </div>
          </div>
        </b-button>
        <div class="text-center description" v-if="myatt[id].description != null">
          {{ myatt[id].description }}
        </div>
      </div>
      <div class="arrows-l">
        <a href="#" 
            @click="before"
            :title="text_before">
          <i class="fas fa-arrow-circle-left fa-2x text-light"></i>
        </a>
      </div>
      <div class="arrows-r">
        <a href="#" 
            @click="after"
            :title="text_after">
          <i class="fas fa-arrow-circle-right fa-2x text-light"></i>
        </a>
      </div>
    </div>
  </b-modal>

  <b-modal id="modal-multi-2" centered size="xl" ok-only >
    <img :src="basepath + myatt[id].main_file" :alt="myatt[id].name" @click="closeme">
  </b-modal>
</div>
</template>