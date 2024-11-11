<script setup>
/**
 * Komponenta pre vypísanie správy o ukončení nákupu.
 * Posledna zmena 11.11.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */

const props = defineProps({
	info: {
		type: Object,
		default: null // {heading: "", message: "", type: ""}
	},
})
</script>

<template>
	<div v-if="info != null"
		class="alert" 
		:class="'alert-'+props.info.type"
	>
		<h4 class="alert-heading" v-if="props.info.heading">{{ props.info.heading }}</h4>

		<span v-html="props.info.message"></span>
	</div>
</template>
