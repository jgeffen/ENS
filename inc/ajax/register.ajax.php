<?php
/* This file handles ajax requests from the register page */
include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once("../maps_functions.inc.php");
include_once("../textdefs0.inc.php");

validateCSRF();

if( isset($_POST['check']) && $_POST['check'] == 'email' && isset($_POST['email'])) {
	$email = mysql_real_escape_string($_POST['email']);	

	$query = sprintf('
		SELECT	
			eid,
			email
		FROM
			mailaddresses
		WHERE
			email = "%s"
		',
		$email);
	
	$query_rs = mysql_query($query, $dbreadonly);

	if(mysql_num_rows($query_rs) > 0 ) {
		print 'invalid'; // False, this email is invalid
	}
	else {
		print 'valid'; // True, this email isn't being used
	}
}
