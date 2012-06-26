<?php
    /*
     * Description: ...
     */
     
    class DBFunctions {
        /*
         * Desc.: delete all tables (including the content) in our DB
         */            
        function deleteDB () {
            require "config.php";
            
            $error_msg = array();
    
            // open connection to DB
            $connection = pg_connect("host=" .$host. " port=" .$port. " dbname=" .$database. " user=" .$user. " password=" .$password) 
                or die("DB ERROR: connect failed!");
        
            // DROP TABLE query
            $drop_tables = array();
            $drop_tables["LOG"] = "DROP TABLE IF EXISTS log";
            $drop_tables["URL"] = "DROP TABLE IF EXISTS url";
            $drop_tables["REFERRER"] = "DROP TABLE IF EXISTS referrer";
            $drop_tables["PLATFORM"] = "DROP TABLE IF EXISTS platform";
            $drop_tables["BROWSER"] = "DROP TABLE IF EXISTS browser";
            
            // for USER INFORMATIONS
            $drop_tables["ip2nationcountries"] = "DROP TABLE IF EXISTS ip2nationcountries";
            $drop_tables["ip2nation"] = "DROP TABLE IF EXISTS ip2nation";
    
            foreach ($drop_tables as $table_name => $query) {
                $result = pg_query($query);
                if (!$result) {
                    $error_msg[] = "Drop table " .$table_name. " failed: " .pg_last_error() . "<br />";
                }
            }
        
            // close connection to DB
            pg_close($connection);
            
            // check results of operations
            if ($error_msg != NULL) {
                print_r($error_msg);
            }
        }

        /*
         * Desc.: create all tables of our DB
         */
        function installDB () {
            require "config.php";
            
            $error_msg = array();
    
            // open connection to DB
            $connection = pg_connect("host=" .$host. " port=" .$port. " dbname=" .$database. " user=" .$user. " password=" .$password) 
                or die("DB ERROR: connect failed!");
    
            // CREATE TABLE query
            $create_tables = array();
            $create_tables["URL"] =
                "CREATE TABLE IF NOT EXISTS url(
                    url_id serial PRIMARY KEY,
                    longurl varchar(200) NOT NULL,
                    shorturl varchar(40),
                    title varchar(200),
                    datetime timestamp DEFAULT CURRENT_TIMESTAMP(0),
                    click_count integer DEFAULT 0)";
    
            $create_tables["REFERRER"] = 
                "CREATE TABLE IF NOT EXISTS referrer(
                    referrer_id serial PRIMARY KEY,
                    url varchar(200) NOT NULL,
                    host varchar(50) NOT NULL)";
            
            $create_tables["PLATFORM"] = 
                "CREATE TABLE IF NOT EXISTS platform(
                    platform_id serial PRIMARY KEY,
                    platform varchar(50) NOT NULL)";
            
            $create_tables["BROWSER"] = 
                "CREATE TABLE IF NOT EXISTS browser(
                    browser_id serial PRIMARY KEY,
                    browser varchar(50) NOT NULL)";
            
            $create_tables["LOG"] = 
                "CREATE TABLE IF NOT EXISTS log(
                    log_id serial PRIMARY KEY,
                    url_id integer REFERENCES URL (url_id),
                    clicktime timestamp DEFAULT CURRENT_TIMESTAMP,
                    referrer_id integer REFERENCES REFERRER (referrer_id),
                    platform_id integer REFERENCES PLATFORM (platform_id),
                    browser_id integer REFERENCES BROWSER (browser_id),
                    country_code char(2))";
            
            /*
             * data from ip2nation
             */
            $create_tables["ip2nation"] = 
                "CREATE TABLE IF NOT EXISTS ip2nation (
                    ip bigint PRIMARY KEY,
                    country char(2) NOT NULL DEFAULT '')";
    
            // execute create-queries
            foreach ($create_tables as $table_name => $query) {
                $result = pg_query($query);
                if (!$result) {
                    $error_msg[] = "Error creating table " .$table_name. "." .pg_last_error(). "<br />";
                }
            }
            
            // close connection to DB
            pg_close($connection);            
    
            // check results of operations
            if ($error_msg != NULL) {
                print_r($error_msg);
            }
        }

        /*
         * Desc.: insert some test data in our DB
         * @return boolean: TRUE if all inserts were successful, otherwise FALSE
         */
        function fillDB () {
            require "config.php";
            
            $error_msg = array();
            
            // open connection to DB
            $connection = pg_connect("host=" .$host. " port=" .$port. " dbname=" .$database. " user=" .$user. " password=" .$password) 
                or die("DB ERROR: connect failed!"); 
                
            // INSERT TABLE query                
            $insert_tables = array();
            $insert_tables["URL"] = 
                "INSERT INTO url (longurl, shorturl, title, datetime, click_count) VALUES
                    ('http://www.php.net/manual/de/pgsql.examples-basic.php', '0001', 'PHP: Grundlegende Nutzung - Manual', '2012-05-20 08:07:13', 2),
                    ('http://www.php-einfach.de/codeschnipsel_8566.php', '0002', 'PHP-Einfach.de - Herkunft einer IP-Adresse ermitteln. (IP to Country)', '2012-04-20 10:12:43', 10),
                    ('http://yourls.com', '0003', 'YOURLS: Your Own URL Shortener', '2012-04-28 20:34:09', 3),
                    ('https://www.facebook.com', '0004', 'Willkommen bei Facebook - anmelden, registrieren oder mehr erfahren', '2012-05-28 15:29:41', 1)";
           
            $insert_tables["REFERRER"] = 
                "INSERT INTO referrer (url, host) VALUES
                    ('http://twitter.com/', 'twitter.com'),
                    ('http://twitter.com/ArminWolf', 'twitter.com'),
                    ('http://google.com/search', 'google.com')";
                
            $insert_tables["PLATFORM"] = 
                "INSERT INTO platform (platform) VALUES
                    ('Windwos XP'),
                    ('Windows Vista'),
                    ('Windows 7'),
                    ('Mac OS'),
                    ('Linux')";
        
            $insert_tables["BROWSER"] =
                "INSERT INTO browser (browser) VALUES
                    ('Internet Explorer'),
                    ('Mozilla Firefox'),
                    ('Opera'),
                    ('Safari'),
                    ('Google Chrome')";
        
            $insert_tables["LOG"] = 
                "INSERT INTO log (url_id, clicktime, referrer_id, platform_id, browser_id, country_code) VALUES
                    (1, '2012-05-20 10:15:23', 1, 3, 1, 'AT'),
                    (1, '2012-05-20 18:09:13', 2, 4, 4, 'AT'),
                    (1, '2012-06-01 10:33:29', 1, 4, 2, 'CH'),
                    (1, '2012-06-19 08:26:19', 3, 4, 4, 'DE'),
                    (1, '2012-06-24 19:22:01', 3, 2, 1, 'AT'),
                    (1, '2012-06-25 10:24:12', 2, 1, 1, 'DE'),
                    (1, '2012-06-25 14:47:20', 3, 1, 1, 'FR'),
                    (1, '2012-06-25 14:48:28', 1, 2, 3, 'CH'),
                    (1, '2012-06-25 14:50:42', 2, 1, 1, 'AT'),
                    (2, '2012-04-23 10:15:23', 1, 5, 3, 'DE'),
                    (2, '2012-04-25 10:15:23', 3, 1, 2, 'SE'),
                    (2, '2012-04-25 20:15:23', 3, 2, 1, 'AT'),
                    (2, '2012-05-03 10:15:23', 3, 4, 5, 'AT'),
                    (2, '2012-05-25 09:17:23', 1, 4, 4, 'GB'),
                    (2, '2012-06-09 10:53:01', 3, 1, 1, 'SE'),
                    (2, '2012-06-17 23:11:11', 2, 3, 1, 'AT'),
                    (2, '2012-06-19 08:04:43', 1, 3, 2, 'AT'),
                    (2, '2012-06-19 15:43:52', 1, 4, 4, 'AT'),
                    (2, '2012-06-19 22:18:05', 3, 4, 5, 'DE'),
                    (2, '2012-06-21 19:08:59', 1, 2, 2, 'IT'),
                    (2, '2012-06-23 18:38:29', 3, 1, 2, 'IT'),
                    (2, '2012-06-24 06:10:29', 2, 2, 1, 'AT'),
                    (3, '2012-04-29 17:15:47', 1, 3, 1, 'AT'),
                    (3, '2012-05-09 20:25:03', 3, 1, 5, 'DE'),
                    (3, '2012-05-20 10:15:23', 3, 4, 4, 'AT'),
                    (3, '2012-06-01 00:10:20', 2, 3, 1, 'CH'),
                    (3, '2012-06-22 17:14:55', 1, 2, 3, 'AT'),
                    (3, '2012-06-23 19:50:50', 2, 1, 1, 'IT'),
                    (3, '2012-06-24 17:14:55', 1, 2, 3, 'AT'),
                    (3, '2012-06-25 14:50:50', 2, 1, 1, 'IT'),
                    (4, '2012-06-01 10:15:23', 3, 4, 5, 'GB')";
                
            // execute insert-queries
            foreach ($insert_tables as $table_name => $query) {
                $result = pg_query($query);
                if (!$result) {
                    $error_msg[] = "Inserts into table " .$table_name. " failed: " .pg_last_error(). "<br />";
                }
            }
                                
            // close connection to DB
            pg_close($connection);     
            
            // check results of operations
            if ($error_msg != NULL) {
                print_r($error_msg);
                return FALSE;
            } else {
                return TRUE;
            }    
        }
    }
?>