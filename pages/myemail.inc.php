<?php
$TEMPLATE_LAYOUT = "one_column";


if(!isset($FOO)) {
	include_once("inc/maps_functions.inc.php");
	$FOO = true;
}


// Fix these because of the new include...
$WEB_PATH = str_replace("/inc", "", $WEB_PATH);
$FILE_PATH = str_replace("/inc", "", $FILE_PATH);

// Check to see if the user has any unconfirmed email addresses.
// Print a div with a simple form asking them to confirm that address.
get_pending_emails( $USER_INFO['id'] );


include("tabs.inc.php");
?>

<div id="map_wrapper">

<div id="main_email_wrapper" class="container contains-twelve">

<h2>My email addresses</h2>

<p>
	The email addresses listed below are currently associated with your Earthquake Notification Service account.
	The <em>day begins</em> and <em>day ends</em> fields allow you to define the primary times you would like to receive notifications.
	This allows you to receive notifications for smaller earthquakes during the day than during the night.
	We are not responsible for problems with the delivery of your notification due to the mail service provider.
</p>
<ul class="email-list">
<?php
foreach($USER_EMAILS as $email) {
	$day_begin = $email['day_begin'];
	$day_end = $email['day_end'];
	$format = $email['format'];
	$timezone = $USER_INFO['timezone'];
	$format_select = "<select name=\"format\">
				<option value=\"HTML\">HTML</option>
				<option value=\"long\">Long (text)</option>
				<option value=\"short\">Short (cell)</option>
				<option value=\"raw\">Raw CUBE Format</option>
			</select>";
	$format_select = str_replace(sprintf("value=\"%s\"", $format), sprintf("value=\"%s\" selected=\"selected\"", $format), $format_select);

	$day_begin_select = "<select name=\"day_begin\">";
	for( $i=0; $i<24; ++$i) {
		if(timeresolve($day_begin + $timezone) == $i) {
			$day_begin_select .= "<option value=\"$i\" selected=\"selected\">$i:00</option>";
		}
		else {
			$day_begin_select .= "<option value=\"$i\">$i:00</option>";

		}
	}
	$day_begin_select .= "</select>";

	$day_end_select = "<select name=\"day_end\">";
	for( $i=0; $i<24; ++$i) {
		if(timeresolve($day_end + $timezone) == $i) {
			$day_end_select .= "<option value=\"$i\" selected=\"selected\">$i:00</option>";
		}
		else {
			$day_end_select .= "<option value=\"$i\">$i:00</option>";
		}
	}
	$day_end_select .= "</select>";


	print "
		<li class=\"email-list-item\">
			<form method=\"post\" action=\"inc/email_ajax.inc.php\">
				<input type=\"hidden\" name=\"eid\" value=\"" . $email['eid'] . "\" />
				<strong class=\"email-address\">
					".$email['email']."
				</strong>
				<div class=\"email-fieldset-wrapper\">
					<fieldset>
						<label for=\"format\">
							<a href=\"" . $WEB_PATH . "/help?ispopup=true#format\" target=\"_blank\">
								Message Format
							</a>
						</label>
						$format_select
					</fieldset>
					<fieldset>
						<label for=\"day_begin\">
							<a href=\"" . $WEB_PATH . "/help?ispopup=true#day_begin\" target=\"_blank\">
								Day Begins
							</a>
						</label>
						$day_begin_select
					</fieldset>
					<fieldset>
						<label for=\"day_end\">
							<a href=\"" . $WEB_PATH . "/help?ispopup=true#day_begin\" target=\"_blank\">
								Day Ends
							</a>
						</label>
						$day_end_select
					</fieldset>
				</div>
				<div class='button-wrapper'>
					<button class='button_small' id=\"save_email_".$email['eid']."\">Save</button>
					<button class='button_small' id=\"delete_email_".$email['eid']."\">Delete</button>
				</div>
			</form>
		</li>
		<li class=\"ajax_result\">
			<div id=\"email_result_".$email['eid']."\"></div>
		</li>
		";
}
 ?>

</ul>

<input type="hidden" id="ens_email_token" name="ens_email_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">

<button class="add_email">Add Another Email</button>

</div>

</div>
