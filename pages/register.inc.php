<?php
	// Assume english
	include $FILE_PATH . "/inc/textdefs0.inc.php";
	$TITLE = "Subscribe to the Earthquake Notification Service";
	$WIDGETS = 'jquery';
	$SCRIPTS = 'inc/js/register.js,inc/js/jquery.plugins.js';
	$success = true;
	$errstring = "";
	$username = param('username');
	$name = param('name');
	$timezone = intval(param('timezone'));
	$language = intval(param('language'));
	$affiliation = param('affiliation');
	$otherinterest = param('otherinterest');
	$updates = param('updates');
	$defer = param('defer');
	$password = param('password');
	$confirm = param('confirm');
	$email = trim(param('email'));
	$hashed_confirm = param('c'); // If the user is coming from the sample email, they'll have a hashed confirm code
	$already_confirmed = 0;
	$no_sample = false;


	// Overwrite the language if required
	if($language == '') $language = 0;
	include_once $FILE_PATH . "/inc/textdefs${language}.inc.php";
    include_once('inc/functions.inc.php');

    if(isset($_POST['mode']) && $_POST['mode'] == "register") {

        session_start();

        $isValidRequest = validateCSRF(param("ens_register_token"));
        if (!$isValidRequest) {
            $success = false; 
            $errstring .= "\t<li>Invalid request.</li>\n";
        } else {
            // skip other checks if the CSRF fails
            if($username == "" || $email == "" || $password == "") { 
                $success = false; 
                $errstring .= "\t<li>Username, email, and password are required.</li>\n";
            }

            // Check that the email doesn't have a space
            if( strpos($email, ' ') !== false ) {
                $success = false;
                $errstring .= "\t<li>Email address cannot contain spaces.</li>\n";
            }

            // Check for valid email address
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $success = false;
                $errstring .= "\t<li>Invalid email address format.</li>\n";
            } else if (!isValidMobileAddress($email)) {
                // no need to check mobile if not a valid email
                $success = false;
                $errstring .= "\t<li>Mobile phone number address must use 10-digit phone number.</li>\n";
            }

            // Trim leading and trailing spaces from the username
            $username = trim($username);

            if($password != $confirm) {
                $success = false;
                $errstring .= "\t<li>Password and confirmed did not match.</li>\n";
            }

            // Check that the email address isn't already used
            #printf ("Looking for email %s<br>", $email);
            $query_email_exists = $dbreadonly->prepare('SELECT eid,email FROM mailaddresses WHERE email = :email LIMIT 1');

            $rs_exists = $query_email_exists->execute(array(
                ':email' => $email
            ));
            $rows = $query_email_exists->fetch(PDO::FETCH_ASSOC);
            if($rows['email'] != '') {
                $success = false;
                $errstring .= "\t<li>The email you provided is associated with another account. You
                        can try to <a href=\"recover\">recover your password</a>.</li>\n";
            }

            // Check to make sure the username does not already exist
            $query_exists = $database->prepare("SELECT username FROM mailusers WHERE username=:username");
            $rs_exists = $query_exists->execute(array(
                ':username' => $username
            ));
            $rows = $query_exists->fetch(PDO::FETCH_ASSOC);
            if($rows['username']) {
                $success = false;
                $username = '';
                $errstring .= "\t<li>The username you provided is already taken by someone else.</li>\n";
            }
        }

		if($success) {
			// Create the account in the mailusers
			$nowstr = date("Y-m-d H:i:s");
			$query_insert = $database->prepare(
				"INSERT INTO mailusers
				(
					username, name, timezone,
					 updates, defer, hashpasswd,
					 affiliation, added, lastlogin
				) VALUES (
					:username, '', :timezone, 'N', 'N', :hashpasswd, 'General Public', :nowstr, :nowstr
				)");

			$rs_insert = $query_insert->execute(array(
				':username' => $username,
				':timezone' => $timezone,
				':hashpasswd' => md5($password),
				':nowstr' => $nowstr,
				':nowstr' => $nowstr
			));

      if(!$rs_insert) {
        print '<h3 class="alert error">There was a problem registering your account. Try again later or contact us.</h3>';
        return;
      }


			$userid = $database->lastInsertId();


			$pids = array();
			// Create the default profiles for the user
			$query_insert = $database->prepare("INSERT INTO mailparams (userid, regionid, comments)
			VALUES (:userid, 398, 'Default World')");

			if (!$query_insert->execute(array(':userid' => $userid))) {
        print '<h3 class="alert error">There was a problem registering your account. Try again later or contact us.</h3>';
        return;
      } else {
				$pids[] = $database->lastInsertId();
			}

			$query_insert = $database->prepare("INSERT INTO mailparams (userid, regionid, comments)
			VALUES (:userid, 474, 'United States')");

			if(!$query_insert->execute(array(':userid' => $userid))) {
        print '<h3 class="alert error">There was a problem registering your account. Try again later or contact us.</h3>';
        return;
      } else {
				$pids[] = $database->lastInsertId();
			}

			// Look to see if they have a hashed confirmation number
			// This should only be the case if they are coming from a link in an email
			if($hashed_confirm != '') {
				$cid = 0;
				$query_confirmed = $database->prepare("SELECT * FROM mailconfirm WHERE uid=0 AND email=:email AND hashconf=:hashed_confirm LIMIT 1");
				if($rs_confirmed = $query_confirmed->execute(array(
					':email' => $email,
				 ':hashed_confirm' => $hashed_confirm
			 	))) {
					$rows = $query_confirmed->fetch(PDO::FETCH_ASSOC);
					$cid = $rows['cid'];
					if ($cid != 0) {
					// The user is coming from an email link and
					//their hashed password is correct,
					// so don't redirect them to the confirm page
						$already_confirmed = true;

					//remove the confirm entry from mailconfirm
	        	$query_clean = $database->prepare("DELETE FROM mailconfirm WHERE cid=:cid LIMIT 1");
						$rs_clean = $query_clean->execute(array(':cid' => $cid));

					// add the email address
	        	$query_addaddr = $database->prepare("INSERT INTO mailaddresses (uid, email, format, day_begin, day_end)
						VALUES (:userid, :email, :format, :day_begin, :day_end)");

						if($query_addaddr->execute(array(
							':userid' => $userid,
							':email' => $email,
							':format' => $rows['format'],
							':day_begin' => $rows['day_begin'],
							':day_end' => $rows['day_end']
						))) {
        		$eid = $database->lastInsertId();
						// associate the email with the profiles
						#printf ("Inserting into email_param_bridge %d/%d %d/%d<br>\n", $eid, $pids[0], $eid, $pids[1]);
          	$query_associate = $database->prepare("INSERT into email_param_bridge (emailid,paramid) values (:eid,:pid1),(:eid,:pid2)");

						$rs_associate = $query_associate->execute(array(
							':eid' => $eid,
							':pid1' => $pids[0],
							':eid' => $eid,
							':pid2' => $pids[1]
						));
					}
				}
				else {
	        print '<h3 class="alert error">There was a problem registering your email address. You will have to add it using the "Register/Confirm/Change Address" page</h3>';

				}
			}
		} // end $rs_confirmed

			// Set these for the redirect
		$hashpasswd = md5($password);
	  $USER_INFO = array('id'=>$userid, 'username'=>$username, 'hashpasswd'=>$hashpasswd, 'lastlogin'=>date("Y-m-d H:i:s"));
	  $USER_EMAILS = array();
	  $LEFT_NAVIGATION = "/template/navigation/navigation.master.inc.php";
	  $firsttime=1;

	  if($already_confirmed) {

      $USER_EMAILS[1] = array('eid'=>$eid, 'uid'=>$userid, 'email'=>$email, 'format'=>$confirmed['format']);
		  $email_count = 1;

		}
			// If we haven't already confirmed they own their address, send a confirm email
			else {

				// The user has signed up, but we need to have them confirm their email
			$r_confnum = rand(99,999);
		  $r_hconf = md5($r_confnum);
	  	$r_begin = (8- $timezone)%24;
		  $r_end = (20 - $timezone)%24;
		  $r_format = "HTML";
		  $headers = "Mime-Version: 1.0\r\n";
		  $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		  $conf_address = sprintf("https://" . $_SERVER['HTTP_HOST'] . dirname(htmlspecialchars($_SERVER['PHP_SELF'])) . "/regaddr?mode=confirm&address=%s&confnum=%s&newaccount=y", $email, $r_confnum);

		  $mailtext = sprintf($newaddremailconftext, $r_confnum);
		  $mailtext .= "<p><a href='$conf_address'>Click here to confirm this email address</a></p>";

	    $query_newconfirm = $database->prepare("INSERT INTO mailconfirm (uid, email, format, day_begin, day_end, hashconf)
        VALUES (:userid, :email, :r_format, :r_begin, :r_end, :r_hconf)");


  	  if($query_newconfirm->execute(array(
			  ':userid' => $userid,
			  ':email' => $email,
			  ':r_format' => $r_format,
			  ':r_begin' => $r_begin,
			  ':r_end' => $r_end,
			  ':r_hconf' => $r_hconf
		  ))) {
        $confirmid = $database->lastInsertId();
        $subject = $newaddremailsubjtext . ' ' . $r_confnum;
        admin_email ($email, $newaddremailsubjtext, $mailtext, $headers, $confirmid);
			  printf("<h3 class=\"alert success\">Confirmation email sent to %s!</h3>\n", $email);
		    print '<p>You will not receive any earthquake notifications until you confirm your email address.
            <br />You can confirm using the link in the email or with the <a href="regaddr">register/confirm address page.</a></p>';
			  $_SESSION['USER_INFO'] = $USER_INFO;
			  $_SESSION['USER_EMAILS'] = $USER_EMAILS;
			  return;
		  }
		  else {
			  print '<h3 class="alert error">There was a problem registering your email address. You will have to add it using the "Register/Confirm/Change Address" page</h3>';
			  return;
		  }
	  }
	  $_SESSION['USER_INFO'] = $USER_INFO;
	  $_SESSION['USER_EMAILS'] = $USER_EMAILS;
			// redirect to the homepage
			// First try to use php, but if that doesn't work (because of the template), use javascript
	  if( !headers_sent() ) {
		  header("Location: userhome_map");
		  return;
	  }
	  else {
		  print "You are being redirected to the Earthquake Notification Service homepage. <a href='userhome_map'>Go there now</a>";
		  print "<script type='text/javascript'>
			  location.href='userhome_map';
			  </script>";
		  return;
	  }

  } else { // Input verification failed
	  print("<h3 class=\"alert error\">An error occurred while creating your account.</h3>\n");
	  printf("<ul>\n%s</ul>\n", $errstring);
	  print("<p>Please correct the problem(s) and try again.</p>\n");
  } // END: if(isset($_POST['mode']) && $_POST['mode'] == "register") {
}

if($language == 1)
	printf("To subscribe in English, <a href=\"%s\">click here</a>.", $WEB_PATH . "/?page=register&language=0");
else
	printf("Para subscribar en Espa&ntilde;ol, <a href=\"%s\">marque aqu&iacute;</a>.", $WEB_PATH . "/?page=register&language=1");
?>

<div class="ens_form">
	<form method="post" action="" name="updateprofile">
	<input type="hidden" name="mode" value="register" />
	<h2>Create an account</h2>
<div>
	<div id="message">
		<?=$errstring;?>
		<br style="clear: right" />
	</div>
	<fieldset>
	<div>

		<label for="email"><?=$passwdemailtext?></label>
	<?php
	$ok_image = '';

	// see if we should make the email field an input field or a hidden field
	$query_confirmed = $database->prepare("SELECT * FROM mailconfirm WHERE uid=0 AND email=:email AND hashconf=:hashed_confirm LIMIT 1");
	$rs_confirmed = $query_confirmed->execute(array(
		':email' => $email,
		':hashed_confirm' => $hashed_confirm
	));

	$rows = $query_confirmed->fetch(PDO::FETCH_ASSOC);
	//$num_rows = count($rows);
	//$email = $rows['email'];
	$cid = $rows['cid'];
	//if ((!$rs_confirmed) && ($success)) {
	//if ($email != '') {
	if ($cid != 0) {
		// their email and confirmation code match, so give them a hidden field
		printf ("<input type=\"hidden\" name=\"c\" value=\"%s\" />", $hashed_confirm);
		printf ("<input type=\"hidden\" name=\"email\" id=\"email\" value=\"%s\" />", $email);
		printf ("<strong>%s</strong>", $email);

		// since we have their email, try to create a username using the first part of the email
		$username = substr($email, 0, strpos($email, '@'));
		// make sure that username isn't taken
		// to save a query, we'll check $username[1-5] also
		$username_query = $database->prepare("
				SELECT username
				FROM mailusers
				WHERE
					username=:username OR
					username=:username1 OR
					username=:username2 OR
					username=:username3 OR
					username=:username4 OR
					username=:username5
				ORDER BY username ASC
				");

		$username_rs = $username_query->execute(array(
			':username' => $username,
			':username1' => $username . "1",
			':username2' => $username . "2",
			':username3' => $username . "3",
			':username4' => $username . "4",
			':username5' => $username . "5"
		));
		$taken_usernames = array();
		while($row = $username_query->fetch(PDO::FETCH_ASSOC)) {
			array_push($taken_usernames, $row['username']);
		}

		if( in_array($username, $taken_usernames) ) {
			for($x=1;$x<=5;$x++) {
				if(!in_array($username . $x, $taken_usernames)) {
					$username = $username . $x;
					$ok_image = '<img src="images/ok.png" id="error_img_username" style="margin: 2px; vertical-align: middle;" alt="Username ok" />';
					break;
				}
			}
		}
		else {
			$ok_image = '<img src="images/ok.png" id="error_img_username" style="margin: 2px; vertical-align: middle;" alt="Username ok" />';

		}

	}
	else {
		if ($email != '') {
			printf ("<input type=\"text\" name=\"email\" id=\"email\" value=\"%s\" />\n", $email);
		} else {
			print '<input type="text" name="email" id="email" value="" />';
		}
		$no_sample = true;
	}
	?>
	</div>
	<div>
		<label for="username"><?=$passwdusernametext?></label>
		<input type="text" name="username" value="<?=$username?>" id="username" />
		<?=$ok_image;?>
	</div>

	<div>
		<label for="password"><?=$passwdpasswdtext?></label>
		<input type="password" name="password" value="" id="password" />
	</div>

	<div>
		<label for="confirm"><?=$passwdconfirmtext?></label>
		<input type="password" name="confirm" value="" id="confirm" />
        </div>
	</fieldset>
</div>
    <hr />
    <input type="hidden" name="newaccount" value="true" />
    <input type="hidden" name="timezone" id="timezone" value="" />
    <input type="hidden" id="ens_register_token" name="ens_register_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" name="submit" class="ens_btn" id="subscribe_btn" >Subscribe</button>
    <div class="disclaimer_message">
        By clicking Subscribe, you acknowledge you have
        <a href="help#disclaimer" target="_blank">read the disclaimer</a>.
    </div>
    <div class="privacy_statement">
        <a href="help#PRA" target="_blank">PRA - Privacy Act Statement</a>
    </div>

	<div class="disclaimer">
	<strong>Disclaimer</strong><br />
		The  events which have been located by the USGS and contributing agencies  should not be considered to be a complete list of
		ALL events M2.5+ in the US and adjacent areas and especially should not be considered to be complete lists of ALL events M4.0+
		in the World. The World Data Center for Seismology, Denver (a part of the USGS National Earthquake Information Center) continues
		to receive data from observatories throughout the world for several months after the events occurred, and using those data, adds
		new events and revises existing events in later publications. For a description of these later publications and the data available,
		see <a href="/research/index.php?areaID=13">Scientific Data</a>.
	</div>
</form>

</div>

<script type="text/javascript">
$(document).ready( function() {

    // try to work out the timezone
    var d = new Date();
    var timezone = (1+d.getTimezoneOffset()/60)*(-1);
    $("#timezone").val(timezone);
});
</script>
