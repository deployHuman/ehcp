#!/usr/bin/env php
<?php

sleep(15); # let init scripts load
include_once("config/dbutil.php"); # app should be removed later... 
include_once("config/adodb/adodb.inc.php"); # adodb database abstraction layer.. hope database abstracted...
include_once("classapp.php"); # real application class


$app = new Application();
$app->cerceve="standartcerceve";
$app->connecttodb();

$app->addDaemonOp('fixMailConfiguration','','','','fixMailConfiguration');
$app->showoutput();
?>
