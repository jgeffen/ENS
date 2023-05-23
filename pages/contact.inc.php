<?php
// THIS FROM IS CURRENTLY UNUSED
//
//
//
//

	// Correct the page title
	$TITLE = "Contact US";

	include_once('inc/config.inc.php');
	include_once('inc/functions.inc.php');
	include_once("inc/securimage/securimage.php");
	session_start();

	$submission_error = false;
	$message_error = '';
	$code_error = '';

	if(isset($_POST['submit'])) {
		$message = trim($_POST['message']);
		$code = trim($_POST['code']);
		$email = trim($_POST['email']);
		$subject = trim($_POST['subject']);

		if( $message == '' ) {
			$submission_error = true;
			$message_error = 'You must enter a message';
		}
		if( $code == '' ) {
			$submission_error = true;
			$code_error = 'Your confirmation code did not match';
		}

		$captcha = unserialize($_SESSION['captcha']);
		$captcha_valid = $captcha->check($code);
		if( !$captcha_valid ) {
			$submission_error = true;
			$code_error = 'Your confirmation code did not match';
		}

		if( !$submission_error ) {
			print '<h3>Thank you for your feedback</h3>';
			print '<p>Your comments are very valuable in helping us make ENS better.</p>
			  <p><a href="' . $CONFIG['MOUNT_PATH'] . '/">' . 
        'Return to the ENS main page</a></p>';
			$receivers = "sdaugherty@usgs.gov, lisa@usgs.gov";
			$headers = "From: USGS ENS <ens@ens.usgs.gov>\r\nMime-Version: 1.0\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

			$emailSubject = "Feedback from ENS contact form";
			$emailMessage .= '
				The following comment was submitted using the ENS contact form on ' . date("F d, Y") . ' at ' . date("H:i") . '<br /><br />
				<strong>Sender:</strong> ' . $email . ' <br />
				<strong>Subject:</strong> ' . $subject . ' <br />
				<strong>Message:</strong><br /> ' .
				$message;
			mail($receivers, $emailSubject, $emailMessage, $headers);
		}
	}

	if(!isset($_POST['submit']) || $submission_error) {
		$captcha =  new Securimage();
		$_SESSION['captcha'] = serialize($captcha);
		$captcha_id = md5(uniqid(time()));
?>
<h3>Contact us</h3>
<p>Have questions, comments, or complaints about ENS? Fill out the form and let us know!</p>
<p>Before sending a message, check the <a href="help">help page</a> to see if your question has already been answered.</p>

<?php if( $submission_error ) { print '<h3 class="alert errror">There was a problem with the form</h3>'; } ?>

<form class="vertical_form" action="contact" method="post" id="contact_form">
<ul>
<li>
	<label for="email">Your email</label>
	<input type="text" name="email" id="email" />
</li>
<li class="help">
	<p>(optional)</p>
</li>
<li>
	<label for="subject">Subject</label>
	<input type="text" name="subject" id="subject" />
</li>
<li class="help">
	<p>(optional)</p>
</li>
<li>
	<label for="message">Message</label>
	<textarea name="message" id="message" rows="10" cols="50" class="required"><?php if(isset($_POST['message'])) { print trim($_POST['message']); } ?></textarea>
</li>
<li class="help">
	<p>
		<?php if( $submission_error && $message_error != '' ) {
			print '<strong class="alert error">'.$message_error.'</strong><br />';
			}
		?>
		(required)
	</p>
</li>
<li>

<a href="#" id="refresh_captcha">
                <img src="inc/securimage/images/arrow_refresh.png" alt="Refresh CAPTCHA image" style="border: 0px none" />
                Get New Code
        </a>
&nbsp;
<a href="inc/securimage/securimage_play.php?sid=<?php echo $captcha_id; ?>" id="show_audio">
                <img src="inc/securimage/images/sound.png" alt="Play CAPTCHA audio" style="border: 0px none" />
        Audio</a>
</li>
<li class="help">
        <img id="captcha" height="45" valign="middle" width="175" alt="Captcha image"  src="inc/securimage/securimage_show.php?sid=<?php echo $captcha_id; ?>" />
</li>
<li>
	<label for="code">Enter letters shown in the image</label>
	<input type="text" name="code" id="code" class="required" />
</li>
<li class="help">
	<p>
		<?php if( $submission_error && $code_error != '' ) {
			print '<strong class="alert error">'.$code_error.'</strong><br />';
			}
		?>
		(required) This check prevents automated programs from using this form to send spam.
	</p>
</li>
<li>
	<button type="submit" class="button_small" name="submit" id="submit">Send Message</button>
</li>
</form>


<script type="text/javascript">
$("#show_audio").click( function(event) {
	if( $("#captcha_audio").length == 0 ) {
		var emb = '<embed type="audio/x-wav" src="inc/securimage/securimage_play.php?sid=<?php echo $captcha_id; ?>" style="display: none" autostart="true" autoplay="true"  loop="false"  width="175" height="25" id="captcha_audio" />';
		$("#captcha").after(emb);
		$("#captcha_audio").css("display", "block");
		$("#captcha_audio").attr("autostart", "true");
	}
	event.preventDefault();
});

$("#refresh_captcha").click( function(event) {
	event.preventDefault();
	var id = Math.ceil(Math.random()*1000);
	$("#captcha").attr("src",  'inc/securimage/securimage_show.php?sid=' +id );
	var emb = '<embed type="audio/x-wav" src="inc/securimage/securimage_play.php?sid=' + id +'" style="display: block" autostart="true" autoplay="true"  loop="false"  width="175" height="20" id="captcha_audio" />';

	$("#captcha_audio").after( emb ).remove();
});

$("#contact_form").submit( function(event) {
	$(".required").each( function() {
		if($(this).val() == '') {
			event.preventDefault();
			alert("You must fill in all required fields");
			return;
		}
	});
});

 </script>

<?php

} // end !isset($_POST['submit']) || $submission_error == true)

?>
