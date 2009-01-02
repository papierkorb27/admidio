<?php
/******************************************************************************
 * Termine anlegen und bearbeiten
 *
 * Copyright    : (c) 2004 - 2008 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 * dat_id   - ID des Termins, der bearbeitet werden soll
 * headline - Ueberschrift, die ueber den Terminen steht
 *            (Default) Termine
 *
 *****************************************************************************/

require("../../system/common.php");
require("../../system/login_valid.php");
require("../../system/classes/table_date.php");

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($g_preferences['enable_dates_module'] == 0)
{
    // das Modul ist deaktiviert
    $g_message->show("module_disabled");
}

if(!$g_current_user->editDates())
{
    $g_message->show("norights");
}

// lokale Variablen der Uebergabevariablen initialisieren
$req_dat_id   = 0;

// Uebergabevariablen pruefen

if(isset($_GET['dat_id']))
{
    if(is_numeric($_GET['dat_id']) == false)
    {
        $g_message->show("invalid");
    }
    $req_dat_id = $_GET['dat_id'];
}

if(!isset($_GET['headline']))
{
    $_GET["headline"] = "Termine";
}

$_SESSION['navigation']->addUrl(CURRENT_URL);

// Terminobjekt anlegen
$date = new TableDate($g_db);

if($req_dat_id > 0)
{
    $date->readData($req_dat_id);

    // Pruefung, ob der Termin zur aktuellen Organisation gehoert bzw. global ist
    if($date->editRight() == false)
    {
        $g_message->show("norights");
    }
}
else
{
    // bei neuem Termin Datum mit aktuellen Daten vorbelegen
    $date->setValue("dat_begin", date("Y-m-d H:00:00", time()));
    $date->setValue("dat_end", date("Y-m-d H:00:00", time()+3600));
}

if(isset($_SESSION['dates_request']))
{
    // durch fehlerhafte Eingabe ist der User zu diesem Formular zurueckgekehrt
    // nun die vorher eingegebenen Inhalte auslesen
    foreach($_SESSION['dates_request'] as $key => $value)
    {
        if(strpos($key, "dat_") == 0)
        {
            $date->setValue($key, stripslashes($value));
        }
    }
    $date_from = $_SESSION['dates_request']['date_from'];
    $time_from = $_SESSION['dates_request']['time_from'];
    $date_to   = $_SESSION['dates_request']['date_to'];
    $time_to   = $_SESSION['dates_request']['time_to'];
    unset($_SESSION['dates_request']);
}
else
{
    // Zeitangaben von/bis aus Datetime-Feld aufsplitten
    $date_from = mysqldatetime("d.m.y", $date->getValue("dat_begin"));
    $time_from = mysqldatetime("h:i",   $date->getValue("dat_begin"));

    // Datum-Bis nur anzeigen, wenn es sich von Datum-Von unterscheidet
    $date_to = mysqldatetime("d.m.y", $date->getValue("dat_end"));
    $time_to = mysqldatetime("h:i",   $date->getValue("dat_end"));
}

// Html-Kopf ausgeben
if($req_dat_id > 0)
{
    $g_layout['title'] = $_GET['headline']. " ändern";
}
else
{
    $g_layout['title'] = $_GET['headline']. " anlegen";
}

$g_layout['header'] = '
<script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
<link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
<script type="text/javascript"><!--
     //Kontrolliert ob das Anfangsdatum wirklich vor dem Enddatum liegt


    // Funktion blendet Zeitfelder ein/aus
    function setAllDay()
    {
        if(document.getElementById("dat_all_day").checked == true)
        {
            document.getElementById("time_from").style.visibility = "hidden";
            document.getElementById("time_from").style.display    = "none";
            document.getElementById("time_to").style.visibility = "hidden";
            document.getElementById("time_to").style.display    = "none";
        }
        else
        {
            document.getElementById("time_from").style.visibility = "visible";
            document.getElementById("time_from").style.display    = "";
            document.getElementById("time_to").style.visibility = "visible";
            document.getElementById("time_to").style.display    = "";
        }
    }

    // Funktion belegt das Datum-bis entsprechend dem Datum-Von
    function setDateTo()
    {
        if(document.getElementById("date_from").value > document.getElementById("date_to").value)
        {
            document.getElementById("date_to").value = document.getElementById("date_from").value;
        }
    }

    var vorbelegt = Array(false,false,false,false,false,false,false,false,false,false);
    var bbids = Array("b","u","i","big","small","center","url","email","img");
    var bbcodes = Array("[b]","[/b]","[u]","[/u]","[i]","[/i]","[big]","[/big]","[small]","[/small]","[center]","[/center]",
                        "[url='.$g_root_path.']","[/url]","[email=adresse@demo.de]","[/email]","[img]","[/img]");
    var bbcodestext = Array("text_bold_point.png","text_bold.png",
                            "text_underline_point.png","text_underline.png",
                            "text_italic_point.png","text_italic.png",
                            "text_bigger_point.png","text_bigger.png",
                            "text_smaller_point.png","text_smaller.png",
                            "text_align_center_point.png","text_align_center.png",
                            "link_point.png","link.png",
                            "email_point.png","email.png",
                            "image_point.png","image.png");

    function emoticon(text)
    {
        var txtarea = document.getElementById("dat_description");

        if (txtarea.createTextRange && txtarea.caretPos)
        {
            txtarea.caretPos.text = text;
        }
        else
        {
            txtarea.value  += text;
        }
        txtarea.focus();
    }

    function bbcode(nummer)
    {
      var arrayid;
      if (vorbelegt[nummer])
      {
         arrayid = nummer*2+1;
      }
      else
      {
         arrayid = nummer*2;
      }
      emoticon(bbcodes[arrayid]);
      document.getElementById(bbids[nummer]).src = "'.THEME_PATH.'/icons/"+bbcodestext[arrayid];
      vorbelegt[nummer] = !vorbelegt[nummer];
    }

    //Funktion schließt alle offnen Tags
    function bbcodeclose()
    {
       for (var i = 0; i < 9; i++)
       {
          if (vorbelegt[i])
          {
             bbcode(i);
          }
       }
    }
    var calPopUp = new CalendarPopup("calendardiv");
    calPopUp.setCssPrefix("calendar");
--></script>';
require(THEME_SERVER_PATH. "/overall_header.php");

// Html des Modules ausgeben
echo "
<form method=\"post\" action=\"$g_root_path/adm_program/modules/dates/dates_function.php?dat_id=$req_dat_id&amp;mode=1\">
<div class=\"formLayout\" id=\"edit_dates_form\">
    <div class=\"formHead\">". $g_layout['title']. "</div>
    <div class=\"formBody\">
        <ul class=\"formFieldList\">
            <li>
                <dl>
                    <dt><label for=\"dat_headline\">Überschrift:</label></dt>
                    <dd>
                        <input type=\"text\" id=\"dat_headline\" name=\"dat_headline\" style=\"width: 345px;\" maxlength=\"100\" value=\"". $date->getValue("dat_headline"). "\" />
                        <span class=\"mandatoryFieldMarker\" title=\"Pflichtfeld\">*</span>
                    </dd>
                </dl>
            </li>";

            // besitzt die Organisation eine Elternorga oder hat selber Kinder, so kann die Ankuendigung auf "global" gesetzt werden
            if($g_current_organization->getValue("org_org_id_parent") > 0
            || $g_current_organization->hasChildOrganizations())
            {
                echo "
                <li>
                    <dl>
                        <dt>&nbsp;</dt>
                        <dd>
                            <input type=\"checkbox\" id=\"dat_global\" name=\"dat_global\" ";
                            if($date->getValue("dat_global") == 1)
                            {
                                echo " checked=\"checked\" ";
                            }
                            echo " value=\"1\" />
                            <label for=\"dat_global\">". $_GET['headline']. " für mehrere Organisationen sichtbar</label>
                            <img class=\"iconHelpLink\" src=\"". THEME_PATH. "/icons/help.png\" alt=\"Hilfe\"  title=\"\"
                                onclick=\"window.open('$g_root_path/adm_program/system/msg_window.php?err_code=date_global&amp;window=true','Message','width=300,height=300,left=310,top=200,scrollbars=yes')\"
                                onmouseover=\"ajax_showTooltip(event,'$g_root_path/adm_program/system/msg_window.php?err_code=date_global',this);\" onmouseout=\"ajax_hideTooltip()\" />
                        </dd>
                    </dl>
                </li>";
            }

            echo "<li><hr /></li>

            <li>
                <dl>
                    <dt><label for=\"date_from\">Beginn:</label></dt>
                    <dd>
                        <span>
                            <input type=\"text\" id=\"date_from\" name=\"date_from\" onchange=\"javascript:setDateTo();\" size=\"10\" maxlength=\"10\" value=\"$date_from\" />
                            <img id=\"ico_cal_date_from\" src=\"". THEME_PATH. "/icons/calendar.png\" onclick=\"javascript:calPopUp.select(document.forms[0].date_from,'ico_cal_date_from','dd.MM.yyyy','date_from','date_to','time_from','time_to');\" style=\"vertical-align:middle; cursor:pointer;\" alt=\"Kalender anzeigen\" title=\"Kalender anzeigen\" />
                            <span id=\"calendardiv\" style=\"position: absolute; visibility: hidden; \"></span>
                        </span>
                        <span style=\"margin-left: 10px;\">
                            <input type=\"text\" id=\"time_from\" name=\"time_from\" size=\"5\" maxlength=\"5\" value=\"$time_from\" />
                            <span class=\"mandatoryFieldMarker\" title=\"Pflichtfeld\">*</span>
                        </span>
                        <span style=\"margin-left: 15px;\">
                            <input type=\"checkbox\" id=\"dat_all_day\" name=\"dat_all_day\" ";
                            if($date->getValue("dat_all_day") == 1)
                            {
                                echo " checked=\"checked\" ";
                            }
                            echo " onclick=\"setAllDay()\" value=\"1\" />
                            <label for=\"dat_all_day\">Ganztägig</label>
                        </span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for=\"date_to\">Ende:</label></dt>
                    <dd>
                        <span>
                            <input type=\"text\" id=\"date_to\" name=\"date_to\" size=\"10\" maxlength=\"10\" value=\"$date_to\" />
                            <img id=\"ico_cal_date_to\" src=\"". THEME_PATH. "/icons/calendar.png\" onclick=\"javascript:calPopUp.select(document.forms[0].date_to,'ico_cal_date_to','dd.MM.yyyy','date_from','date_to','time_from','time_to');\" style=\"vertical-align:middle; cursor:pointer;\" alt=\"Kalender anzeigen\" title=\"Kalender anzeigen\" />
                        </span>
                        <span style=\"margin-left: 10px;\">
                            <input type=\"text\" id=\"time_to\" name=\"time_to\" size=\"5\" maxlength=\"5\" value=\"$time_to\" />
                            <span class=\"mandatoryFieldMarker\" title=\"Pflichtfeld\">*</span>
                        </span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for=\"dat_cat_id\">Kalender:</label></dt>
                    <dd>
                        <select id=\"dat_cat_id\" name=\"dat_cat_id\" size=\"1\" tabindex=\"3\">
                            <option value=\" \"";
                           if($date->getValue("dat_cat_id") == 0)
                           {
                              echo " selected=\"selected\"";
                           }
                              echo ">- Bitte wählen -</option>";

                            $sql = "SELECT * FROM ". TBL_CATEGORIES. "
                                     WHERE cat_org_id = ". $g_current_organization->getValue("org_id"). "
                                       AND cat_type   = 'DAT'
                                     ORDER BY cat_sequence ASC ";
                            $result = $g_db->query($sql);

                            while($row = $g_db->fetch_object($result))
                            {
                                echo "<option value=\"$row->cat_id\"";
                                    if($date->getValue("dat_cat_id") == $row->cat_id)
                                    {
                                        echo " selected=\"selected\" ";
                                    }
                                echo ">$row->cat_name</option>";
                            }
                        echo "</select>
                        <span class=\"mandatoryFieldMarker\" title=\"Pflichtfeld\">*</span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for=\"dat_location\">Ort:</label></dt>
                    <dd>
                        <input type=\"text\" id=\"dat_location\" name=\"dat_location\" style=\"width: 345px;\" maxlength=\"50\" value=\"". $date->getValue("dat_location"). "\" />";
                        if($g_preferences['dates_show_map_link'])
                        {
                            echo "<img class=\"iconHelpLink\" src=\"". THEME_PATH. "/icons/help.png\" alt=\"Hilfe\" title=\"\" onclick=\"window.open('$g_root_path/adm_program/system/msg_window.php?err_code=date_location_link&amp;window=true','Message','width=300,height=180,left=310,top=200,scrollbars=yes')\" onmouseover=\"ajax_showTooltip(event,'$g_root_path/adm_program/system/msg_window.php?err_code=date_location_link',this);\" onmouseout=\"ajax_hideTooltip()\" />";
                        }
                    echo "</dd>
                </dl>
            </li>";
            if($g_preferences['dates_show_map_link'])
            {
                if(strlen($date->getValue("dat_country")) == 0)
                {
                    $date->setValue("dat_country", $g_preferences['default_country']);
                }
                echo '<li>
                    <dl>
                        <dt>&nbsp;</dt>
                        <dd>
                            <select size="1" id="dat_country" name="dat_country">';
                                // Datei mit Laenderliste oeffnen und alle Laender einlesen
                                $country_list = fopen("../../system/staaten.txt", "r");
                                $country = trim(fgets($country_list));
                                while (!feof($country_list))
                                {
                                    echo '<option value="'.$country.'"';
                                    if($country == $date->getValue("dat_country"))
                                    {
                                        echo ' selected="selected" ';
                                    }
                                    echo '>'.$country.'</option>';
                                    $country = trim(fgets($country_list));
                                }
                                fclose($country_list);
                            echo '</select>
                        </dd>
                    </dl>
                </li>';
            }

            if ($g_preferences['enable_bbcode'] == 1)
            {
                echo "
                <li>
                    <dl>
                        <dt>&nbsp;</dt>
                        <dd>
                            <div style=\"width: 350px;\">
                                <div style=\"float: left;\">
                                    <a class=\"iconLink\" href=\"javascript:bbcode(0);\"><img id=\"b\"
                                        src=\"". THEME_PATH. "/icons/text_bold.png\" title=\"Fett schreiben\" alt=\"Fett schreiben\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:bbcode(1);\"><img id=\"u\"
                                        src=\"". THEME_PATH. "/icons/text_underline.png\" title=\"Text unterstreichen\" alt=\"Text unterstreichen\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:bbcode(2);\"><img id=\"i\"
                                        src=\"". THEME_PATH. "/icons/text_italic.png\" title=\"Kursiv schreiben\" alt=\"Kursiv schreiben\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:bbcode(3);\"><img id=\"big\"
                                        src=\"". THEME_PATH. "/icons/text_bigger.png\" title=\"Größer schreiben\" alt=\"Größer schreiben\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:bbcode(4);\"><img id=\"small\"
                                        src=\"". THEME_PATH. "/icons/text_smaller.png\" title=\"Kleiner schreiben\" alt=\"Kleiner schreiben\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:bbcode(5);\"><img id=\"center\"
                                        src=\"". THEME_PATH. "/icons/text_align_center.png\" title=\"Text zentrieren\" alt=\"Text zentrieren\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon('[url=http://www.admidio.org]Linktext[/url]')\"><img id=\"url\"
                                        src=\"". THEME_PATH. "/icons/link.png\" title=\"Link einfügen\" alt=\"Link einfügen\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon('[email=name@admidio.org]Linktext[/email]')\"><img id=\"email\"
                                        src=\"". THEME_PATH. "/icons/email.png\" title=\"E-Mail-Adresse einfügen\" alt=\"E-Mail-Adresse einfügen\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon('[img]http://www.admidio.org/images/admidio_small.png[/img]');\"><img id=\"img\"
                                        src=\"". THEME_PATH. "/icons/image.png\" title=\"Bild einfügen\" alt=\"Bild einfügen\" /></a>
                                </div>
                                <div style=\"float: right;\">
                                    <a class=\"iconLink\" href=\"javascript:bbcodeclose();\"><img id=\"all-closed\"
                                        src=\"". THEME_PATH. "/icons/delete.png\" title=\"Alle Tags schließen\" alt=\"Alle Tags schließen\" /></a>
                                    <img class=\"iconLink\" src=\"". THEME_PATH. "/icons/help.png\"
                                        onclick=\"javascript:window.open('$g_root_path/adm_program/system/msg_window.php?err_code=bbcode&amp;window=true','Message','width=600,height=500,left=310,top=200,scrollbars=yes');\"
                                        onmouseover=\"ajax_showTooltip(event,'$g_root_path/adm_program/system/msg_window.php?err_code=bbcode',this);\"
                                        onmouseout=\"ajax_hideTooltip()\" alt=\"Hilfe\" title=\"\" />
                                </div>
                            </div>
                        </dd>
                    </dl>
                </li>";
            }
            echo "
            <li>
                <dl>
                    <dt><label for=\"dat_description\">Text:</label>";
                        if($g_preferences['enable_bbcode'] == 1)
                        {
                             echo "<br /><br />&nbsp;&nbsp;
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':)');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_smile.png\" alt=\"Smile\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon(';)');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_wink.png\" alt=\"Wink\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':D');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_grin.png\" alt=\"Grin\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':lol:');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_happy.png\" alt=\"Happy\" /></a>
                                    <br />&nbsp;&nbsp;
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':(');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_unhappy.png\" alt=\"Unhappy\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':p');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_tongue.png\" alt=\"Tongue\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':o');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_surprised.png\" alt=\"Surprised\" /></a>
                                    <a class=\"iconLink\" href=\"javascript:emoticon(':twisted:');\"><img
                                        src=\"". THEME_PATH. "/icons/smilies/emoticon_evilgrin.png\" alt=\"Evilgrin\" /></a>";
                        }
                    echo "</dt>
                    <dd>
                        <textarea id=\"dat_description\" name=\"dat_description\" style=\"width: 345px;\" rows=\"10\" cols=\"40\">". $date->getValue("dat_description"). "</textarea>
                        <span class=\"mandatoryFieldMarker\" title=\"Pflichtfeld\">*</span>
                    </dd>
                </dl>
            </li>
        </ul>

        <hr />

        <div class=\"formSubmit\">
            <button name=\"speichern\" type=\"submit\" value=\"speichern\"><img src=\"". THEME_PATH. "/icons/disk.png\" alt=\"Speichern\" />&nbsp;Speichern</button>
        </div>
    </div>
</div>
</form>

<ul class=\"iconTextLinkList\">
    <li>
        <span class=\"iconTextLink\">
            <a href=\"$g_root_path/adm_program/system/back.php\"><img
            src=\"". THEME_PATH. "/icons/back.png\" alt=\"Zurück\" title=\"Zurück\"/></a>
            <a href=\"$g_root_path/adm_program/system/back.php\">Zurück</a>
        </span>
    </li>
</ul>

<script type=\"text/javascript\">
    document.getElementById('dat_headline').focus();
    setAllDay();
</script>";

require(THEME_SERVER_PATH. "/overall_footer.php");

?>