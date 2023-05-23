<?php
// This file provides the ajax processing for the register page. 
// It returns simple messages based on the result of the calculations
include("../config.inc.php");

validateCSRF();

if(isset($_POST['username']) && isset($_POST['ajax'])) {
	$username = trim($_POST['username']);
	$user_query = sprintf("SELECT username FROM mailusers WHERE username='%s' LIMIT 1", $username);
	$user_rs = mysql_query($user_query, $database);

	if(mysql_num_rows($user_rs) == 1) {
		print 'exists';
	}
	else {
		print 'valid';
	}

}
?>
