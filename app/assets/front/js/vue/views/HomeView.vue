<script setup>
	import ShowArticle from '../components/ShowArticle'
	import BwfotoFixedHomepage from '../components/Menu/BWfoto_Fixed_Homepage'
</script>

<template>
	<div>
		<!-- text nad nahladmi -->
		<show-article
			:id_hlavne_menu_lang = "14"
			container_id = "uvodnyText"
			view_h1
			text_class = "text-justify"
		/>

		<!-- Hlavne menu -->
		<bwfoto-fixed-homepage />

		<!-- text pod nahladmi -->
		<show-article
			:id_hlavne_menu_lang = "15"
			container_id = "popisDole"
		/>

	</div>
</template>
