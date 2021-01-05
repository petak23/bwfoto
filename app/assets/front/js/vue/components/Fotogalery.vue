<script>
import ImageTitle from './components/Fotogalery/ImageTitle.vue';

export default {
  props: {
    title: {
      type: String,
      required: true
    }
  },
  components: {
    ImageTitle
  }
  //data() {
  //  return {
  //    fotogalery: "Moja nová fotogaleria s vue"
  //  }
  //<main-window></main-window>
  //<image-list></image-list>
  //<image-description></image-description>
  //}
};
</script>

<template>
  <ImageTitle title=title></ImageTitle>
</template>