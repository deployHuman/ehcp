<?php
/*

EASY HOSTING CONTROL PANEL MAIN index.php FILE Version 0.30 - www.ehcp.net
IF YOU SEE THIS ON BROWSER,  IMMADIATELY STOP WEBSERVER with /etc/init.d/apache2 stop, otherwise, your passwords may be seen by others...
IF YOU SEE THIS INSTEAD OF A WEB PAGE, THEN YOU PROBABLY DIDN'T INSTALL PHP EXTENSION, PLEASE RE-RUN EHCP INSTALL SCRIPT OR MANUALLY INSTALL APACHE2-PHP EXTENSION..

*
by Ehcp Developer
mail&msn: info@ehcp.net

see classapp.php for real application.
*/


include_once("config/dbutil.php"); # this should be removed later...
include_once("classapp.php"); # real application class

$app = new Application();
$app->run();

?>
