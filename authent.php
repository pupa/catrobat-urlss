<?php
     session_start();

     if (!isset($_SESSION['login']) || !$_SESSION['login']) 
	 {
      header("location:index.php");
      exit;
     }
?>