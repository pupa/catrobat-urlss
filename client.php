<?php
    require "config.php";
    ini_set('soap.wsdl_cache_enabled', '0'); 
    ini_set('soap.wsdl_cache_ttl', '0');
    
    /*
     * Description:
     * This is a short test file to proof if the configurations of our
     * webservice works correctly
     */
    
    $soap_client = new SoapClient($wsdl, $client_options);
    try {
        echo $soap_client->getURL("yourls.com");
    }
    catch (SoapFault $sf) {
        print_r($sf);
    }
?>