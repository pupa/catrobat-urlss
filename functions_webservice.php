<?php
    /*
     * Desc.: This function proofs if the original URL is valid
     * @param string longurl: contains the original URL
     * @return boolean: TRUE if the URL is valid, otherwise FALSE
     */
    function checkURLvalid($longurl) {
	   if (substr($longurl, 0, 7) == "http://" || substr($longurl, 0, 8) == "https://") {
		  return TRUE;
	   } else {
		  return FALSE;
	   }
    }

    /*
     * Desc.: generate a new shorurl to a longurl
     * @param string longurl: contains the original URL which should be shortened
     * @return string shorturl: contains the new short URL for the original one
     */
    function getShortURL($longurl) {
	   $query = "SELECT url_id FROM url";
	   $result = pg_query($query) or die("Executing query failed: " . pg_last_error());

	   // find the last url_id and increase
	   $url_id = pg_affected_rows($result);
	   $url_id++;

	   // generate the new shorturl from the url_id
	   $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	   $shorturl = "0000";
	   $size_string = strlen($characters);

	   // calculating the position
	   $result = (int)($url_id / $size_string);

	   // first position of the shorturl
	   if ($result >= 3844) {
		  $result_top = (int)($url_id / ($size_string * $size_string * $size_string));
		  $shorturl[0] = $characters[$result_top];
		  $result_new = (int)($url_id / ($size_string * $size_string));
		  $shorturl[1] = $characters[$result_new % $size_string];
		  $shorturl[2] = $characters[$result % $size_string];
		  $mod = $url_id % $size_string;
		  $shorturl[3] = $characters[$mod];
	   }
	   // second position of the shorturl
	   else if ($result >= 62) {
		  $result_new = (int)($url_id / ($size_string * $size_string));
		  $shorturl[1] = $characters[$result_new];
		  $shorturl[2] = $characters[$result % $size_string];
		  $mod = $url_id % $size_string;
		  $shorturl[3] = $characters[$mod];
	   }
	   // third position of the shorturl
	   else if ($result >= 1) {
		  $shorturl[2] = $characters[$result];
		  $mod = $url_id % $size_string;
		  $shorturl[3] = $characters[$mod];
	   }
	   // fourth position of the shorturl
	   else if ($result == 0) {
    	  $mod = $url_id % $size_string;
		  $shorturl[3] = $characters[$mod];
	   }
       
       // read out the title of the original url
       $data = implode("", file($longurl)); 
       if (preg_match("/<title>(.*)<\/title>/i", $data, $title)) {
           $titelrow = $title[1]; 
       }

	   if (addNewURL($longurl, $shorturl, $titelrow)) {
	       return $shorturl;
	   } else {
	       // couldn't save the entry in our DB
           $shorturl = "ERROR: Couldn't save the new entry into our Database!";
	   }
    }

    /*
     * Desc.: adding a new entry into our table url
     * @param string longurl: contains the original URL
     * @param string shorurl: contains the related shorturl
     * @param string title: contains the title of the orignial URL (optional)
     * @return boolean: TRUE when the entry saved successful to our DB, otherwise FALSE
     */
    function addNewURL ($longurl, $shorturl, $title=" ") {
        $query = "INSERT INTO url (longurl, shorturl, title) VALUES ('" .$longurl. "', '" .$shorturl. "', '".$title. "')";
        $result = pg_query($query);

        if (!$result) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
?>