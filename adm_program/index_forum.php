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

require(THEME_SERVER_PATH. "/overall_header.php");

// Html des Modules ausgeben
?>
<script type="text/javascript"> 
<!--
	notIE=document.getElementById&&!document.all
	function resizeIframe(obj, id)
	{
		var notIE = document.getElementById&&!document.all;
		var heightOffset = 0;
		var IFrameObj = document.getElementById(id);
		IFrameObj.style.height="";
		var IFrameDoc;
		if (notIE) {
			// For NS6
			IFrameDoc = IFrameObj.contentDocument; 
			IFrameObj.style.height=IFrameDoc.body.scrollHeight+(notIE?0:heightOffset)+"px";
		} else {
			// For IE5.5 and IE6 and IE7
			this.obj=obj
			this.obj.style.height=""
			setTimeout("this.obj.style.height=this.obj.contentWindow.document.body.scrollHeight+(notIE?heightOffset:0)",10)
		}
	}
// -->
</script>
<br />
<iframe id="sizeframe" name="sizeframe" style="padding:0px; margin:0px; width:<?php if ($g_preferences['forum_width']){echo $g_preferences['forum_width'];}else{echo "570";}?>px ;height:100px;" scrolling="no" frameborder="no" allowtransparency="true" background-color="transparent" marginheight="0" marginwidth="0"  src="<?php echo $g_forum->url_intern; ?>" onload="resizeIframe(this, 'sizeframe');"></iframe>
<br />
<?
require(THEME_SERVER_PATH. "/overall_footer.php");
?>