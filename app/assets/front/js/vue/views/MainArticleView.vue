<script>
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
 */
import FotoGalery from '../components/FotoGalery'
import FotoCollage from '../components/FotoCollage'
import FotoPanorama from '../components/FotoPanorama'
import Menucardorder from '../components/Menucardorder'
import EditArticle from '../../../../components/EditArticle/EditArticle'
import SingleMenu from '../components/Menu/SingleMenu'
import Attachments from '../components/Attachments/Attachments'

export default {
	components: {
		FotoGalery,
		FotoCollage,
		FotoPanorama,
		Menucardorder,
		EditArticle,
		SingleMenu,
		Attachments,
	},
	props: {
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
	},
	computed: {
		template_id() {
			return this.$store.state.article != null ? this.$store.state.article.template : 0
		}
	},
}
</script>

<template>
	<div> <!-- Vo vue3 tento div odstrániť !-->
		<article class="article-main">
			<edit-article
				:link="this_link"
				:link_to_admin="link_to_admin"
			></edit-article>
		</article>

		<!-- ----- BWfoto_foto_album ------ -->
		<foto-galery 
			v-if="template_id == 2"
			:first_id="first_id" 
		/>

		<!-- ----- BWfoto_foto_section -----  -->
		<menucardorder 
			v-else-if="template_id == 3"
		/>

		<!-- ----- BWfoto_foto_album_lg ----- -->
		<foto-panorama
			v-else-if="template_id == 6"
			:first_id="first_id" 
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