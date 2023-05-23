<?php
    include_once('inc/functions.inc.php');
    session_start();
?>

<div id="main">

	<div class="six column col">
		<p>The Earthquake Notification Service (ENS) is a free service that can send you automated notification emails when earthquakes happen in your area.
		</p>
		<div class="centered">
            <img src="<?= $WEB_PATH ?>/images/preview_sm.jpg" alt="Sample ENS notification message" />
			<p style="margin: 1px; font-size: 80%; color: #555555">(Example of an ENS notification email)</p>
		</div>

		<p>
			New accounts default to receiving notifications about earthquakes with
			magnitude 6.0 or greater however you can customize ENS to only deliver
			messages for certain areas, at specified times, and to multiple
			addresses.
			For most carriers, ENS can also send text notifications to your cell phone.
		</p>
		<p>
			<a href="help">Instructions and More Information</a>
		</p>

		<p style="text-align: center;">
			<button type="button" class="btn"><a href="<?= $WEB_PATH . "/register" ?>">Subscribe to ENS
            </a>
            </button>
            <br />
			<a href="<?= $WEB_PATH . "/register" ?>">Suscr&iacute;bete a ENS
            </a>
		</p>
	</div>

	<?php
		if(in_array($page, $nologinok)) {
			$topage = "userhome_map";
		} else if ($page == "login") {
			$topage = "userhome_map";
		} else {
			$topage = $page;
			print ("<p class=\"alert error\">You must be logged in to view this page!</p>\n");
		}
	?>

	<div class="four column col">
<?php
// This simple check should be sufficient to see if we need to give an error message
// It will only come up when the login page is displayed after the user has entered a username/password
if(isset($_POST['username']) && isset($_POST['textpass'])) {
    if ($IS_ACCOUNT_LOCKED) {
?>
        <div class="alert error" style="background:#FFFFEE">
            <p>Your account has been temporarily locked due to too many failed login attempts. Please try again later.</p>
        </div>
<?php
    } else {
        if (!$isValidRequest) {
?>
            <div class="alert error" style="background:#FFFFEE">
                <p>Invalid Request. <br />
                Please refresh this page and retry.</p>
            </div>
<?php
        } else {
?>
            <div class="alert error" style="background:#FFFFEE">
                <p>Invalid Username/Password combination. <br />
                Please carefully re-enter your information.</p>
                <p><a href="<?= $WEB_PATH . "/recover" ?>">Forgot username/password?</a></p>
            </div>
<?php
        }
    } 
}// if username/textpass
?>
<form  method="post" action="<?=$WEB_PATH?>/" id="frmLogin" >
<h2>Manage Your Account</h2>
<fieldset>
    <input type="hidden" name="initlogin" value="1" />
    <input type="hidden" name="jsEnabled" value="false" id="jsEnabled" />
    <input type="hidden" name="page" value="<?=$topage?>" />
    <label for="username">Username:</label>
    <input id="username" name="username" type="text" />
    <label for="textpass">Password:</label>
    <input id="textpass" name="textpass" type="password" />
    <input type="hidden" id="ens_login_token" name="ens_login_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
<button class="ens_btn" type="submit" name="login">Login</button>
</fieldset>
</form>
		<p><a href="<?= $WEB_PATH . "/recover" ?>">Forgot username/password</a></p>
		<p><a href="<?= $WEB_PATH . "/register" ?>">Sign up for ENS</a></p>
		<p><a href="<?= $WEB_PATH . "/unsubscribe" ?>">Unsubscribe from ENS</a></p>
</div>
</div>
<script type="text/javascript">
$(document).ready( function() {
	$(".thickbox").each( function(i) {

		var href = $(this).attr("href");
		$(this).attr("href", href + "&ajax=true");
	});

	// Use jquery to set the value of jsEnabled hidden field
	// This allows us to check that the user has javascript enabled and that jquery is working
	$("#jsEnabled").val("true");
});

</script>
