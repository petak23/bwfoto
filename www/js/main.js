import jquery from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import 'bootstrap/dist/css/bootstrap.min.css';
import naja from 'naja';
import netteForms from 'nette-forms';
import Vue from 'vue';
import axios from 'axios'

/*
 * node_modules/jquery/dist/jquery.min.js
   node_modules/bootstrap/dist/js/bootstrap.bundle.js
   node_modules/nette-forms/src/assets/netteForms.min.js
   node_modules/naja/dist/Naja.js
   node_modules/vue/dist/vue.js
   node_modules/axios/dist/axios.min.js
   www/js/pomocne_front.js
   www/js/nav-shrink.js
   www/js/vue/MainVue.js
 */

window.Nette = netteForms;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
netteForms.initOnLoad();

var app = new Vue({
  el: '#vueapp'
});