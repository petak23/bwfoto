<script>
export default {
  props: {
    source: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      fotogalery: Fotogalery
    }
  }
};
</script>

<template>
  <div class="fotogalery">
    {{ fotogalery }}
  </div>
</template>
