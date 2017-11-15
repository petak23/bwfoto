var options = {
//	boxId: 				'testID',
//	dimensions: 		true,
//	captions: 			true,
	prevImg: 			true,
	nextImg: 			true,
	hideCloseBtn: 		false,
  hideEshopBtn:     false,
	closeOnClick: 		false,
//	loadingAnimation: 	200,
//	animElCount: 		4,
//	preload: 			true,
	carousel: 			true,
	animation: 			400,
	nextOnClick: 		true,
	responsive: 		true,
	maxImgSize:			0.8,
	keyControls: 		true,
	// callbacks
	onopen: function(){
		// ...
//    var img = document.getElementsByClassName('jslghtbx-animate-init');
//    console.log(img.getAttribute(src));
	},
//	onclose: function(){
//		// ...
//	},
	onload: function(){
		// ...
    
	}//,
//	onresize: function(event){
//		// ...
//	},
//	onloaderror: function(event){
//		// ...
//	}
};
var lightbox = new Lightbox();
lightbox.load(options);
