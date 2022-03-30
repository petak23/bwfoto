document.addEventListener('DOMContentLoaded', function(){
  // Pre male rozlisenia
  let topNav = document.getElementById('topNav');
  let contentNav = document.getElementById('navbarSupportedContent');
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
  document.getElementById("topMenuButton").onclick = function(){
		contentNav.classList.toggle("hidecont");
    
  };
});