<?php
/* Blocco Flatnux che visualizza le notizie della sezione "News"
   con uno scorrimento verticale automatico.
   Ogni titolo è un link al messaggio completo.
   Quando non si è nella sezione news, compare un link alla sezione News in fondo al blocco (in italiano).
   Utilizza il tag Marquee. Dalla versione 1.5 utilizza jquery.
   v 1.4 Aggiunta una barra laterale destra che con il mouseover cambia la direzione dello scorrimento in su o in giù.
   v 1.5 Migliorata la grafica e migliorata la funzionalità della barra laterale con l'utilizzo di jquery. Controlla sia
         la direzione che la velocità dello scorrimento.
   v 1.6 Utilizzo di jquery per determinare se il blocco si trova nella colonna destra o sinistra, o lo costruisce
         dinamicamente affinché la barra laterale sia sempre esterna.
   v 1.6.5 Ritoccato l'aspetto grafico, corretto un problema col character set che si presenta su alcuni server,
         semplificata la struttura
   */
// *** lwangaman sets the document headers to avoid the Internet Explorer cache-problem
header( "Cache: no-cache" );
header( "Pragma: no-cache" );
// *** end set headers -- lwangaman

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
		fwrite($fp,"<?php exit(0);?>\n");
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
                $newstitle[$i] .= "<span style='font-size:10;color:DimGray;'>"._POSTATO.date("d-", $newsdata + (3600 * $jet_lag)).date("m-", $newsdata + (3600 * $jet_lag)).date("Y ", $newsdata + (3600 * $jet_lag))."<br> &rArr; ("._LETTO;
                $string=file_get_contents("{$_FN['datadir']}/$newsdir/$text.xml");
                $tmp = preg_replace("/.*<fn:reads>/si", "", $string);
                $tmp = preg_replace("/<\\/fn:reads>.*/s", "", $tmp);
                $newstitle[$i] .= " $tmp "._VOLTE.")</span>";
                $newstitle[$i] = iconv("ISO-8859-1","UTF-8",$newstitle[$i]);
                if (isset($_GET["latestnews"]) && $i==1){echo $newstitle[$i];}
                elseif (!isset($_GET["latestnews"]) && $i < $newspp){echo "<hr>".$newstitle[$i];}
                elseif (!isset($_GET["latestnews"])) {echo "<hr>".$newstitle[$i]."<hr>";}
        }
?>