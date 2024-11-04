/* 
 * Main js file
 * Posledná zmena(last change): 04.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 */

import netteForms from 'nette-forms';

window.Nette = netteForms;

/* Inicializácia pre ajax knižicu NAJA */
netteForms.initOnLoad();

/** Zmenšenie headeru  
	@TODO - je potreba ak je to v nav-shrink.js ???
*/
if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
	document.getElementById("topNav").classList.add('shrink');
}
import '../css/main.scss';

window.onload = function() {
	let colArt = document.getElementById("colArt")
	if (colArt !== null) { // Len ak sa na stránke nachádza...
		colArt.addEventListener("click", function() {
			this.classList.add("d-none");	
		});
	}
}

import './nav-shrink.js';
import './vue/MainVue.js';

