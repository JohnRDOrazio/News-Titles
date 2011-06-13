<?php
/* Blocco Flatnux che visualizza le notizie della sezione "News"
   con uno scorrimento verticale automatico.
   Ogni titolo è un link al messaggio completo.
   Quando non si è nella sezione news, compare un link alla sezione News in fondo al blocco (in italiano).
   Utilizza il tag Marquee. Dalla versione 1.5 utilizza jquery.
   v 1.4 Aggiunta una barra laterale destra che con il mouseover cambia la direzione dello scorrimento in su o in giù.
   v 1.5 Migliorata la grafica e migliorata la funzionalità della barra laterale destra che controlla con il mouseover
         sia la direzione che la velocità dello scorrimento, utilizzando la libreria jquery
   v 1.6 Utilizza jquery per verificare se il blocco si trova effettivamente nella colonna destra oppure no,
         e lo costruisce dinamicamente a seconda se sta nella colonna destra o sinistra, invertendo tra di loro
         il marquee e la barra laterale
   v 1.6.5  Qualche ritocco al aspetto grafico; semplificata la struttura; aggiunto un div sopra che tiene la notizia
         più recente
   v 1.7 Aggiunto in fondo il link per aggiungere una notizia se si è amministratori delle news
   v 2.0 Aggiornato in base alle esigenze di php 5.3 (funzioni ereg* deprecated) e migliorato
         il modo dell'integrazione con flatnux
   v 2.1 Il blocco è ora configurabile dagli amministratori del sito quando utilizzato insieme al tema "glorioso"
         con Modalità Modifica ON.
   v 2.2 Corretti alcuni errori di encoding... (La funzione get_file(), definita in shared.php, utilizza fgets, che non supporta UTF-8 e richiede un nuovo encoding)
 *    */
// *** lwangaman sets the document headers to avoid the Internet Explorer cache-problem
header( "Cache: no-cache" );
header( "Pragma: no-cache" );
// *** end set headers -- lwangaman
mb_internal_encoding('UTF-8'); // always needed before mb_ functions, check note below
$opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
$context = stream_context_create($opts);

require_once('../flatnux.php');

$sctnews = find_section("news");

global $_FN;
//da config
global $newspp,$allownewscomments,$guestnews,$guestcomment,$show_prev_next_news,$show_same_argument_news,$htmleditornews,$group_news,$hide_news_icon;
//grafica
global $show_news_icon,$hide_news_icon;
//funzioni per le news
fn_require_once('sections/'. $sctnews .'/functions.php');
//configurazione news
include ('sections/' . $sctnews . '/config.php');
$newsdir = get_section_id(find_section("news"));
//inizializzazione ---------------->
if ( !file_exists("{$_FN['datadir']}/$newsdir") )
	mkdir("{$_FN['datadir']}/$newsdir");
	
//----setto i permessi della cartella news in modo che l' utente possa caricare dei files ---->
$perm=getsectperm("{$_FN['datadir']}/$newsdir/");
if ( $perm['group']!=$group_news )
{
	if ( $group_news=="" )
	{
		unlink("{$_FN['datadir']}/$newsdir/level.php");
	}
	else
	{
		$fp=fopen("{$_FN['datadir']}/$newsdir/level.php","w");
		fwrite($fp,"<?"."php exit(0);?".">\n");
		fwrite($fp,"\n");
		fwrite($fp,"\n");
		fwrite($fp,"$group_news\n");
		fwrite($fp,"\n");
		fwrite($fp,"\n");
		fclose($fp);
	}
}
//----setto i permessi della cartella news in modo che l' utente possa caricare dei files ----<
foreach ( $_FN['listlanguages'] as $l )
{
	if ( !file_exists("{$_FN['datadir']}/$newsdir/$l") )
		mkdir("{$_FN['datadir']}/$newsdir/$l");
}
if ( !file_exists("{$_FN['datadir']}/$newsdir/arguments") )
{
	mkdir("{$_FN['datadir']}/$newsdir/arguments");
	fn_copy_dir("images/news/","{$_FN['datadir']}/$newsdir/arguments");
}
if ( $htmleditornews=="" )
{
	$htmleditornews=$_FN['htmleditor'];
}
if ( $group_news!="" )
{
	// creo il gruppo degli amministratori delle news
	if ( !file_exists($_FN['datadir']."/fndatabase/groups/$group_news.php") )
	{
		$table=new XMLTable("fndatabase","groups",$_FN['datadir']);
		$table->InsertRecord(array("groupname"=>"$group_news"));
	}
}
//inizializzazione ----------------<
if ( isset($hide_news_icon)&&$hide_news_icon==1 )
	$show_news_icon=0;
$modlist = array();
$modlocked = array();
$handle = opendir("{$_FN['datadir']}/$newsdir/" . $_FN['lang']);
	while (false!==($file=readdir($handle)))
	{
		if ( preg_match('/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].xml$/si',$file) )
		{
			$string=get_file("{$_FN['datadir']}/$newsdir/{$_FN['lang']}/$file");
			if(function_exists("mb_convert_encoding")){ $string = mb_convert_encoding($string, 'HTML-ENTITIES','UTF-8'); }
      else { $string = htmlentities($string, ENT_QUOTES, "UTF-8"); }
      $locked=preg_replace("/.*<fn:locked>/si","",$string);
			$locked=preg_replace("/<\\/fn:locked>.*/si","",$locked);
			if ( $locked==1 )
			{
				$modlocked[]=$file;
			}
			else
			{
				$modlist[]=$file;
			}
		}
	}
	closedir($handle);
	rsort($modlist);
	rsort($modlocked);
	foreach ( $modlist as $mod )
	{
		$modlocked[]=$mod;
	}
	$i=0;
	
	foreach ( $modlocked as $mod )
	{
		$i++;
		if ( $i>$newspp )
			break;
		$string=get_file("{$_FN['datadir']}/$newsdir/{$_FN['lang']}/$mod");
		if(function_exists("mb_convert_encoding")){ $string = mb_convert_encoding($string, 'HTML-ENTITIES','UTF-8'); }
    else { $string = htmlentities($string, ENT_QUOTES, "UTF-8"); }
		$title=preg_replace("/.*<fn:title>/s","",$string);
		$title=preg_replace("/<\\/fn:title>.*/s","",$title);
		$text=str_replace(".xml","","{$_FN['lang']}/".$mod);
		$text=str_replace(".php","",$text);
		global $newstitle;
		$newstitle[$i] = "<a style='font-weight:bold; color:DarkBlue;' href='index.php?mod=".find_section("news")."&amp;opmod=read&amp;id=".$text."'>".$title."</a><br>";
    $string = "";
    if (!isset ($title))
     	$title = "";
    global $jet_lag;
    $text = str_replace(".xml", "", $text);
    $newsdata = substr($text, (1 + strpos($text, "/")));
    $newstitle[$i] .= "<span style='font-size:10;color:DimGray;font-family:\"Lucida Sans\";'>"._POSTATO.date('d-', $newsdata + (3600 * $jet_lag)).date('m-', $newsdata + (3600 * $jet_lag)).date('Y ', $newsdata + (3600 * $jet_lag))."<br> ⇒ ("._LETTO;
    $string=file_get_contents("{$_FN['datadir']}/$newsdir/$text.xml",false,$context);
    $tmp = preg_replace("/.*<fn:reads>/si", "", $string);
    $tmp = preg_replace("/<\\/fn:reads>.*/s", "", $tmp);
    $newstitle[$i] .= " $tmp "._VOLTE.")</span>";


    if (isset($_GET["latestnews"]) && $i==1){echo $newstitle[$i];}
    elseif (!isset($_GET["latestnews"]) && $i < $newspp){echo "<hr>".$newstitle[$i];}
    elseif (!isset($_GET["latestnews"])) {echo "<hr>".$newstitle[$i]."<hr>";}
  }
?>