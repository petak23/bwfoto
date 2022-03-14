<template>
  <div class="vue-photo-collage-wrapper">
    <photo-collage
      :disabled="disabled"
      :width="width"
      :height="internalHeight"
      :layout="layout"
      :layoutPhotoMaps="layoutPhotoMaps"
      :layoutNum="layoutNum"
      :remainingNum="remainingNum"
      :showNumOfRemainingPhotos="showNumOfRemainingPhotos"
      :maxRandomPercWidth="maxRandomPercWidth"
      :widerPhotoId="widerPhotoId"
      @itemClick="(data, i) => $emit('itemClick', data, i)"
    >
    </photo-collage>
  </div>
</template>

<script>
import PhotoCollage from "./PhotoCollage.vue";

function createPhotoIds(photos) {
  return photos.map((data, i) => {
    return { ...data, id: i };
  });
}

function createLayoutPhotoMaps(layout, photos) {
  const newPhotos = createPhotoIds(photos);
  const newMaps = {};
  layout.reduce((accumulator, currentValue, currentIndex) => {
    newMaps[currentIndex] = newPhotos.slice(
      accumulator,
      accumulator + currentValue
    );
    return accumulator + currentValue;
  }, 0);

  return newMaps;
}

export default {
  name: "PhotoCollageWrapper",
  components: {
    PhotoCollage,
  },
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
    width: {
      type: String,
      default: "800px",
    },
    height: {
      type: Array,
    },
    layout: {
      type: Array,
      default: [],
    },
    photos: {
      type: Array,
      default: [],
    },
    showNumOfRemainingPhotos: {
      type: Boolean,
      default: false,
    },
    maxRandomPercWidth: {
      type: Number,
      default: 20
    },
    widerPhotoId: {
      type: Array,
      default: [],
    },
    gapSize: String,
    borderRadius: String,
  },
  data() {
    const layout = this.layout;

    return {
      internalHeight: [],
      allowRender: false,
      layoutPhotoMaps: {},
      viewerIsOpen: false,
      currentImage: 0,
    };
  },
  watch: {
    layoutPhotoMaps: {
      handler() {
        this.allowRender = !Object.keys(this.layoutPhotoMaps).length;
      },
    },
    gapSize: {
      handler() {
        this.setGapSize();
      },
      deep: true,
    },
    borderRadius: {
      handler() {
        this.setBorderRadius();
      },
      deep: true,
    },
    layout: {
      handler() {
        this.reconfigurate();
      },
      deep: true,
    },
    photos: {
      handler() {
        this.reconfigurate();
      },
      deep: true,
    },
    height: {
      handler() {
        this.internalHeight = this.height

        if (this.internalHeight.length < this.layout.length) {
          for (let i = 0; i < this.layout.length; i++) {
            this.internalHeight[i] = "200px";
          }
        }
      },
      deep: true,
    }

  },
  created() {
    this.reconfigurate();
  },
  methods: {
    reconfigurate() {
      this.layoutPhotoMaps = createLayoutPhotoMaps(this.layout, this.photos);
      this.setGapSize();
      this.setBorderRadius();
    },
    setGapSize() {
      if (document) {
        document.documentElement.style.setProperty(
          "--vue-photo-grid-gap",
          this.gapSize
        );
      }
    },
    setBorderRadius() {
      if (document) {
        document.documentElement.style.setProperty(
          "--vue-photo-grid-radius",
          this.borderRadius
        );
      }
    },
  },
  computed: {
    layoutNum() {
      return this.layout.reduce(
        (accumulator, currentValue) => accumulator + currentValue,
        0
      );
    },
    remainingNum() {
      return this.photos.length - this.layoutNum;
    },

  },
};
</script>