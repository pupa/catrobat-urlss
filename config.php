<?php
/* 
 * This is the config file for the catrobat URL shortening service.
 * Edit this file with the correct settings and save it on your server.
 */

/*
 * PostgreSQL settings
 */
 
 	// PostgreSQL DB hostname
 	$host = "localhost";
	
	// PostgreSQL DB portnumber
	$port = 5432;
	
	// The name of the database for the Catrobat URL shortening service
	$database = "CatrobatURLSS";
	
	// PostgreSQL DB user name
	$user = "catrobat";
	
	// PostgreSQL DB password
	$password = "catrobat123";
    
/*
 * Webservice settings
 */
     
    // location of the WSDL-File
    $wsdl = "http://localhost/~Bernhard/WebService.wsdl";
    
    // Client options
    $client_options = array("trace" => "1");
	 
/*
 * Site options
 */
	
	/*
	 * Maybe we will also define username(s) and password(s) here 
	 * allowed to access the admin site ie:
	 */
	 $user_password = array(
	 	"admin" => "password",
		"username" => "passme"   // you can have more one ore more 'login' => 'password' lines
		);
?>