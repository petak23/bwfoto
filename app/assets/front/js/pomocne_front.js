import jquery from 'jquery';

/* Časť funkcií pre jquery */
jquery(function() {
  
	/*Pre zobrazenie celého článku*/
	let cely_cl = jquery('.cely_clanok');     //Nájdem doplnok textu
	cely_cl.next().hide();               //Skryjem ho
	cely_cl.click(function() {           //Pri kliku na článok
		jquery(this).fadeOut(200, function() {
			jquery(this).remove();             //Odstránim odkaz
		}).next().slideDown('slow');		//Skryjem samotný odkaz
		return false; 					        //Zakážem odkaz
	});

	/*Pre zobrazenie celého oznamu*/
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
  
});