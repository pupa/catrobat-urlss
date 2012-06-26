<?php
session_start();
require "config.php";

$username = $_POST["username"];
$password = $_POST["password"];
$link1 = "login.php";
$log = 0;


if($password == $user_password[$username])
  $log = 1;


if($log == 1)
{
  $_SESSION["username"] ="user";
  //$_SESSION["password"];
  header("location:admin.php");
}

if($log == 0)
{
  header("location:loginerror.php");
}

?>