<?php
include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once 'functions.inc.php';

session_start();

// Prevent caching...
$now = gmdate("D, j M Y H:i:s");

// Fix these because of the new include...
$WEB_PATH = str_replace("/inc", "", $WEB_PATH);
$FILE_PATH = str_replace("/inc", "", $FILE_PATH);

$xmltext = '';
// Try to get some pre-existing xml text
$mode = param('mode');
$ajax = param('ajax');
$isValidRequest = true;

if($mode == "syntax") { // User has manually edited the xml and is checking syntax (for some reason?)
    validateCSRF();
	$xmltext = $_GET['xmltext'];
} else if ($mode == "pid") { // User is coming from an existing profile, so try to get the text from the DB
    $isValidRequest = validateCSRF(param("ens_xml_token"), false);
    
    if ($isValidRequest) {
        $xmltext = '';
        $pid = $_GET['pid'];
        $query = $database->prepare('
                SELECT xmltext
                FROM mailparams
                WHERE pid = :pid');
        if ($query->execute(array(':pid' => $pid))) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $xmltext = stripslashes($row['xmltext']);
        }
        $query = null;
    }
} else if ($mode == "file") { // User has loaded a new file, so read it and get it from there
	$xmlfile = $_GET['xmlfile'];
	$xmltext = file($_FILES['xmlfile']['tmp_name']);
	$xmltext = join($xmltext, "");
} else { // If none of the above, then assume there is no pre-existing xml text
	$xmltext = "";
}

if (!$isValidRequest) {
	print $message = "<h4 class=\"alert error\">Invalid Request. Please try again.</h4>";
	$cansubmit = "N";    
    return;
}

// The following line will also set $lats and $lons on success
$lats = array();
$lons = array();
if(!ens_xml_parse($xmltext)) {
	$message = "<h4 class=\"alert error\">Could not parse xml.  Please double check your syntax.</h4>";
	$cansubmit = "N";
} else if( $xmltext != "") {
	$message = "<h4 class=\"alert success\">XML Parsed Succesfully!</h4>";
	$cansubmit = "Y";
} else {
	$message = "<p>Type raw XML text below.</p>";
	$cansubmit = "U";
}

// if this page is being used to validate, return the message and exit
if($ajax=="true") {
	print $message;
	return;
}

?>
		<div class="header" style="margin: 5px;">
			<h1>USGS Earthquake Notification: XML Manager</h1>

		<div id="xml_message"><?=$message?></div>

		<form method="post" id="xmltext" action="inc/ajax/xml_ajax.inc.php">
			<input type="file" name="xml-file" id="xml-file" accept="application/xml">
			<input type="hidden" name="mode" value="syntax" />
			<input type="hidden" name="cansubmit" value="<?=$cansubmit?>" />
            <input type="hidden" id="ens_xml_token" name="ens_xml_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">

			<label for="xmltext_box">
				Edit Current XML Text
				<small>(<a href="<?php echo $CONFIG['MOUNT_PATH'] ?>/help?ispopup=true#xmlsyntax" target="_blank">Syntax Help</a>)</small>
			</label>
			<textarea name="xmltext" id="xmltext_box" rows="15" cols="60" onchange="javascript:stopSubmit();"></textarea>
			<br/>
			<input type="submit" value="Validate XML" onclick="javascript:validateXML();" />
			<input type="reset" value="Undo Changes" />
			<input type="button" name="submit" id="validate" value="Submit this XML" onclick="javascript:submitXML();" />
		</form>
<script language="javascript" type="text/javascript">
    var pid = <?= $pid ?>;
    //alert (pid);
    //console.log(originalPts);
    
    if (pid > 0) {
        points = profiles_array[pid].regions[0].points;
        var count = points.length;

        // XML header removed as it is rejected by firewall rule
        var xml = "<region>\n";
        for(i = 0; i < count; i ++) {
            var lat = points[i].lat;
            var lon = points[i].lng;
            xml += "<point>";
            xml += "<lat>"+(Math.round(lat*1000)/1000)+"</lat>";
            xml += "<lon>"+(Math.round(lon*1000)/1000)+"</lon>";
            xml += "</point>\n";
        }
        xml += "</region>\n";
    } else {
        xml = "";
    }
    //alert(xml);
    
    document.getElementById('xmltext_box').value = xml;
    //console.log(originalPts);
    document.getElementById('xml-file').addEventListener('change', function(e) {
        var file = e.target.files[0],
            reader = new FileReader();

        reader.onload = function(e){
            var xml = e.target.result;
            //console.log(xml);
            document.getElementById('xmltext_box').value = xml;
        };
        reader.readAsText(file);
    });

			<!-- // Text Hiding
			function submitXML() {
				var cansubmit = document.forms['xmltext'].elements['cansubmit'].value;
				//console.log(originalPts);
				if( cansubmit == 'Y') {
					var pid = <?=$pid?>;
					var xml = $("#xmltext_box").val();
					focus_profile.info.xmltext = xml;
					tb_remove();
					xmlStuff = $.parseXML( xml ), $xml = $( xmlStuff );
					var newlen = $(xml).find("point").length;
					var newlats = new Array(0);
                                        var newlons = new Array(0);
                                        $(xml).find("point").each(function () {
                                                var lat = parseFloat($(this).find("lat").text());
                                                var lon = parseFloat($(this).find("lon").text());
                                                newlats.push (lat);
                                                newlons.push (lon);
                                        } );
					//alert (pid);
					if (pid > 0) {
						while(profiles_array[pid].regions[0].points.length > 0) {
    							profiles_array[pid].regions[0].points.pop();
						}
					//alert(profiles_array[pid].regions[0].points.lat[0]);
					//alert(xml);
 					//alert (newlen);
						for (i = 0; i < newlen; i++) {
							var newlat = newlats[i];
							var newpoint = {
								lat: newlats[i],
								lng: newlons[i]
							}
							profiles_array[pid].regions[0].points.push (newpoint);
						}
					//profiles_array[pid].regions[0].redraw();
						profiles_array[pid].regions[0].redraw(profiles_array[pid].regions[0].points);
						profiles_array[pid].setExtent();
					} else {
						var newpolygon = new Array(0);
						var newpoint_array = new Array(0);
						var sumlat = 0;
						var sumlon = 0;
						for (i = 0; i < newlen; i++) {
							sumlat = sumlat + newlats[i];
							sumlon = sumlon + newlons[i];
							newpoint_array.push( L.latLng(newlats[i], newlons[i]));
						}
						var avglat = sumlat/newlen;
						var avglon = sumlon/newlen;
						var newcenter = L.latLng(avglat,avglon);
						newpolygon[0] = new Polygon (newpoint_array, newcenter, -1, next_color, null, true);

						//console.log(newcenter);
						//console.log(newpolygon[0]);
						new_profile.regions = newpolygon;
						new_profile.setExtent();
						$('#save').prop('disabled', false);
					}
					alert ("Click 'Save Changes' to update the database");
  					//$(xml).find("point").each(function () {
				//		var lat = $(this).find("lat").text();
				//		alert (lat);
				//	} );
				} else if ( cansubmit == 'U') {
					alert('You must validate this XML before submitting.');
					document.forms.xmltext.elements['submit'].disabled = true;
				} else {
					alert('XML Validation Failed.  Correct syntax, validate, and try again.');
					document.forms.xmltext.elements['submit'].disabled = true;
				}
				return true;
			}
			function stopSubmit() {
				document.forms['xmltext'].elements['cansubmit'].value = 'U';
				return true;
			}

			function validateXML() {
				var xml = $("#xmltext_box").val();
                
                if (isValidString(xml)) {
                    $.ajax({
                        type: "GET",
                        url: '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>',
                        data: {ajax:"true", xmltext:xml, mode:"syntax"},
                        headers: getCSRFHeader($("#ens_xml_token").val()),
                        success: function(result) {
                            //alert(result);
                            $("#xml_message").html(result);
                            if(result.indexOf("success") != -1) {
                                document.forms['xmltext'].elements['cansubmit'].value = 'Y';
                                document.forms.xmltext.elements['submit'].disabled = false;
                            }
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown){
                        // Error returned from the server
                        if (jqXHR.status == ENS_HTMLCODE.FORBIDDEN) {
                            // May have been rejected by firewall
                            ensAlert("Error", "Your XML text may contain forbidden characters. Please update your XML text and try again!");
                        } else {
                            ensAlert("Error", "There was an error processing your request. Please try again later.");
                        }
                    });                    
                } else {
                    ensAlert("Invalid Data", "Your XML text contains forbidden characters. Please update your XML and try again!");
                }

				return true;
			}

			$("#xmltext").submit( function(e) {
				e.preventDefault();
			});

			// -->
</script>
