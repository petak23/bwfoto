<script>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 03.02.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.8
 * pouzita kniznica: https://github.com/seanghay/vue-photo-collage
 */
import { PhotoCollageWrapper } from 'vue-photo-collage'
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
  },
  data() {
    return {
      id: 0,
      col_len: 0,
      min_row: 2,
      schema: [1, 0, 3, 2, 1, 4],
      //wid: 0,
      uroven: 0, // Premenná sleduje uroveň zobrazenia

      collage: {
        width: "600px",
        height: ["250px", "180px", "350px", "150px", "250px", "150px"],
        layout: [],
        photos: [],
        borderRadius: ".2rem",
        showNumOfRemainingPhotos: true,
      },
      image: {
        name: "",
        main_file: "",
        description: null
      }
    }
  },
  methods: {
    itemClickHandler(data, i) {
      // click event
      //console.log(data.id_foto)
      var odkaz = this.basepath + '/api/documents/document/' + data.id_foto
      axios.get(odkaz)
              .then(response => {
                //this.items = Object.values(response.data)
                //this.$store.commit('SET_INIT_ADMIN_MENU', this.convert(response.data))
                this.image = response.data
                //console.log(response.data)
                this.$bvModal.show("modal-multi-1")
              })
              .catch((error) => {
                console.log(odkaz)
                console.log(error)
              })
    },
    matchHeight () {
      this.collage.width = this.$refs.imgDetail.clientWidth + 'px';
    },
    // Generovanie url pre lazyloading obrázky
    /*getImageUrl(text) {
      return this.basepath + text
    },*/
  },
  created() {
    window.addEventListener("resize", this.matchHeight);

    this.collage.photos = JSON.parse(this.attachments)
    this.col_len = this.collage.photos.length;
    this.collage.layout = []
    var i = this.col_len
    var r = 0 // riadok
    do {
      this.collage.layout.push( this.min_row + this.schema[r] )

      r = r + 1 >= this.schema.length ? 0 : r + 1 
      i -= this.min_row + this.schema[r]
    }
    while (i > 0);
    //console.log(this.col_len)
    //console.log(this.collage.layout)
  },
  destroyed() {
    window.removeEventListener("resize", this.matchHeight);
  },
  computed: {},
  mounted () {
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