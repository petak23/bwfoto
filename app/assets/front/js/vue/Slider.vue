<script>
export default {
  props: {
    source: {
      type: String,
      required: true
    }
  },
  // template: ``
};
</script>

<template>
  <img :src="source" alt="Slider">
</template>