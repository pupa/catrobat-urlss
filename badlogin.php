<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
  session_start();
  require "config.php";

  $username = $_POST["username"];
  $password = $_POST["password"];
  $log = 0;


  if($password == $user_password[$username])
  {
    $log = 1;
  }
  if($username == NULL || $password == NULL)
  {
	$log = 0;  
  }


  if($log == 1)
  {
    $_SESSION['login'] = true;
    header("location:admin.php");
	exit;
  }

  if($log == 0)
  {
    header("location:badlogin.php");
	exit;
  }
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Admin Login</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
<h1>Admin Login</h1>
<h4>Wrong Username or Password.</h4>
<form method="POST" action="index.php">
<table border="0" cellpadding="5">
  <tr>
    <td>Username: </td>
    <td><input type="text" name="username" id="username" size="20"></td>
  </tr>
  <tr>
    <td>Password: </td>
    <td><input type="password" name="password" id="password" size="20"></td>
  </tr>
  <tr>
    <td><input type="submit" value="Login"></td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
</div>

</body>
</html>