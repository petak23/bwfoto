<script>
/** 
 * @lastchange 17.03.2022
 *
 * @editedby Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @version 1.0.1
 */
export default {
  props: {
    rowHeight: Number,
  },
};
</script>

<template>
  <div class="vue-photo-row" :style="{ height: rowHeight+'px' }">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
.vue-photo-row {

  display: flex;
  box-sizing: border-box;

  & + & {
    margin-top: var(--vue-photo-grid-gap);
  }

}
</style>
