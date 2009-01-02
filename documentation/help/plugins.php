
<?php include("help_menu.php"); ?>

<td style="background-color: #ffffff; padding-left: 15px;">
    <h2>Der Unterschied zwischen Plugins und Modulen:</h2>

    <p>Plugins sind kleine Php-Scripte f&uuml;r bestimmte Aufgaben (z.B. n&auml;chsten 2 Termine anzeigen, 
    aktuelle Geburtstage anzeigen ...). Sie m&uuml;ssen im Gegensatz zu Modulen aber nicht unbedingt als 
    eigene Seite aufgerufen werden, sondern k&ouml;nnen in jede beliebige Html- oder Php-Seite eingebunden 
    werden. Sie k&ouml;nnen die Plugins auch in der body_top.php oder body_bottom.php von Admidio 
    einbauen, so dass bestimmte Informationen auf allen Admidio-Seiten angezeigt werden.</p>
    
    <h2>Wie baue ich Plugins ein ?</h2>
    
    <p><a href="http://forum.admidio.org/viewforum.php?f=9">Laden Sie das Plugin von unserer Homepage herunter</a> und entpacken 
    Sie die Zip-Datei. Kopieren Sie das Verzeichnis mit den Dateien in den Ordner <strong>adm_plugins</strong> in Ihrem 
    Admidio-Ordner. Falls der Ordner adm_plugins noch nicht existiert, m&uuml;ssen Sie ihn zuerst noch erstellen. 
    Dieser sollte sich auf derselben Ebene wie <i>adm_my_files</i> befinden.</p>
    
    <p>Plugins sind meistens in Php geschrieben und sollen ihren Inhalt in eine bestehende Html- oder 
    Php-Seite einbinden.</p>
    
    <p>Falls Sie das Plugin nun in eine reine Html-Seite einbinden wollen, sollten Sie die Dateierweiterung
    der Seite zuerst von <i>html</i> in <i>php</i> umbenennen. Nun k&ouml;nnen Sie das Modul mit Hilfe von Php
    in Ihre ehemals reine Html-Seite integrieren. F&uuml;gen Sie nun das Plugin mit einer der folgenden Methoden
    in Ihren Html-Code an der Stelle ein, an der die Ausgabe des Plugins dargestellt werden soll:</p>
    
    <p><strong>1.</strong> Benutzen Sie das Plugin <strong>nur</strong> innerhalb von Admidio-Seiten, so reicht ein einfacher Aufruf von:
    
    <p class="code">&lt;?php<br />
    include(SERVER_PATH. "/adm_plugins/sidebar_dates/sidebar_dates.php");<br />
    ?&gt;</p>
    
    <p><strong>2.</strong> Wird das Plugin auf Seiten benutzt, die nichts mit Admidio zu tun haben, so muss vorher noch die <i>common.php</i>
    <strong>ganz am Anfang</strong> Ihrer Php-Datei &uuml;ber den Serverpfad eingebunden werden:</p>
    
    <p class="code">&lt;?php<br />
    include_once($_SERVER['DOCUMENT_ROOT']. "/<i>Pfad-zu-Admidio-Ordner</i>
    /adm_program/system/common.php");<br />
    ?&gt;</p>
    
    <p>Danach folgt, wie bei Punkt 1, der Einbau der Plugins an den von Ihnen gewünschten Stellen:</p>
    
    <p class="code">&lt;?php<br />
    include($_SERVER['DOCUMENT_ROOT']. "/<i>Pfad-zu-Admidio-Ordner</i>
    /adm_plugins/sidebar_dates/sidebar_dates.php");<br />
    ?&gt;</p>
    
    <p><strong>3.</strong> Haben die vorherigen Varianten nicht zum Erfolg gef&uuml;hrt kann man die Plugins auch direkt 
    &uuml;ber eine URL einbinden. Allerdings wird bei dieser Variante die common.php f&uuml;r jedes Plugin wiederholt
    abgearbeitet, was der Performance des Webservers nicht gerade dienlich ist.</p>
    
    <p class="code">&lt;?php<br />
        include("http://<i>www.meine-domain.de/Pfad-zu-Admidio</i>
        /adm_plugins/sidebar_dates/sidebar_dates.php");<br />
    ?&gt;</p>
    
    <h2>Wo kann ich Plugins konfigurieren ?</h2>
    
    <p>Konfigurieren k&ouml;nnen Sie die meisten Plugins &uuml;ber eine <strong>config.php</strong> Datei, die sich im selben Ordner
    wie das Plugin befindet. Hier stehen Ihnen einige Variablen mit Default-Werten zur Verf&uuml;gung. Diese k&ouml;nnen
    Sie Ihren Bed&uuml;rfnissen anpassen.</p>
    
    <p>Bei einem Update des Plugins brauchen und sollen Sie diese Datei nicht 
    &uuml;berschreiben. Sind durch das Update neue Einstellungen hinzugekommen, so m&uuml;ssen Sie die neuen Variablen aus
    der heruntergeladenen config.php in Ihre bisherige config.php des Plugins kopieren und den gew&uuml;nschten Wert einsetzen.</p>
    
    <p>Manche Plugins besitzen noch eine <strong>CSS-Datei</strong>. Über diese Datei können Sie das Aussehen und 
    teilweise die Anordnung bestimmter Bereiche eines Plugins anpassen. Diese CSS-Datei müssen Sie in den &lt;head&gt;-Bereich
    der PHP-Seiten integrieren, auf denen das Plugin zu sehen ist.</p>
    
    <p class="code">&lt;link rel="stylesheet" type="text/css" href="http://<i>Pfad-zu-Admidio</i>/adm_plugins/birthday/birthday.css"&gt;</p>
    
    <p>Ein Beispiel f&uuml;r eine Integration von mehreren Plugins in einer Seitenleiste sehen Sie auf diesem 
    Screenshot im hervorgehobenen Bereich:</p>
    
    <a style="target-new: tab;" href="help/images/screenshots/plugins_md.png"><img
        style="border: 0px;" src="help/images/screenshots/plugins_md_thumb.png" alt="Beispiel f&uuml;r Plugins"
    title="Beispiel f&uuml;r Plugins" /></a>

   <br /><br />

   <div style="text-align: left; float: left;">
      <a href="index.php?help/layout.php"><img src="help/images/icons/back.png" style="vertical-align: bottom; border: 0px;" alt="Admidio dem eigenen Layout anpassen" title="Admidio dem eigenen Layout anpassen" /></a>
   </div>
   <div style="text-align: right;">
      <strong>N&auml;chster Artikel:</strong>&nbsp;
      <a href="index.php?help/rollen.php">Rollen anlegen und pflegen</a>&nbsp;
      <a href="index.php?help/rollen.php"><img src="help/images/icons/forward.png" style="vertical-align: bottom; border: 0px;" alt="Rollen anlegen und pflegen" title="Rollen anlegen und pflegen" /></a>
   </div>
</td>