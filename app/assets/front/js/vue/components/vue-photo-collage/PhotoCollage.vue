<script setup>
import { computed } from "vue"
import RowPhotos from "./RowPhotos.vue" //v3

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
  width: String,
  height: Array,
  padding: Array,
  layout: Array,
  layoutPhotoMaps: Object,
  layoutNum: Number,
  remainingNum: Number,
  showNumOfRemainingPhotos: Boolean,
  maxRandomPercWidth: Number,
  widerPhotoId: Array,
})

const photoCollageStyle = computed(() => {
  return { width: props.width }
})
</script>

<template>
  <div
    class="vue-photo-collage"
    :class="[disabled && 'vue-photo-collage--disabled']"
    :style="photoCollageStyle">
    <row-photos
      @itemClick="(data, i) => !disabled && $emit('itemClick', data, i)"
      v-for="(data, i) in layout"
      :key="i"
      :height="height[i]"
      :padding="padding[i]"
      :photos="layoutPhotoMaps[i]"
      :layoutNum="layoutNum"
      :remainingNum="remainingNum"
      :showNumOfRemainingPhotos="showNumOfRemainingPhotos"
      :maxRandomPercWidth="maxRandomPercWidth"
      :widerPhotoId="widerPhotoId[i]"
    ></row-photos>
  </div>
</template>

<style lang="scss">
.vue-photo-collage {
  font-family: inherit;
}

.vue-photo-collage.vue-photo-collage--disabled {
  .vue-photo-grid, .vue-view-more {
    cursor: inherit;
  }
}
</style>
