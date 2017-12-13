var options = {
//	boxId: 				'test1ID',
//	dimensions: 		true,
//	captions: 			true,
	prevImg: 			true,
	nextImg: 			true,
	hideCloseBtn: 		false,
  hideEshopBtn:     true,
	closeOnClick: 		true,
//	loadingAnimation: 	200,
//	animElCount: 		4,
//	preload: 			true,
	carousel: 			true,
	animation: 			400,
	nextOnClick: 		true,
	responsive: 		true,
	maxImgSize:			0.8,
	keyControls: 		true,
  border_a_width: 5,
  border_a_color: '#fff',
  border_b_width: 5,
  border_b_color: '#999',
  border_c_width: 5,
  border_c_color: '#339'//,
	// callbacks
//	onopen: function(){
		// ...
//    var img = document.getElementsByClassName('jslghtbx-animate-init');
//    console.log(img.getAttribute(src));
//	},
//	onclose: function(){
//		// ...
//	},
//	onload: function(){
		// ...
    
//	}//,
//	onresize: function(event){
//		// ...
//	},
//	onloaderror: function(event){
//		// ...
//	}
};
var lightbox = new Lightbox();
lightbox.load(options);
