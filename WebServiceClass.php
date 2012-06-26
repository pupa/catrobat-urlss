<?php
    /*
     * This class contains all the functions which are needed for
     * our URL Shortening Service
     */
    class WebServiceClass {        
        /*
         * Desc.: this is the public function of our webservice
         *        you can generate a new shorturl related to a longurl with it
         * @param string longurl: contains the url which should be shortened
         * @return string: contains the new shorturl
         */
        public function getURL($longurl) {
            require "config.php";
            require "functions_webservice.php";
            
            $shorturl = "";
            
            // check if longurl is valid
            if (checkURLvalid($longurl)) {
                // open connection to DB
                $connection = pg_connect("host=" .$host. " port=" .$port. " dbname=" .$database. " user=" .$user. " password=" .$password) 
                    or die("DB ERROR: connect failed!");
            
                $query = "SELECT shorturl FROM url WHERE longurl LIKE '" .$longurl. "'";
                $result = pg_query($query) or die ("Executing query failed: " .pg_last_error());
                
                if (pg_num_rows($result) == 0) {
                    // there is no entry for this longurl in our database
                    // so we generate a new shorturl
                    $shorturl = getShortURL($longurl);
                } else {
                    // this longurl already exists in our database
                    // return the related shorturl
                    $line = pg_fetch_row($result, NULL, PGSQL_ASSOC);
                    $shorturl = $line["shorturl"];
                }
                
                // close connection to DB
                pg_close($connection);
                
                return $shorturl;
            } else {
                $shorturl = "ERROR: Invalid URL: Use format http://... or https://... !";
            }
            
            return $shorturl;
        }
    }
?>