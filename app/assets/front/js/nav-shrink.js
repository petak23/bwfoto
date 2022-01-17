// --- jquery version
/*$(document).ready(function () {
	$(window).scroll(function () {
		if ($(document).scrollTop() > 80) {
			$("#topNav").addClass('shrink');
		} else {
			$("#topNav").removeClass('shrink');
		}
	});
	$("#topMenuButton").click(function () {
		$("#topNav").height("auto");
	});
});*/

// --- pure js equivalent
document.addEventListener('DOMContentLoaded', function(){
  // Pre male rozlisenia
  var topNav = document.getElementById('topNav');
  var contentNav = document.getElementById('navbarSupportedContent');
  if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
	topNav.classList.add('shrink');
	contentNav.classList.add('hidecont');
  } else {
    topNav.classList.remove('shrink');
	contentNav.classList.remove('hidecont');
  }
  
  // Pre vacsie rozlisenia
  window.onscroll = function() {
    if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
	    topNav.classList.add('shrink');
		contentNav.classList.add('hidecont');
		} else {
    	topNav.classList.remove('shrink');
		contentNav.classList.remove('hidecont');
		}
  };
//  document.getElementById("topMenuButton").onclick = function(){
//    document.getElementById("topNav").style.height = "auto";
//  };
});