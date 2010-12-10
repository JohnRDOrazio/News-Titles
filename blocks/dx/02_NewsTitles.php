<script typt="text/javascript">
if (typeof jQuery == 'undefined') {  
  var headID = document.getElementsByTagName("head")[0];         
  var newScript = document.createElement('script');
  newScript.type = 'text/javascript';
  newScript.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js';
  headID.appendChild(newScript);
}
if (typeof jQuery.ui == 'undefined') {
  var headID = document.getElementsByTagName("head")[0];         
  var newScript = document.createElement('script');
  newScript.type = 'text/javascript';
  newScript.src = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js';
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
