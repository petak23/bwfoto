<template>
  <div 
    class="vue-photo-grid" 
    @click="$emit('click')"
    :style="thumbStyle"
  >

    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    photostyle: Object,
  },
  computed: {
    thumbStyle() {
      var style = {};
      if (typeof this.photostyle.width !== 'undefined') {
        style.width = this.photostyle.width + '%';
      }
      return style;
    }
  }
}
</script>

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
