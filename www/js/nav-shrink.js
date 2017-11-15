// --- jquery version
//$(document).ready(function () {
//	$(window).scroll(function () {
//		if ($(document).scrollTop() > 80) {
//			$("#topNav").addClass('shrink');
//		} else {
//			$("#topNav").removeClass('shrink');
//		}
//	});
//	$("#topMenuButton").click(function () {
//		$("#topNav").height("auto");
//	});
//});

// --- pure js equivalent
document.addEventListener('DOMContentLoaded', function(){ 
  window.onscroll = function() {
    var topNav = document.getElementById("topNav");
    if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
      topNav.classList.add('shrink');
		} else {
      topNav.classList.remove("shrink");
		}
  };
  document.getElementById("topMenuButton").onclick = function(){
    document.getElementById("topNav").style.height = "auto";
  };
}/*,false*/);