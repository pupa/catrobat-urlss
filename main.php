<?php
    include 'config.php';
    
    /////////////////////////////////////////////////////////////////////////////////////
    // URL VALIDATION!!!!!!!!!! FERTIG!!!
    //////////////////////////////////////
    /*
    *  Checks the url address
    *
    *  @param $url_new: the new url address
    *  
    *  @return true if the url is valid, false if the url is invalid
    */
    function checking_url_valid( $url_new )
    {
	  if (substr($url_new, 0, 7) == "http://" or substr($url_new, 0, 8) == "https://")
	  {
	    return true;
	  }
	  
	  // print error message
	  header("location:error.php?error=invalid_url");
      exit;
      
      return false;
    }
    /////////////////////////////////////////////////////////////////////////////////////
    
    
    /////////////////////////////////////////////////////////////////////////////////////
    // URL DUPLIKAT ÜBERPRÜFUNG!!!!!!!!!
    ///////////////////////////////////
    /*
    *  Checks if the url exists in database
    *
    *  @param $url_new: the new url address
    *  
    *  @return the old or the new shorturl
    */   
    function checking_url_db( $url_new )
    {
      include 'config.php';
      
      // open connection to database
      $connection = pg_connect('host=' .$host. ' port=' .$port. ' dbname=' .$database. ' user=' .$user. ' password=' .$password) 
          or die("DB ERROR: connect failed!");
        
      $query = "SELECT longurl FROM URL WHERE longurl = '" .$url_new. "'";  
        
      // execute a SQL query for URL
      $result = pg_query($query) or die('Executing query failed: ' .pg_last_error());   
      $long_url = pg_fetch_all_columns($result, 0);
      
      if (count($long_url) == 1)
      {
        // Getting the short url
        $query = 'SELECT shorturl FROM URL WHERE url_id = ' .($counter + 1);
        $result = pg_query($query) or die('Executing query failed: ' .pg_last_error());
        $short_url = pg_fetch_all_columns($result, 0);          
        
        //close connection to DB
        pg_close($connection);
           
        return $short_url[0];      
      }
      else
      {
        // generate new short url
        $new_short_url = url_shorten( $url_new );
      
        //close connection to DB
        pg_close($connection);    
      
        return $new_short_url;        
      }

    }
    /////////////////////////////////////////////////////////////////////////////////////


    /////////////////////////////////////////////////////////////////////////////////////
    // URL SHORTEN VORBEREITEN!!!!!
    //////////////////////
    /*
    *  Shortening an url address and adding it to database
    *
    *  @param $url_new: the new url address
    *  
    *  @return the new shorturl
    */    
    function url_shorten( $url_new )
    { 
      // getting the last url_id
      $query = 'SELECT url_id FROM URL';
      $result = pg_query($query) or die('Executing query failed: ' .pg_last_error()); 
      
      $last_url_id = pg_affected_rows($result);
      $new_url_id = $last_url_id + 1;
      
      // generate the new short url from the url_id
      $new_short_url = url_converter( $new_url_id );
      
      // getting the title from the url
      $url_title = "";
      $html = implode("", file($url_new)); 
      if (preg_match("/<title>(.*)<\\/title>/i", $html, $title))
      {
        $url_titel = $title[1];
      }
    
      // adding a new url to the database  
      add_new_url( $url_new, $new_short_url, $url_title );
      
      return $new_short_url;
    }
    /////////////////////////////////////////////////////////////////////////////////////


    /////////////////////////////////////////////////////////////////////////////////////
    // ADD NEW URL TO DATABASE!!!!!
    ///////////////////////////
    /*  
    *  adding a new url to the url table in database
    *
    *  @param $url_new: the new url address
    *  @param $new_short_url: the new shorturl address
    *  @param $url_title: the url title
    *
    *  @return void
    */          
    function add_new_url( $url_new, $new_short_url, $url_titel )
    { 
      $insert =  "INSERT INTO URL (longurl, shorturl, title) VALUES ('"
                 .$url_new."', '"
                 .$new_short_url."', '"
                 .$url_title."')";
                     
      $result = pg_query($insert) or die('Executing query failed: ' .pg_last_error());
    }
    /////////////////////////////////////////////////////////////////////////////////////
     
     
    /////////////////////////////////////////////////////////////////////////////////////
    // URL CONVERTER!!!
    ////////////////////////////
    /*  
    *  Generates a new shorturl by the url_id
    *
    *  @param $url_id: the url_id from the new url
    *
    *  @return the shorturl
    */     
    function url_converter( $url_id )
    {
      $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";                   
           
      $short_url = "0000";
      $size_string = strlen($characters);

      // calculating the position
      $result = (int)($url_id / $size_string);
    
      // first postion of the short_url
      if ($result >= 3844)
      {
        $result_top = (int)($url_id / ($size_string*$size_string*$size_string));   
        $short_url[0] = $characters[$result_top];
        $result_new = (int)($url_id / ($size_string*$size_string));   
        $short_url[1] = $characters[$result_new % $size_string];
        $short_url[2] = $characters[$result % $size_string]; 
        $mod = $url_id % $size_string;
        $short_url[3] = $characters[$mod];             
      }
      // second position of the short_url
      else if ($result >= 62)
      {
        $result_new = (int)($url_id / ($size_string*$size_string));   
        $short_url[1] = $characters[$result_new];
        $short_url[2] = $characters[$result % $size_string]; 
        $mod = $url_id % $size_string;
        $short_url[3] = $characters[$mod];                    
      }
      // third position of the short_url
      else if ($result >= 1)
      {
        $short_url[2] = $characters[$result]; 
        $mod = $url_id % $size_string;
        $short_url[3] = $characters[$mod];      
      }
      // fourth position of the short_url
      else if ($result == 0)
      {
        $mod = $url_id % $size_string;
        $short_url[3] = $characters[$mod];            
      }                  
      
      return $short_url;  
    }
      
    /////////////////////////////////////////////////////////////////////////////////////
 
    /////////////////////////////////////////////////////////////////////////////////////
    // CHECK SHORT URL!!!
    /////////////////////
    /*  
    *  Checks if the shorturl exists in database
    *
    *  @param $url: the url address
    *
    *  @return the correct site or an error site
    */      
    function check_short_url( $url )
    {
      include 'config.php';
      
      // open connection to database
      $connection = pg_connect('host=' .$host. ' port=' .$port. ' dbname=' .$database. ' user=' .$user. ' password=' .$password) 
          or die("DB ERROR: connect failed!");    
    
      $size = strlen($url);
      $short_url = substr($url, $size-4, $size);
      
      // getting the correct url address
      $query = "SELECT longurl FROM URL WHERE shorturl = '" .$short_url. "'";
      $result = pg_query($query) or die('Executing query failed: ' .pg_last_error());
      $long_url = pg_fetch_all_columns($result, 0); 
        
      if (count($long_url) == 1)
      {
        // increment the click value
        increment_click_count( $short_url );
        
        //close connection to DB
        pg_close($connection);
                
        // redirect to the correct url
        header("Location: $long_url[0]");
        exit;
        //return $long_url[0];
      }
      else
      {
        //close connection to DB
        pg_close($connection);
                
        //error message
        header("location:error.php?error=no_page");
        exit;
        
        //return "";
      }
    }
    /////////////////////////////////////////////////////////////////////////////////////
    
    /////////////////////////////////////////////////////////////////////////////////////
    // INCREMENT URL CLICK COUNT
    ////////////////////////////
    /*  
    *  Increment the click_count in the url table
    *
    *  @param $url: the url address
    *
    *  @return void
    */       
    function increment_click_count( $short_url )
    {
      // getting the click_count
      $query = "SELECT click_count FROM URL WHERE shorturl = '" .$short_url. "'";
      $result = pg_query($query) or die('Executing query failed: ' .pg_last_error());
      $click_array = pg_fetch_all_columns($result, 0);
      
      // increment the click_count
      $click_count = $click_array[0] + 1;
      
      // updating the click_count
      $query = "UPDATE URL SET click_count = '" .$click_count. "' WHERE shorturl = '"
               .$short_url. "'";
      $result = pg_query($query) or die('Executing query failed: ' .pg_last_error());  
          
    }
    /////////////////////////////////////////////////////////////////////////////////////     
    
    /////////////////////////////////////////////////////////////////////////////////////
    // ZUM TESTEN!!!!!!!!!
          
    $url_new = "httpf://www.google.com";
    $url = "http://catrobat.at/0001";
    
    $url_valid = checking_url_valid( $url_new );
    //$short_url = checking_url_db( $url_new );
    
    //echo $short_url."<p>";
    
    $short_url_in_db = check_short_url($url);
    
    //echo "Die neue ShortURL: ", $new_short_url;
    //echo "<p>";
    /*
      if ($short_url_in_db)
      {
        echo "SHORT URL in Datenbank!!!!!";
        echo "<p>";
      }
      else
      {
        echo "SHORT URL nicht  in Datenbank!!!!!";
        echo "<p>";      
      }
    */

    /////////////////////////////////////////////////////////////////////////////////////
?>