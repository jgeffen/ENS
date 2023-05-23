<?php
	// The error should be set before including this, but if not, we'll just pretend it is a 500 Server Error.
	if( ($errorid = param('errorid')) == "")
		$errorid = "500";
	
	$TITLE =  "";
	printf("<h1 style=\"color: #990000;\">%s</h1>\n", $ERRORS_TEXT[$errorid]);
	print ("<p>You can use the one of the links to the left to continue accessing your account.</p>\n");
	print ("<p class=\"alert warning\">Please use the form below to generate an error report that will be sent to the ENS Administrator.</p>\n");
	
	// Show a form that will send email
?>
		