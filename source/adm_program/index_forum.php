<?php
/******************************************************************************
 * Index Seite des Forums
 *
 * Copyright    : (c) 2004 - 2007 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Thomas Thoss
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
include("system/common.php");

// Url-Stack loeschen
$_SESSION['navigation']->clear();

// Html-Kopf ausgeben
$g_layout['title']  = "Admidio Forum";
$g_layout['header'] = '<link rel="stylesheet" href="'. THEME_PATH. '/css/overview_modules.css" type="text/css" />';

//require(THEME_SERVER_PATH. "/overall_header.php");
// Header hier Ausgeben (Kopie von overall_header.php mit anderem Doctype wegen Script kompabilität)
/******************************************************************************
 * Anfang Html-Kopf der in allen Admidio-Dateien integriert wird
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!! W I C H T I G !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Diese Bereich bitte NICHT anpassen, da diese bei jedem Update ueberschrieben
 * werden sollte. Individuelle Anpassungen koennen in der header.php bzw. der 
 * body_top.php im Ordner adm_config gemacht werden.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 *****************************************************************************/

if(isset($g_layout['title']))
{
    $g_layout['title'] = strStripTags($g_layout['title']);
}
else
{
    $g_layout['title'] = "";
}

if(isset($g_layout['header']) == false)
{
    $g_layout['header'] = "";
}

if(isset($g_layout['onload']))
{
    $g_layout['onload'] = " onload=\"". $g_layout['onload']. "\"";
}
else
{
    $g_layout['onload'] = "";
}

if(isset($g_layout['includes']) == false)
{
    $g_layout['includes'] = true;
}
$orga_name = "";
if(isset($g_current_organization))
{
    $orga_name = $g_current_organization->getValue("org_longname");
}

header('Content-type: text/html; charset=utf-8'); 
if( eregi("(msie) ([0-9]{1,2}.[0-9]{1,3})",$_SERVER['HTTP_USER_AGENT'] ,$regs) )
{
   echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
}

echo '
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
<head>
    <!-- (c) 2004 - 2008 The Admidio Team - http://www.admidio.org -->
    
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <title>'. $orga_name; 
    if(strlen($g_layout['title']) > 0)
    {
        echo " - ". $g_layout['title'];
    }
    echo '</title>    
    
    <link rel="stylesheet" type="text/css" href="'. THEME_PATH. '/css/system.css" />
    <script type="text/javascript" src="'. $g_root_path. '/adm_program/system/js/common_functions.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/tooltip/ajax-tooltip.js"></script>
    ';
    
    echo $g_layout['header']. '

    <!--[if lt IE 7]>
    <script type="text/javascript">
        window.attachEvent("onload", correctPNG);
    </script>
    <![endif]-->';

    if($g_layout['includes'])
    {
        require(THEME_SERVER_PATH. "/my_header.php");
    }
    
echo "</head>
<body". $g_layout['onload']. ">";
    if($g_layout['includes'])
    {
        require(THEME_SERVER_PATH. "/my_body_top.php");
        if(isset($g_db))
        {
            // falls Anwender andere DB nutzt, hier zur Sicherheit wieder zu Admidio-DB wechseln
            $g_db->setCurrentDB();
        }
    }
/******************************************************************************
 * ENDE Html-Kopf der in allen Admidio-Dateien integriert wird
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!! W I C H T I G !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Diesen Bereich bitte NICHT anpassen, da diese bei jedem Update ueberschrieben
 * werden sollte. Individuelle Anpassungen koennen in der header.php bzw. der 
 * body_top.php im Ordner adm_config gemacht werden.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 *****************************************************************************/
// Header Ende


// Html des Modules ausgeben
?>

<script type="text/javascript"> 
<!--
notIE=document.getElementById&&!document.all
heightOffset=20
function resizeIframe(obj)
{
	this.obj=obj
	this.obj.style.height=""
	setTimeout("this.obj.style.height=this.obj.contentWindow.document.body.scrollHeight+(notIE?heightOffset:0)",10)
}
// -->
</script>

<iframe id="sizeframe" name="sizeframe" width="<?php if ($g_preferences['forum_width']){echo $g_preferences['forum_width'];}else{echo "550";}?>px" height="100px" scrolling="no" frameborder="no" allowtransparency="true" background-color="transparent" src="<?php echo $g_forum->url; ?>" onload="resizeIframe(this)"></iframe>

<?
require(THEME_SERVER_PATH. "/overall_footer.php");

?>