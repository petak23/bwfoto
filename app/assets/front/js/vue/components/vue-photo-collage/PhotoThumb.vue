<script setup>
/** 
 * @lastchange 27.11.2024
 *
 * @editedby Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @version 1.0.3
 */
import { computed } from "vue"

const props = defineProps({
	thumb: String,
})

const thumbStyle = computed(() => {
	return { 'background-image': `url(${props.thumb})`, } 
})
</script>

<template>
	<div class="vue-photo-thumb" :style="thumbStyle"><slot></slot></div>
</template>

<style lang="scss" scoped>
.vue-photo-thumb {
	flex: 1;
	background-position: center;
	background-size: cover;
	background-repeat: no-repeat;
	border-radius: var(--vue-photo-grid-radius);
}
</style>
