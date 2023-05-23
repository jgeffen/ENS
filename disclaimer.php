<?php
	if(!isset($TEMPLATE)) {
		// Set a default title, this will be changed in the included file
		$TITLE = "Earthquake Notification Service Disclaimer";
		$CONTACT = "ensadmin@usgs.gov";
		$TEMPLATE = "ens";
		// We use buffering because some parts of the template will be changed 
		// depending on what happens during the page content generation.
		include_once $_SERVER['DOCUMENT_ROOT'] . "/template/template.inc.php";
	}
?>
<p>
The earthquake information delivered through the Earthquake Notification
Service (ENS) is preliminary.  Subsequent review usually results in some
revision to the data, and all users are advised to check the USGS earthquake
program pages at <a href="http://earthquake.usgs.gov">http://earthquake.usgs.gov</a> for updates. Data users are
cautioned to consider carefully the provisional nature of the information
before using it for decisions that concern personal or public safety or
the conduct of business that involves substantial monetary or operational
consequences.  Earthquakes are a common occurrence, and many are either
not large enough to cause damage or not located sufficiently close to
populations centers to produce damage. E-mail alerts sent through ENS do not
imply an impending threat.
</p><p> 
ENS is an informational tool and NOT a robust earthquake or tsunami
warning system. The USGS does not produce tsunami warnings. For the
information about tsunamis, please refer to the information given in
the NOAA website <a href="http://tsunami.gov">http://tsunami.gov</a>.
</p><p> 
On a global basis, earthquakes of magnitude 5.0 or greater are generally reviewed and
distributed by ENS within 2 hours or their ocurrence.  Some events of magnitude 5.0 to
6.0 in remote parts of the world, especially on mid-ocean ridges in parts of the Southern
Hemisphere, may not be distributed until 24 hours after their occurrence.  Within the US,
widely felt earthquakes are generally distributed within 5 minutes.  Additionally, processing
and sending the messages typically takes 30 minutes.  The USGS cannot guarantee the receipt or
timeliness of an e-mail message after it has been sent.
</p><p> 
If numerous bounced messages are received from your account, the automated
system will not remove your address from the ENS mailing list.  If that should
happen, your mail profiles will be marked 'inactive' and the system will
stop sending you mail. If you fix your address and you still want to receive e-mails, just log in and mark your profiles active.
</p><p> 
Please address unresolved mailing list issues to: <a href="mailto:ensadmin@usgs.gov">ensadmin@usgs.gov</a>.
</p>
