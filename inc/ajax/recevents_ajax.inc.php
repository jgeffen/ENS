<?php
include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once './login.inc.php';

//include_once("../gmaps_functions.inc.php");
include_once("../textdefs0.inc.php");


#session_start();

validateCSRF();

$USER_INFO = $_SESSION['USER_INFO'];
$USER_EMAILS = $_SESSION['USER_EMAILS'];


$mag = "Any";
$networks = array("Any");

$eids = array();
foreach($USER_EMAILS as $emailinfo)
	$eids[] = $emailinfo['eid'];
$eids = join($eids, ",");

$events_query = $database->prepare('SELECT DISTINCT events.* FROM events LEFT JOIN notifylist
	ON events.id=notifylist.event_id WHERE notifylist.addr_id IN (:eids) ORDER BY notifytime DESC LIMIT 25');

$rs_events = $events_query->execute(array(':eids' => $eids));

?>
		var icon1;
		var icon2;
		var icon3;
		var icon4;
		var icon5;
		var icon6;
		var recent_events = [];

		icon1 = L.icon({iconUrl: "images/wiggle-icon1.png", iconSize: [67, 70], shadowUrl: "images/wiggle-icon1-shadow.png", shadowSize: [70, 70],  iconAnchor: [0, 60], popupAnchor: [30, -50]});

		icon2 = L.icon({iconUrl: "images/wiggle-icon2.png", iconSize: [54, 56], shadowUrl: "images/wiggle-icon2-shadow.png",  shadowSize: [60, 56],  iconAnchor: [2, 56], popupAnchor: [27, -41]});

		icon3 = L.icon({iconUrl: "images/wiggle-icon3.png", iconSize: [42, 45], shadowUrl: "images/wiggle-icon3-shadow.png",  shadowSize: [53, 45],  iconAnchor: [1, 45], popupAnchor: [21, -43]});

		icon4 = L.icon({iconUrl: "images/wiggle-icon4.png", iconSize: [32, 33], shadowUrl: "images/wiggle-icon4-shadow.png",  shadowSize: [40, 33],  iconAnchor: [1, 33], popupAnchor: [16, -33]});

		icon5 = L.icon({iconUrl: "images/wiggle-icon5.png", iconSize: [21, 22], shadowUrl: "images/wiggle-icon5-shadow.png",  shadowSize: [26, 21],  iconAnchor: [1, 22], popupAnchor: [10, -21]});
<?php
$cutoff = strtotime("-1 week");
$eventnum = 0;
	//while($event = $rs_events->fetch(PDO::FETCH_ASSOC)) {
while($event = $events_query->fetch(PDO::FETCH_ASSOC)) {

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
		$infolink = sprintf ("<a href=\"http://earthquake.usgs.gov/earthquakes/eventpage/%s\" target=_blank>", $evid);
	} else {
		$infolink = "";
	}

	$infostring = sprintf ("%sM%3.1f %s</a><br>Lat: %3.2f, Lon: %3.2f<br>%s %s<br />", $infolink, $mag, $time, $lat, $lon, $net, $eventid);

	if ($deleted == 'Y') { $infostring .= "<br />This event has been deleted"; }

	$lastaddress = "";
	$mailcount = 0;
	$ndup = 0;

	$iconnum = 5;
	if ($mag >= 3.0) { $iconnum = 4; }
	if ($mag >= 4.0) { $iconnum = 3; }
	if ($mag >= 5.0) { $iconnum = 2; }
	if ($mag >= 6.0) { $iconnum = 1; }

	++$eventnum;
	printf ("recent_events[%d] = L.marker([%3.4f,%3.4f], {icon: icon%d})", $eventnum, $lat, $lon, $iconnum);
	printf (".bindPopup('%s').addTo(map);\n", $infostring);

} // End: while()
if ($eventnum == 0) {
	// We didnt' find anything
	printf("alert('%s');\n", $recenteventsnoeventstext);
}

?>
