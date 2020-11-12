import jquery from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import netteForms from 'nette-forms';
import naja from 'naja';
//import './jquery-ui-1.12.1.js';
//import './jquery.ui.datepicker-sk.js';
//import '../../../vendor/ublaboo/datagrid/assets/datagrid.js';
//import '../../../vendor/ublaboo/datagrid/assets/datagrid-instant-url-refresh.js';
//import '../../../vendor/ublaboo/datagrid/assets/datagrid-spinners.js';
//import './pomocne_admin.js';

/*
        - node_modules/jquery/dist/jquery.min.js
        - node_modules/bootstrap/dist/js/bootstrap.bundle.js
        - node_modules/nette-forms/src/assets/netteForms.min.js
        - node_modules/naja/dist/Naja.js
        - www/js/admin/nette.ajax.js
        - www/js/admin/jquery-ui-1.12.1.js
        - www/js/admin/jquery.ui.datepicker-sk.js
        - vendor/ublaboo/datagrid/assets/datagrid.js
        - vendor/ublaboo/datagrid/assets/datagrid-instant-url-refresh.js
        - vendor/ublaboo/datagrid/assets/datagrid-spinners.js
        - www/js/pomocne_admin.js
 */

window.Nette = netteForms;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
netteForms.initOnLoad();      