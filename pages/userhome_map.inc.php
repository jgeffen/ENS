<?php

$TITLE = 'Welcome to the Earthquake Notification Service';

include_once("inc/textdefs0.inc.php");

#print ("In userhome_map.php<br>\n");

// If they were trying to confirm their address, we need to redirect to the
// regaddr page.
if( isset($_SESSION['confnum']) && isset($_SESSION['address']) && $_SESSION['confnum'] != '') {
	$location = sprintf('regaddr?mode=confirm&address=%s&confnum=%s&newaccount=%s',
		$_SESSION['address'], $_SESSION['confnum'], $_SESSION['newaccount']) ;

	if( !headers_sent() ) {
		header(sprintf("Location: %s", $location));
		return;
	}
	else {
		print "You are being redirected to the Earthquake Notification Service homepage. <a href='userhome_map'>Go there now</a>";
		print sprintf("<script type='text/javascript'>
			location.href='%s';
			</script>", $location );

		return;
	}
}


if(!isset($FOO)) {
	#print ("Including inc/maps_functions.inc.php<br>\n");
	include_once("inc/maps_functions.inc.php");
	$FOO = true;
}


//	  if(!isset($FOOBAR)) {
// Fix these because of the new include...
$WEB_PATH = str_replace("/inc", "", $WEB_PATH);
$FILE_PATH = str_replace("/inc", "", $FILE_PATH);


$pid = param('pid');
$day_begin = param('day_begin');
$day_end = param('day_end');
$debug = 0; // Prevent any error messages
$showlines = 0;
$js_buffer = '';
$js_buffer_immediate = '';
$profile_list = array();
$placename = '';

#print_r ($_POST);
#print_r ($_SESSION);

$color_array = array("#77fe87" => "#00ea1d", "#80bbfd" => "#1071de", "#a883fd"=> "#5315e1", "#fd77d8"=> "#e700a7", "#ff9e77" =>"#ff4900", "#ffe877" => "#ffd500", "#dfff77" => "#c0fb00", "#77fdad"=> "#00e45c", "#ff778a"=>"#fa0023", "#ffcabd" => "#ff9e88", "#ffeaa5"=>"#ffe388", "#9cd2c5" =>"#58a692", "#a7add7"=>"#6871b0");
$color = reset($color_array);

$userid = $USER_INFO['id'];


/**
 *	Figure out the correct zoom level and center for the map
 *	Use the lon_max, lon_min, lat_max, lat_min fields to calculate the maximum span

 2014-02-06: A new method, fit_all() in Profile.js handles this

**/
/*
$query = "SELECT
		max(mr.lon_max) as lon_max,
		min(mr.lon_min) as lon_min,
		max(mr.lat_max) as lat_max,
		min(mr.lat_min ) as lat_min
	FROM
		mailregions mr,
		mailparams mp
	WHERE
		mp.userid=$userid AND
		mp.regionid=mr.geo_id
	LIMIT 1";

#$query_rs = mysql_query($query, $database);
#$row = mysql_fetch_assoc($query_rs);
#printf ("SQL is %s<br>\n", $query);
$query_rs = $database->query($query);
$row = $query_rs->fetch(PDO::FETCH_ASSOC);

$lat_min = $row['lat_min'];
$lat_max = $row['lat_max'];
$lon_min = $row['lon_min'];
$lon_max = $row['lon_max'];

$latcenter = $lat_min + ($lat_max - $lat_min)/2;
$loncenter = $lon_min + ($lon_max - $lon_min)/2;

$latspan = $lat_max - $lat_min;
$lonspan = $lon_max - $lon_min;

// If the user has no profiles,
// or they have some strange profiles that don't have mins and maxes stored
// zoom to fit the entire map
if($latspan == 0 && $lonspan==0) {
	$latspan = 180;
}

	$FOOBAR = true;

	$js_buffer .= sprintf ("var midLat = (%3.3f + %3.3f) / 2;\n", $lat_max, $lat_min);
	$js_buffer .= sprintf ("var zoom = Math.max((%3.3f),(%3.3f));\n", $lonspan, $latspan);
	$js_buffer .= sprintf ("zoom = zoom / 0.00539 / Math.cos(midLat / 180 * Math.PI);\n");
	$js_buffer .= sprintf ("zoom = Math.log(zoom) / Math.LN2;\n");
	$js_buffer .= sprintf ("zoom = Math.ceil(zoom);\n");
	$js_buffer .= sprintf ("zoom = 17 - zoom;\n");
	$js_buffer .= sprintf ("if(zoom<=0) zoom=1;\n");
	$js_buffer .= sprintf ("map.setView(L.latLng(%3.4f, %3.4f), zoom);\n", $latcenter, $loncenter);

// End of set zoom level and center
*/

#if (isset($database)) {
  #printf ("userhome_map: Database is defined<br>db_dsn is %s\n", $db_dsn);
  #var_dump($database);
#}

// Start creating the javascript to display the profiles on the map

$user_profiles = $database->prepare("SELECT mp.* FROM mailparams mp WHERE mp.userid=:userid ORDER BY pid ASC");
#printf ("Fetching mailparams data for userid %d<br>\n", $userid);
$profiles_rs = $user_profiles->execute(array(':userid' => $userid));
//$profiles_rs = mysql_query($user_profiles, $database);

$profile_num=1;
//while($row = mysql_fetch_assoc($profiles_rs)) {
while ($row = $user_profiles->fetch(PDO::FETCH_ASSOC)) {

	$pid = $row['pid'];
	#printf ("Looking up which emails are associated with profile %d<br>\n", $pid);
  $emailarray = array();
  $query_email_list = $database->prepare("SELECT distinct emailid from email_param_bridge where paramid=:pid");
	$rs_email_list = $query_email_list->execute(array(':pid' => $pid));

  while($eidlist = $query_email_list->fetch(PDO::FETCH_ASSOC)) {
    $emailarray[] = $eidlist['emailid'];
               //printf ("eid is %s<br>", $eidlist['emailid']);
  }
	$emaillist = implode (",", $emailarray);

	$sql = $database->prepare("
		SELECT
			mp.regionid,
			mp.comments,
			mr.geo_type,
			mr.placename,
			mp.active,
			mp.mag_min,
			mp.mag_night,
			mr.geo_flag,
			mp.depth_min,
			mp.depth_max,
			mp.networks,
			mp.xmltext,
			mr.lat_min,
			mr.lat_max,
			mr.lon_min,
			mr.lon_max

		FROM
			mailparams as mp,
			mailregions as mr
		WHERE
			mp.pid=:pid AND
			mp.regionid=mr.geo_id");


	$result = $sql->execute(array(':pid' => $pid));

	if (!$result) {
		continue;
	}

	$myrow = $sql->fetch(PDO::FETCH_ASSOC);

	#$num = $result->rowCount();

	$regionid =	$myrow["regionid"];
	$regionname =	$myrow["comments"];
	$geo_type =	$myrow["geo_type"];
	$placename =	$myrow["placename"];
	//$emaillist =	$myrow["emaillist"];
	$active =	$myrow['active'];
	$mag_min =	$myrow['mag_min'];
	$mag_night =	$myrow['mag_night'];
	$canned =	$myrow['geo_flag'];
	$depth_min =	$myrow['depth_min'];
	$depth_max =	$myrow['depth_max'];
	$networks =	$myrow['networks'];
	$xml =		$myrow['xmltext'];
	$lat_min =	$myrow['lat_min'];
	$lat_max =	$myrow['lat_max'];
	$lon_min =	$myrow['lon_min'];
	$lon_max =	$myrow['lon_max'];


	// not all polygons will have xml saved, but in the cases that do, we can save some significant processing power
	// (the $xml variable contains either '' or the stored value)

	// The PolyProfile class will create an ordered list of points from either:
	// - the center and radius (circle)
	// - the two opposite corners (rectangle)
	// - the triangles in the mailgeography table (polygon)
	$profile_poly = new PolyProfile($regionid, $database, $geo_type, $geo_type . $profile_num,  $color, $xml);
	// The javascript method returns the script required to create a GPolygon for the region
	// The GPolygon is stored in the respective Shape() object as the overlay property
	$editable = true;
	if( $canned == 'canned' ) {
		$editable = false;
	}
	$js_buffer .= $profile_poly->javascript($editable);


	$js_buffer .= sprintf ("profiles_array[%s] = new Profile(%s);\n", $pid, $pid);
	$js_buffer .= sprintf ("profiles_array[%s].regions = %s;\n", $pid, $geo_type.$profile_num);
	$js_buffer .= sprintf ("profiles_array[%s].activate();\n", $pid);

	$js_buffer .= sprintf ("
		profiles_array[%s].info = {
			profileid: %s,
			regionid: %s,
			regionname: %s,
			type: '%s',
			placename: %s,
			emaillist: '%s',
			canned: '%s',
			active: '%s',
			mag_min: '%3.1f',
			mag_night: '%3.1f',
			depth_min: '%s',
			depth_max: '%s',
			networks: '%s',
			lat_min: '%s',
			lat_max: '%s',
			lon_min: '%s',
			lon_max: '%s'
		};\n",
		$pid,
		$pid,
		$regionid,
		json_encode(htmlspecialchars($regionname, ENT_QUOTES, 'UTF-8')),
		$geo_type,
		json_encode(htmlspecialchars($placename, ENT_QUOTES, 'UTF-8')),
		$emaillist,
		$canned,
		$active,
		$mag_min,
		$mag_night,
		$depth_min,
		$depth_max,
		$networks,
		$lat_min,
		$lat_max,
		$lon_min,
		$lon_max
	);

	$display_color = key($color_array);
	$profile_list[$pid] = array( 'id' =>$pid,
	                             'name' => $regionname,
	                             'type' => $geo_type,
	                             'placename' => $placename,
	                             'color'=> $display_color);

	$profile_num++;
	$color = next($color_array);
	if($color === false) {
		$color = reset($color_array);
	}

} // end of while(fetch_assoc) for profiles


// Do a little administrative work to allow the map controls to make less ajax calls

// The list of predefined profiles for the select box
$js_buffer_immediate .= "predefined_regions_text = '" . get_subregion(0, $database) . "';\n";

// removing "More Options" panel from interface so we don't need this list

// List of places for more_info for circles
$js_buffer_immediate .= "predefined_places_text = '";
$places = file('inc/bigplaces');
foreach($places as $place) {
	$name = substr($place, 19, -1);
	$parts = preg_split ("/\s+/", trim($place));
	$clat = $parts[1];
	$clon = $parts[0];
	$latlon = $clat . "," . $clon;

	if($placename == $name)
		$selected = "selected=\"selected\"";
	else
		$selected = "";
	//$name = utf8_encode($name);
	$js_buffer_immediate .= sprintf("<option value=\"%s\"%s>%s</option>", $latlon, $selected, $name);
}

$js_buffer_immediate .= "';\n";


// List of default networks
$js_buffer_immediate .= sprintf("default_net_list = '%s';\n", implode(',' , $defaultnetlist));

// Show a firsttimer welcome text if user has never logged in before
	if ($firsttime == 1) {
		printf("<div class=\"alert success\" id=\"welcome_wrapper\">%s
			<p><strong><a target=\"_blank\" href=\"help?ispopup=true\" onmouseout=\"window.status=''; return true;\" onclick=\"var new_window = window.open('help?ispopup=true','win1','width=500,height=500,scrollbars,resizable'); new_window.focus(); return false;\">Learn more about ENS</a></strong></p>
			<p><a href=\"#\" id=\"hide_welcome\">Hide this message</a></p>
		</div>\n", $welcomemessagetext);


		print '<script type="text/javascript">
			//<![CDATA[
			$("#hide_welcome").click(function(e) {
				e.preventDefault();
				$("#welcome_wrapper").slideUp("normal");
			});
			//]]>
			</script>';


		// if they don't have a confirmed email, tell them.
		if($email_count <= 0) {
			print '<p class="alert success"><strong>Check your email for a confirmation code</strong></p>
				<p>You must confirm at least one email address before you can receive earthquake notifications.</p>';
		}

		// make sure they don't get the first time notifications next time
		$nowstr = date("Y-m-d H:i:s");
		$query_login = $database->prepare("UPDATE mailusers SET lastlogin=:now WHERE id=:uid LIMIT 1");
		$query_login->execute(array(
			':now' => $nowstr,
			':uid' => $USER_INFO['id']
		));


	} // End of firsttime message



	// If they have clicked a confirmation link from an email, give them a message
	// We get here from a redirect in regaddr.inc.php
	if(param('confirmed') == 'y') {
		print '<p class="alert success"><strong>Email address confirmed! You can now receive earthquake notifications.</strong></p>';
	}

	// Check to see if the user has any unconfirmed email addresses.
	// Print a div with a simple form asking them to confirm that address.
	get_pending_emails( $USER_INFO['id'] );
?>


<?php
	include("tabs.inc.php");
?>


	<div id="map_wrapper">
	<div id="map">
		<div align="center" id="map_loading">
			<p style="text-align: center; color: #93a2b0"><br /><br /><strong>Map Loading</strong>
				<br />
				<img src="images/loading.gif" alt="loading" />
			</p>
			<p style="text-align: center"><a href="userhome">Click here if the map fails to load</a></p>
		</div>
	</div>

	<div id="control_add_wrapper" class="control_add_wrapper">
		<div id="control_add" class="control_add">
			<h4>Add New Region</h4>
			<div class="button-wrapper">
				<button id="btn_pre"></button>
				<button id="btn_custom"></button>
				<button id="btn_custom_adv"></button>
			</div>
			<select id="add_predefined_select"></select>
		</div>
		<div id="profile_list">
			<div id="profile_list_header">
				<h4>My ENS Profiles</h4>
			</div>
			<div id="profile_wrapper">
				<ul></ul>
			</div>
		</div>
		<input type="hidden" id="ens_control_token" name="ens_control_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
	</div>

	<div id="map_info">

		<form action="" method="post" id="profile_form">
		<div class="field_container_header">
			<label for="profile_name">Profile Name</label>
			<input type="text" id="profile_name" value="(Click a profile on the map)" tabindex="1" />
			<div id="type" class="type"></div>
			<img src="images/plus.png" id="expand_profile" alt="Expand" />
		</div>
		<div id="profile_info">
			<div class="field_container_row">
				<fieldset>
				<legend>Send email to:</legend>
				<ul class="checkbox-list" id="email_list">

<?php
	foreach($USER_EMAILS as $email) {
		print '<li>' .
				'<input type="checkbox" name="email[]" value="' . $email['eid'] .
            '" id="user-email-id-' . $email['eid'] . '" />' .
				'<label for="user-email-id-' . $email['eid'] . '" >' .
            $email['email'] . '</label>' .
			'</li>';
}
?>
				</ul>
				<a href="#" class="small_link add_email">Add another email</a>
				</fieldset>
				<fieldset>
					<legend>Magnitude:</legend>
					<label class="mag_label" for="mag_min">Day</label>
					<input type="text" name="mag_min" id="mag_min" maxlength="5" tabindex="2" />
					<label class="mag_label" for="mag_night">Night</label>
					<input type="text" name="mag_night" id="mag_night" maxlength="5" tabindex="3" />
				</fieldset>

				<fieldset>
					<legend>Active</legend>
					<ul class="radio-list">
						<li>
							<input type="radio" name="active" id="active-yes" value="Y" />
							<label for="active-yes" class="active_label">Yes</label>
						</li>
						<li>
							<input type="radio" name="active" id="active-no" value="N" />
							<label for="active-no" class="active_label">No</label>
						</li>
					</ul>
				</fieldset>
<!-- more_options section follows -->
				<fieldset>
					<legend>More Options</legend>
					<p id="no_more_options">None</p>
					<a href="#" class="small_link" id="show_more_options">Show more options</a>

					<div id="more_options"><div></div></div>
				</fieldset>
<!-- end of more_options section -->
			</div>
<?php

	if($USER_INFO['userclass'] > 1) {
		$hide_style = '';
	}
	else {
		// Hide the networks list if the user isn't special
		$hide_style = ' display: none';
	}

	printf ('<div class="field_container_row" style="%s">', $hide_style);

	#print poplink(sprintf("%s/help?ispopup=true#networks", $WEB_PATH), $editnetworkstext);
	#printf ("%s/help?ispopup=true#networks", $WEB_PATH);

	print '<fieldset class="networks">';
	print '<legend>Networks</legend>';
	print '<ul class="checkbox-list network-list"><li>';
	foreach ($netlist as $net){
		print '<li>';
		printf("<input name=\"networks[]\" type=\"checkbox\" value=\"%s\"
        id=\"%s\"/>\n", $net, $net);
		printf("<label for=\"%s\">%s</label>\n", $net, $net);
		print '</li>';
	}
	print '</ul>';

	if($USER_INFO['userclass'] > 0) {
		$hide_style = '';
	}
	else {
		// Hide the networks list if the user isn't special
		$hide_style = ' display: none';
	}
	print '</fieldset>';

	print '<fieldset>';
	#print poplink("help?ispopup=true#depth", $editdepthtext);
	#printf ("help?ispopup=true#depth", $editdepthtext);

	print "<legend>" . $editdepthtext . "</legend>";
	print "<label>Min</label>";
	printf ("\n<input type=\"text\" id=\"depth_min\" name=\"depth_min\" size=\"6\" value=\"%4.1f\" />\n", $depth_min);
	print "<label>Max</label>";
	printf ("<input type=\"text\" id=\"depth_max\" name=\"depth_max\" size=\"6\" value=\"%4.1f\" />\n", $depth_max);
	print '</fieldset>';
	print '</div>';

?>
				<input type="hidden" id="ens_profile_token" name="ens_profile_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
           	 
				<div class="field_container_row">
					<div class="button-wrapper">
						<button id="save" class="green" tabindex="4">Save Changes</button>
						<button id="discard" tabindex="5">Discard Changes</button>
						<button id="delete_btn" tabindex="6">Delete Region</button>
					</div>
				</div>

			</form>
		</div>
	</div>
    <input type="hidden" id="ens_recent_token" name="ens_recent_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
        
</div>

<p>If you are having issues managing your account or have comments/suggestions please email <a href="mailto:ensadmin@ens.usgs.gov">ensadmin@ens.usgs.gov</a>.</p>

<script type="text/javascript">
//<![CDATA[
var editable = new Array(0);
var map;
var default_net_list = 'CI';
<?php
// $js_buffer_immediate contains code that must be run before the dependent js files,
// like Profile.js and maps_functions.js get loaded
print $js_buffer_immediate;
?>
$(document).ready( function() {
	fullIEWarning = function() {
		var html = '<h2 class="alert error">Map failed to load</h2>';
		html += '<p><a href="userhome">Click here to use a text-only version of ENS</a></p>';
		html += '<p><strong>Internet Explorer users</strong> When you arrived at this page, you probably saw a notification similar to the ones below. The map will only load correctly if you choose to allow nonsecure items. ';
		html += 'Only the map is delivered over standard HTTP; none of your account data is at risk. ';
		html += 'Try <a href="javascript:location.reload(true)">refreshing the page</a> and choosing the other option. We apologize for the inconvienence. We are currently working on a solution for this issue.</p>';
		html += '<div><img src="images/warningIE7.jpg" alt="Internet Explorer 7 users should Click Yes"><p><strong>Internet Explorer 7</strong></p></div>';
		html += '<div><img src="images/warningIE8.jpg" alt="Internet Explorer 8 users should Click No"><p><strong>Internet Explorer 8</strong></p></div>';

		$("#map_loading").html(html);
	};

	/**
	 * Insert a message for IE users below the "Map Loading" bar. The message tells them that
	 * they probably didn't click the right button on the "View secure content?" popup.
	IEWarning = function() {
		var message = '<a href="#" id="ie_message">Internet Explorer users read this message</a>';
		if( $.browser.msie ) {
			$('#map_loading').append(message);
			$("#ie_message").click(fullIEWarning);
		}
	};

	IEWarning();
	*/

	function loadMapViewer() {

		// zoom animation disabled b/c fit_all() in Profile.js sometimes triggers a Leaflet bug (map unresponsive) if animations are enabled
		map = L.map('map', { minZoom: 1, zoomAnimation: false, scrollWheelZoom: false });
		L.tileLayer('https://{s}.arcgisonline.com/ArcGIS/rest/services/' +
	      'World_Street_Map/MapServer/tile/{z}/{y}/{x}',
			  {
			    subdomains: ['server', 'services'],
			    attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, ' +
			        'Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, ' +
			        'Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the ' +
			        'GIS User Community'
			  }).addTo(map);

		<?php
		print $js_buffer;
		?>

		//console.log(profiles_array);
		fit_all();

		redraw_profile_list();
		$("#collapse_profiles").show();
		$("#profile_list ul").show();

	<?php
		// If we requested the recent events, switch to that view
		if(isset($_GET['recent']) && $_GET['recent'] == 'true') {
			// Delay the execution to make sure the map has loaded
			print "setTimeout('$(\"#tab_recent\").click()', 300);\n";
		}
	?>

	}
	//loadMapViewer();
    try {
        var mapLoadingDialog;

        loadMapViewer();

        // Map loaded without exception, remove the loading message
        mapLoadingDialog = document.querySelector('#map_loading');
        mapLoadingDialog.parentNode.removeChild(mapLoadingDialog);

        $("#profile_form").validate({
            rules: {
                depth_min: {
                    number: true,
                    range: [-100, 1000]
                },
                depth_max: {
                    number: true,
                    range: [-100, 1000]
                }
            }
        });
    }
    catch (err) {
        console.log(err);
        // For IE users, show the error message
        if( $.browser.msie ) {
            fullIEWarning();
        }
    }
});
//]]>
</script>
