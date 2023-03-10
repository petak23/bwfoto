<script>
/** 
 * Component Slider
 * Posledná zmena(last change): 10.03.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 */

import axios from 'axios'

export default {
	props: {
		filesPath: { // Adresár k súborom
			type: String,
			required: true
		},
	},
	data() {
		return {
			items: null,
			show: null
		}
	},
	methods: {
		getSlider() {
			let odkaz = this.$store.state.apiPath + 'slider/getall/1'
			axios.get(odkaz)
				.then(response => {
					//console.log(response.data)
					this.items = response.data
					if (this.items !== null) this.findIn()
					this.$root.$refs.myslider.style.backgroundImage = 'url(' + this.filesPath + this.show.subor + ')'
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		},
		/*
	 	 * Najdenie poloziek slidera */
		findIn() {
			
			//$slider_zobrazenie = $slider -> fetchPairs("id", "zobrazenie");
			let vysa = []
			let vysledok = false
			let _v = null
			// Nájdi priamo daný klúč
			vysa[0] =  this.items.find(el => parseInt(el.zobrazenie) == this.$store.state.main_menu_active)
			if (vysa[0] == undefined) { // Ak nieje ...
				vysa = [];
				this.items.forEach((item, index) => {
					let s = item.zobrazenie
					if (s === null) {
						vysledok = true
					} else {
						_v = item.zobrazenie.indexOf(",") !== -1 ? item.zobrazenie.split(",") : item.zobrazenie
						vysledok = false
						if (Array.isArray(_v)) {
							_v.forEach((z) => {
								vysledok = this.zisti(parseInt(z)) ? true : vysledok
							})
						} else {
							vysledok = this.zisti(parseInt(_v));
						}
					}
					if (vysledok) {
						vysa.push(index);
					}
				})
			}
			this.show = this.items[vysa[0]]
		},
		/* Pre vyhodnotenie zobrazenia položky z*/
		zisti(z)
		{
			return z == null ? true
				: (z == 0 ? true
					: (z > 0 && (this.$store.state.main_menu_open.find(el => el == parseInt(z) !== undefined) )))
		}
	},
	watch: {
		'$store.state.main_menu_active': function() {
			if (this.$store.state.main_menu_active !== undefined) {
				//console.log(this.zisti(this.$store.state.main_menu_active))
				this.getSlider()
			}
			//this.$refs.myslider.style.backgroundImage = this.$store.state.basePath + '/www/' + this.source
			
			//document.getElementById("slider").style.backgroundImage = this.$store.state.basePath + '/www/' + this.source
		}
	},
};
</script>
<template>
</template>