<script>
/* 
 * Component Fotogalery
 * Posledná zmena(last change): 10.01.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
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
      id: 0
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
    }
  },
  computed: {
    // Parsovanie JSON-u  na array
    myatt() {
      return JSON.parse(this.attachments)
    }
  }
};
</script>

<template>
<div class="col-12 main-win">
  <div class="row">
    <h4 class="col-12 bigimg-name">
      {{ myatt[id].name }}
    </h4>
  </div>
  <div class="row" id="webThumbnails">
    <div class="col-8 detail">
      <a  v-if="myatt[id].type == 'menu'"
          :href="myatt[id].web_name" 
          :title="myatt[id].name">
        <img  :src="basepath + myatt[id].main_file" 
              :alt="myatt[id].name">
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
            :alt="myatt[id].name">
        <br><h6>{{ myatt[id].name }}</h6>
      </button>  
      <button v-else-if="myatt[id].type == 'attachments2' || myatt[id].type == 'product'"
              v-b-modal.modal-multi-1
              type="button" class="btn btn-link">
        <img :src="basepath + myatt[id].main_file" 
            :alt="myatt[id].name">
      </button>
    </div>
    <div class="col-4 thumbnails row">
      <div class="col-12 col-md-6 col-lg-4 thumb" v-for="(im, index) in myatt" :key="im.id">
        <a @click="changebig(index)"
            href="#" :title="'Odkaz' + (im.type == 'menu' ? im.view_name : im.name)" 
            :class="'thumb-a, ajax' + (index == id ? ', selected' : '')">
          <img :src="basepath + im.thumb_file" 
              :alt="im.name" class="img-thumbnail">
        </a>
        {{ im.name }}
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 bigimg-description">{{ myatt[id].description }}</div>
  </div>

  <b-modal  id="modal-multi-1" centered size="xl" 
            :title="myatt[id].name" ok-only
            modal-class="lightbox-img">
    <div class="modal-content">
      <div class="modal-body">
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

  <b-modal id="modal-multi-2" centered size="xl" ok-only>
    <img :src="basepath + myatt[id].main_file" :alt="myatt[id].name">
  </b-modal>
</div>
</template>