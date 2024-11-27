<script setup>
/** 
 * @lastchange 27.11.2024
 *
 * @editedby Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @version 1.0.3
 */
const props = defineProps({
  rowHeight: Number,
  rowPadding: Number,
})
</script>

<template>
  <div class="vue-photo-row" :style="{ height: props.rowHeight+'px', 'padding-bottom': props.rowPadding+'px' }">
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
