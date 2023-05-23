<?php

if (!isset($TEMPLATE)) {
  include_once('functions.inc.php');
	include_once('inc/config.inc.php');
	include_once('inc/functions.inc.php');
	include_once("inc/securimage/securimage.php");

	session_start();

  // Assume english
  include "inc/textdefs0.inc.php";

  $language = intval(param('language'));
  // Overwrite the language if required
  if($language == '') $language = 0;
  include_once "inc/textdefs${language}.inc.php";

	$path_inc = 'inc';

	// a simple check to include the template if this page isn't being called in the thickbox
	$ajax_flag = param('ajax');
	if($ajax_flag != 'true') {
		include "template.inc.php";
	}
} else {
	// if this page is called in thickbox, we still need to attache the stylesheet
	$styles = dirname(htmlspecialchars($_SERVER['PHP_SELF'])) . "/../css/styles.css";
	print '<!doctype html>
<html>
<head>
  <title>Earthquake Notification Service</title>
<style type="text/css"><!--
.popup_control { display: none; }
--></style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<meta http-equiv="Content-Language" content="en-us"/>
<meta name="keywords" content="aftershock,earthquake,epicenter,fault,foreshock,geologist,geophysics,hazard,hypocenter,intensity,intensity scale,magnitude,magnitude scale,mercalli,plate,richter,seismic,seismicity,seismogram,seismograph,seismologist,seismology,subduction,tectonics,tsunami,quake"/>
<meta name="description" content="USGS Earthquake Hazards Program, responsible for monitoring, reporting, and researching earthquakes and earthquake hazards"/>';

	print "<link rel=\"stylesheet\" type=\"text/css\" href=\"$styles\" />";

	print '</head><body>
	';

}

//if the user has submitted the form, send them an email, then close this window
if(isset($_POST['email_sample'])) {
    validateCSRF();

	// Check the captcha
	$img = unserialize($_SESSION['captcha']);
	#print_r($img);
	#print_r($_POST);
	#print_r($_SESSION);

	$captcha_valid = $img->check($_POST['code']);

	if ( $captcha_valid == true ) {

	// Check if we were able to get the user's timezone information
	if(isset($_POST['timezone']) && $_POST['timezone'] != '') {
		$user_timezone = intval($_POST['timezone']);
	}
	else {
		$user_timezone = 0; //default to UTC if we didn't pass a timezone
	}

        $email = trim($_POST['email_sample']);

	// If they were trying to send to a phone, don't let them
	if( isPhone($email) ) {
		$output_message = '<p>Sorry. The sample email cannot be sent to a cell phone.
				If you want to subscribe, you can use the
				<a href="register">registration page</a></p>';
		print $output_message;
		return;
	}

        $headers = "From: ens@ens.usgs.gov\r\nMime-Version: 1.0\r\n";
        // To send HTML mail, the Content-type header must be set
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

	// Set up the default values to add to the mailconfirm table
	$r_code = $_POST['code'];
	$r_replace = 'N';
	$r_id = 0;
	$r_format = 'HTML';
	//$r_begin = (8 - $user_timezone )%24;
	//$r_end = (20 - $user_timezone)%24;
  // Default begin and end to 0. We only send sample messages to email accounts, and day/night doesn't mean much for email.
  $r_begin = 0;
  $r_end = 0;
	$r_uid = 0; // If the user used "Try it now", they don't have a user id yet.
  $r_confnum = rand(99,999);
  $r_hconf = md5($r_confnum);

	// We need to put in a check that only allows the user to send 5 sample emails
	$spam_check_query = $database->prepare("SELECT COUNT(*) as cnt, email FROM mailconfirm WHERE uid=0 AND email=:email GROUP BY email");
	$spam_check_rs = $spam_check_query->execute(array(
    ':email' => $email
  ));
 	$spam_check_row = $spam_check_query->fetchAll(PDO::FETCH_ASSOC);
	if(count($spam_check_row) >= 5) {
		$output_message = '<p><strong class="alert error">Failed to send email</strong></p>
				<p>You have sent too many sample emails to ' . $email .'.</p>
				<p>If you believe you received this message as a mistake, <a href="mailto:lisa@usgs.gov">contact us</a></p>';
	}
	else {

	        $subject = 'Sample email from the Earthquake Notification Service';
		$urlstring = "https://" . $_SERVER['HTTP_HOST'] . dirname(htmlspecialchars($_SERVER['PHP_SELF'])) . "/register?email=$email&c=$r_hconf";
		$message = file_get_contents("inc/emailsample_html.inc.php");
	       	$message = sprintf($message, $urlstring, $email);
	        $query_request = $database->prepare(
	               "INSERT INTO mailconfirm (uid, email, format, day_begin, day_end, hashconf)
	        	       VALUES (:r_uid, :email, :r_format, :r_begin, :r_end, :r_hconf)");

	        $query_rs = $query_request->execute(array(
            ':r_uid' => $r_uid,
            ':email' => $email,
            ':r_format' => $r_format,
            ':r_begin' => $r_begin,
            ':r_end' => $r_end,
            ':r_hconf' => $r_hconf
          ));


		if(mail($email, $subject, $message, $headers)) {
				$output_message = "<p><strong class='alert success'>Sample email sent to $email successfully.</strong></p>
					<p>Please check your email</p>";

			// If they used a common email provider, give them a link to the email login page
			$email_provider = array("yahoo.com" => "mail.yahoo.com", "gmail.com" => "gmail.com", "hotmail.com" => "mail.live.com", "live.com" => "mail.live.com", "aol.com" => "mail.aol.com", "msn.com" => "mail.live.com");
			$user_provider = substr(strstr($email, '@'), 1);
			if(array_key_exists($user_provider, $email_provider)) {
				$output_message .= '<a href="http://'.$email_provider[$user_provider].'" target="_blank" >Go to '.$user_provider.'</a>';
			}
			// if we sent the email correctly, we want to close the window after 6 seconds
			$output_message .= '<script type="text/javascript">setTimeout("parent.tb_remove()", 6000);</script>';
		} //end if(mail(
	} // end spam_check if/else

	print $output_message;
	} // end of if($captcha_valid)
	else {
		print "<p class='alert error'>You didn't enter the correct code.</p>";
		print "<p>If you need assistance, <a href='mailto:ensadmin@usgs.gov'>please contact us.</a></p>";

	}


}
else {

	$captcha =  new Securimage();
	$_SESSION['captcha'] = serialize($captcha);

	$captcha_id = md5(uniqid(time()));
?>


<div id="send_email">
<h2>ENS will send you a sample email notification message.</h2>
<h4>If you choose to subscribe to ENS, the sample email provides a quick link to do so.</h4>
<form action="<?php print htmlspecialchars($_SERVER['PHP_SELF']); ?>?width=370&amp;height=390" class="email_sample_form thickbox" id="email_sample_form" method="post"  >
<fieldset>
	<label for="email_sample"><?=$sampleemailentertext; ?></label>
	<input type="text" id="email_sample" name="email_sample" value="" tabindex="1" />
</fieldset>


<fieldset>

<div id="captcha_div">

<a href="#" id="refresh_captcha">
                <img src="<?=$path_inc;?>/securimage/images/arrow_refresh.png" alt="Refresh CAPTCHA image" style="border: 0px none" />
                Get New Code
        </a>
&nbsp;
<a href="<?=$path_inc;?>/securimage/securimage_play.php?sid=<?php echo $captcha_id; ?>" id="show_audio">
                <img src="<?=$path_inc;?>/securimage/images/sound.png" alt="Play CAPTCHA audio" style="border: 0px none" />
	Audio</a>

	<img id="captcha" height="45" valign="middle" width="175" alt="Captcha image"  src="<?=$path_inc;?>/securimage/securimage_show.php?sid=<?php echo $captcha_id; ?>" />


</div>

<div id="captcha_info">
		<img src="<?=$path_inc;?>/securimage/images/help.png" alt="Question Mark" /> This ensures that a person, not an automated program, is submitting this form.
</div>

	<label for="code">Enter word shown in image above</label>
<input type="text" name="code" id="code" tabindex="2" />


</fieldset>
<fieldset>
	<input type="submit" id="send_button" class="ens_btn" value="<?=$sampleemailsubmit;?>" />

	<input type="hidden" name="ajax" value="<?=$ajax_flag?>" />

</fieldset>


</form>

<script type="text/javascript" src="/lib/jquery-1.11.0/jquery.js"></script>
<script type="text/javascript">

		$('#email_sample_form').submit(function (event) {
			event.preventDefault();
			// try to work out the user's timezone information
			var d = new Date();
			var timezone = (1+d.getTimezoneOffset()/60)*(-1);
			$.post($(this).attr('action'), {email_sample: $('#email_sample').val(), timezone: timezone, ajax: true, code: $("#code").val() },  function(html) {
				$('#send_email').html(html);
			});

		});

		$("#show_audio").click( function(event) {
			if( $("#captcha_audio").length == 0 ) {
				var emb = '<embed type="audio/x-wav" src="<?=$path_inc;?>/securimage/securimage_play.php?sid=<?php echo $captcha_id; ?>" style="display: none" autostart="true" autoplay="true"  loop="false"  width="175" height="25" id="captcha_audio" />';
				$("#captcha").after(emb);
				$("#captcha_audio").css("display", "block");
				$("#captcha_audio").attr("autostart", "true");
			}
			event.preventDefault();
		});

		$("#refresh_captcha").click( function(event) {
			event.preventDefault();
			var id = Math.ceil(Math.random()*1000);
			$("#captcha").attr("src",  '<?=$path_inc; ?>/securimage/securimage_show.php?sid=' +id );
                        var emb = '<embed type="audio/x-wav" src="<?=$path_inc;?>/securimage/securimage_play.php?sid=' + id +'" style="display: block" autostart="true" autoplay="true"  loop="false"  width="175" height="20" id="captcha_audio" />';

			$("#captcha_audio").after( emb ).remove();
		});

 </script>

<?php
}
if($ajax_flag == 'true') {

	print "</div></body></html>";
}

?>
