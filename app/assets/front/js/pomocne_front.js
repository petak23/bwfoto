window.onload = function() {
	let colArt = document.getElementById("colArt")
	if (colArt !== null) { // Len ak sa na stránke nachádza...
		colArt.addEventListener("click", function() {
			this.classList.add("d-none");	
		});
	}
}
/*
import jquery from 'jquery';

// Časť funkcií pre jquery 
jquery(function() {

	// Pre zobrazenie celého oznamu
	let cely = jquery('.cely_oznam');      //Nájdem doplnok textu
	let textC = cely.next().html();		//Najdem cely text
	let textU = cely.prev();          //Najdem upraveny text
	cely.next().hide().remove();      //Skryjem ho
	cely.click(function() {           //Pri kliku na článok
		textU.append('<span class="ost">' + textC + '</span>');
		let ost = jquery('.ost');
		ost.hide();
		jquery(this).fadeOut(200, function() {
			jquery(this).remove();             //Odstránim odkaz
  		ost.slideDown('slow');        //Skryjem samotný odkaz
		});			  
		return false;                   //Zakážem odkaz
	});

  jquery('.thumbnails').find('.thumb-a').each(function(){
    let el = jquery(this);
    el.click(function(){
      jquery('.thumb-a').removeClass('selected');
      jquery(this).addClass('selected');
    });
  });
  
});*/