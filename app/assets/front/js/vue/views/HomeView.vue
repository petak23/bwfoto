<script setup>
	import ShowArticle from '../components/ShowArticle'
	import BwfotoFixedHomepage from '../components/Menu/BWfoto_Fixed_Homepage'
</script>

<template>
	<div>
		Homeview...
		<!-- text nad nahladmi -->
		<show-article
			:id_hlavne_menu_lang = "14"
			container_id = "uvodnyText"
			view_h1 = "1"
			text_class = "text-justify"
		/>

		<!-- Hlavne menu -->
		<bwfoto-fixed-homepage
			part="1"
			avatar-dir="/files/menu/"
		/>

		<!-- text pod nahladmi -->
		<show-article
			:id_hlavne_menu_lang = "15"
			container_id = "popisDole"
		/>

	</div>
</template>
