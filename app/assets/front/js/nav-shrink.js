/* 
 * Js súbor pre zmenu hlavného navbar-u.
 * Posledná zmena(last change): 04.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */
document.addEventListener('DOMContentLoaded', function(){
	// Pre male rozlisenia
	let topNav = document.getElementById('topNav');
	//let contentNav = document.getElementById('navbarSupportedContent');
	if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
		topNav.classList.add('shrink');
		//contentNav.classList.add('hidecont');
	} else {
		topNav.classList.remove('shrink');
		//contentNav.classList.remove('hidecont');
	}
	
	// Pre vacsie rozlisenia
	window.onscroll = function() {
		if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
			topNav.classList.add('shrink');
			//contentNav.classList.add('hidecont');
		} else {
			topNav.classList.remove('shrink');
			//contentNav.classList.remove('hidecont');
		}
	};
	//document.getElementById("topMenuButton").onclick = function(){
	//	contentNav.classList.toggle("hidecont"); 
	//};
});