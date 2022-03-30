//document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
$(function() {
  $.nette.init(); //ajax pravdepodobne sa inicializuje v texyle...
	$( "#locale" ).change(function() {
			$( "#datepicker" ).datepicker( "option",
				$.datepicker.regional[ 'sk' ] );
	});
	$("input.date").each(function () { // input[type=date] does not work in IE
        let el = $(this);
        let value = el.val();
        let date = (value ? $.datepicker.parseDate($.datepicker.W3C, value) : null);

        let minDate = el.attr("min") || null;
        if (minDate) minDate = $.datepicker.parseDate($.datepicker.W3C, minDate);
        let maxDate = el.attr("max") || null;
        if (maxDate) maxDate = $.datepicker.parseDate($.datepicker.W3C, maxDate);

        // input.attr("type", "text") throws exception
        if (el.attr("type") == "date") {
            let tmp = $("<input/>");
            $.each("class,disabled,id,maxlength,name,readonly,required,size,style,tabindex,title,value".split(","), function(i, attr)  {
                tmp.attr(attr, el.attr(attr));
            });
            el.replaceWith(tmp);
            el = tmp;
        }
        el.datepicker({
            minDate: minDate,
            maxDate: maxDate
        });
        el.val($.datepicker.formatDate(el.datepicker("option", "dateFormat"), date));
    });

  // -----------------------
	//Pre zobrazenie celého článku
	let cely_cl = $('.cely_clanok'); //Nájdem doplnok textu
	cely_cl.parent().next().hide();              //Skryjem ho
	//ostatok.hide();
	cely_cl.click(function()  //Pri kliku na článok
	{
		$(this).fadeOut(200, function()
		{
			$(this).remove();	//Odstránim odkaz
		}).parent().next().slideDown('slow');//fadeIn('slow');			  //Skryjem samotný odkaz
		return false; 					  //Zakážem odkaz
	});
	// -----------------------
	//Pre zobrazenie celého oznamu
	let cely = $('.cely_oznam'); //Nájdem doplnok textu
	let textC = cely.next().html();		//Najdem cely text
	let textU = cely.prev();		//Najdem upraveny text
	cely.next().hide().remove();              //Skryjem ho
	cely.click(function()  //Pri kliku na článok
	{
		textU.append('<span class="ost">' + textC + '</span>');
		let ost = $('.ost');
		ost.hide();
		$(this).fadeOut(200, function()
		{
			$(this).remove();	//Odstránim odkaz
			ost.slideDown('slow');
		});//.next().slideDown('slow');//fadeIn('slow');			  //Skryjem samotný odkaz
		return false; 					  //Zakážem odkaz
	});

	// -----------------------
	// Pre zobrazenie tagu, ktorý je pred položkou s id=nova_polozka na ktorú sa kliklo
	$('#nova_polozka').click(function()
	{
		$(this).prev().slideDown(1000);
		$(this).delay(1000).fadeTo(500,.01).slideUp(500, function() {
			$(this).remove();	//Odstránim odkaz
		});
		return false;
	});
	// Pre zobrazenie tagu, ktorý je pred položkou s id=nova_upload, na ktorú sa kliklo
	// a pre ukrytie nasledujúceho
	$('#nova_upload').click(function()
	{
		$(this).prev().slideDown(750);
		$(this).next().slideUp(750);
		$(this).fadeTo(500,.01).slideUp(500, function() {
			$(this).remove();	//Odstránim odkaz
		});
		return false;
	});

	$('.stav').click(function() {
		$(this).fadeTo(500,.01).slideUp(500, function() {
			$(this).remove();	//Odstránim odkaz
		});
	});
  
	$("#cela_nav").css({'display': 'none'});
	$(".menu_ukaz").click(function() {
		$(".menu_ukaz").fadeOut("fast", function(){
			$("#cela_nav").slideDown(1000);
		});
		return false;
	});
  
  /* pre zmenu náhľadu pri zmenách okrajového rámčeka */
  $("#frm-titleArticle-zmenOkrajForm").find("input.input_number").each(function(){ // frm-products-zmenOkrajForm
    let el = $(this);
    el.change(function(){
      let val = el.val();
      let cl = el.attr('name').split("_");
      $(".okraj-"+cl[1]).each(function(){
        $(this).css("border-width", val+"px");
      });
    });
  });
  
  $("#frm-titleArticle-zmenOkrajForm").find("input[type=color]").each(function(){ //frm-products-zmenOkrajForm
    let el = $(this);
    el.change(function(){
      let val = el.val();
      let cl = el.attr('name').split("_");
      $(".okraj-"+cl[1]).each(function(){
        $(this).css("border-color", val);
      });
    });
  });

  $(".btn-for-big-image").on('click', function() {
    let targ = $(this).data('target');
  	let imc = $(targ);
  	let imgsrc = $(this).data('imgsrc');
  	let img = imc.find('.modal-body img');
  	img.attr("src", function() {
  		return $(this).data('src') + imgsrc;
  	});
  	img.attr('alt', $(this).data('imgname'));
  	imc.modal('show');
  	return false;
  });
});