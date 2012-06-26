<?php 
    ini_set('soap.wsdl_cache_enabled', '0'); 
    ini_set('soap.wsdl_cache_ttl', '0');
    
    require "config.php";
    require "WebServiceClass.php";
    
    $soap_server = new SoapServer($wsdl);
    $soap_server->setClass("WebServiceClass");
    $soap_server->handle();
?>