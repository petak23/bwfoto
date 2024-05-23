<script>
/**
 * Komponenta pre vypísanie správy o ukončení nákupu.
 * Posledna zmena 23.05.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
	export default {
		props: {
			info: {
				type: Object,
				default: null // {heading: "", message: "", type: ""}
			},
		},
	}
</script>

<template>
	<div v-if="info != null"
		class="alert" 
		:class="'alert-'+info.type"
	>
		<h4 class="alert-heading" v-if="info.heading">{{ info.heading }}</h4>

		<span v-html="info.message"></span>
	</div>
</template>
