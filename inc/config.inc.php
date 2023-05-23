<?php

	include_once dirname(__FILE__) . '/../../conf/config.inc.php';
	include_once("PdoSessionHandler.php");

	$session_dsn = $CONFIG['SESSION_DB_DRIVER'] . ':' .
	    'host=' . $CONFIG['SESSION_DB_HOST'] . ';' .
	    'port=' . $CONFIG['SESSION_DB_PORT'] . ';' .
	    'dbname=' . $CONFIG['SESSION_DB_NAME'];

	$session_pdo = new PDO($session_dsn,
	    $CONFIG['SESSION_DB_USER'], $CONFIG['SESSION_DB_PASS']);

	$session_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$session_handler = new PdoSessionHandler($session_pdo, array(
	  'db_table'    => $CONFIG['SESSION_DB_TABLE'],
	  'db_id_col'   => 'session_id',
	  'db_data_col' => 'session_data',
	  'db_time_col' => 'session_expiration'
	));

	session_set_save_handler($session_handler, true);

	// --------------------------------------------
	// Connect to the database
	// --------------------------------------------
	$db_dsn = $CONFIG['DB_DSN'];
	#$dbname      = $_SERVER['REDIRECT_DB_NAME'];
	$dbadminuser = $CONFIG['DB_ADMINUSER'];
	$dbadminpass = $CONFIG['DB_ADMINPASS'];
	$dbreaduser  = $CONFIG['DB_READUSER'];
	$dbreadpass  = $CONFIG['DB_READPASS'];
	$dbconerrtxt = "<h3 style=\"color: #990000;\">There has been an error connecting to the database.  " .
		"The database may be down for maintenance.  Please try again in a few minutes.  If this error " .
		"persists, mail the <a href=\"mailto:ensadmin@usgs.gov\">ENS administrator</a></h3>";
        #printf ("trying to connect to %s with %s/%s<br>\n", $db_dsn, $dbadminuser, $dbadminpass);
	$database = new PDO($db_dsn, $dbadminuser, $dbadminpass);
	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
#mysql_connect($dbhostname, $dbadminuser, $dbadminpass) or die($dbconerrtxt);
	$dbreadonly = new PDO($db_dsn, $dbreaduser, $dbreadpass);
	$dbreadonly->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
#mysql_connect($dbhostname, $dbreaduser, $dbreadpass) or die($dbconerrtxt);
	#mysql_select_db($dbname, $database);
	#mysql_select_db($dbname, $dbreadonly);

	// Use this for breaking up a polygon
	$triangle_path = $CONFIG['TRIANGLE_PATH'];

	// Use this for relative href links
	$WEB_PATH = $CONFIG['MOUNT_PATH']; // Something like /eqcenter/ens
	// Use this for includes and requires
	$FILE_PATH = dirname(dirname(__FILE__));

	$lang_list = array(0 => "English-US", 1 => "Spanish");
	$user_class = array(0 => "Regular User", 1 => "First Responder", 2 => "Seismologist");
	$interest_list = array("General Public",
		"Scientist/Network Operator",
		"Educator/University",
		"Educator/Elementary or Secondary",
		"Emergency Services Coordinator/Manager",
		"First Responder (e.g. police, fire, paramedic)",
		"Utility Operator",
		"Lifeline Operator (e.g. transportation)",
		"Critical Facility Operator (e.g. hospital, government services)",
		"News Media Representative",
		"Other Media Representative",
		"Organized Volunteer (e.g. Red Cross, Salvation Army)",
		"Engineer in Public Sector","Engineer in Private Sector",
		"Consultant","Elected Official or Staff",
		"Student",
		"Other - specify below"
	);

	$sendmail_list = array("Scientist/Network Operator",
		"Emergency Services Coordinator/Manager",
		"First Responder (e.g. police, fire, paramedic)",
		"Utility Operator",
		"Lifeline Operator (e.g. transportation)",
		"Critical Facility Operator (e.g. hospital, government services)",
		"Elected Official or Staff"
	);

	$timezones = array(14,13,12,11,10.5,10,9.5,9,8,7,6,5.5,5,4.5,4,3,2,1,0,-1,-2,-3,-4,-5,-6,-7,-8,-9,-10,-11,-12);
	$timezonenames = array('+14:00','+13:00','+12:00','+11:00','+10:30','+10:00 Australia-east','+9:30 Australia-central','+9:00','+8:00 Australia-west','+7:00','+6:00','+5:30','+5:00','+4:30','+4:00',
		'+3:00','+2:00 Eastern Europe','+1:00 Central Europe','0:00 UTC (GMT)','-1:00','-2:00','-3:00','-4:00 Atlantic','-5:00 US Eastern','-6:00 US Central',
		'-7:00 US Mountain','-8:00 US Pacific','-9:00 Alaska','-10:00 Hawaii','-11:00','-12:00');

	$netlist = array('CI','NC','NN','UU','UW','AK','NM','HV','PR','US','SE','LD','NE','MB','WY','AR','AT','PT','DR','OK','TX');
    $defaultnetlist = array('CI','NC','NN','UU','UW','AK','NM','HV','PR','US','SE','LD','NE','MB','WY','AR','DR','OK','TX');
	$errmsg = "<div class=\"alert error\"><h3>An error occured while loading the page.</h3>\n<ul>\n";

	$nav_loc_regex = "/<a href=\"#\" onclick=\"%s\.submit\(\)\" class=\"navitem\">([^<]*)<\/a>/";
	$nav_loc_replace = "<strong><a href=\"#\" onclick=\"%s\.submit()\">$1</a></strong>";

	$ERRORS_TEXT = array(
		"403" => "403: Forbidden.  You do not sufficient privileges to access this page.",
		"404" => "404: Page Not Found.  The requested page was not found on our server.",
		"500" => "500: Internal Server Error.  An internal error occured.  " .
				"If the problem persists, please contact the " .
				"<a href=\"mailto:ensadmin@usgs.gov\">administrator</a>."
	);
	$ERROR_MESSAGE = "\t<div class=\"alert error\"><ul style=\"list-style: none inherit none;\">\n";

	$nologinok = array("login", "recover", "help", "unsubscribe", "register", "faq", "announcement");
	$firsttime = false; // Usually not a users first time
	$geoflags = array("canned" => "Predefined Region", "custom" => "Custom Region");

	// the obligatory_fields have been replaced by sessions.
	//$obligatory_fields = '<input type="hidden" name="username" value="%s" /><input type="hidden" name="hashpass" value="%s" />'; //<input type="hidden" name="page" value="%s" />';
	$obligatory_fields = '';
	$javascriptrequired = "<noscript><b><a href=\"mailto:ensadmin@usgs.gov\" title=\"Ask an Admin for Assistance\" target=\"_blank\">Javascript Required</a></b></noscript>";

	// These are reset if you are logged in
	$username = "to ENS";
	$hashpass = "";
	$USER_INFO = NULL;

	// Reset upon successfull login
	$USER_EMAILS = NULL;
	$email_count = 0;
?>
