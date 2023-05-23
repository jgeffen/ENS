<?php

if(isset($_POST['networks']))
	$networks = $_POST['networks'];
else
	$networks = array("Any");
	
if(isset($_POST['mag']))
	$mag = intval($_POST['mag']);
else
	$mag = "Any";

?>
	<form method="post" action="" name="filterevents">
	<?php printf($obligatory_fields, $USER_INFO['username'], $USER_INFO['hashpasswd'], $page); ?>
	<table cellpadding="0px" cellspacing="0px" border="0px">
	<tr>
		<td><label for="mag"><?=$showonlyeventstext?>:</label></td><td><select name="mag">
<?php for($i = 0; $i < 8; ++$i)
			printf("\t\t<option value=\"%s\">%s</option>\n", $i, $i);
?>
		</select></td><td rowspan="3"><img src="images/legend.png" align="right" /></td>
	</tr>
<?php if($USER_INFO['userclass'] > 0) { ?>
	<tr>
		<td><label for="networks"><?=$showonlynettext?></label></td><td><select name="networks[]" size="3" multiple>
			<option value="Any" selected>Any Network</option>
<?php	sort($netlist); foreach($netlist as $network)
			printf("\t\t<option value=\"%s\">%s</option>\n", $network, $network);
?>
		</select></td>
	</tr>
<?php } // End: if(userclass) ?>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="filterevents" value="<?=$filtereventstext?>" /></td>
	</tr>
	</table>
	</form>
	<hr width="90%" />
<?php

$eids = array();
foreach($USER_EMAILS as $emailinfo)
	$eids[] = $emailinfo['eid'];
$eids = join($eids, ",");

$query_events = sprintf("SELECT DISTINCT events.* FROM events LEFT JOIN notifylist ON events.id=notifylist.event_id WHERE notifylist.addr_id IN (%s)", $eids);
if($mag != "Any")
	$query_events .= sprintf(" AND magnitude > %s", $mag);
if(!in_array("Any", $networks))
	$query_events .= sprintf(" AND net IN ('%s')", join($networks, "','"));
$query_events .= " ORDER BY notifytime DESC LIMIT 25";

if(!($rs_events = $database->query($query_events)) || ($rs_events->rowCount() == 0)) {
	printf("<h3 class=\"alert warning\">%s</h3>\n", $recenteventsnoeventstext);
} else { // Succeeded in getting list of events ?>
	<h3><?=$recenteventsheadertext?></h3>
	<div id="map" style="width: 600px; height: 500px; border: 1px solid black;"></div>
	<script language="javascript" type="text/javascript">
	/* <![CDATA[ */
		var icon1;
		var icon2;
		var icon3;
		var icon4;
		var icon5;
		var icon6;
		var map;

		loadMapViewer = function() {
		icon1 = L.icon({
			iconUrl: "images/wiggle-icon1.png",
			shadowUrl: "images/wiggle-icon1-shadow.png",
			iconSize: [67, 70], //width, height
			shadowSize: [75, 70],
			iconAnchor: [2, 70],
			popupAnchor: [60, 0]
		});

		icon2 = L.icon({
			iconUrl: "images/wiggle-icon2.png",
			shadowUrl: "images/wiggle-icon2-shadow.png",
			iconSize: [54, 56], //width, height
			shadowSize: [60, 56],
			iconAnchor: [2, 56],
			popupAnchor: [53, 0]
		});

		icon3 = L.icon({
			iconUrl: "images/wiggle-icon3.png",
			shadowUrl: "images/wiggle-icon3-shadow.png",
			iconSize: [42, 45], //width, height
			shadowSize: [53, 45],
			iconAnchor: [1, 45],
			popupAnchor: [35, 0]
		});				

		icon4 = L.icon({
			iconUrl: "images/wiggle-icon4.png",
			shadowUrl: "images/wiggle-icon4-shadow.png",
			iconSize: [32, 33], //width, height
			shadowSize: [40, 33],
			iconAnchor: [1, 33],
			popupAnchor: [27, 0]
		});
		
		icon5 = L.icon({
			iconUrl: "images/wiggle-icon5.png",
			shadowUrl: "images/wiggle-icon5-shadow.png",
			iconSize: [21, 22], //width, height
			shadowSize: [26, 21],
			iconAnchor: [1, 22],
			popupAnchor: [17, 0]
		});
				
		map = L.map('map', {
			center: [0, 0],
			zoom: 1,
			scrollWheelZoom: false
		});

		$(document).ready( loadMapViewer );
	/* ]]> */
	</script>
<?php
	$cutoff = strtotime("-1 week");
	$eventnum = 1;
	printf ("<script language=\"javascript\" type=\"text/javascript\">\n/* <![CDATA[ */\n");
	print   "loadMapViewer2 = function() {\n";
	while($event = $rs_events->fetch(PDO::FETCH_ASSOC)) {		
		
		$id = $event["id"];
		$eventid = $event["eventid"];
		$net = $event["net"];
		$time = $event["time"];
		$rawtime = strtotime($time);
		$mag = $event["magnitude"];
		$lat = $event["lat"];
		$lon = $event["lon"];
		$deleted = $event["deleted"];
		$notifytime = $event["notifytime"];
		$evid = sprintf("%s %s", $net, $eventid);
		$evid = preg_replace("/\s+/", "", $evid);
		$evid = strtolower($evid);
		
		if (($rawtime > $cutoff) && ($deleted != 'Y')) {
			if (($net == 'US') || ($net == 'AT'))
				$infolink = sprintf ("<a href=http://earthquake.usgs.gov/eqcenter/recenteqsww/Quakes/%s.php target=_blank>", $evid);
			else
				$infolink = sprintf ("<a href=http://earthquake.usgs.gov/eqcenter/recenteqsus/Quakes/%s.php target=_blank>", $evid);
		} else {
			$infolink = "";
		}
		
		$infostring = sprintf ("%sM%3.1f %s</a><br>%3.2f, %3.2f<br>%s %s<br />", $infolink, $mag, $time, $lat, $lon, $net, $eventid);
		
		if ($deleted == 'Y') { $infostring .= "<br />This event has been deleted"; }
		
		$lastaddress = "";
		$mailcount = 0;
		$ndup = 0;
		
		$iconnum = 5;
		if ($mag >= 3.0) { $iconnum = 4; }
		if ($mag >= 4.0) { $iconnum = 3; }
		if ($mag >= 5.0) { $iconnum = 2; }
		if ($mag >= 6.0) { $iconnum = 1; }
		
		printf ("var marker%d = L.marker([%3.4f,%3.4f], icon%d).addTo(map);\n", $eventnum, $lon, $lat, $iconnum);
		printf ("marker%d.bindPopup(\"%s\"); })\n", $eventnum, $infostring);
		
		++$eventnum;
		
	} // End: while()

	print   "}\n$(document).ready(loadMapViewer2);\n";
	printf ("/* ]]> */\n</script>\n");
} // End: if(mysql_query(events))
?>
