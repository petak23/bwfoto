<template>
  <div class="single-upload">
    <div class="d-flex justify-content-around">
      <div class="upload-container d-flex justify-content-center align-items-center">
        <input
          :id="id"
          type="file"
          ref="imageUploader"
          class="input-uploader"
          @change="imageInserted"
        />
        <div class="uploader-icon"><i class="fa-solid fa-upload fa-2x"></i></div>
        <!--img class="uploader-icon" :src="path + 'images/upload.png'" alt /-->
      </div>
      <div class="flex-grow-1 d-flex flex-column px-3 justify-content-around">
        <bProgress v-if="isUploading" :value="value" :max="max" show-progress animated />
        <bImg
          v-if="uploadedImage"
          class="mt-1"
          height="80px"
          width="80px"
          :src="uploadedImage"
          thumbnail
          fluid
          alt="Responsive image"
        />
      </div>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import API from "../../services";
export default {
  props: {
    apiUrl: {
      type: String,
      required: true,
    },
    path: {
      type: String,
      required: true,
    },
    id: {
      type: String,
      required: true,
    },
  },
  data: () => ({
    value: 0,
    max: 100,
    file: null,
    isUploading: false,
    uploadedImage: null,
  }),
  methods: {
    imageInserted() {
      this.file = this.$refs.imageUploader.files[0];
      this.uploadImageMethod();
      this.$refs.imageUploader.value = null;
    },
    async uploadImageMethod() {
      let formData = new FormData();
      formData.append("file", this.file);
      for(var pair of formData.entries()) {
        console.log(pair[0], pair[1]);
      }
      this.isUploading = true;
      let { data } = await API.post(/*this.apiUrl + 'tmp/'*/'/files/prilohy/tmp', formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: ({ total, loaded }) => {
          this.value = (loaded / total).toFixed(2) * 100;
        },
      });
      console.log(data);
      this.uploadedImage = data.imagePath;
      this.file = null;
      this.isUploading = false;
    },
  },
  /*mounted () {
    const url = "" //window.location.origin;
    API.defaults.baseURL = url + this.path
  },*/
};
</script>

<style lang="scss" scoped>
.single-upload {
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