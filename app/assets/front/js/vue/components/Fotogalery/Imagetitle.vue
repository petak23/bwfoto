<script setup>
const props = defineProps({
	title: String
})
</script>

<template>
	<div class="row">
		<h4 class="col-12 bigimg-name">
			{{ props.title }}
		</h4>
	</div>
</template>