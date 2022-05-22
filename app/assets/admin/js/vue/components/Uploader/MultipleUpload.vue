<script>
/**
 * Komponenta pre multiupload súborov.
 * Posledna zmena 28.04.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */

import _ from "lodash";
import axios from 'axios';

export default {
  props: {
    apiUrl: {  // Relatívna adresa pre API
      type: String,
      required: true,
    },
    basePath: {
      type: String,
      required: true,
    },
    id: {   // Id komponenty
      type: String,
      default: "multiple-uploader-component",
    },
    id_hlavne_menu: { // Id článku, ku ktorému nahrávam súbory
      type: String,
      required: true,
    },
    backLink: { // Link na presmerovanie po úspešnom nahratí
      type: String,
      required: true,
    },
    withThumb: {
      default: 0
    }
  },
  data: () => ({
    value: 0,
    max: 100,
    files: null,
    isUploading: false,
    uploadedImages: [],
  }),
  methods: {
    async imagesInserted() {
      this.files = this.$refs.imageUploader.files;
      await this.uploadImagesMethod();
    },
    async uploadImagesMethod() {
      let formData = new FormData();
      _.forEach(this.files, (file) => {
        formData.append("files[]", file);
      });
      this.isUploading = true;
      let odkaz = this.basePath + "/" + this.apiUrl + "/save/" + this.id_hlavne_menu
      //let _this = this
      await axios.post(odkaz, formData, {  
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: ({ total, loaded }) => {
          this.value = (loaded / total).toFixed(2) * 100;
        },
      })
      .then(response => {
        _.forEach(response.data.data, (file) => {
          this.uploadedImages.push(file)
        })
        if (response.data.status == 200) {
          this.$root.$emit('flash_message', [{'message':'Položka(y) bola(i) úspešne nahratá(é).', 
                                              'type':'success',
                                              'heading': 'Podarilo sa...'
                                            }])
        } else if (response.data.status == 500) {
          this.$root.$emit('flash_message', [{'message':'Niektorá položka sa nenahrala správne.', 
                                              'type':'danger',
                                              'heading': 'Ups...'
                                            }])
        }

        this.files = null;
        this.isUploading = false;
        this.$refs.imageUploader.value = null;

      })
      .catch((error) => {
        console.log(odkaz);
        console.log(error);
      });
    },
    deleteFile(id) {
      console.log(id)
      let odkaz =  this.basePath + "/" + this.apiUrl + "/delete/" + id

      axios.get(odkaz)
              .then(response => {
                console.log(response.data)
                //this.uploadedImages
              })
              .catch((error) => {
                console.log(this.odkaz);
                console.log(error);
              });
    },
    close() {
      // https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
      //window.location.href = this.backLink;

      this.$root.$emit('products_add', this.uploadedImages)
      this.$bvModal.hide("myModalAddMultiProductsChange")
    },
  },
};
</script>

<template>
  <div class="multiple-upload">
    <div class="d-flex justify-content-around">
      <div class="upload-container d-flex justify-content-center align-items-center">
        <input
          :id="id"
          multiple
          type="file"
          ref="imageUploader"
          class="input-uploader"
          @change="imagesInserted"
        />
        <div class="uploader-icon"><i class="fa-solid fa-upload fa-2x"></i></div>
      </div>
      <div class="flex-grow-1 d-flex flex-column px-3 justify-content-around">
        <bProgress v-if="isUploading" :value="value" :max="max" show-progress animated />
      </div>
    </div>
    <div class="d-flex flex-column mt-3">
      <div v-for="img in uploadedImages" :key="img.id">
        <bImg
          class="mt-1 mr-2"
          height="80px"
          width="80px"
          :src="basePath + '/' +  img.thumb_file"
          thumbnail
          fluid
          alt="Responsive image"
        />
        <button class="btn btn-outline-danger" @click="deleteFile(img.id)">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
      
    </div>
    <div class="d-flex justify-content-end">
      <button class="btn btn-success" @click="close">Skonč</button>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.multiple-upload {
  .upload-container {
    height: 100px !important;
    width: 100px !important;
    border: 2px dashed #ccc;
    border-radius: 5px !important;
  }
  .upload-container:hover {
      background-color: rgb(175, 245, 169) !important;
  }

  .input-uploader {
    opacity: 0;
    position: absolute;
    width: 100px !important;
    height: 100px !important;
  }

  .uploader-icon {
    width: 35px !important;
    height: 35px !important;
    color: #28a745;
  }
}
</style>