<script>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 30.03.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.5
 * Z kniznica pouzite súbory a upravene: https://github.com/seanghay/vue-photo-collage
 */
import PhotoCollageWrapper from "./vue-photo-collage/PhotoCollageWrapper.vue";
import axios from 'axios'

export default {
  components: {
    PhotoCollageWrapper,
  },
  props: {
    attachments: {
      type: String
    },
    basepath: {
      type: String,
      required: true,
    },
    maxrandompercwidth: { // Percento, o ktoré sa môže meniť naviac šírka fotky
      type: String,
      default: 20,
    },
    myschema: String
  },
  data() {
    return {
      id: 0, 

      collage: { // Objekt pre koláž
        width: "",
        height: [],
        layout: [],
        photos: [],
        borderRadius: ".2rem",
        showNumOfRemainingPhotos: false,
        maxRandomPercWidth: 20,
        widerPhotoId: [], // poradie fotky v riadku, ktorá má byť širšia 1,2,... 
                          // ak je zadané 0 generuje sa náhodne
                          // ak je zadané -1 všetky fotky budú rovnaké
      },
      image: {
        name: "",
        main_file: "",
        description: null
      },
      sch: [
        {
          // Max. šírka koláže pre ktorú platí
          max_width: 320,  
          // Počet fotiek v jednotlivých riadkoch
          schema: [2, 1, 3, 4, 4, 3, 4, 4], 
          // Výška jednotlivých riadkov v px
          height: [85, 60, 85, 60, 70, 95, 70, 60],
          // Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku:
          // Ak je zadané číslo väčšie ako 0 (1,2,...) tak tá konkrétna bude širšia, 
          // ak je zadané 0 generuje sa náhodne,
          // ak je zadané -1 všetky fotky v riadku budú rovnaké.
          widerPhotoId: [-1, 2, 0, 1, -1, 0, 2, 1], 
        },
        {
          max_width: 700,
          schema: [4, 3, 5, 4, 3, 4, 5, 4],
          height: [130, 175, 105, 120, 175, 130, 105, 120],
          widerPhotoId: [-1, 0, 2, 0, -1, 2, 3, 1],
        },
        {
          max_width: 1300,
          schema: [6, 7, 8, 7, 6, 8, 7, 6],
          height: [225, 170, 135, 170, 225, 135, 170, 225],
          widerPhotoId: [2, -1, 0, 2, -1, 1, 2, 1],
        },
        {
          max_width: 10000,
          schema: [6, 7, 8, 7, 6, 8, 7, 6],
          height: [318, 240, 190, 240, 318, 190, 240, 318],
          widerPhotoId: [3, 0, -1, 2, 2, -1, 3, 4],
        },
      ],
      // Koniec sch -----
    }
  },
  methods: {
    itemClickHandler(data, i) {
      // click event
      let odkaz = this.basepath + '/api/documents/document/' + data.id_foto
      axios.get(odkaz)
              .then(response => {
                this.image = response.data
                this.$bvModal.show("modal-multi-1")
              })
              .catch((error) => {
                console.log(odkaz)
                console.log(error)
              })
    },
    matchHeight () {
      this.computeLayout(this.$refs.imgDetail.clientWidth)
    },
    computeLayout(client_width) {
      let res = { 
        max_width: 0,  
        schema: [],  
        height: [],  
        layout: [],
        widerPhotoId: []
      };
      this.sch.forEach(x => {
        if (client_width < x.max_width && res.max_width == 0) {
          res = x
        }
      })
      res.layout = [] // Musí ostať inak nefunguje !?!
      this.collage.photos = JSON.parse(this.attachments)
      let i = this.collage.photos.length
      let r = 0 // riadok
      do {
        // Zisti počet foto v riadku. Ak by bolo 0 tak nahraď to 1
        let fr = res.schema[r] > 0 ? res.schema[r] : 1;
        res.layout.push( fr )

        r = r + 1 >= res.schema.length ? 0 : r + 1 
        i -= fr
      }
      while (i > 0);
      this.collage.width = client_width + 'px';
      this.collage.height = res.height
      this.collage.layout = res.layout
      this.collage.maxRandomPercWidth = parseInt(this.maxrandompercwidth)
      this.collage.widerPhotoId = res.widerPhotoId
    },
    // Generovanie url pre lazyloading obrázky
    /*getImageUrl(text) {
      return this.basepath + text
    },*/
  },
  created() {
    window.addEventListener("resize", this.matchHeight);
  },
  destroyed() {
    window.removeEventListener("resize", this.matchHeight);
  },
  computed: {},
  mounted () {
    /*if (typeof this.myschema !== 'undefined') {
      this.sch = JSON.parse(this.myschema)
      console.log(this.sch)
    }*/
    /* Naviazanie na sledovanie zmeny veľkosti stránky */
    this.matchHeight();
  },

};
</script>

<template>
  <span>
    <div ref="imgDetail" id="imgDetail"> 
      <photo-collage-wrapper 
        gapSize="6px"
        @itemClick="itemClickHandler"
        v-bind="collage">
      </photo-collage-wrapper>
    </div>
    <b-modal  id="modal-multi-1" centered size="xl" 
              :title="image.name" ok-only
              modal-class="lightbox-img"
              ref="modal1fo">
      <div class="modal-content">
        <div class="modal-body my-img-content">  
          <img :src="basepath + image.main_file" 
                :alt="image.name" 
                id="big-main-img"
                class="" />
          <div class="text-center description" v-if="image.description != null">
            {{ image.description }}
          </div>
        </div>
        
      </div>
    </b-modal>
  </span>
</template>

<style lang="scss" scoped>
  img {
    max-width: 80vw;
    height: 80vh;
  }
</style>