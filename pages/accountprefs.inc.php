<?php
$TEMPLATE_LAYOUT = "one_column";

if(!isset($FOO)) {
        include_once("inc/maps_functions.inc.php");
        $FOO = true;
}

// Fix these because of the new include...
$WEB_PATH = str_replace("/inc", "", $WEB_PATH);
$FILE_PATH = str_replace("/inc", "", $FILE_PATH);


        $name =         $USER_INFO['name'];
        $address1 =     $USER_INFO['address1'];
        $address2 =     $USER_INFO['address2'];
        $city =         $USER_INFO['city'];
        $state =        $USER_INFO['state'];
        $zipcode =      $USER_INFO['zipcode'];
        $phone =        $USER_INFO['phone'];
        $timezone =     $USER_INFO['timezone'];
        $optin =        $USER_INFO['optin'];
        $language =     $USER_INFO['language'];
        $userclass =    $USER_INFO['userclass'];
        $aftershock =   $USER_INFO['aftershock'];
        $updates =      $USER_INFO['updates'];
        $defer =        $USER_INFO['defer'];
		$oaf =        	$USER_INFO['oaf'];
		$pager =        $USER_INFO['pager'];
		$shakemap = 	$USER_INFO['shakemap'];
		$shakealert =   $USER_INFO['shakealert'];
        $affiliation =  $USER_INFO['affiliation'];
        $otherinterest= $USER_INFO['otherinterest'];
        $notification= 	$USER_INFO['alert'];

	if(!isset($startdate)) {
		$startdate = date("Y-m-d");
	}
	if(!isset($enddate)) {
		$enddate = date("Y-m-d");
	}
// Check to see if the user has any unconfirmed email addresses.
// Print a div with a simple form asking them to confirm that address.
get_pending_emails( $USER_INFO['id'] );

include("tabs.inc.php");

?>

<div id="map_wrapper">
	<div id="account_prefs" class="row account-preferences">
		<div class="column one-of-two">
			<h2>Account Preferences</h2>
			<form class="ens_form" action='' method='post' id="account_form">
				<div>
					<label for="name">
						<?=$passwdnametext?>
					</label>
					<input type="text" name="name" value="<?=$name?>" />
				</div>
				<div>
					<label for="timezone">
						<?php printf ("<a href=\"%s/help?ispopup=true#timezone\" target=\"_blank\">%s</a>", $WEB_PATH, $passwdtimezonetext) ?>
					</label>
					<select name="timezone">
						<?php
						for($i = 0; $i < count($timezones); ++$i) {
							if($timezone == $timezones[$i])
								$selected = " selected=\"selected\"";
							else
								$selected = "";
							printf("<option value=\"%s\"%s>%s</option>\n", $timezones[$i], $selected, $timezonenames[$i]);
						}
						?>
					</select>
				</div>

				<div>
					<label for="language">
						<?php printf ("<a href=\"%s/help?ispopup=true#language\" target=\"_blank\">%s</a>", $WEB_PATH, $passwdlanguagetext) ?>
					</label>
					<select name="language">
						<?php
						for($i = 0; $i < count($lang_list); ++$i) {
							if($language == $i)
								$selected = " selected=\"selected\"";
							else
								$selected = "";
							printf("<option value=\"%s\"%s>%s</option>\n", $i, $selected, $lang_list[$i]);
						}
						?>
					</select>
				</div>

				<div>
					<label for="affiliation">
						<?php printf ("<a href=\"%s/help?ispopup=true#affiliation\" target=\"_blank\">%s</a>", $WEB_PATH, $passwdaffiliationtext) ?>
					</label>
					<select name="affiliation">
						<?php
						foreach($interest_list as $interest) {
							if($affiliation == $interest)
								$selected = " selected=\"selected\"";
							else
								$selected = "";
							printf("<option value=\"%s\"%s>%s</option>\n", $interest, $selected, $interest);
						}
						?>
					</select>
				</div>

				<div>
					<label for="otherinterest">
						<?=$passotherinteresttext?>
					</label>
					<input type="text" name="otherinterest" value="<?=$otherinterest?>" />
				</div>

				<div>
					<label for="aftershock">
						<?php printf ("<a href=\"%s/help?ispopup=true#aftershock\" target=\"_blank\">%s</a>", $WEB_PATH, $passwdaftershocktext) ?>
					</label>
						<?php
							if($aftershock == "Y") {
								print '<input type="radio" name="aftershock" id="aftershock-yes" value="Y" checked="checked" />';
								print '<label for="aftershock-yes">Yes</label>';
								print '<input type="radio" name="aftershock" id="aftershock-no" value="N" />';
								print '<label for="aftershock-no">No</label>';
							} else {
								print '<input type="radio" name="aftershock" id="aftershock-yes" value="Y" />';
								print '<label for="aftershock-yes">Yes</label>';
								print '<input type="radio" name="aftershock" id="aftershock-no" value="N" checked="checked" />';
								print '<label for="aftershock-no">No</label>';
							}
						?>
				</div>

				<div>
					<label for="updates">
						<?php printf ("<a href=\"%s/help?ispopup=true#updates\" target=\"_blank\">%s</a>", $WEB_PATH, $passwdupdatestext) ?>
					</label>
						<?php
						if($updates == "Y") {
							print '<input type="radio" name="updates" id="updates-yes" value="Y" checked="checked" />';
							print '<label for="updates-yes">Yes</label>';
							print '<input type="radio" name="updates" id="updates-no" value="N" />';
							print '<label for="updates-no">No</label>';
						} else {
							print '<input type="radio" name="updates" id="updates-yes" value="Y" />';
							print '<label for="updates-yes">Yes</label>';
							print '<input type="radio" name="updates" id="updates-no" value="N" checked="checked" />';
							print '<label for="updates-no">No</label>';
						}
						?>
				</div>

				<div>
					<label for="defer">
						<?php printf ("<a href=\"%s/help?ispopup=true#defer\" target=\"_blank\">%s</a>", $WEB_PATH, $passwddefertext) ?>
					</label>
						<?php
						if($defer == "Y") {
							print '<input type="radio" name="defer" id="defer-yes" value="Y" checked="checked" />';
							print '<label for="defer-yes">Yes</label>';
							print '<input type="radio" name="defer" id="defer-no" value="N" />';
							print '<label for="defer-no">No</label>';
						} else {
							print '<input type="radio" name="defer" id="defer-yes" value="Y" />';
							print '<label for="defer-yes">Yes</label>';
							print '<input type="radio" name="defer" id="defer-no" value="N" checked="checked" />';
							print '<label for="defer-no">No</label>';
						}
						?>
				</div>
				<div>
					<label for="alerts">
						<?php printf ("<a href=\"%s/help?ispopup=true#alerts\" target=\"_blank\">%s</a>", $WEB_PATH, $passwdalertnotifications) ?>
					</label>
					Products below come as individual additional reports, normally a few minutes to a few tens of minutes after the earthquake that triggers them.
					<br>
					<label for="pager" style="font-weight:bold;" id="account_pref_pager">PAGER (Prompt Assessment of Global Earthquakes for Response)</label>
					If selected 'Yes', ENS will send you an email or text summarizing the category of earthquake impact in terms of fatalities and economic losses as estimated by the USGS PAGER product. <?php printf ("<a href=\"%s/help?ispopup=true#pager\" target=\"_blank\">%s</a>", $WEB_PATH, 'For more details...') ?>
					<br>
						<?php
						if($pager == "Y") {
							print '<input type="radio" name="pager" id="pager-yes" value="Y" checked="checked" />';
							print '<label for="pager-yes">Yes</label>';
							print '<input type="radio" name="pager" id="pager-no" value="N" />';
							print '<label for="pager-no">No</label>';
						} else {
							print '<input type="radio" name="pager" id="pager-yes" value="Y" />';
							print '<label for="pager-yes">Yes</label>';
							print '<input type="radio" name="pager" id="pager-no" value="N" checked="checked" />';
							print '<label for="pager-no">No</label>';
						}
						?>
					<label for="shakemap" style="font-weight:bold; width: 100px;" id="account_pref_shakemap">ShakeMap</label>
					If selected 'Yes', ShakeMaps show where earthquake shaking occurred, and how strong it was. ShakeMaps are made for most moderate and large earthquakes worldwide. <?php printf ("<a href=\"%s/help?ispopup=true#shakemap\" target=\"_blank\">%s</a>", $WEB_PATH, 'For more details...') ?>
					<br>
						<?php
						if($shakemap == "Y") {
							print '<input type="radio" name="shakemap" id="shakemap-yes" value="Y" checked="checked" />';
							print '<label for="shakemap-yes">Yes</label>';
							print '<input type="radio" name="shakemap" id="shakemap-no" value="N" />';
							print '<label for="shakemap-no">No</label>';
						} else {
							print '<input type="radio" name="shakemap" id="shakemap-yes" value="Y" />';
							print '<label for="shakemap-yes">Yes</label>';
							print '<input type="radio" name="shakemap" id="shakemap-no" value="N" checked="checked" />';
							print '<label for="shakemap-no">No</label>';
						}
						?>						
					<label for="shakealert" style="font-weight:bold; width: 100px;" id="account_pref_shakealert">ShakeAlert&reg;</label>
					The ShakeAlert&reg; earthquake early warning system operates in California, Oregon, and Washington. ShakeAlert sends a preliminary short report for the USGS web pages for event magnitudes 4 or greater. <?php printf ("<a href=\"%s/help?ispopup=true#shakealert\" target=\"_blank\">%s</a>", $WEB_PATH, 'For more details...') ?>
					<br>
						<?php
						if($shakealert == "Y") {
							print '<input type="radio" name="shakealert" id="shakealert-yes" value="Y" checked="checked" />';
							print '<label for="shakealert-yes">Yes</label>';
							print '<input type="radio" name="shakealert" id="shakealert-no" value="N" />';
							print '<label for="shakealert-no">No</label>';
						} else {
							print '<input type="radio" name="shakealert" id="shakealert-yes" value="Y" />';
							print '<label for="shakealert-yes">Yes</label>';
							print '<input type="radio" name="shakealert" id="shakealert-no" value="N" checked="checked" />';
							print '<label for="shakealert-no">No</label>';
						}
						?>		
					<label for="oaf" style="font-weight:bold;" id="account_pref_oaf">Operational Aftershock Forecast (OAF)</label>
					Aftershock forecasts are developed for M 5.0 and larger earthquakes in the US and US territories. The OAF message from ENS will give the 7-day probability for an M>=5 aftershock. <?php printf ("<a href=\"%s/help?ispopup=true#oaf\" target=\"_blank\">%s</a>", $WEB_PATH, 'For more details...') ?>
					<br>
					<?php
						if($oaf == "Y") {
							print '<input type="radio" name="oaf" id="oaf-yes" value="Y" checked="checked" />';
							print '<label for="oaf-yes">Yes</label>';
							print '<input type="radio" name="oaf" id="oaf-no" value="N" />';
							print '<label for="oaf-no">No</label>';
						} else {
							print '<input type="radio" name="oaf" id="oaf-yes" value="Y" />';
							print '<label for="oaf-yes">Yes</label>';
							print '<input type="radio" name="oaf" id="oaf-no" value="N" checked="checked" />';
							print '<label for="oaf-no">No</label>';
						}
						?>				 
				</div>
				<input type="hidden" id="ens_profile_token" name="ens_profile_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
				<button name="submit" type="submit">Save Changes</button>
				<span id="account_result"></span>
			</form>
		</div>

		<div class="column one-of-two">
			<h2>Change Password</h2>
			<form class="ens_form" action='' method='post' id="password_form">
				<div>
					<label for="password">
						<?=$passwdpasswdtext?>
					</label>
					<input type="password" name="password" value="" />
				</div>
				<div>
					<label for="confirm">
						<?=$passwdconfirmtext?>
					</label>
					<input type="password" name="confirm" value="" />
				</div>
				<button name="submit" type="submit">Change Password</button>
				<span id="password_result"></span>
				<input type="hidden" id="ens_passwd_token" name="ens_passwd_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
			</form>

			<h2>Unsubscribe from ENS</h2>
			<form method="post" action="" name="unsubscribe_confirm" id="unsubscribe_confirm" class="ens_form">
				<p>
					Please allow up to 3 days to unsubscribe during a significant earthquake sequence. You will continue to receive notifications during that time.
				</p>
				<button type="submit" name="unsubscribe_confirm_button" id="unsubscribe_confirm_button">Unsubscribe</button>
			</form>

			<h2>Vacation</h2>
			<form method="post" action="" name="vacation" id="vacation" class="ens_form">
				<input type="hidden" name="mode" value="vacation" id="mode" />
				<p>You can suspend notifications for any amount of time. We <strong>do not</strong> save undelivered messages for your return.</p>
				<div id="vacation_current"></div>
				<div>
				<label for="lveDate">
					Leaving On
				</label>
				<input type="text" name="lveDate" id="lveDate" value="<?=$startdate; ?>" maxlength="10" placeholder="YYYY-MM-DD" />
				</div>
				<div>
					<label for="rtnDate">
						Returning On
					</label>
					<input type="text" name="rtnDate" id="rtnDate" value="<?=$enddate; ?>" maxlength="10" placeholder="YYYY-MM-DD" />
				</div>
				<input type="hidden" id="ens_vacation_token" name="ens_vacation_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">                
				<div>
					<button type="submit" name="addVacation" id="addVacation" >Suspend Notifications</button>
					<span id="vacation_result"></span>
				</div>
			</form>
		</div>

	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	currentVacations();

	$('#lveDate').datepicker( {dateFormat: 'yy-mm-dd'});
	$('#rtnDate').datepicker( {dateFormat: 'yy-mm-dd'});
});
</script>
