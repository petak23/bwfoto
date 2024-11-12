<script setup>
/**
 * Component attachments
 * Posledna zmena 21.08.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 * 
 */

import AttImages from './Images.vue'
import AttAudios from './Audios.vue'
import AttOthers from './Others.vue'
</script>

<template>
	<div class="col-12">
		<att-images />
		<att-audios />
		<att-others />
	</div>
</template>