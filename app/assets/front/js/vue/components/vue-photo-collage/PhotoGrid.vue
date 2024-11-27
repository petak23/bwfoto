<script setup>
/** 
 * @lastchange 27.11.2024
 *
 * @editedby Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @version 1.0.2
 */
import { computed } from "vue"

const props = defineProps({
  photostyle: Object,
})

const thumbStyle = computed(() => {
  let style = {};
  if (typeof props.photostyle.width !== 'undefined') {
    style.width = props.photostyle.width + '%';
  }
  return style; 
})
</script>

<template>
  <div 
    class="vue-photo-grid" 
    @click="$emit('click')"
    :style="thumbStyle"
  >
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
.vue-photo-grid {
  display: flex;
  position: relative;
  flex: auto; /*1;*/
  cursor: pointer;
  
  & + & {
    margin-left: var(--vue-photo-grid-gap);
  }

}
</style>
