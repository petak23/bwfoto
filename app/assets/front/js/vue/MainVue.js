/* 
 * Main Vue.js app file
 * Posledna zmena(last change): 04.01.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.4
 */

import Vue from 'vue';
import Slider from './components/Slider.vue';
import Autocomplete from './components/Autocomplete.vue';
import Fotogalery from './components/Fotogalery.vue';

  
var vm = new Vue({
  el: '#vueapp',
  data: function () {
    return {
      handle: null,
      param: null,
      viewBigImg: true,
      idBigImg: 0,
      aktual_file: ""
    }
  },
  components: { 
    Slider, 
    Autocomplete,
    Fotogalery
  },
  methods: {
    onBigImgClick() {
      this.viewBigImg = !this.viewBigImg;
    },
    openLightbox(file_image) {
      this.aktual_file = file_image;
    }
  },
  mounted: function () {
    this.idBigImg = this.$el.getAttribute('data-idBigImg');
//    this.param = this.$el.getAttribute('data-handle-param');
  }
});//.$mount('#autocomplete');   