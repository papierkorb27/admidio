<?php
/******************************************************************************
 * RSS - Feed fuer Photos
 *
 * Copyright    : (c) 2004 - 2008 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Jochen Erkens
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Erzeugt einen RSS 2.0 - Feed mit Hilfe der RSS-Klasse fuer die 10 neuesten Fotoalben
 *
 *
 * Spezifikation von RSS 2.0: http://www.feedvalidator.org/docs/rss2.html
 *
 *****************************************************************************/

require("../../system/common.php");
require("../../system/classes/rss.php");


// Nachschauen ob RSS ueberhaupt aktiviert ist...
if ($g_preferences['enable_rss'] != 1)
{
    $g_message->setForwardUrl($g_homepage);
    $g_message->show("rss_disabled");
}

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($g_preferences['enable_photo_module'] == 0)
{
    // das Modul ist deaktiviert
    $g_message->show("module_disabled");
}
elseif($g_preferences['enable_photo_module'] == 2)
{
    // nur eingeloggte Benutzer duerfen auf das Modul zugreifen
    require("../../system/login_valid.php");
}

// die neuesten 10 Fotoalben aus der DB fischen...
$sql = "SELECT pho.*,
               cre_surname.usd_value as create_surname, cre_firstname.usd_value as create_firstname,
               cha_surname.usd_value as change_surname, cha_firstname.usd_value as change_firstname
          FROM ". TBL_PHOTOS. " pho
          LEFT JOIN ". TBL_USER_DATA ." cre_surname
            ON cre_surname.usd_usr_id = pho_usr_id_create
           AND cre_surname.usd_usf_id = ".$g_current_user->getProperty("Nachname", "usf_id")."
          LEFT JOIN ". TBL_USER_DATA ." cre_firstname
            ON cre_firstname.usd_usr_id = pho_usr_id_create
           AND cre_firstname.usd_usf_id = ".$g_current_user->getProperty("Vorname", "usf_id")."
          LEFT JOIN ". TBL_USER_DATA ." cha_surname
            ON cha_surname.usd_usr_id = pho_usr_id_change
           AND cha_surname.usd_usf_id = ".$g_current_user->getProperty("Nachname", "usf_id")."
          LEFT JOIN ". TBL_USER_DATA ." cha_firstname
            ON cha_firstname.usd_usr_id = pho_usr_id_change
           AND cha_firstname.usd_usf_id = ".$g_current_user->getProperty("Vorname", "usf_id")."
         WHERE (   pho_org_shortname = '". $g_current_organization->getValue("org_shortname"). "'
               AND pho_locked = 0)
         ORDER BY pho_timestamp_create DESC
         LIMIT 10";
$result = $g_db->query($sql);

// Rekursive Funktion zum Erfassen der Bilder in Unteralben
function countImages($adm_photo_album)
{
    global $g_db;
    $total_images = $adm_photo_album["pho_quantity"];
    
    $sql = "SELECT *
              FROM ". TBL_PHOTOS. "
             WHERE pho_pho_id_parent = ".$adm_photo_album["pho_id"]."
               AND pho_locked = 0";
    $result_child = $g_db->query($sql);
    
    while($adm_photo_child = $g_db->fetch_array($result_child))
    {
        $total_images = $total_images + countImages($adm_photo_child);
    }
    return $total_images;
}

// ab hier wird der RSS-Feed zusammengestellt

// Ein RSSfeed-Objekt erstellen
$rss = new RSSfeed("http://". $g_current_organization->getValue("org_homepage"), $g_current_organization->getValue("org_longname"). " - Fotos", "Die 10 neuesten Fotoalben");

// Dem RSSfeed-Objekt jetzt die RSSitems zusammenstellen und hinzufuegen
while ($row = $g_db->fetch_array($result))
{
    // Die Attribute fuer das Item zusammenstellen

    //Titel
    $parents = "";
    $pho_parent_id = $row['pho_pho_id_parent'];
    //Titel muss mit Ordnerstruktur zusammengesetzt werden
    while ($pho_parent_id != NULL)
    {
        //Erfassen des Eltern Albums
        $sql=" SELECT *
                 FROM ". TBL_PHOTOS. "
                WHERE pho_id = $pho_parent_id ";
        $result_parents = $g_db->query($sql);
        $adm_photo_parent = $g_db->fetch_array($result_parents);

        //Link zusammensetzen
        $parents = " > ".$adm_photo_parent["pho_name"].$parents;

        //Elternveranst
        $pho_parent_id=$adm_photo_parent["pho_pho_id_parent"];
    }
    $title = "Fotogalerien".$parents."&nbsp;&gt;&nbsp;".$row['pho_name'];

    //Link
    $link  = "$g_root_path/adm_program/modules/photos/photos.php?pho_id=". $row['pho_id'];

    //Bildersumme
    $bildersumme = countImages($row);

    //Inhalt zusammensetzen
    $description = "Fotogalerien".$parents." > ". $row['pho_name'];
    $description = $description. "<br /><br /> Bilder: ".$bildersumme;
    $description = $description. "<br /> Datum: ".mysqldate("d.m.y", $row['pho_begin']);
    //Enddatum nur wenn anders als startdatum
    if($row['pho_end'] != $row['pho_begin'])
    {
        $description = $description. " bis ".mysqldate("d.m.y", $row['pho_end']);
    }
    $description = $description. "<br />Fotos von: ".$row['pho_photographers'];

    //die letzten fuenf Bilder sollen als Beispiel genutzt werden
    if($row['pho_quantity'] >0)
    {
        $description = $description. "<br /><br />Beispielbilder:<br />";
        for($bild=$row['pho_quantity']; $bild>=$row['pho_quantity']-4 && $bild>0; $bild--)
        {
            $bildpfad = SERVER_PATH. "/adm_my_files/photos/".$row['pho_begin']."_".$row['pho_id']."/".$bild.".jpg";
            //Zu Sicherheit noch überwachen ob das Bild existiert, wenn ja raus damit
            if (file_exists($bildpfad))
            {
                $description = $description. "
                 <img src=\"$g_root_path/adm_program/modules/photos/photo_show.php?pho_id=".$row['pho_id']."&amp;pic_nr=".$bild."&amp;pho_begin=".$row['pho_begin']."&amp;scal=100\" border=\"0\" />&nbsp;";
            }
        }
    }

    //Link zur Momepage
    $description = $description. "<br /><br /><a href=\"$link\">Link auf ". $g_current_organization->getValue("org_homepage"). "</a>";

    // Den Autor und letzten Bearbeiter des Albums ermitteln und ausgeben
    $description = $description. "<br /><br /><i>Angelegt von ". $row['create_firstname']. ' '. $row['create_surname'].
								 " am ". mysqldatetime("d.m.y h:i", $row['pho_timestamp_create']). "</i>";

    if($row['pho_usr_id_change'] > 0)
    {
        $description = $description. "<br /><i>Zuletzt bearbeitet von ". $row['change_firstname']. ' '. $row['change_surname'].
									 " am ". mysqldatetime("d.m.y h:i", $row['pho_timestamp_change']). "</i>";
    }

    $pubDate = date('r',strtotime($row['pho_timestamp_create']));

    // Item hinzufuegen
    $rss->addItem($title, $description, $pubDate, $link);
}

// jetzt nur noch den Feed generieren lassen
$rss->buildFeed();

?>