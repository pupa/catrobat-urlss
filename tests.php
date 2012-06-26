<?php
    require "config.php";
    require "DBFunctions.php";
    require "functions_statistics.php";
    
    
    /*
     * DB Tests
     */    
    echo "run DATABASE TESTS: <br /><br />";
    
    $db_test = new DBFunctions();
    
    // check functionality of deleteDB
    $db_test->deleteDB();
    
    // open connection to DB
    $connection = pg_connect("host=" .$host. " port=" .$port. " dbname=" .$database. " user=" .$user. " password=" .$password) 
            or die("DB ERROR: connect failed!");     
    
    $query = "SELECT count(table_name) FROM information_schema.tables WHERE table_schema = 'public'";
    $result = pg_query($query) or die ("Executing query failed: " .pg_last_error());
    
    $line = pg_fetch_row($result);
    if ($line[0] == 0) {
        echo "delete DB: successful <br />";
    } else {
        echo "delete DB: failed <br />";
    }
    
    // close connection to DB
    pg_close($connection);    
    
    // check functionality of installDB
    $db_test->installDB();

    // open connection to DB
    $connection = pg_connect("host=" .$host. " port=" .$port. " dbname=" .$database. " user=" .$user. " password=" .$password) 
            or die("DB ERROR: connect failed!");     
    
    $query = "SELECT count(table_name) FROM information_schema.tables WHERE table_schema = 'public'";
    $result = pg_query($query) or die ("Executing query failed: " .pg_last_error());    
    
    $line = pg_fetch_row($result);
    if ($line[0] == 6) {
        echo "create DB: successful <br />";
    } else {
        echo "create DB: failed <br />";
    }
    
    // close connection to DB
    pg_close($connection);      
    
    // check functionality of fillDB
    if ($db_test->fillDB()) {
        echo "fill DB: successful <br />";
    } else {
        echo "fill DB: failed <br />";
    }
      
    /*
     * WebService Tests
     */
    echo "<br />";
    echo "------------------------------------------------------------------------------------- <br /><br />";
    echo "run WEBSERVICE TESTS: <br /><br />";

    /*
     * generate a test client to proof if the configurations of
     * our webservice works correctly
     */
    ini_set('soap.wsdl_cache_enabled', '0');   // don't use wsdl-files in cache
    ini_set('soap.wsdl_cache_ttl', '0');
        
    $soap_client = new SoapClient($wsdl, $client_options);
    try {
        // 1st case: invalid url
        echo "Test invalid URL: <br />";
        echo "expected result: ERROR: Invalid URL: Use format http://... or https://... ! <br />";
        echo "result: " .$soap_client->getURL("yourls.com") . "<br />";
        echo "------------------------------------------------------------------------------------- <br />";
        
        // 2nd case: valid url -> doesn't exist in our DB
        echo "Test insert new URL: <br />";
        echo "expected result: 0005 (= new shorturl for current test data) <br />";
        echo "result: " .$soap_client->getURL("https://online.tugraz.at/tug_online/webnav.ini") . "<br />";
        echo "------------------------------------------------------------------------------------- <br />";
        
        // 3rd case: valid url -> exists in our DB
        echo "Test URL already exists: <br />";
        echo "expected result: 0003 (= existing shorturl to this longurl) <br />";        
        echo "result: " .$soap_client->getURL("http://yourls.com") . "<br />";
    }
    catch (SoapFault $sf) {
        print_r($sf);
    }
    
    /*
     * AdminPage Tests
     */
    echo "<br />";
    echo "------------------------------------------------------------------------------------- <br /><br />";
    echo "run ADMIN-PAGE TESTS: <br /><br />";
?>