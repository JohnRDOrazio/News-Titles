<script typt="text/javascript">
if (typeof jQuery == 'undefined') {  
  var headID = document.getElementsByTagName("head")[0];         
  var newScript = document.createElement('script');
  newScript.type = 'text/javascript';
  newScript.src = 'http://www.somedomain.com/somescript.js';
  headID.appendChild(newScript);
}
jQuery(document).ready(function(){

  // according to whether the block is on the right or left side of the page, keep the scrollbar external
  $(".newsbox").each(function(index) {
	if($(this).offset().left < 100){
		$(this).children("div.newsview").css({'float':'right'});
		$(this).children("div.newsbar").css({'float':'left'});		
	}
  	else {
		$(this).children("div.newsview").css({'float':'left'});
		$(this).children("div.newsbar").css({'float':'right'});
  	}
  });

	$("td.newsbar-up-fastest").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("start").attr({ direction: "down", scrollAmount: 10});
	});
	$("td.newsbar-up-faster").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("start").attr({ direction: "down", scrollAmount: 4});
	});
	$("td.newsbar-up-fast").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("start").attr({ direction: "down", scrollAmount: 1});
	});
	$("td.newsbar-stop").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("stop");
	});
	$("td.newsbar-down-fast").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("start").attr({ direction: "up", scrollAmount: 1});
	});
	$("td.newsbar-down-faster").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("start").attr({ direction: "up", scrollAmount: 4});
	});
	$("td.newsbar-down-fastest").hover(function(){
	    $(this).parents("div.newsbox").find("marquee").trigger("start").attr({ direction: "up", scrollAmount: 10});
	});


  $("div#newstitles-newnews").hide();
  $.get("/include/ajax/getnewstitles.php", {latestnews: "true"}, function(newnewsdata){$('div#newstitles-newnews').html(newnewsdata);$("div#newstitles-newnews").fadeIn(600);});

  
  $("marquee#newstitles").hide();
  $.get("/include/ajax/getnewstitles.php", function(mydata){
  	if(mydata!=""){$('marquee#newstitles').html(mydata).fadeIn(600);}
	else{$('marquee#newstitles').html("THERE ARE NO NEWS TITLES IN THIS LANGUAGE<br /><br />NO HAY TITULOS DE NOTICIAS EN ESTO IDIOMA<br /><br />IL N'Y A PAS DE TITRES DE NOUVELLES DANS CETTE LANGUE").fadeIn(600);}
  });

});
</script>
<?php

/* Blocco Flatnux che visualizza le notizie della sezione "News"
   con uno scorrimento verticale automatico.
   Ogni titolo � un link al messaggio completo.
   Quando non si � nella sezione news, compare un link alla sezione News in fondo al blocco (in italiano).
   Utilizza il tag Marquee, e dalla versione 1.5 utilizza la libreria jquery

   v 1.4 Aggiunta una barra laterale destra che con il mouseover cambia la direzione dello scorrimento in su o in gi�.

   v 1.5 Migliorata la grafica e migliorata la funzionalit� della barra laterale destra che controlla con il mouseover sia

         la direzione che la velocit� dello scorrimento, utilizzando la libreria jquery

   v 1.6 Utilizza jquery per verificare se il blocco si trova effettivamente nella colonna destra oppure no,

         e lo costruisce dinamicamente a seconda se sta nella colonna destra o sinistra, invertendo tra di loro

         il marquee e la barra laterale

   v 1.6.5  Qualche ritocco al aspetto grafico; semplificata la struttura; aggiunto un div sopra che tiene la notizia

         pi� recente

   v 1.7 Aggiunto in fondo il link per aggiungere una notizia se si � amministratori delle news

   v 2.0 Aggiornato in base alle esigenze di php 5.3 (funzioni ereg* deprecated) e migliorato
         il modo dell'integrazione con flatnux
   v 2.1 Modificata la struttura puramente a tabelle a una struttura a div e tabella      
*/

?>
<div class="newsbox">
	<div class="newnews" id="newstitles-newnews"></div>
	<div class='newsbar'>
		<table class="newsbar-tbl">
			<tr><td class="newsbar-up-fastest">^</td></tr>
			<tr><td class="newsbar-up-faster midbuttons">&middot;</td></tr>
			<tr><td class="newsbar-up-fast midbuttons">&middot;</td></tr>
			<tr><td class="newsbar-stop midbuttons">X</td></tr>
			<tr><td class="newsbar-down-fast midbuttons">&middot;</td></tr>
			<tr><td class="newsbar-down-faster midbuttons">&middot;</td></tr>
			<tr><td class="newsbar-down-fastest">v</td></tr>
		</table>
	</div>
	<div class='newsview'>
		<marquee class="newstitles" id="newstitles" direction="up" scrollAmount=1 scrolldelay=0>
		</marquee>
	</div>
	<div style="clear:both;"></div>

</div>
