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
        <div class="uploader-icon"><i class="fa-solid fa-upload"></i></div>
        <!-- img class="uploader-icon" :src="path + 'images/upload.png'" alt / -->
      </div>
      <div class="flex-grow-1 d-flex flex-column px-3 justify-content-around">
        <bProgress v-if="isUploading" :value="value" :max="max" show-progress animated />
      </div>
    </div>
    <div class="d-flex flex-wrap mt-3">
      <bImg
        v-for="img in uploadedImages"
        class="mt-1 mr-2"
        height="80px"
        width="80px"
        :src="img"
        thumbnail
        fluid
        :key="img"
        alt="Responsive image"
      />
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import _ from "lodash";
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
      console.log("FILES", this.files);
      _.forEach(this.files, (file) => {
        formData.append("files", file);
      });
      this.isUploading = true;
      let { data } = await API.post(this.apiUrl + 'tmp/', formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: ({ total, loaded }) => {
          this.value = (loaded / total).toFixed(2) * 100;
        },
      });
      this.uploadedImages = [...this.uploadedImages, ...data];
      this.files = null;
      this.isUploading = false;
      this.$refs.imageUploader.value = null;
    },
  },
  mounted () {
    const url = "" //window.location.origin;
    API.defaults.baseURL = url + this.path
  },
};
</script>

<style lang="scss" scoped>
.multiple-upload {
  .upload-container {
    height: 100px !important;
    width: 100px !important;
    border: 2px dashed #ccc;
    border-radius: 5px !important;

    :hover {
      background-color: #ccc !important;
    }
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
  }
}
</style>