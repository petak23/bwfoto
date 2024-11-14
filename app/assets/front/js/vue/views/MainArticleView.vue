<script setup>
/**
 * Zobrazenie hlavnej časti článkov.
 * Posledna zmena 12.09.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 * 
 * @description https://www.npmjs.com/package/vue-session
 *
 */

import { computed } from 'vue'
import { useMainStore } from '../store/main.js'
const store = useMainStore()

import FotoGalery from '../components/FotoGalery'
import FotoCollage from '../components/FotoCollage'
import FotoPanorama from '../components/FotoPanorama'
import Menucardorder from '../components/Menucardorder'
import EditArticle from '../../../../components/EditArticle/EditArticle'
import SingleMenu from '../components/Menu/SingleMenu'
import Attachments from '../components/Attachments/Attachments'

const props = defineProps({
	first_id: {
		type: String, // Todo - prerob na Number
		default: "0", 
	},
	this_link: {
		type: String,
	},
	link_to_admin: {
		type: String,
	},
})

const template_id = computed(() => {
	return store.article != null && store.article.template !== undefined ? store.article.template : 0
})

const edit_enabled = computed(() => {
	return store.user != null && store.checkUserPermission('Front:Clanky', 'edit')
})
</script>

<template>
	<div> <!-- Vo vue3 tento div odstrániť !-->
		<article class="article-main">
			<edit-article
				:link="props.this_link"
				:link_to_admin="props.link_to_admin"
				:edit_enabled="edit_enabled"
				edit-menu-color-type="warning"
			></edit-article>
		</article>

		<!-- ----- BWfoto_foto_album ------ -->
		<foto-galery 
			v-if="template_id == 2"
			:first_id="props.first_id" 
		/>

		<!-- ----- BWfoto_foto_section -----  -->
		<menucardorder 
			v-else-if="template_id == 3"
			:id_hlavne_menu="store.main_menu_active"
			:edit_enabled="edit_enabled"
		/>

		<!-- ----- BWfoto_foto_album_lg ----- -->
		<foto-panorama
			v-else-if="template_id == 6"
			:first_id="props.first_id" 
		/>

		<!-- ----- BWfoto_foto_collage ----- -->
		<foto-collage 
			v-else-if="template_id == 7"
		/>
		
		<!-- ----- default ------ -->
		<section v-else class="row">
			<div class="col-12">
				<single-menu></single-menu>
			</div>
			<attachments />
			<!-- <div class="row" n:if="(isset($komponenty) && count($komponenty))">
				<div class="col-xs-12 col-md-12">
					<section id="nahlady" n:if="isset($komponenty) && count($komponenty)">
						{foreach $komponenty as $komp}
							{ifset $komp["parametre"]}
								{var $nazov_komp = $komp['nazov'].'-'.$iterator->counter}
								{control $nazov_komp, $komp["parametre"]}
							{else}
								{control $komp["nazov"]}
							{/ifset}
						{/foreach}
					</section>
				</div>
			</div>-->
		</section>
	</div>
</template>



<style lang="sass" scoped>

</style>