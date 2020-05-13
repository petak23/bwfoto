/* Inicializácia pre ajax knižicu NAJA */
document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

/* Časť funkcií pre jquery */
$(function() {
  
	/*Pre zobrazenie celého článku*/
	var cely = $('.cely_clanok');     //Nájdem doplnok textu
	cely.next().hide();               //Skryjem ho
	cely.click(function() {           //Pri kliku na článok
		$(this).fadeOut(200, function() {
			$(this).remove();             //Odstránim odkaz
		}).next().slideDown('slow');		//Skryjem samotný odkaz
		return false; 					        //Zakážem odkaz
	});

	/*Pre zobrazenie celého oznamu*/
	var cely = $('.cely_oznam');      //Nájdem doplnok textu
	var textC = cely.next().html();		//Najdem cely text
	var textU = cely.prev();          //Najdem upraveny text
	cely.next().hide().remove();      //Skryjem ho
	cely.click(function() {           //Pri kliku na článok
		textU.append('<span class="ost">' + textC + '</span>');
		var ost = $('.ost');
		ost.hide();
		$(this).fadeOut(200, function() {
			$(this).remove();             //Odstránim odkaz
  		ost.slideDown('slow');        //Skryjem samotný odkaz
		});			  
		return false;                   //Zakážem odkaz
	});

  $('.thumbnails').find('.thumb-a').each(function(){
    var el = $(this);
    el.click(function(){
      $('.thumb-a').removeClass('selected');
      $(this).addClass('selected');
    });
  });
  
});