<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
$username=$_SESSION['loggedin_username'];
$password=$_SESSION['loggedin_password'];
$isloggedin=$_SESSION['isloggedin'];

if((!$isloggedin) or ($password=="")){
        header("Location: ..");
        exit;// this is only exit to redirect to loginform, when not logged in.
};

phpinfo();