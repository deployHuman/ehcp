<?php

$ua=getenv("HTTP_USER_AGENT");
$path=getenv("PATH");

if(strstr($path,"WINNT")or strstr($ua,'testediliyor')or strstr($path,'WINDOWS')) $ortam="test";

else $ortam="gercek";


$confdir=dirname(__FILE__)."/";
$root=$confdir."../";

ini_set("display_errors","1");
include_once($confdir."dbconf.php");
include_once($confdir."dbutil.php");

$ip = getenv ("REMOTE_ADDR");

$baglanti=mysql_connect("localhost", "$mysqlkullaniciadi", "$mysqlsifre");
if(!$baglanti){echo mysql_error();die ("<br>(dbbaglan)Baglanilamadi-confdir: $confdir, username: $mysqlkullaniciadi,  cagirandosya:".$_SERVER['PHP_SELF']);};



?>
