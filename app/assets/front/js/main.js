//import jquery from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import naja from 'naja';
import netteForms from 'nette-forms';

window.Nette = netteForms;

/* Inicializácia pre ajax knižicu NAJA */
document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
netteForms.initOnLoad();

/* Zmenšenie headeru */
if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
	document.getElementById("topNav").classList.add('shrink');
}

import './pomocne_front.js';
import './nav-shrink.js';
import './vue/MainVue.js';