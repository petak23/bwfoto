<script>
/** 
 * Component BWfoto_Tree_Main
 * Posledná zmena(last change): 14.02.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */
import axios from 'axios'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
	props: {
		apiPath: { // Cesta k API
			type: String,
			required: true,
		},
		part: {
			type: String,
			default: "1",
		},
		ulClass: {
			type: String,
			default: "navbar-nav mr-md-2 flex-grow-1 justify-content-end"
		}
	},
	data() {
		return {
			menu: {},
		}
	},
	methods: {
		getmenu() {
			// Načítanie údajov priamo z DB
			let odkaz = this.apiPath + 'menu/getmenu/0/Front'
			axios.get(odkaz)
				.then(response => {
					this.menu = response.data[this.part]
					//console.log(this.menu.children )
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});  
		}
	},
	mounted() {
		this.getmenu();
	},
	created() {
		// Reaguje na uloženie titulných informácií článku
		this.$root.$on('title-save', data => {
			this.getmenu()
		})
	}
}
</script>

<template>
	<ul :class="ulClass">
		<li class="nav-item"  v-for="item in menu.children" :key="item.id">
			<a  :href="item.link" 
					:title="item.name"
					class="nav-link"
					
			><!--:class="item.getItemClass('active')"-->
				{{ item.name }}
				<br v-if="item.tooltip != null" />
				<small v-if="item.tooltip != null">{{ item.tooltip }}</small>
			</a>
		</li>
	</ul>
</template>

<style lang="scss" scoped>

</style>