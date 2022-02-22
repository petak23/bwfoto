<script>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 22.02.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.0
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
    basepath: String,
    maxrandompercwidth: { // Percento, o ktoré sa môže meniť naviac šírka fotky
      type: String,
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
          // Minimálny počet fotiek na riadok
          min_row: 1,      
          // Počet fotiek v jednotlivých riadkoch + min_row
          schema: [1, 0, 2, 1, 2, 0, 1, 2], 
          // Výška jednotlivých riadkov
          height: ["110px", "150px", "78px", "110px", "78px", "150px", "110px", "78px"],
          // Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku:
          // Ak je zadané číslo väčšie ako 0 (1,2,...) tak tá konkrétna bude širšia, 
          // ak je zadané 0 generuje sa náhodne,
          // ak je zadané -1 všetky fotky v riadku budú rovnaké.
          widerPhotoId: [1, 0, 1, 0, 2, 0, 1, 2],
          // Výsledok, vypočítaný programom !!! NIČ NEZADǍVAŤ !!!
          layout: [],  
        },
        {
          max_width: 700,
          min_row: 2,
          schema: [1, 0, 1, 2, 1, 0, 2, 1],
          height: ["170px", "235px", "170px", "120px", "170px", "235px", "120px", "170px"],
          layout: [],
          widerPhotoId: [1, 0, 1, 0, 2, 0, 1, 2],
        },
        {
          max_width: 1300,
          min_row: 3,
          schema: [0, 1, 2, 3, 0, 1],
          height: ["300px", "220px", "180px", "170px", "235px", "220px"],
          layout: [],
          widerPhotoId: [1, 0, -1, 0, 2, 0],
        },
        {
          max_width: 10000,
          min_row: 3,
          schema: [0, 1, 2, 3, 1, 0],
          height: ["400px", "300px", "265px", "220px", "300px", "400px"],
          layout: [],
          widerPhotoId: [1, 0, -1, 0, 2, 0],
        },
      ],
    }
  },
  methods: {
    itemClickHandler(data, i) {
      // click event
      var odkaz = this.basepath + '/api/documents/document/' + data.id_foto
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
      var res = { 
        max_width: 0,
        min_row: 0,  
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
      var i = this.collage.photos.length
      var r = 0 // riadok
      do {
        res.layout.push( res.min_row + res.schema[r] )

        r = r + 1 >= res.schema.length ? 0 : r + 1 
        i -= res.min_row + res.schema[r]
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
    if (typeof this.myschema !== 'undefined') {
      this.sch = JSON.parse(this.myschema)
      console.log(this.sch)
    }
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
    width: 80vw;
    height: 80vh;
  }
</style>