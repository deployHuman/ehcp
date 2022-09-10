#!/usr/bin/env php
<?php

include_once("config/dbutil.php"); # app should be removed later... 
include_once("adodb5/adodb.inc.php"); # adodb database abstraction layer.. hope database abstracted...
include_once("classapp.php"); # real application class


$app = new Application();
$app->cerceve="standartcerceve";
$app->connecttodb();


// $app->addDaemonOp('fixApacheConfigNonSsl2','','','','fixApacheConfigNonSsl2');
$q="update misc set value='nginx' where name='webservertype'";
$app->executeQuery($q);
$app->loadConfig();
$app->addDaemonOp('rebuild_webserver_configs','','','','rebuild_webserver_configs');
$app->addDaemonOp('syncdomains','','','','sync domains');
$app->addDaemonOp('syncftp','','','','sync ftp for nonstandard homes');
$app->addDaemonOp('syncdns','','','','sync dns');
// $app->addDaemonOp('syncapacheauth','','','','sync apache auth');
$app->showoutput();

?>
