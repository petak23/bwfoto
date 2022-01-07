import jquery from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import './jquery-ui-1.12.1.js';

import naja from 'naja';
document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

import datagrid from 'ublaboo-datagrid/assets/datagrid.js';

import netteForms from 'nette-forms';
netteForms.initOnLoad(); 
window.Nette = netteForms;

import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh.js';
import 'ublaboo-datagrid/assets/datagrid-spinners.js';

//import './pomocne_admin.js';
jquery(function() {
/* pre zmenu náhľadu pri zmenách okrajového rámčeka */
  jquery("#frm-titleArticle-zmenOkrajForm").find("input.input_number").each(function(){
    var el = jquery(this);
    el.on("change", function(){
      var val = el.val();
      var cl = el.attr('name').split("_");
      jquery(".okraj-"+cl[1]).each(function(){
        jquery(this).css("border-width", val+"px");
      });
    });
  });

  jquery("#frm-titleArticle-zmenOkrajForm").find("input[type=color]").each(function(){
    var el = jquery(this);
    el.on("change", function(){
      var val = el.val();
      var cl = el.attr('name').split("_");
      jquery(".okraj-"+cl[1]).each(function(){
        jquery(this).css("border-color", val);
      });
    });
  });
});

//import './vue/MainVue.js';

import '../css/main.css';