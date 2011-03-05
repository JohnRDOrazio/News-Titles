<?php

/* Blocco Flatnux che visualizza le notizie della sezione "News"
   con uno scorrimento verticale automatico.
   Ogni titolo è un link al messaggio completo.
   Quando non si è nella sezione news, compare un link alla sezione News in fondo al blocco (in italiano).
   Utilizza il tag Marquee, e dalla versione 1.5 utilizza la libreria jquery
   v 1.4 Aggiunta una barra laterale destra che con il mouseover cambia la direzione dello scorrimento in su o in giù.
   v 1.5 Migliorata la grafica e migliorata la funzionalità della barra laterale destra che controlla con il mouseover sia
         la direzione che la velocità dello scorrimento, utilizzando la libreria jquery
   v 1.6 Utilizza jquery per verificare se il blocco si trova effettivamente nella colonna destra oppure no,
         e lo costruisce dinamicamente a seconda se sta nella colonna destra o sinistra, invertendo tra di loro
         il marquee e la barra laterale
   v 1.6.5  Qualche ritocco al aspetto grafico; semplificata la struttura; aggiunto un div sopra che tiene la notizia
         più recente
   v 1.7 Aggiunto in fondo il link per aggiungere una notizia se si è amministratori delle news
   v 2.0 Aggiornato in base alle esigenze di php 5.3 (funzioni ereg* deprecated) e migliorato
         il modo dell'integrazione con flatnux
*/

require_once("NewsTitles/config.php");

global $_FN;

if(!function_exists("is_news_admin")){
  function is_news_admin(){
  	$user = getparam("myforum",PAR_COOKIE,SAN_FLAT);
  	if ( $user != "" )
  	{
  		if ( (is_admin()||user_in_group($user,"newsadmin")) && versecid($user) )
  		{
  			return true;
  		}
  	}
  	return false;
  }
}
$news_section = find_section("news");
$topbox = ($_NEWSTITLES_CFG['use-topbox'] == 1) ? "<div id=\"newstitles-newnews\"></div><hr />" : "";
$newsbar = ($_NEWSTITLES_CFG['use-jqueryui'] == 0) ?
   "<table id=\"newstitles-verticalbar-table\">
			<tr><td class=\"newsbar-up-fastest\">^</td></tr>
			<tr><td class=\"newsbar-up-faster midbuttons\">&middot;</td></tr>
			<tr><td class=\"newsbar-up-fast midbuttons\">&middot;</td></tr>
			<tr><td class=\"newsbar-stop midbuttons\">X</td></tr>
			<tr><td class=\"newsbar-down-fast midbuttons\">&middot;</td></tr>
			<tr><td class=\"newsbar-down-faster midbuttons\">&middot;</td></tr>
			<tr><td class=\"newsbar-down-fastest\">v</td></tr>
		</table>"
    : "";

echo $topbox;

?>

<div id="newstitles-newsbox">
	<div id="newstitles-wrapper"><marquee id="newstitles-view"></marquee></div>
  <div id="newstitles-verticalbar"><?php echo $newsbar; ?></div>
</div>
<?php if ($_FN["vmod"]!=$news_section) { echo "<div id=\"gotonews\"><a href=\"index.php?mod=news\">Vai alle News</a></div>"; } ?>
<?php if (is_news_admin()) { echo "<div id=\"addnews\"><a href=\"".fn_rewritelink("index.php?mod=news&amp;op=news")."\">"._ADDNEWS."</a></div>"; } ?>

<script type="text/javascript">

jQuery(document).ready(function(){

  // according to whether the block is on the right or left side of the page, keep the scrollbar external
	if($("#newstitles-newsbox").offset().left < 100){
		latopagina = "sx";
    $("#newstitles-wrapper").css({'right':'1px'});
		$("#newstitles-verticalbar").css({'left':'1px'});		
	}
  else {
		latopagina = "dx";
    $("#newstitles-wrapper").css({'left':'1px'});
		$("#newstitles-verticalbar").css({'right':'1px'});
  }

<?php
echo ($_NEWSTITLES_CFG['use-jqueryui'] == 0) ? "var a = document.createElement('link'),
        b = document.getElementsByTagName('head')[0];     
    a.rel = 'stylesheet';
    a.type = 'text/css';
    a.href = 'blocks/'+latopagina+'/NewsTitles/style.css';
    b.appendChild(a);" : "var a = document.createElement('link'),
        b = document.getElementsByTagName('head')[0];     
    a.rel = 'stylesheet';
    a.type = 'text/css';
    a.href = 'blocks/'+latopagina+'/NewsTitles/style-ui.css';
    b.appendChild(a);";
?>

if( $("#newstitles-verticalbar-table").length!=0 ) {
	$("#newstitles-verticalbar-table td.newsbar-up-fastest").hover(function(){
	    $("#newstitles-view").trigger("start").attr({ direction: "down", scrollAmount: 10});
	});
	$("#newstitles-verticalbar-table td.newsbar-up-faster").hover(function(){
	    $("#newstitles-view").trigger("start").attr({ direction: "down", scrollAmount: 4});
	});
	$("#newstitles-verticalbar-table td.newsbar-up-fast").hover(function(){
	    $("#newstitles-view").trigger("start").attr({ direction: "down", scrollAmount: 1});
	});
	$("#newstitles-verticalbar-table td.newsbar-stop").hover(function(){
	    $("#newstitles-view").trigger("stop");
	});
	$("#newstitles-verticalbar-table td.newsbar-down-fast").hover(function(){
	    $("#newstitles-view").trigger("start").attr({ direction: "up", scrollAmount: 1});
	});
	$("#newstitles-verticalbar-table td.newsbar-down-faster").hover(function(){
	    $("#newstitles-view").trigger("start").attr({ direction: "up", scrollAmount: 4});
	});
	$("#newstitles-verticalbar-table td.newsbar-down-fastest").hover(function(){
	    $("#newstitles-view").trigger("start").attr({ direction: "up", scrollAmount: 10});
	});
}
else {
	$("#newstitles-verticalbar").slider({
			orientation: "vertical",
			min: 0,
			max: 100,
			value: 40,
			slide: function( event, ui ) {
        if (ui.value>45&&ui.value<55){ $( "#newstitles-view" ).trigger("stop"); }
        else {
  				b = (ui.value>50) ? "down" : "up";
          if (ui.value>50&&ui.value<90) { c =  (ui.value / (100 - ui.value)) }
          if (ui.value>=90) { c = (ui.value / 10) }
          if (ui.value<50&&ui.value>10) { c = ((100 - ui.value) / ui.value) }
          if (ui.value<=10) { c = ((100-ui.value) / 10 ) }
          $( "#newstitles-view" ).trigger("start").attr({ direction: b, scrollAmount: c });
        }
			}
		});
  $("#newstitles-newsbox .ui-slider").removeClass("ui-corner-all");
  sliderwidth = ( $("#newstitles-newsbox .ui-slider").width() - 2);
  $("#newstitles-newsbox .ui-slider-handle").append("<span class='ui-icon ui-icon-grip-dotted-horizontal'></span>");   
}

if( $("#newstitles-newnews").length!=0 ){
  $("#newstitles-newnews").hide();
  $.get("/include/ajax/getnewstitles.php", {latestnews: "true"}, function(newnewsdata){ $('#newstitles-newnews').html(newnewsdata).fadeIn(600); });
}
  
  $("#newstitles-view").hide();
  $.get("/include/ajax/getnewstitles.php", function(mydata){
  	if(mydata!=""){ $('#newstitles-view').html(mydata).fadeIn(600);}
	  else{$('#newstitles-view').html("THERE ARE NO NEWS TITLES IN THIS LANGUAGE<br /><br />NO HAY TITULOS DE NOTICIAS EN ESTO IDIOMA<br /><br />IL N'Y A PAS DE TITRES DE NOUVELLES DANS CETTE LANGUE").fadeIn(600);}
  });
  $("#newstitles-view").attr({direction:"up",behaviour:"scroll",scrollamount:1,height:200});

});
</script>