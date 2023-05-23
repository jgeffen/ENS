<?php
include_once("../config.inc.php");
include_once("../maps_functions.inc.php");
include_once("../textdefs0.inc.php");
include_once("../functions.inc.php");
include_once("functions.inc.php");

session_start();

$USER_INFO = null;
$USER_EMAILS = null;
if (isset($_SESSION['USER_INFO'])) {
    $USER_INFO = $_SESSION['USER_INFO'];
}

if (isset($_SESSION['USER_EMAILS'])) {
    $USER_EMAILS = $_SESSION['USER_EMAILS'];
}

	$TITLE = $passwdheadertext;
	$startdate = null;
	$enddate = null;

	if(isset($_GET['mode']) && $_GET['mode'] == "unsubscribe_confirm") {
		?>
		<div id="unsubscribe_wrapper">
		<h3>Are you sure you want to unsubscribe from the Earthquake Notification Service?</h3>
		<form method="post" action="" name="unsubscribe" id="unsubscribe" class="ens_form">
			<input type="hidden" id="ens_unsub_token" name="ens_unsub_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
			<button type="submit" name="unsubscribe_button" id="unsubscribe_button">Yes, unsubscribe</button>
			<button name="unsubscribe_cancel" id="unsubscribe_cancel">No, don't unsubscribe</button>
		</form>
		</div>
		<script type="text/javascript">
		<!--
		// Don't allow the submit action to actually submit the form.
		// They have to actually click on the unsubscribe button
		$("#unsubscribe_confirm").submit( function(e)  {e.preventDefault();})

		$("#unsubscribe_button").click( function(e) {
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: "inc/ajax/account_prefs_ajax.inc.php",
				data: { mode:"unsubscribe" },
				headers: getCSRFHeader($("#ens_unsub_token").val()),
				success: function(html) {
					$("#unsubscribe_wrapper").html(html);
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown){
				$("#unsubscribe_wrapper").html('<h3 class=\"alert error\">Failed to delete your account.</h3>');
			});  
		});

		-->
		</script>

		<?php
	}

	if(isset($_POST['mode']) && $_POST['mode'] == 'unsubscribe') {
		validateCSRF();

		unsubscribe($USER_INFO['id']);
	}

	if(isset($_POST['mode']) && $_POST['mode'] == 'change_pass') {
		validateCSRF();
    
		$password = $_POST['password'];
		$confirm = $_POST['confirm'];

		// Now update the database information
		if($password != "" && $confirm != "") {
			if($password != $confirm) {
				print '<h3 class="alert warning">The confirm and password fields did not match.  Password is unchanged.</h3>';
				return;
			} else {
				$hash = md5($password);
				$query = $database->prepare('
					UPDATE mailusers
					SET
						hashpasswd= :hashpass
					WHERE id= :id
					LIMIT 1');

					$result = $query->execute(array(
						':hashpass' => $hash,
						':id' => $USER_INFO['id']));

				if( $result ) {
					print '<h3 class="alert success">Password changed</h3>';
					$USER_INFO['hashpasswd'] = md5($password);
					$_SESSION['USER_INFO'] = $USER_INFO;
				}
				else {
					print '<h3 class="alert warning">Failed to change password.</h3>';
				}

			}
		} else {
			print '<h3 class="alert warning">Password is unchanged</h3>';
			return;
		} // End:: if(password & confirm)
	}

	if(isset($_POST['mode']) && $_POST['mode'] == "update") {
		validateCSRF();
    
		// Get the variables from the post...
		extract($_POST);

		// Figure out the user class
		$userclass = 0;
		if (strstr($affiliation, "Scientist")) {
			$userclass = 2;
		}
		if (strstr($affiliation, "Emergency Services") ||
			strstr($affiliation, "First Responder") ||
			strstr($affiliation, "Critical Facility Operator") ||
			strstr($affiliation, "Elected Official or Staff") ||
			strstr($affiliation, "News Media Representative") ||
			strstr($affiliation, "Other Media Representative")) {
			$userclass = 1;
		}

		$query_update = $database->prepare("
			UPDATE mailusers
			SET
				name=:name,
				timezone=:timezone,
				language=:language,
				userclass=:userclass,
				aftershock=:aftershock,
				updates=:updates,
				defer=:defer,
				affiliation=:affiliation,
				otherinterest=:otherinterest
			WHERE id=:id");

		if($result = $query_update->execute(array(
			':name' => $name,
			':timezone' => $timezone,
			':language' => $language,
			':userclass' => $userclass,
			':aftershock' => $aftershock,
			':updates' => $updates,
			':defer' => $defer,
			':affiliation' => $affiliation,
			':otherinterest' => $otherinterest,
			':id' => $USER_INFO['id']
				))) {
			print ("<h3 class=\"alert success\">Account Successfully Updated!</h3>\n");
		}
		else {
			print ("<h3 class=\"alert error\">Failed to update account.  Please try again later.</h3>\n");
		}
	} else if(isset($_POST['mode']) && $_POST['mode'] == 'vacation_add') {
		validateCSRF();
        
		$startdate = param('lveDate');
		$enddate = param('rtnDate');
		$date = "/^\d{4}-\d{2}-\d{2}$/";
		$success = (preg_match($date, $startdate) && preg_match($date, $enddate));
		if(!$success) {$startdate = null; $enddate = null;}

		// Update their profile if they are leaving today
		if($startdate == date("Y-m-d")) {
			$query_profiles = $database->prepare("UPDATE mailparams SET active='N' WHERE userid=:id");
			$success = ($success && $query_profiles->execute(array(':id' => $USER_INFO['id'])));
		}

		$query_vacation = $database->prepare("INSERT INTO mailvacation (userid, startdate, returndate)
		VALUES (:id, :startdate, :enddate)");
		$success = ($success && $query_vacation->execute(array(
			':id' => $USER_INFO['id'],
			':startdate' => $startdate,
			':enddate' => $enddate
		)));

		if($success)
			print 'success';
		else
			print 'error';

		//extract($USER_INFO);
	} else if(isset($_POST['mode']) && $_POST['mode'] == 'vacation_current') {
		validateCSRF();
        
		$userid = $USER_INFO['id'];
		$today = date("Y-m-d");
		$query = $database->prepare('
			SELECT *
			FROM mailvacation
			WHERE
				userid = :id AND returndate > :today
			ORDER BY startdate ASC');
		if($query->execute(array(
			':id' => $USER_INFO['id'], ':today' => $today ))) {

			$rows = $query->fetchAll(PDO::FETCH_ASSOC);
			if (count($rows) > 0) {
				print '<strong>Current Vacations</strong><br />';
				print '<ul class="currentVacations">';
				foreach ($rows as $row) {
					print '<li>Start: ' . $row['startdate'] . ' End: ' .
					$row['returndate'];
					print ' <a href="#" class="remove_vacation" rel="' .
					$row['vid'] . '">Remove</a></li>';
				}
				print '</ul>';
			}
		}
	} else if (isset($_POST['mode']) && $_POST['mode'] == 'vacation_delete') {
		validateCSRF();
        
		$vid = $_POST['id'];
		$today = date("Y-m-d");
		$query = $database->prepare('UPDATE mailvacation set returndate=:today where vid=:vid LIMIT 1');

		if( $query->execute(array(':today' => $today, ':vid' => $vid))) {
			print 'success';
		} else {
			print 'error';
		}
	} // END: if(isset($_POST['mode']))

?>
