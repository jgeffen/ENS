<?php
/* This file contains several ways to pass information back to the map controller page */
include_once 'functions.inc.php';
include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once("../maps_functions.inc.php");
include_once("../textdefs0.inc.php");

session_start();

$USER_INFO = $_SESSION['USER_INFO'];
$USER_EMAILS = $_SESSION['USER_EMAILS'];
function check_params($type) {
    // Set up some parameters for valid_params() to check
    // valid_params makes all these values global to be able to read them
    global $depth_min, $depth_max, $addressflags, $lat_min, $lat_max, $lon_min, $lon_max, $clat, $clon, $radius;

}

// return the javascript necessary to add a predefined profile to the map
if (isset($_POST['mode']) && $_POST['mode'] == 'get_predefined') {
    validateCSRF();
    
	$id = $_POST['id'];
	$color = $_POST['color'];

	// Check valid integer id and matching hex color
	if (is_numeric($id) && preg_match('/^#[a-f0-9]{6}$/i', $color)) {
		$id = intval($id);

		if ($id > 0) {
			$profile = new PolyProfile($id, $database, "undefined", "new_profile", $color);
			print $profile->javascript(false);
			print "new_poly = new_profile;";
		} else {
			http_response_code(ResponseConstant::HTTP_BAD_REQUEST);
			print json_encode(getFormattedResponse(ResponseConstant::INVALID_INPUT, 'Invalid identifier'));
		}
	} else {
		http_response_code(ResponseConstant::HTTP_BAD_REQUEST);
		print json_encode(getFormattedResponse(ResponseConstant::INVALID_INPUT, 'Invalid parameters'));
	}
}

// used for "Show more options" for a circle
if(isset($_POST['mode']) && $_POST['mode'] == 'get_places') {
    $places = file('bigplaces');
    foreach($places as $place) {
        $name = substr($place, 19);
        if($placename == $name)
                $selected = "selected=\"selected\"";
        else
            $selected = "";

            $name = utf8_encode($name);
            printf("\t\t<option value=\"%s\"%s>%s</option>\n", $name, $selected, $name);
    }
}
/*
if(isset($_POST['mode']) && $_POST['mode'] == 'get_emails') {
	$USER_INFO = $_SESSION['USER_INFO'];
	$pid = $_POST['pid'];
	$user_id = $USER_INFO['id'];
	$query_emaillist = "
		SELECT
			emaillist
		FROM
			mailparams
		WHERE
			pid=$pid
		LIMIT 1
		";

	$query_emaillist_rs = mysql_query($query_emaillist, $database);
	$emaillist = mysql_fetch_assoc($query_emaillist_rs);
	$emaillist = $emaillist['emaillist'];

	$query = "
		SELECT
			ma.*
		FROM
			mailaddresses AS ma
		WHERE
			ma.uid = $user_id
		ORDER BY
			ma.enum
		";
	$query_rs = mysql_query($query, $database);

	while($email = mysql_fetch_assoc($query_rs) ) {
 	       $enum = strtoupper(dechex($email["enum"]));
               $emailflags = split(",", $emaillist);
               if(in_array($enum, $emailflags) || $emaillist=='')
        	       $checked = "checked=\"checked\" ";
               else
                       $checked = "";

		print "<div>";
		print $email['email'] . ' <input type="checkbox" name="email[]" value="' . $enum . '" ' . $checked . '/>';
		print "</div>";
	}
}
*/
if(isset($_GET['mode']) && $_GET['mode'] == 'delete_profile_confirm') {
    $pid = $_GET['pid'];
    $profile_name = $_GET['name'];
?>
        <div id="delete_wrapper" style="text-align: center">
        <form action="inc/ajax/profile_ajax.inc.php" method="get" id="delete_profile_confirm">
                <h3>Are you sure you want to remove this region (<?php print $profile_name; ?>)?</h3>
                <p>You will no longer receive notification messages for earthquakes in this region. This cannot be undone.</p>
                
                <input type="hidden" id="ens_profile_delete_token" name="ens_profile_delete_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
                
                <button type="submit" class="thickbox" id="submit_delete_profile" alt="inc/ajax/profile_ajax.inc.php?mode=delete_profile&pid=<?=$pid?>">Yes, delete it</button>
                <button onclick="tb_remove()">No, don't remove it</button>
        </form>
        </div>

        <script type="text/javascript">
            $("#delete_profile_confirm").submit( function(e) { e.preventDefault(); });
                $("#submit_delete_profile").click( function(e) {
                    e.preventDefault();

                    $.ajax({
                        type: "GET",
                        url: "inc/ajax/profile_ajax.inc.php",
                        data: {mode:"delete_profile", pid:<?=$pid?>},
                        headers: getCSRFHeader($("#ens_profile_delete_token").val()),
                        success: function(html) {
                            $('#delete_wrapper').html(html);
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown){
                        ensAlert("Invalid Request", "Failed to delete a profile. Please try again.");
                    });
                });
        </script>

<?php
}

if(isset($_GET['mode']) && $_GET['mode'] == 'delete_profile') {
    validateCSRF();
        
	$success = true;
    $regionid = param('regionid');
    $pid = $_GET['pid'];

	// get the region for this profile
	$query_select = $database->prepare('
		SELECT
			mp.regionid,
			mr.geo_type
		FROM mailparams mp, mailregions mr
		WHERE
			pid=:pid AND
			userid = :id AND
			mr.geo_id=mp.regionid
		LIMIT 1
		');

	$query_select->execute(array(
		':pid' => $pid,
		':id' => $USER_INFO['id']
	));
	$row = $query_select->fetch(PDO::FETCH_ASSOC);
	$regionid = $row['regionid'];
	$geo_type = $row['geo_type'];
	$query_select = null;

	if( $regionid != '') {
		$query_delete = $database->prepare('DELETE FROM mailparams WHERE pid=:pid AND userid=:id LIMIT 1');
		$success = ($success && $query_delete->execute(array(
			':pid' => $pid,
			':id' => $USER_INFO['id']
		)));

	    if($success && $geo_type != "canned" && $query_delete->rowCount() == 1) {
			$query_delete = $database->prepare("DELETE FROM mailregions WHERE geo_id=:geo_id AND geo_flag != 'canned' LIMIT 1");
			$success = ($success && $query_delete->execute(array(
				':geo_id' => $regionid
			)));

			if($success && $query_delete->rowCount() == 1) {
				$query_delete = $database->prepare("DELETE FROM mailgeography WHERE ruleid=:regionid");
				$success = ($success && $query_delete->execute(array(
					':regionid' => $regionid
				)));
			}
		}

		$query_delete = null;
	} else {
		$success = false;
	}

    if($success) {
    ?>
		<h3 class=\"alert success\">Profile successfully deleted</h3>
		<script type="text/javascript">
            // remove overlay
            profiles_array[<?=$pid?>].hide();

            // remove from profiles_array
            profiles_array[<?=$pid?>] = null;
			delete profiles_array[<?=$pid?>];

		    // remove entry from profiles list
		    $("#"+<?=$pid?>).remove();

            document.getElementById("profile_form").reset();
            $("#more_options").hide();
            $("#profile_info .field_container").hide("slow");

		    focus_profile = null;
		    deactivate_all();
		    show_all();
		    original = null;

		    setTimeout("tb_remove()", 2000);
		</script>
	<?php

	} else {
        print ("<h3 class=\"alert error\">Profile Deletion Failed!</h3>\n");
    }

}

if (isset($_POST['mode']) && $_POST['mode'] == 'save') {
    validateCSRF();
        
    $USER_INFO = $_SESSION['USER_INFO'];

    $success = true;

    $regionid = param('regionid');
    $type = param('type');
    if ($type == 'custom_xml') {
        $type = 'polygon';
    }
    $canned = param('canned');
    $depth_min = param('depth_min');
    $depth_max = param('depth_max');
    $addressflags = param('addressflags');
    $lat_min = param('lat_min');
    $lat_max = param('lat_max');
    $lon_min = param('lon_min');
    $lon_max = param('lon_max');
    $clat = param('clat');
    $clon = param('clon');
    $radius = param('radius');
    $pid = param('profileid');
    $comments = param('regionname');
    $mag_night = param('mag_night');
    $mag_min = param('mag_min');
    $active = param('active');
    $networks = param('networks');
    #$temp = 	param('points_json');
    $points = json_decode(param('points_json'), true);
    $xmltext = param('xmltext');

    #printf ("ID is %d, JSON points are %s<br>\n", $pid, $temp);


    $valid = valid_params($type);
    // Temporary hack to prevent valid_params from modifying $clon and $clat
    $clat = param('clat');
    $clon = param('clon');

    if ($valid) {
        if ($pid == -1) {
            // If the profile we're saving is a new profile, find the next pid
            $query_pid = $database->prepare("
                INSERT INTO
                    mailparams
                (
                    userid
                )
                VALUES
                (
                    :id
                )
                ");
            if ($query_pid_rs = $query_pid->execute(array(
                ':id' => $USER_INFO['id']
                    ))) {
                $pid = $database->lastInsertId();
            } else {
                print "There was an error saving the profile";
                return;
            }


            if ($type != 'canned' && $regionid == -1) {
                // If this is a custom profile, we need to create the mailregion also
                $query_geo_id = $database->prepare("
                    INSERT INTO
                        mailregions
                    (
                        geo_type,
                        geo_flag,
                        placename
                    ) VALUES (
                        :type,
                        'custom',
                        :comments
                    )
                    ");

                if (!($query_geo_id_rs = $query_geo_id->execute(array(
                    ':type' => $type,
                    ':comments' => $comments
                        )))) {
                    print "There was a problem saving your profile";
                    return;
                }
                $regionid = $database->lastInsertId();

                $query_update = $database->prepare("
                            UPDATE mailparams
                                SET
                                    regionid = :regionid
                                WHERE
                                        userid=:id AND
                                        pid=:pid
                                LIMIT 1
                                ");

                if (!$query_update->execute(array(
                            ':regionid' => $regionid,
                            ':id' => $USER_INFO['id'],
                            ':pid' => $pid
                        ))) {
                    print "There was a problem saving your profile";
                    return;
                }
            }
        }

        //$emaillist = join(",", $addressflags);

        if ($canned == 'canned') {
            $cannedrule = $_POST['regionid'];

            $query_update = $database->prepare("
                UPDATE mailparams
                SET
                    active=:active,
                    networks=:networks,
                    regionid=:regionid,
                    depth_min=:depth_min,
                    depth_max=:depth_max,
                    mag_min=:mag_min,
                    mag_night=:mag_night,
                    comments=:comments,
                    xmltext=''
                WHERE
                    pid=:pid AND
                    userid=:id");

            $success = ($success && ($query_update->execute(array(
                        ':active' => $active,
                        ':networks' => $networks,
                        ':regionid' => $cannedrule,
                        ':depth_min' => $depth_min,
                        ':depth_max' => $depth_max,
                        ':mag_min' => $mag_min,
                        ':mag_night' => $mag_night,
                        ':comments' => $comments,
                        ':pid' => $pid,
                        ':id' => $USER_INFO['id']
            ))));
        } else {
            if ($valid) {

                // First, look to see if the region exists and is not canned
                // This is a check to make sure that nobody can hack the $_POST
                // info to modify a canned profile

                $query_check = $database->prepare("
                    SELECT *
                    FROM mailregions
                    WHERE
                        geo_id=:regionid AND
                        geo_flag='custom'
                    LIMIT 1
                    ");
                $result = $query_check->execute(array(
                    ':regionid' => $regionid
                ));
                if (!$result && $regionid != 0) {
                    // The region is predefined, so throw an error
                    print "There was a problem saving the region";
                    return;
                }

    // Change the entry in the mailparams table
                $query_update = $database->prepare("
                    UPDATE mailparams
                    SET
                        active=:active,
                        networks=:networks,
                        depth_min=:depth_min,
                        depth_max=:depth_max,
                        mag_min=:mag_min,
                        mag_night=:mag_night,
                        comments=:comments,
                        xmltext=''
                    WHERE
                        pid=:pid AND
                        userid=:id");

                $success = ($success && $query_update->execute(array(
                            ':active' => $active,
                            ':networks' => $networks,
                            ':depth_min' => $depth_min,
                            ':depth_max' => $depth_max,
                            ':mag_min' => $mag_min,
                            ':mag_night' => $mag_night,
                            ':comments' => $comments,
                            ':pid' => $pid,
                            ':id' => $USER_INFO['id']
                )));

                // See if they are trying to convert a canned region to a custom
                if ($regionid == 0) {
                    $query_insert = $database->prepare("
                        INSERT INTO mailregions
                        (
                            geo_type,
                            geo_flag,
                            placename,
                            lat_max,
                            lat_min,
                            lon_max,
                            lon_min
                        )
                        VALUES (
                            :geo_type,
                            :geo_flag,
                            :comments,
                            :lat_max,
                            :lat_min,
                            :lon_max,
                            :lon_min
                        )");
                    if ($query_insert->execute(array(
                                ':geo_type' => $type,
                                ':geo_flag' => $canned,
                                ':comments' => $comments,
                                ':lat_max' => $lat_max,
                                ':lat_min' => $lat_min,
                                ':lon_max' => $lon_max,
                                ':lon_min' => $lon_min
                            ))) {
                        $regionid = $database->lastInsertId;

                        $query_update = $database->prepare("
                            UPDATE mailparams
                            SET
                                regionid = :regionid
                            WHERE
                                userid=:id AND
                                pid=:pid
                            LIMIT 1
                            ");
                        if (!$query_update->execute(array(
                                    ':regionid' => $regionid,
                                    ':id' => $USER_INFO['id'],
                                    ':pid' => $pid
                                ))) {
                            print "There was a problem saving your profile";
                            return;
                        }
                    }
                } else {
                    $query_clean_geo = $database->prepare("
                        DELETE FROM mailgeography
                        WHERE ruleid=:regionid");

                    $query_clean_geo->execute(array(
                        ':regionid' => $regionid
                    ));
                }



                if ($type == 'rectangle') {
                    if (($lon_min < -180) || ($lon_max > 180)) {
                        // We crossed the Date Line!!!!
                        // lon_min is the west side of the
                        // rectangle. If we crossed the Date Line
                        // it will be lower than -180. Similarly,
                        // if the rectangle was drawn on the right
                        // side of the map, lon_max will be > 180.
                        // -sms 10-feb-2014
                        $query_update = $database->prepare("INSERT INTO mailgeography
                            (ruleid, v1y, v1x, v2y, v2x) VALUES
                            (:regionid,:lat_min,:lon_max,:lat_max,:lon_min)");

                        $success = ($success && $query_update->execute(array(
                                    ':regionid' => $regionid,
                                    ':lat_min' => $lat_min,
                                    ':lon_max' => $lon_max,
                                    ':lat_max' => $lat_max,
                                    ':lon_min' => $lon_min
                        )));
                        if ($lon_min < -180) {
                            $lon_min = $lon_min + 360;
                            $lon_max = $lon_max + 360;
                        } else {
                            $lon_min = $lon_min - 360;
                            $lon_max = $lon_max - 360;
                        }

                        $query_update = $database->prepare("INSERT INTO mailgeography
                            ( ruleid, v1y, v1x, v2y, v2x, drawlines ) VALUES
                            ( :regionid, :lat_min, :lon_max, :lat_max,:lon_min ,'12,23,31')");

                        $success = ($success && $query_update->execute(array(
                                    ':regionid' => $regionid,
                                    ':lat_min' => $lat_min,
                                    ':lon_max' => $lon_max,
                                    ':lat_max' => $lat_max,
                                    ':lon_min' => $lon_min
                        )));
                    } else {

                        $query_update = $database->prepare("INSERT INTO mailgeography
                            ( ruleid, v1y, v1x, v2y, v2x ) VALUES
                            ( :regionid, :lat_min, :lon_max, :lat_max,:lon_min )");

                        $success = ($success && $query_update->execute(array(
                                    ':regionid' => $regionid,
                                    ':lat_min' => $lat_min,
                                    ':lon_max' => $lon_max,
                                    ':lat_max' => $lat_max,
                                    ':lon_min' => $lon_min
                        )));
                    }
                } else if ($type == 'circle') {
                    $query_update = $database->prepare("
                                    INSERT INTO mailgeography
                                    (
                                            ruleid,
                                            v1y,
                                            v1x,
                                            v2y
                                    ) VALUES (
                                            :regionid,
                                            :clat,
                                            :clon,
                                            :radius
                                    )");
                    // Sanity check -sms 2017-04-18
                    $dateline = 0;  // Assume we're not crossing the Date Line
                    if ($clon < -180) {
                        $clon = $clon + 360;
                    }
                    if ($clon > 180) {
                        $clon = $clon - 360;
                    }
                    $lat_range = (($radius * 1.6093471) / 40075) * 360;
                    $lat_min = $clat - $lat_range;
                    $lat_max = $clat + $lat_range;
                    $lon_range = ((($radius * 1.6093471) / 40075) * 360) * cos($clat);
                    $lon_min = $clon - $lon_range;
                    $lon_max = $clon + $lon_range;
                    if ((abs($lon_max) > 180) || (abs($lon_min) > 180)) {
                        $dateline = 1;
                    }

                    $success = ($success && $query_update->execute(array(
                                ':regionid' => $regionid,
                                ':clat' => $clat,
                                ':clon' => $clon,
                                ':radius' => $radius
                    )));
                    if ($dateline == 1) {
                        // We crossed the Date Line, so define it twice in east and West
                        if ($clon > 0.0) {
                            $clon = $clon - 360.0;
                        } else {
                            $clon = $clon + 360.0;
                        }
                        $success = ($success && $query_update->execute(array(
                                    ':regionid' => $regionid,
                                    ':clat' => $clat,
                                    ':clon' => $clon,
                                    ':radius' => $radius
                        )));
                    }
                } else if ($type == 'polygon') {

                    $points_arr = $points;

                    if ($xmltext != '') {
                        $xml = $xmltext;
                        // The following line will also set $lats and $lons on success
                        $lats = array();
                        $lons = array();
                        ens_xml_parse($xmltext);
                        $latlist = $lats;
                        $lonlist = $lons;
                    } else {
                        $pointslist = get_point_list('all', $points_arr);
                        $xml = create_xml($pointslist);
                        $latlist = get_point_list('lat', $points_arr);
                        $lonlist = get_point_list('lon', $points_arr);
                    }
                    $numpoints = sizeof($latlist);

                    // splitpoly runs the necessary queries to add triangles to mailgeography
                    splitpoly($regionid, $latlist, $lonlist, $numpoints, $triangle_path, 0);
                    ##splitpoly($regionid, $lonlist, $latlist, $numpoints, $triangle_path, 0);
                    // If we have xml for the polygon, we need to save it
                    if ($xml != '') {
                        $query_update = $database->prepare("
                            UPDATE mailparams
                            SET
                                xmltext = :xml
                            WHERE
                                pid=:pid AND
                                userid=:id
                            LIMIT 1
                        ");

                        // We don't check the query because it isn't essential
                        $query_update->execute(array(
                            ':xml' => $xml,
                            ':pid' => $pid,
                            ':id' => $USER_INFO['id']
                        ));
                    }
                }
            }// end if($type=='polygon')
            // One more generic update for all region types
            $query_update = $database->prepare("
                        UPDATE mailregions
                            SET
                                placename=:comments,
                                lat_max=:lat_max,
                                lat_min=:lat_min,
                                lon_max=:lon_max,
                                lon_min=:lon_min
                            WHERE
                                    geo_id=:regionid");
            $success = ($success && $query_update->execute(array(
                        ':comments' => $comments,
                        ':lat_max' => $lat_max,
                        ':lat_min' => $lat_min,
                        ':lon_max' => $lon_max,
                        ':lon_min' => $lon_min,
                        ':regionid' => $regionid
            )));
        } //if ($canned == 'canned') else 

        // Now take care of the bridge table -sms
        $query_bridge = $database->prepare("DELETE from email_param_bridge
            where paramid=:pid");
        $success = ($success && $query_bridge->execute(array(
                    ':pid' => $pid
        )));

        $profile = null;

        foreach ($addressflags as $addressid) {
            $query_bridge = $database->prepare("INSERT into email_param_bridge
                (emailid,paramid) values (:addressid,:pid)");
            $success = ($success && $query_bridge->execute(array(
                        ':addressid' => $addressid,
                        ':pid' => $pid
            )));

            if ($success) {
                $profile = array('profileid' => $pid, 'regionid' => $regionid);
            }
        }

        // print JSON with updated region
        print json_encode($profile);
    } // if ($valid)
} // end of save

function get_points($regions) {
    // $regions is a multi-dimensional array, but for this purpose, make it just a list of points
    $points_arr = array();
    foreach( $regions as $points) {
        $points = substr($points, 1, -1);
        $arr = split('\),\(', $points);
        foreach($arr as $key => $val) {
            $arr[$key] = split(', ', $val);
        }
        array_push($points_arr, $arr);
    }
    return $points_arr;
}

function get_point_list( $type, $points_arr ) {
    $latlon = array();
    foreach($points_arr as $points) {
         #       foreach($points as $point) {
                    if($type == 'lat' ) {
                array_push($latlon, $points[0]);
            }
            else if($type == 'lon' ) {
                array_push($latlon, $points[1]);
            }
            else if($type == 'all') {
                array_push($latlon, $points);
            }
    #   }
        }
    return $latlon;
}

function create_xml($points_arr) {
    if(empty($points_arr)) {
        return '';
    }
    
    $xml = "<region>\n";
    foreach($points_arr as $key => $point) {
        $xml .= sprintf("<point index=\"%s\">", $key);
        $xml .= sprintf("<lat>%4.3f</lat>", $point[0]);
        $xml .= sprintf("<lon>%4.3f</lon>", $point[1]);
        $xml .= "</point>\n";
    }
    $xml .= "</region>";
    return $xml;
}


?>
