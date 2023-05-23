<?php
	function getRootGeoId($cur_id, $root_id=0) {
		$query_template = 'SELECT mailruleid FROM mailregions where geo_id=:geo_id';
		global $dbreadonly; $parent_id = -1;
		while(($cur_id != $root_id) && ($cur_id != 0)) {
			$parent_id = $cur_id;
			$query = $dbreadonly->prepare($query_template);
			$query->execute(array(
				':geo_id' => $cur_id
			));
			$row = $query->fetch(PDO::FETCH_ASSOC);
			$cur_id = $row['mailruleid'];
		}
		return $parent_id;
	}

	function make_img_button($web_path_to_img, $dest_page, $alt_text, $onclick="", $other_params=array()) {
		global $USER_INFO; global $obligatory_fields;
				$unique = rand(0,99); // Yes, misleading, but likely good enough....
		$img_button = sprintf("<form method=\"post\" action=\"%s\" name=\"img_%s_%s\" id=\"img_%s_%s\" class=\"hidden_form\">",
			$dest_page, $dest_page, $unique, $dest_page, $unique);
		$img_button .= sprintf($obligatory_fields, $USER_INFO['username'], $USER_INFO['hashpasswd']);

			    if(strlen($onclick) != 0)
					$img_button .= sprintf("<input type=\"image\" src=\"%s\" title=\"%s\" alt=\"%s\" onclick=\"%s\" />", $web_path_to_img, $alt_text, $alt_text, $onclick);
				else
					$img_button .= sprintf("<input type=\"image\" src=\"%s\" title=\"%s\" alt=\"%s\" />", $web_path_to_img, $alt_text, $alt_text);

			    foreach($other_params as $p_name=>$p_value) {
						if($p_name == "page") { continue; }
			$img_button .= sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />",
				$p_name, $p_value);
				}
		$img_button .= "</form>";

		return $img_button;
	}


	function ens_left_nav_item_bool($form_name, $page_name, $display_text, $is_current=false, $other_params=array()) {
		/*// Get some global params
		global $USER_INFO; global $obligatory_fields;

		// Fill the nav item
		$nav_item = sprintf("<li><form method=\"post\" action=\"%s\" name=\"noscript_%s\" id=\"noscript_%s\" class=\"nav_left\">", $page_name, $form_name, $form_name);
		$nav_item .= sprintf($obligatory_fields, $USER_INFO['username'], $USER_INFO['hashpasswd']);
		foreach($other_params as $p_name=>$p_value)
			$nav_item .= sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />", $p_name, $p_value);
		$nav_item .= sprintf("%s<input type=\"submit\" name=\"submit\" value=\"%s\" class=\"nav_left\" />%s</form></li>",
			($is_current)?"<strong>":"",  $display_text, ($is_current)?"</strong>":"");
		*/

		$first = true;

		$nav_item = sprintf("<li>%s<a class=\"nav_left\" href=\"%s", ($is_current)?"<strong>":"", $page_name);
		foreach($other_params as $p_name=>$p_value) {
			if($first) {
				$nav_item .= "?";
				$first = false;
			}
			else {
				$nav_item .= "&";
			}
			$nav_item .= $p_name . "=" . $p_value;
		}
		$nav_item .= sprintf("\">%s%s</a></li>",  $display_text, ($is_current)?"</strong>":"");


		// Reurn the navigation item
		return $nav_item;
	}


	/**
	 * This function creates a 'random' string of length $len.
	 * The string must contain at least one uppercase, one lowercase, and one number.
	 * This restriction guarantees that we produce a reasonably strong password. Without
	 * it we could, by chance, generate a password like 'asdfg'.
	 */
	function rand_string($len=8) {
		$upper = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		$lower = 'abcdefghjkmnpqrstuvwxyz';
		$nums = '23456789';
		#$chars = "ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789";
		$rtn_string = "";
		$x = 0;
		while(strlen($rtn_string) < $len) {
			if( $x%3 == 0 ) {
				$chars = $lower;
				$substrlen = rand()%3;
			}
			else if( $x%3 == 1 ) {
				$chars = $nums;
				$substrlen = rand()%2;
			}
			else if( $x%3 == 2 ) {
				$chars = $upper;
				$substrlen = rand()%2;
			}

			if( $substrlen == 0 ) { $substrlen = 1; }
			$rtn_string .= substr($chars, rand()%strlen($chars), $substrlen);
			$x++;
		} // End: while()

		# The substring might have made the return string too long,
		# so we should chop it down.
		$rtn_string = substr($rtn_string, 0, $len);

		return $rtn_string;
	}

    /*
     * Generate password of number of characters passed in
     */
    function generate_password($length) {
        $string = "";
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#+-";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[random_int(0, $size - 1)];
        }
        return $string;
    }

    function generateCSRFToken($length) {
        return bin2hex(random_bytes($length));
    }
    
    // Get CSRF token
    function getCSRFToken($renew=false, $length = 32) {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = generateCSRFToken($length);
            // error_log("ENS LOG - Generate CSRF token" . $_SESSION['csrf_token']);
        } else {
            // Change CSRF token
            if ($renew) {
                $_SESSION['csrf_token'] = generateCSRFToken($length);
                // error_log("ENS LOG - Renew and create a new CSRF token" . $_SESSION['csrf_token']);
            }
        }
        
        return $_SESSION['csrf_token'];
    }    

    /**
     * Get CSRF token from the custom request header
     */
    function getRequestCSRF() {
        $csrfHeader = filter_input(INPUT_SERVER, 'HTTP_X_CSRF_TOKEN', FILTER_SANITIZE_STRING);
        return $csrfHeader;
    }
    
    // Compare the CSRF token
    function isMatchCSRF($userToken) {
        $isMatch = false;
        $serverToken = getCSRFToken();
        
        //error_log("ENS LOG - isMatchCSRF - server token " . $serverToken . " usertoken " . $userToken);

        if ($serverToken && $userToken && $serverToken === $userToken) {
            $isMatch = true;
        }
        return $isMatch;
    }    

    /** 
     * Check for CSRF token and exit if not valid. $userToken is set when not using ajax
     */
    function validateCSRF($userToken=null, $isResponseHTML = false) {
        $isInvalid = false;
        $returnHTML = $isResponseHTML; // Not ajax so return HTML response. This is set to true if $userToken is not null
        
        if (!isset($_SESSION['csrf_token'])) {
            $isInvalid = true;
        } else {
            if (is_null($userToken)) {
                if (!isMatchCSRF(getRequestCSRF())) {
                    // ajax uses a custom CSRF header
                    $isInvalid = true;
                }
            } elseif (!isMatchCSRF($userToken)) {
                // $userToken is sent when using forms
                $isInvalid = true;
                $returnHTML = true;
            }
        }
        
        if ($isInvalid) {
            if (isset($_SESSION['USER_INFO']['username'])) {
                $userInfo = $_SESSION['USER_INFO']['username'];
                error_log("ENS LOG - CSRF token validation failed. Username: $userInfo");
            }
            if (!$returnHTML) {
                http_response_code(ResponseConstant::HTTP_BAD_REQUEST);
                print json_encode(getFormattedResponse(ResponseConstant::INVALID_INPUT, 'Invalid request'));
                exit();            
            } else {
                return false;
            }
        }
        return true;
    }
    
	/**
	 * Try to determine if the given email address corresponds
	 * to a cell phone. The algorithm checks the first 10 digits
	 * to see if they are numbers and checks to see if the hostname is
	 * one of the known SMS gateways (vtext.com, txt.att.com, etc)
	 * @param $email String to be tested. Doesn't need to be an email address.
	 * 	  Can just be a 10 digit number.
	 * return True if the algorithm thinks the string is a phone, false otherwise
	 */
	function isPhone( $email ) {
		$gateways = array('vtext.com', 'message.alltel.com', 'txt.att.net',
                        'mms.att.net', 'cingularme.com', 'myboostmobile.com',
                        'messaging.nextel.com', 'messaging.sprintpcs.com',
                        'tmomail.net', 'vzwpix.com', 'vmobl.com');

		if( strpos( $email, '@') !== false ) {
			$pieces = explode( '@', $email );
			$domain = $pieces[1];
			$address = $pieces[0];

			if( in_array( $domain, $gateways) ) {
				return true;
			}

			if( strlen($address) == 10 &&  is_numeric( $address ) ) {
				return true;
			}

		}
		else {
			if( strlen($email) == 10 &&  is_numeric( $email ) ) {
				return true;
			}
		}

		// If we got here, its probably not a phone
		return false;
	}

    /**
     * Checks to see if the mobile number is using 10-digit all numbers.
     * For now, it only checks Verizon as EMAG requires it.
     * @param type $email
     * @return boolean
     */
    function isValidMobileAddress( $email ) {
        $isValid = true; 
        $gateways = array('vtext.com', 'vzwpix.com', 'mypixmessages.com', 'cvzvmg.biz');

        if( strpos( $email, '@') !== false ) {
            $pieces = explode( '@', $email );
            $domain = $pieces[1];
            $address = $pieces[0];

            if( in_array( $domain, $gateways) ) {
                // Verizon domain. Check for valid 10 digit numbers
                if( strlen($address) == 10 && is_numeric($address) ) {
                    $isValid = true;
                } else {
                    $isValid = false;
                }
            }
        }
        return $isValid;
    }

	/**
	 * This function attempts to parse $xmltext as valid
	 * xml text for defining a region in ENS.  On success,
	 * the global scope arrays $lats and $lons will be
	 * filled with the list of the lats/lons defined
	 * by the $xmltext and true is returned.  On failure false is
	 * returned and the status of $lats and $lons is unknown.
	 *
	 * NOTE: If $xmltext == "" true is returned and $lats and $lons are unchanged.
	 *
	 */
	function ens_xml_parse($xmltext) {
		if($xmltext == "") return true;
		$xml_parser = xml_parser_create();
		if(xml_parse_into_struct($xml_parser, $xmltext, $vals, $keys) == 0) return false;
		xml_parser_free($xml_parser);
#echo "<pre>";
#echo "Keys array\n";
#print_r($keys);
#echo "\nVals array\n";
#print_r($vals);
#echo "</pre>";

		$nlats = sizeof($keys['LAT']);
		$nlons = sizeof($keys['LON']);
		$npoints = sizeof($keys['POINT']);
		$nregs = sizeof($keys['REGION']);
		if(($npoints/2) != ($nregs-2)) return false; // Multiple regions
		// Check for too few points, mismatch lat,lon pairs, or empty points
		if(($nlats < 3) || ($nlats != $nlons) || ($npoints != 2*$nlats) ) return false;

		// Seems to parse well, so fill the lats/lons arrays
		global $lats; global $lons;
		foreach($keys['LAT'] as $k=>$idx)
			$lats[] = $vals[$idx]['value'];
		foreach($keys['LON'] as $k=>$idx)
			$lons[] = $vals[$idx]['value'];

		// Just double checking
		for($idx = 0; $idx < $nlats; ++$idx)
			if(!is_numeric($lats[$idx]) || !is_numeric($lons[$idx])) return false;

		return true;
	}

    /**
     * This function checks the all the input parameters for the given $type.
     * If an error occurs, an error message is printed and false is returned.
     * Otherwise the function proceeds silently and true is returned.
     */
    function valid_params($type) {
        $retval = true; // Assume true
        $errstring = "";
        global $success;

        // These are for all types
        global $depth_min; global $depth_max;
        if ($depth_min < -100 || $depth_min > 1000) {
            $errstring .= "\t\t\t<li>Depth min must be between -100 and 1000.</li>\n";
        }

        if ($depth_max < -100 || $depth_max > 1000) {
            $errstring .= "\t\t\t<li>Depth max must be between -100 and 1000.</li>\n";
        }

        if($depth_min > $depth_max) {
            // Switch them, but don't cause an error, since it is correctable
            $temp = $depth_min;
            $depth_min = $depth_max;
            $depth_max = $temp;
        }

        if($depth_min == $depth_max)
            $errstring .= "\t\t\t<li>The depth range you provided is empty.</li>\n";

        global $addressflags;
        if(is_null($addressflags) || sizeof($addressflags) == 0)
            $errstring .= "\t\t\t<li>You must have at least one address associated with this profile.</li>\n";

		// These are type-specific
		if($type == "rectangle") {
			global $parentList;
			// Check the lat/lons
			global $lat_min;
			global $lat_max;
			global $lon_min;
			global $lon_max;
			global $dateline;

			$dateline = 0; // Assume we do not cross the dateline

#                        if (abs($lon_max) > 180) {
#                               $errstring .= "\t\t\t<li>Longitude out of range!</li>\n";
#                        }
#                        if (abs($lon_min) > 180) {
#                               $errstring .= "\t\t\t<li>Longitude out of range!</li>\n";
#                        }

#			if($lat_min > $lat_max) {
				// We don't treat this as a failure since it is correctable
#				$temp = $lat_min;
#				$lat_min = $lat_max;
#				$lat_max = $temp;
#			}
#			if ($lat_min == $lat_max || $lon_min == $lon_max)
#				$errstring .= "\t\t\t<li>Your region bounds were equal and define a null region!</li>\n";
#			if($lat_min < -90 || $lat_max > 90)
#				$errstring .= "\t\t\t<li>Latitude values must be between -90&deg; and +90&deg;</li>\n";
#
#                        if (($lon_max > 180) || ($lon_min < -180)) {
                          // We crossed the date line
# 	  		  print ("we crossed the date line\n");
#                          $dateline = 1;
#                        }
			//if($maxlon > 180)
			//	$dateline = 1;
			//if($minlon < -180)
			//	$dateline = -1;
		} else if ($type == "circle") {
			global $clat;
			global $clon;
			global $radius;
			global $place;
			global $parentList;

			if($place != "select") {
				global $FILE_PATH;
				$places = file($FILE_PATH . "/inc/bigplaces") or die("Cannot read file " . $FILE_PATH . "/inc/bigplaces");
				foreach($places as $testplace) {
					if(preg_match("/$place/", $testplace)) {
						$placename = rtrim(substr($testplace, 19)); // Use rtrim so we don't get newlines in the database
						$testplace = preg_replace ("/\s+/", " ", $testplace);
						$testplace = trim($testplace);
						$parts = preg_split ("/\s+/", $testplace);
						$clat = $parts[1];
						$clon = $parts[0];
						break;
					} // END: if(preg_match)
				} // END: foreach
			} // END: if($place)

			// These are for the mailregions table, but don't have a real bearing on the geography
			global $lat_min; global $lat_max; global $lon_min; global $lon_max;
			$lat_range = (($radius*1.6093471)/40030)*360;
			$lat_min = $clat - $lat_range;
			$lat_max = $clat + $lat_range;
			$lon_range = ((($radius*1.6093471)/40030)*360)*cos($clat);
			$lon_min = $clon - $lon_range;
			$lon_max = $clon + $lon_range;

			if($clat < -90 || $clat > 90)
				$errstring .= "\t\t\t<li>Latitude values must be between -90&deg; and +90&deg;</li>\n";
			if($clon < -180 || $clon > 180)
				$errstring .= "\t\t\t<li>Longitude values must be between -180&deg; and +180&deg;</li>\n";
			if($radius > 12500 || $radius <= 0)
				$errstring .= "\t\t\t<li>The radius you provided (${radius}) is out of range.  (0 < radius <= 12500).</li>\n";
		} else if ($type == "polygon") {
			// Nothing to do here?

		} // END: if($type==...)

        // Check for an error and print it if so (and set retval)
        if(strlen($errstring) != 0) {
            $retval = false;
            $success = false;
            printf("<h3 class=\"alert warning\">Some of your input parameters were out of range.</h3>\n\t\t<ul>%s\t\t</ul>\n", $errstring);
        }
        return $retval;
	}

	/**
	 * This function takes the $parentList (generated by the map), and
	 * creates xmltext defining the points that were selected.
	 *
	 * Author: Stan Schwarz
	 */
	function makexmltext ($parentList, $debug=0) {
		list($latlist,$lonlist) = explode("&", $parentList);
		global $lats;
		global $lons;
		$lats = explode(",", $latlist);
		$lons = explode(",", $lonlist);

		$xmltext = "<?xml version='1.0'?>\n";
		$xmltext .= "\t<region>\n";

		for ($i = 0; $i < sizeof($lats); ++$i) {
			$x1 = $lons[$i];
			$y1 = $lats[$i];
			$xmltext .= sprintf("\t\t<point><lat>%3.4f</lat><lon>%3.4f</lon></point>\n", $y1, $x1);
		}

		$xmltext .= "</region>";

		return($xmltext);
	}
	/**
	 * This function inserts a space into a the $val at regular intervals
	 * such that $val will wrap in at most $maxlen characters.  Generally,
	 * appropriate break points include ",./@-_", but other delimiters can
	 * be specified by passing the optional third parameter $morebreakers
	 */
	function smart_breaks($val, $maxlen, $morebreakers=array()) {
		$mybreaks = array(",", ".", "/", "@", "-", "_");
		$mybreaks = array_merge($morebreakers, $mybreaks);
		$substrings = array();
		while(strlen($val) > $maxlen) {
			for($i = ($maxlen-1); $i > 0; --$i) {
				if(in_array(substr($val, $i, 1), $mybreaks)) {
					array_push($substrings, substr($val, 0, $i));
					$val = substr($val, $i);
					break; // Jumps out of the for loop
				}

				if($i == 1) { // We checked every character, and none were special, but we gotta break the string
					array_push($substrings, substr($val,0,$maxlen-1));
					$val = substr($val, $maxlen-1);
					break;
				}
			}
		} // END: while(strlen)

		//foreach($substrings as $substring)
		for($i = (sizeof($substrings) - 1); $i >= 0; --$i)
			$val = $substrings[$i] . " " . $val;
		return $val;
	}
	/**
	 * This function gets the username and password combination from the global environment and
	 * checks with the ens database to see if such a user exists.  If so, true is returned, else
	 * false is returned.  This function handles both text passwords and hash'd passwords. Upon
	 * successful authentication of the user, the global $USER_INFO variable will be an associative
	 * array filled with $key=>$value pairs corresponding to table columns=>values for the specified
	 * username in the database.
	 */
	function is_logged_in() {
		global $username;
		global $hashpass;
		global $database;
		global $errmsg;
		global $USER_INFO;
		global $firsttime;

		$retval = true;

		if(isset($_POST['tpass'])) {
			$hashpass = md5($_POST['tpass']);
		} else {
			$hashpass = param('hpass');
		}

		if(isset($_POST['username']))
			$username = $_POST['username'];

		$query_exists = $database->prepare("SELECT * FROM mailusers WHERE username=:username AND hashpasswd=:hashpass");
		#printf ("SQL is %s<br>\n", $query_exists);
		$result = $query_exists->execute(array(
			':username' => $username,
			':hashpass' => $hashpass
		));
		$USER_INFO = $result->fetch(PDO::FETCH_ASSOC);

		if (count($USER_INFO) != 1) {
			$errmsg .= "\t<li>Invalid username or password.</li>\n";
			$retval = false;
		} else {

			if($USER_INFO['lastlogin'] == $USER_INFO['added']) $firsttime = true;

			if($retval && isset($_POST['initlogin']) ) { // User is logging in, so update lastlogin
				$cur_login = date ("Y-m-d H:i:s");
				$update_login = $database->prepare("UPDATE mailusers SET lastlogin=:cur_login WHERE id=:id");

				// It is not a big deal if this succeeds, so we'll do it, but not check the result
				$rs_login = $update_login->execute(array(
					':cur_login' => $cur_login,
					':id' => $USER_INFO['id']
				));
			}

		}
		return $retval;
	}

	/**
	 * This function prints a simple form with a hidden username, hashpass, and ref_page so
	 * when submitted (by a url onclick method likely), these variables will be in scope and
	 * the user will remain logged in.  In addition to the above, one can optionally provide
	 * additional hidden elemnts in a key=>value array to be passed via the POST array.  Such
	 * elements will have name=>value parameters.  The form will be named by the given $formname.
	 */
	function write_form($formname, $target, $paramlist=array()) {
		$USER_INFO = $_SESSION['USER_INFO'];
		//printf("\t<form method=\"post\" action=\"%s\" name=\"%s\">", $target, $formname);
		//printf("<input type=\"hidden\" name=\"username\" value=\"%s\" />", $USER_INFO['username']);
		//printf("<input type=\"hidden\" name=\"hashpass\" value=\"%s\" />", $USER_INFO['hashpasswd']);
		//printf("<input type=\"hidden\" name=\"page\" value=\"%s\" />", $target);
		//foreach($paramlist as $name=>$value)
		//	printf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />", $name, $value);
		//print ("</form>\n");
	}


	/**
	 * This function resolves the given time to a truely allowable time.
	 */
	function timeresolve($t) {
		if($t < 0)
			$t += 24;
		if($t > 24)
			$t -= 24;
		return $t;
	}

	/**
	 * This function shows an error page when user request fails
	 * Note that this does not stop page execution upon completion, so
	 * one should use this in an exclusive if/else branch for page flow
	 */
	 function show_error($error_code='404') {
		global $FILE_PATH;
		$_GET['error'] = $error_code;
		include $FILE_PATH . "/pages/error.inc.php";
	}

	/**
	 * This is sort of hacky.  It sends a seperate request to the ENS userpages.
	 * This is heavily reliant on the requested page being 100% HTML compliant.
	 *
	 * $host = The host to send the request to.  By default it is earthquake.usgs.gov
	 * $query = The post query string to send. i.e. var1=val1&var2=val2...etc...
	 * $page = The ENS include page to show (see /pages/*.inc.php).
	 *
	 */
	function do_redirect($host='earthquake.usgs.gov',$query='',$page='') {
		global $USER_INFO;
		global $WEB_PATH;
		// Add the username/password to the post for authentication
		$query .= sprintf("&username=%s&hpass=%s", $USER_INFO['username'], $USER_INFO['hashpasswd']);

		$post = sprintf("POST %s/index.php?req_page=%s HTTP/1.1\r\n", $WEB_PATH, $page);
		$post .= sprintf("Host: %s\r\n", $host);
		$post .= "Content-type: application/x-www-form-urlencoded\r\n";
		$post .= sprintf("User-Agent: %s\r\n", $_SERVER['HTTP_USER_AGENT']);
		$post .= sprintf("Content-length: %s\r\n", strlen($query));
		$post .= "Connection: close\r\n\r\n";
		$post .= $query;
		ob_clean();
		//print $post;
		// Send the request
		$http = fsockopen($host, 80);
		fputs($http, $post);
		for($a = 0, $r = ''; !$a;) {
			$b = fread($http, 8192);
			$r .= $b;
			$a = (($b=='')?1:0);
		}
		fclose($http);
		// Clean up the responce code
		$r = preg_replace("/^[^<]*/", "", $r);
		// Clean up some errenous output
		$r = preg_replace("/\n\d[^\n]+\n/", "", $r);
		print $r;
		exit(0);
	}


	/**
	 * This function finds the pre-created basemap file that best fits the defined
	 * region and returns the filename.jpg
	 *
	 * Author: Stan Schwarz
	 */
	function getbasemap ($lats, $lons) {
		$basemap = "";

		$lon_min = min($lons);
		$lon_max = max($lons);
		$lat_min = min($lats);
		$lat_max = max($lats);

		if (($lon_min >= -180) && ($lon_max <= 0) && ($lat_min >= -90) && ($lat_max <= 90))
			$basemap = 'worldmap5_reg.jpg';
		if (($lon_min >= 0) && ($lon_max <= 180) && ($lat_min >= -90) && ($lat_max <= 90))
			$basemap = 'worldmap6_reg.jpg';
		if (($lon_min >= -180) && ($lon_max <= -60) && ($lat_min >= 0) && ($lat_max <= 50))
			$basemap = 'Namerica_reg.jpg';
		if (($lon_min >= 20) && ($lon_max <= 50) && ($lat_min >= 30) && ($lat_max <= 45))
			$basemap = 'turkey.jpg';
		if (($lon_min >= 85) && ($lon_max <= 155) && ($lat_min >= -18) && ($lat_max <= 28))
			$basemap = 'SEasia.jpg';
		if (($lon_min >= -130) && ($lon_max <= -80) && ($lat_min >= 12) && ($lat_max <= 34))
			$basemap = 'mexico.jpg';
		if (($lon_min >= -130) && ($lon_max <= -62) && ($lat_min >= 22) && ($lat_max <= 50))
			$basemap = 'CONUS.jpg';
		if (($lon_min >= -128) && ($lon_max <= -110) && ($lat_min >= 30) && ($lat_max <= 50))
			$basemap = 'USwest.jpg';
		if (($lon_min >= -115) && ($lon_max <= -90) && ($lat_min >= 30) && ($lat_max <= 50))
			$basemap = 'USmtn.jpg';
		if ((($lon_min >= -105) && ($lon_max <= -75)) && (($lat_min >= 25) && ($lat_max <= 50)))
			$basemap = 'USmidwest.jpg';
		if ((($lon_min >= -90) && ($lon_max <= -60)) && (($lat_min >= 25) && ($lat_max <= 50)))
			$basemap = 'USeast.jpg';
		if (($lon_min >= -128) && ($lon_max <= -110) && ($lat_min >= 30) && ($lat_max <= 42))
			$basemap = 'california.jpg';
		if ((($lon_min >= -180) && ($lon_max <= -130)) && (($lat_min >= 50) && ($lat_max <= 80)))
			$basemap = 'alaska.jpg';
		if ((($lon_min >= -164) && ($lon_max <= -150)) && (($lat_min >= 14) && ($lat_max <= 22)))
			$basemap = 'hawaii.jpg';

		// If none of the above caught it then it is a special case
		if ($basemap == '') {
			$lon_middle = $lon_max - ($lon_max - $lon_min)/2;

			if (($lon_middle > -270) && ($lon_middle <= -90))
				$basemap = 'worldmap1_reg.jpg';
			if (($lon_middle > -180) && ($lon_middle <= 0))
				$basemap = 'worldmap2_reg.jpg';

			/* The region crosses the International Date Line, so kluge it */
			if ((min($lon_min,$lon_max) < -180) || (max($lon_min,$lon_max) > 180))
				$basemap = 'worldmap1_reg.jpg';
			/* The average lon is around 0. The region really is centered on 0 */
			if (($lon_middle > -90) && ($lon_middle <= 90))
				$basemap = 'worldmap3_reg.jpg';
			if (($lon_middle > 90) && ($lon_middle <= 180))
				$basemap = 'worldmap4_reg.jpg';
		}
		return($basemap);
	}

	/*
	 * This is the line clipping routine. It cuts off lines at the edges
	 * of the map. It is based on an algorithm I found at
	 * http://www.cc.gatech.edu/grads/h/Hao-wei.Hsieh/Haowei.Hsieh/mm.html
	 *
	 * Author: Stan Schwarz
	 */
	function clip($x1,$y1,$x2,$y2,&$xp1,&$yp1,&$xp2,&$yp2,$xsize,$ysize,$xoffset,$yoffset) {
		$outcode1 = 0;
		$outcode2 = 0;
		$xmax = $xsize + $xoffset;
		$ymax = $ysize + $yoffset;
		// Set outcode 1
		if ($x1 < $xoffset)
			$outcode1 = $outcode1 | 1;
		if ($x1 > $xmax)
			$outcode1 = $outcode1 | 2;
		if ($y1 > $ymax)
			$outcode1 = $outcode1 | 4;
		if ($y1 < $yoffset)
			$outcode1 = $outcode1 | 8;
		// Set outcode2
		if ($x2 < $xoffset)
			$outcode2 = $outcode2 | 1;
		if ($x2 > $xmax)
			$outcode2 = $outcode2 | 2;
		if ($y2 > $ymax)
			$outcode2 = $outcode2 | 4;
		if ($y2 < $yoffset)
			$outcode2 = $outcode2 | 8;

		$xp1 = $x1;
		$xp2 = $x2;
		$yp1 = $y1;
		$yp2 = $y2;

		$xprime = 0;
		$yprime = 0;

		// If the both outcodes are 0, the the whole line is inside the map.
		// This is the 'trivial' case, so just return.
		if (($outcode1 == 0) && ($outcode2 == 0)) { return(0); }

		// First check outcode1
		if ($outcode1 > 0) {
			$xdiff = $x2 - $x1;
			$ydiff = $y2 - $y1;
			if ($outcode1 & 8) {
				if ($ydiff != 0)
					$xprime = $x1 + ($x2 - $x1) * ($yoffset - $y1) / ($y2 - $y1);
				$yprime = $yoffset;
			}

			if ($outcode1 & 4) {
				if ($ydiff != 0)
					$xprime = $x1 + ($x2 - $x1) * ($ymax - $y1) / ($y2 - $y1);
				$yprime = $ymax;
			}

			if ($outcode1 & 2) {
				if ($xdiff != 0)
					$yprime = $y1 + ($y2 - $y1) * ($xmax - $x1) / ($x2 - $x1);
				$xprime = $xmax;
			}

			if ($outcode1 & 1) {
				if ($xdiff != 0)
					$yprime = $y1 + ($y2 - $y1) * ($xoffset - $x1) / ($x2 - $x1);
				$xprime = $xoffset;
			}

			$xp1 = $xprime;
			$yp1 = $yprime;
		}

		// Now check outcode 2
		if ($outcode2 > 0) {
			$xdiff = $x1 - $x2;
			$ydiff = $y1 - $y2;

			if ($outcode2 & 8) {
				if ($ydiff != 0)
					$xprime = $x2 + ($x1 - $x2) * ($yoffset - $y2) / ($y1 - $y2);
				$yprime = $yoffset;
			}

			if ($outcode2 & 4) {
				if ($ydiff != 0)
					$xprime = $x2 + ($ymax - $y2)*($x1 - $x2)/($y1 - $y2);
				$yprime = $ymax;
			}

			if ($outcode2 & 2) {
				if ($xdiff != 0)
					$yprime = $y2 + ($y1 - $y2) * ($xmax - $x2) / ($x1 - $x2);
				$xprime = $xmax;
			}

			if ($outcode2 & 1) {
				if ($xdiff != 0)
					$yprime = $y2 + ($y1 - $y2) * ($xoffset - $x2) / ($x1 - $x2);
				$xprime = $xoffset;
			}

			$xp2 = $xprime;
			$yp2 = $yprime;
		}

		// Now check to see if the line is completely outside the map.
		if (($xp1 == $xp2) && ($yp1 == $yp2)) // The line is outside the map, so flag it not to draw
			$ret = -1;
		else
			$ret = 0;
		return($ret);
	}

    /**
     * This function breaks up the n-sided polygon into n - 2 triangles.  Uses an algorithm found
     * at: http://www-2.cs.cmu.edu/~quake/triangle.html for the bulk of the logic.
     *
     * Author: Stan Schwarz
     */
    function splitpoly ($regionid, $lats, $lons, $numpoints, $triangle_path, $debug) {
        #
        # Start by assuming that we have only one polygon to deal with.
        $npoly = 1;

        global $database;

        if ($debug > 0) {
            printf ("%d polygons to deal with<br>\n", $npoly);
        }

        for ($k = 0; $k < $npoly; $k++) {
            if ($debug > 0) {
                printf ("Processing polygon %d<br>\n", $k);
            }
            if ($npoly > 1) {
                $plat[$k] = preg_replace("/,$/", "", $plat[$k]);
                $plon[$k] = preg_replace("/,$/", "", $plon[$k]);
                if ($debug > 0) { printf ("Polygon %d is %s/%s<br>\n", $k, $plat[$k], $plon[$k]); }

                $lats = explode(",", $plat[$k]);
                $lons = explode(",", $plon[$k]);
                $numpoints = count($lats);
            }
            $lastpoint = $numpoints - 1;
            $latdiff = abs($lats[0] - $lats[$lastpoint]);
            $londiff = abs($lons[0] - $lons[$lastpoint]);
            if (($latdiff < 0.0001) && ($londiff < 0.0001)) {
                # If the first and last point are the same, axe the last point.
                $numpoints = $numpoints - 1;
            }
            if ($debug > 0) { printf ("%d points in polygon %d<br>\n", $numpoints, $k); }
            // Now we need to check to see if we're crossing the Date Line
            // Also, which hemisphere are we (mostly) in?
            // This is so we correctly kluge the Date Line case, but don't mess
            // with the Prime Meridian case, since if we mess with things that
            // cross the Meridian, it ends up wrapping the polygon the long way around.
            $medlon = calculate_median($lons);
            if ($medlon > 0) {
                // Eastern Hemisphere
                $offset = 1;
            } else {
                //  Western Hemisphere
                $offset = -1;
            }
            if ((max($lons) > 0) && (min($lons) < 0)) {
                // Signs are different. We crossed either the
                // date line or prime meridian
                $lonspan = abs(min($lons)) + abs(max($lons));
                if ($lonspan > 180) {
                    // We crossed the Date Line
                    $dateline = 1;
                    // Now we need to force all the lons to the same sign
                    for ($i=0; $i<$numpoints; $i++) {
                        if (($lons[$i] < 0) && ($offset == 1)) { $lons[$i] = $lons[$i] + 360; }
                        if (($lons[$i] > 0) && ($offset == -1)) { $lons[$i] = $lons[$i] - 360; }
                    }
                }
            }

            $tempname = tempnam("/tmp", "");
            if ($debug > 0) {
                printf ("Paramid is %d<br>\n", $paramid);
                printf ("Temp file name is %s\n", $tempname);
            }
            $handle = fopen ("$tempname.poly","w+");
            $string = sprintf ("%d 2 0 0\n", $numpoints);
            fwrite ($handle, $string);
            for ($i=0; $i<$numpoints; $i++) {
                $string = sprintf ("%d %3.4f %3.4f\n", $i+1, $lons[$i], $lats[$i]);
                fwrite ($handle, $string);
            }
            $string = sprintf ("%d 0 0\n", $numpoints);
            fwrite ($handle, $string);
            for ($i=0; $i<$numpoints; $i++) {
                if ($i+2 > $numpoints) {
                    $string = sprintf ("%d %d 1\n", $i+1, $i+1);
                } else {
                    $string = sprintf ("%d %d %d\n", $i+1, $i+1, $i+2);
                }
                fwrite ($handle, $string);
            }
            $string = sprintf ("0\n");
            fwrite ($handle, $string);
            fclose ($handle);

            #    Use the 'triangle' program to break up the polygon
            #    http://www-2.cs.cmu.edu/~quake/triangle.html
            if ($debug > 0) {
                printf ("Path to triangle is %s<br>\n", $triangle_path);
                system ("$triangle_path -V -F $tempname.poly");
                printf ("Now let's see what triangle did to the polygon<br>\n");
            } else {
                system ("$triangle_path -Q -F $tempname.poly");
            }

            $handle = fopen ("$tempname.1.node","r");
            $string = fgets($handle, 4096);
            if ($debug > 0) {printf ("string is %s<br>\n", $string);}
            list ($newnumpoints,$junk1,$junk2,$junk3) = preg_split ("/\s+/", $string);
            $numpoints = $newnumpoints;
            if ($debug > 0) {printf ("Triangle says we now have %d points<br>\n", $numpoints);}

            for ($i=0; $i<$numpoints; $i++) {
                $string = fgets($handle, 4096);
                if (!preg_match("/^#/", $string)) {
                    if ($debug > 0) {printf ("string is %s<br>\n", $string);}

                    $string = preg_replace ("/^\s+/", "", $string);
                    list ($junk1,$newlons[$i],$newlats[$i]) = preg_split ("/\s+/", $string);
                    if ($debug > 0) {
                        printf ("Point %d is %3.3f, %3.3f<br>\n", $junk1, $newlats[$i], $newlons[$i]);
                    }
                }
            }
            fclose ($handle);

            $handle = fopen ("$tempname.1.ele","r");
            $string = fgets($handle, 4096);
            list ($numtriangle,$junk1,$junk2) = preg_split ("/\s+/", $string);
            if ($debug > 0) {
                printf ("Number of triangles is %d<br>\n", $numtriangle);
                printf ("New lat/lon points are:<br>\n");
                for ($i=1; $i<=$newnumpoints; $i++) {
                    $index = $i-1;
                    printf ("%3.3f %3.3f<br>\n", $newlats[$index], $newlons[$index]);
                }
            }

            #    $drawlines = '';
            for ($i=0; $i<$numtriangle; $i++) {
                $string = fgets($handle, 4096);
                if (!preg_match("/^#/", $string)) {
                    if ($debug > 0) {printf ("string is %s<br>\n", $string);}
                    list ($junk1,$junk2,$v1a[$i],$v2a[$i],$v3a[$i]) = preg_split ("/\s+/", $string);
                    if ($debug > 0) {
                        printf ("number of points is %d<br>\n", $numpoints);
                        printf ("v1,v2,v3 are %d/%d/%d<br>\n", $v1a[$i], $v2a[$i], $v3a[$i]);
                    }
                    $p1 = min($v1a[$i], $v2a[$i]);
                    $p2 = max($v1a[$i], $v2a[$i]);
                    $lineseg1 = sprintf ("%d-%d", $p1, $p2);
                    $p1 = min($v2a[$i], $v3a[$i]);
                    $p2 = max($v2a[$i], $v3a[$i]);
                    $lineseg2 = sprintf ("%d-%d", $p1, $p2);
                    $p1 = min($v3a[$i], $v1a[$i]);
                    $p2 = max($v3a[$i], $v1a[$i]);
                    $lineseg3 = sprintf ("%d-%d", $p1, $p2);
                    $index = $i*3;
                    $drawlinesar[$index] = $lineseg1;
                    $drawlinesar[$index+1] = $lineseg2;
                    $drawlinesar[$index+2] = $lineseg3;
                    #	$drawlines = $drawlines . $lineseg1 . $lineseg2 . $lineseg3;
                }
            }

            sort($drawlinesar);
            $numseg = count($drawlinesar);
            $lastseg='';
            $hidelines = "";
            for ($i=0; $i<$numseg; $i++) {
                $seg = $drawlinesar[$i];
                if ($debug > 0) { printf ("Checking line segment %s<br>\n", $seg); }
                if ($seg == $lastseg) {
                    # This segment is drawn more than once, so it's an interior segment
                    $hidelines = $hidelines . ":" . $seg;
                }
                $lastseg = $seg;
            }
            #    $hidelines = preg_replace("/^,/", "", $hidelines);
            $hidelines = $hidelines . ":";
            if ($debug > 0) { printf ("We want to hide lines %s<br>\n", $hidelines); }

            for ($i=0; $i<$numtriangle; $i++) {
                #      $string = fgets($handle, 4096);
                #      if (!preg_match("/^#/", $string)) {
                #      if ($debug > 0) {printf ("string is %s<br>\n", $string);}
                #      $drawlines = preg_replace ("/^,/", "", $drawlines);



                $drawlines = '';
                if ($debug > 0) { printf ("value of drawlines is %s<br>\n", $drawlines); }
                $v1 = $v1a[$i];
                $v2 = $v2a[$i];
                $v3 = $v3a[$i];

                if ($debug > 0) { printf ("value of v1/v2/v3 is %d/%d/%d<br>\n", $v1, $v2, $v3); }
                $p1 = min($v1, $v2);
                $p2 = max($v1, $v2);
                $lineseg1 = sprintf ("%d-%d", $p1, $p2);
                if ($debug > 0) { printf ("value of lineseg1 is %s<br>\n", $lineseg1); }
                if (preg_match("/:$lineseg1:/", $hidelines)) {
                    $seg2hide = '12';
                    $drawlines = $drawlines . "," . $seg2hide;
                }

                $p1 = min($v2, $v3);
                $p2 = max($v2, $v3);
                $lineseg2 = sprintf ("%d-%d", $p1, $p2);
                if (preg_match("/:$lineseg2:/", $hidelines)) {
                    $seg2hide = '23';
                    $drawlines = $drawlines . "," . $seg2hide;
                }
                $p1 = min($v3, $v1);
                $p2 = max($v3, $v1);
                $lineseg3 = sprintf ("%d-%d", $p1, $p2);
                if (preg_match("/:$lineseg3:/", $hidelines)) {
                    $seg2hide = '31';
                    $drawlines = $drawlines . "," . $seg2hide;
                }
                if ($debug > 0) { printf ("value of drawlines is %s<br>\n", $drawlines); }
                $v1--;
                $v2--;
                $v3--;
                $v1y = $newlats[$v1];
                $v1x = $newlons[$v1];
                $v2y = $newlats[$v2];
                $v2x = $newlons[$v2];
                $v3y = $newlats[$v3];
                $v3x = $newlons[$v3];

                $drawlines = preg_replace ("/^,/", "", $drawlines);

                // Added 9/16/2009 Seth Daugherty
                // To invoke the dateline kludge below, we need to make all the points be in the same hemisphere
                // That is, we need to pick a hemisphere based on the first point and add/subtract 360 from
                // the other points to put them in the right hemisphere
                /*
                $is_positive = $v1x >= 0;
                $offset = 360;
                if(!$is_positive) $offset *= -1;

                // If the 2nd point isn't in the right hemisphere, switch its hemisphere
                if( (($v2x < 0) && $is_positive) || (($v2x >= 0) && !$is_positive)) {
                    $v2x += $offset;
                }
                // Same thing for the 3rd point
                if( (($v3x < 0) && $is_positive) || (($v3x >= 0) && !$is_positive)) {
                    $v3x += $offset;
                }
                */
                $tempv1x = $v1x;
                $tempv2x = $v2x;
                $tempv3x = $v3x;
                $is_positive = $tempv1x >= 0;
                $offset = 360;
                if(!$is_positive) $offset *= -1;

                // If the 2nd point isn't in the correct hemisphere, switch its hemisphere
                if( (($tempv2x < 0) && $is_positive) || (($tempv2x >= 0) && !$is_positive)) {
                    $tempv2x += $offset;
                }
                // Same thing for the 3rd point
                if( (($tempv3x < 0) && $is_positive) || (($tempv3x >= 0) && !$is_positive)) {
                    $tempv3x += $offset;
                }

                // The triangle crosses the Prime Meridian if the normalized triangle has a span < 180
                // Otherwise it is crossing the date line, so we need to invoke the cludge
                if((max($tempv1x, $tempv2x, $tempv3x) - min($tempv1x, $tempv2x, $tempv3x)) < 180) {
                    $v1x = $tempv1x;
                    $v2x = $tempv2x;
                    $v3x = $tempv3x;
                }

                $query_update = $database->prepare("INSERT INTO mailgeography (ruleid,v1y,v1x,v2y,v2x,v3y,v3x,drawlines) VALUES (:regionid,:v1y,:v1x,:v2y,:v2x,:v3y,:v3x,:drawlines)");

                // We don't check the query because it isn't essential
                $query_update->execute(array(
                    ':regionid' => $regionid,
                    ':v1y' => $v1y,
                    ':v1x' => $v1x,
                    ':v2y' => $v2y,
                    ':v2x' => $v2x,
                    ':v3y' => $v3y,
                    ':v3x' => $v3x,
                    ':drawlines' => $drawlines
                ));

                #
                # Note these special cases:
                #   If a triangle crosses the international date line, then enter it
                #   twice, once with negative values, and once with positive
                if (max($v1x,$v2x,$v3x) > 180) {
                    if ($debug > 0) {echo "invoking date line kluge for lon &gt; 180<br>\n"; }
                    $tempv1x = $v1x - 360;
                    $tempv2x = $v2x - 360;
                    $tempv3x = $v3x - 360;
                    $drawlines = '12,23,31'; // Extra kluge triangles should be heard and not seen
                    $query_update = $database->prepare("INSERT INTO mailgeography (ruleid, v1y, v1x, v2y, v2x, v3y, v3x, drawlines) VALUES (:regionid, :v1y, :v1x, :v2y, :v2x, :v3y, :v3x, :drawlines)");

                    // We don't check the query because it isn't essential
                    $query_update->execute(array(
                        ':regionid' => $regionid,
                        ':v1y' => $v1y,
                        ':v1x' => $v1x,
                        ':v2y' => $v2y,
                        ':v2x' => $v2x,
                        ':v3y' => $v3y,
                        ':v3x' => $v3x,
                        ':drawlines' => $drawlines
                    ));

                }
                if (min($v1x,$v2x,$v3x) < -180) {
                    if ($debug > 0) {echo "invoking date line kluge for lon &lt; 180<br>\n"; }
                    $tempv1x = $v1x + 360;
                    $tempv2x = $v2x + 360;
                    $tempv3x = $v3x + 360;
                    $drawlines = '12,23,31'; // Extra kluge triangles should be heard and not seen
                    $query_update = $database->prepare("INSERT INTO mailgeography (ruleid, v1y, v1x, v2y, v2x, v3y, v3x, drawlines) VALUES (:regionid, :v1y, :v1x, :v2y, :v2x, :v3y, :v3x, :drawlines)");

                    // We don't check the query because it isn't essential
                    $query_update->execute(array(
                        ':regionid' => $regionid,
                        ':v1y' => $v1y,
                        ':v1x' => $v1x,
                        ':v2y' => $v2y,
                        ':v2x' => $v2x,
                        ':v3y' => $v3y,
                        ':v3x' => $v3x,
                        ':drawlines' => $drawlines
                    ));

                }
                #
                # End of date line kluge
                #
            }

            if ($debug > 0) {printf ("Finished with polygon %d<br>", $k);}
        }
    }
	
	/**
	 * Get a response data format
	 */
	function getFormattedResponse($returnCode, $description) {
		$response['code'] = $returnCode;
		$response['message'] = $description;
		return $response;
	}

	class ResponseConstant {
		const INVALID_INPUT = 1;		
		
		const HTTP_BAD_REQUEST = 400;
	}
/**
 *	Unsubscribe the user
 *	This function will delete the user's account along with all their associated emails and profiles
 *	@param userid Id of user to be deleted. This id will be checked against the userid stored in $_SESSION to help ensure that users only unsubscribe themselves
 *	@param confirm Boolean. Only required if userid is not the same as the userid stored in $_SESSION
**/
function unsubscribe($userid, $confirm=false) {

	global $database;
	global $CONFIG;

	$USER_INFO = $_SESSION['USER_INFO'];

	$failedText =  sprintf("<h3 class=\"alert error\">Failed to delete your account.</h3>\n<p>Please email the " .
		       "<a href=\"mailto:ensadmin@usgs.gov\">ENS Admin</a> with your username and userid (%s) " .
		       "to have your account deleted manually.</p>\n",  $userid);


	// If the user is trying to unsubscribe someone else and didn't set the confirm flag, don't let them continue
	if($userid != $USER_INFO['id'] && !$confirm) {
		print $failedText;
		return;
	}

	$success = true; // Assume success
	// Get a list of custom profiles that we need to delete the region and geo entries for
	$query_customs = $database->prepare("SELECT mp.regionid FROM mailparams AS mp, mailregions AS mr WHERE mr.geo_id=mp.regionid AND mr.geo_flag='custom' AND mp.userid=:userid");
	$rs_customs = $query_customs->execute (array(':userid' => $userid));
	while ($row = $query_customs->fetch(PDO::FETCH_ASSOC)) {

			// Delete the entry in mailregions
		$query_delete = $database->prepare("DELETE FROM mailregions WHERE geo_id=:regionid");
		$success = $query_delete->execute (array(':regionid' => $row['regionid']));

			// Delete the entry in mailgeography
		$query_delete = $database->prepare("DELETE FROM mailgeography WHERE ruleid=:regionid");
		$success = $query_delete->execute (array(':regionid' => $row['regionid']));
	}  // End: while()	} // END: if(!$rs_customs...)

	// Delete the mailparams, mailaddresses, email_param_bridge, and mailusers entries
	$query_eid = $database->prepare("SELECT eid from mailaddresses where uid=:userid");
	$success = $query_eid->execute(array(':userid' => $userid));
	while ($row = $query_eid->fetch(PDO::FETCH_ASSOC)) {
		$query_delete = $database->prepare("DELETE FROM email_param_bridge where emailid=:eid");
		$query_delete->execute(array(':eid' => $row['eid']));
	}

	$query_delete = $database->prepare("DELETE FROM mailparams where userid=:userid");
	$success = $query_delete->execute (array(':userid' => $userid));

	$query_delete = $database->prepare("DELETE FROM mailaddresses WHERE uid=:userid");
	$success = $query_delete->execute (array(':userid' => $userid));

	$query_delete = $database->prepare("DELETE FROM mailusers WHERE id=:userid LIMIT 1");
	$success = $query_delete->execute (array(':userid' => $userid));

	if($success) {
		unset($_SESSION['USER_INFO']);
		unset($_SESSION['USER_EMAILS']);
		unset($_SESSION['USER_JS_ENABLED']);
		$_SESSION = array();
		#header('Location: login');

	?>
		<script type="text/javascript" language="javascript">
			<!-- // Hide script from older browsers
			alert('Your account has been deleted.');
			window.location = "<?php echo $CONFIG['MOUNT_PATH'] ?>/index.php";
			// -->
		</script>
<?php
	} else {
		print $failedText;
	} // END: if($success)

}

/**
 * The unsubscribe.inc.php page doesn't require the user to be logged in (actually there is
 * no link to get there if the user is logged in), so we need to validate their credentials then unsubscribe.
 */
function login_and_unsubscribe( $username, $hashpass ) {
	global $database;

  $query_exists = $database->prepare('
		SELECT count(id) from mailusers WHERE
		username = :username AND hashpasswd = :hashpass');

	$rs_validate = $query_exists->execute(array(
		':username' => $username,
		':hashpass' => $hashpass
		));
		//printf ("email is %s rs_info is $rs_info<br>\n", $email);
	$info = $query_exists->fetch(PDO::FETCH_ASSOC);
	$num = $info['count(id)'];

  if ($num == 0) {
		print '<h3 class="alert error">Incorrect username/password.</h3>';
	}
	if ($num == 1) {
		$query_validate = $database->prepare('
			SELECT id, username
			FROM mailusers
			WHERE
			username = :username AND
			hashpasswd = :hashpass
		');

		$rs_validate = $query_validate->execute(array(
			':username' => $username,
			':hashpass' => $hashpass
		));
		$userInfo = $query_validate->fetch(PDO::FETCH_ASSOC);

		// We are logged in, so unsubscribe.
		$userId = $userInfo['id'];
		unsubscribe($userId, true);
	} else {
		if ($num > 1) {
			print ("<h4>There is something wrong. Please contact <a href=\"mailto:ensadmin@ens.usgs.gov\">ensadmin@ens.usgs.gov</a>
			so we can fix the problem.</h4>");
		}
	}
}

// Print out a simple form for each unconfirmed email address associated with $userid
function get_pending_emails( $userid ) {
	global $database;

	/*
	$pending_query = "
			SELECT *
			FROM mailconfirm
			WHERE
				uid=$userid
			";
	*/
	// 10/04/11 -- EMM: Now that we accept any previous confirmation number,
	// there may be quite a few requests in the mailconfirm table for the same
	// email address. Don't want to further confuse users, so only show one
	// confirm box for each email.
	$pending_query = $database->prepare("SELECT distinct email FROM mailconfirm WHERE uid = :userid");

	if ($pending_result = $pending_query->execute(array(':userid' => $userid))) {

		//if (count($pending_result) > 0) {
			//$count = count($pending_result);
			$index = 0;

			while ($pending = $pending_query->fetch(PDO::FETCH_ASSOC)) {
				if ($index == 0){
					print "<div class=\"alert warning seven column\" id=\"confirm_wrapper\" >";
					print ("<h3>You have unconfirmed email addresses</h3>");
					$index++;
				}

				print "<form action=\"regaddr\" method=\"get\">\n";
				print "<input type=\"hidden\" name=\"mode\" value=\"confirm\" />\n";
				print "<input type=\"hidden\" name=\"redirect\" value=\"y\" />\n";
				printf ("<input type=\"hidden\" name=\"address\" value=\"%s\" />\n", $pending['email']);
				printf ("<label>%s", $pending['email']);
				print "<input type=\"text\" name=\"confnum\" size=\"5\" maxlength=\"3\" value=\"\" />\n";
				print "</label>\n";
                
				print "<input type=\"hidden\" id=\"ens_confirm_token\" name=\"ens_confirm_token\" value=\"" .
                    htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') . "\" />\n";
                
				print "<button type=\"submit\" name=\"confirm\">Confirm</button>\n";
				print "<button type=\"submit\" name=\"remove_pending\">Remove</button>\n";
				print "<a href=\"javascript:void(null);\" name=\"resend_code\">Resend code</a>\n";
				print "</form>\n";
				print "<br />\n";
			}
			if ($index > 0) {
			?>
			<a href="javascript:void(null);" id="hide_confirm">Hide this box</a>
			</div>
			<script type="text/javascript">
                //<![CDATA[
                $('button[name="remove_pending"]').click( function(e) {
                    e.preventDefault();
                    var email = $('input[name="address"]', $(this.parentNode)).val();

                    $.ajax({
                        type: "GET",
                        url: "inc/ajax/email_ajax.inc.php",
                        data: {mode: "remove_pending", email: escape(email)},
                        headers: getCSRFHeader($("#ens_confirm_token").val()),
                        success: function(result) {
                            if(result == 'success') {
                                $(e.currentTarget.parentNode).remove();

                                if (document.querySelectorAll('button[name="remove_pending"]').length === 0) {
                                    $("#confirm_wrapper").slideUp("normal");
                                }
                            } else {
                                $("#confirm_wrapper").append(result);
                            }
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown){
                        ensAlert("Invalid Request", "Failed to remove email address. Please try again.");
                    });
                });

                $('a[name="resend_code"]').click(function (e) {
                    e.preventDefault();
                    var email = $('input[name="address"]', $(this.parentNode)).val();
                    var link = $(this);
                    
                    $.ajax({
                        type: "POST",
                        url: "inc/ajax/email_ajax.inc.php",
                        data: { mode: "resend_code", email: escape(email) },
                        headers: getCSRFHeader($("#ens_confirm_token").val()),
                        success: function(result) {
                            if (result.indexOf('success') != -1 ||
                                    result.indexOf('Success') != -1) {
                                link.after(' <span class="alert success">Sent new code</s`pan>');
                            } else {
                                link.after(' <span class="alert error">Failed to send code</span>');
                            }
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown){
                        ensAlert("Invalid Request", "Failed to send the code. Please try again.");
                    });
                });

				$("#hide_confirm").click(function(e) {
					e.preventDefault();
					$("#confirm_wrapper").slideUp("normal");
				});
				//]]>
				</script>
			<?php
		}
	}
}

function calculate_median($arr) {
    sort($arr);
    $count = count($arr); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $arr[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $arr[$middleval];
        $high = $arr[$middleval+1];
        $median = (($low+$high)/2);
    }
    return $median;
}

function admin_email ($email, $subject, $msgtext, $headers, $id) {

    global $database;

// The 'id' is an arbitrary number to identify this message;
#    printf ("admin_email was called<br>");

    $fullmessagetext = "To: %address\r\nSubject: %subjstr\r\n" . $headers . "\r\n\r\n" . $msgtext;
    //$fullmessagetext = "This is a test";
    $sql = $database->prepare("INSERT INTO msg_templates (event_id,template_key,version,subjectstr,template_text) values (:id,'0004',1,:subject,:fullmessagetext)");

    $result = $sql->execute(array(
			':id' => $id,
			':subject' => $subject,
			':fullmessagetext' => $fullmessagetext
		));

    $sql = $database->prepare("INSERT into mailqueue (
      eid,
      event_id,
      template_key,
      version,
      email,
      messagetext,
      defertime,
      status,
      priority,
      hostname
    ) values (
      0,
      :event_id,
      '0004',
      1,
      :email,
      '',
      0,
      'Q',
      1,
      '')"
    );

    $result = $sql->execute(array(
			':event_id' => $id,
			':email' => $email
		));
}

?>
