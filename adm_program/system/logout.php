<?php
/******************************************************************************
 * User aus Admidio ausloggen
 * Cookies loeschen
 *
 * Copyright    : (c) 2004 - 2007 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 *
 ******************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *****************************************************************************/

require("common.php");

// Session pruefen

$sql    = "SELECT * FROM ". TBL_SESSIONS. " WHERE ses_session = {0} ";
$sql    = prepareSQL($sql, array($g_session_id));
$result = mysql_query($sql, $g_adm_con);
db_error($result);

$session_found = mysql_num_rows($result);

// Inhalt der Cookies loeschen
$domain = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], ':'));
setcookie("adm_session", "" , time() - 1000, "/", $domain, 0);

unset($_SESSION['g_current_organizsation']);
unset($_SESSION['g_current_user']);

if ($session_found > 0)
{
    // Session loeschen

    $sql    = "DELETE FROM ". TBL_SESSIONS. " WHERE ses_session = {0}";
    $sql    = prepareSQL($sql, array($g_session_id));
    $result = mysql_query($sql, $g_adm_con);
    db_error($result);
}

// Wenn die Session des Forums aktiv ist, diese ebenfalls löschen.
if($g_forum)
{
    mysql_select_db($g_forum_db, $g_forum_con);

    // User-Session im Forum löschen
    $sql    = "DELETE FROM ". $g_forum_praefix. "_sessions WHERE session_user_id = $g_forum_userid ";
    $result = mysql_query($sql, $g_forum_con);
    db_error($result);

    mysql_select_db($g_adm_db, $g_adm_con);


    // Cookie fuer die Anmeldung im Forum löschen
    setcookie($g_forum_cookie_name."_sid", "", time() - 31536000, $g_forum_cookie_path, $g_forum_cookie_domain, $g_forum_cookie_secure);

	// Session Varibale löschen
	unset($_SESSION['s_user_valid']);

    $message_code = "logoutforum";    
}
else
{
    $message_code = "logout";
}

// Hinweis auf erfolgreiches Ausloggen und weiter zur Startseite
$g_message->setForwardUrl("home", 2000);
$g_message->show($message_code);
?>