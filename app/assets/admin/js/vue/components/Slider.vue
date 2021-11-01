<script>
export default {
  props: {
    source: {
      type: String,
      required: true
    }
  },
};
</script>

<template>
  <img :src="source" alt="Slider">
</template>