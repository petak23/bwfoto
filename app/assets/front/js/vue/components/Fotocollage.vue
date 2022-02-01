<script>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 01.02.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 * pouzita kniznica: https://github.com/seanghay/vue-photo-collage
 */
import { PhotoCollageWrapper } from 'vue-photo-collage'

export default {
  components: {
    PhotoCollageWrapper,
  },
  props: {
    attachments: {
      type: String
    },
  },
  data() {
    return {
      id: 0,
      col_len: 0,
      min_row: 3,
      schema: [1, 0, 2],
      //wid: 0,
      uroven: 0, // Premenná sleduje uroveň zobrazenia

      collage: {
        width: "600px",
        height: [],//"300px", "250px"],
        layout: [],//4, 3, 5],
        photos: [],
        borderRadius: ".2rem",
        //showNumOfRemainingPhotos: true,
      },
    }
  },
  methods: {
    itemClickHandler(data, i) {
      // click event
    },
    // Zmena id
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
  <div ref="imgDetail" id="imgDetail"> 
    <photo-collage-wrapper 
      gapSize="6px"
      @itemClick="itemClickHandler"
      v-bind="collage">
    </photo-collage-wrapper>
  </div>
</template>