<?php
include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once("../maps_functions.inc.php");
include_once("../textdefs0.inc.php");


session_start();

$USER_INFO = $_SESSION['USER_INFO'];
$USER_EMAILS = $_SESSION['USER_EMAILS'];


if(isset($_POST['mode']) && $_POST['mode'] == 'get_pending') {
  validateCSRF();
  
  get_pending_emails( $USER_INFO['id'] );
}

if(isset($_POST['mode']) && $_POST['mode'] == 'resend_code') {
  validateCSRF();    
    
  // Instead of rewriting all the code to remake a confirmation code,
  // We'll just add the email address again.
  $email = urldecode($_POST['email']);
  $query = $database->prepare('SELECT * FROM mailconfirm WHERE email=:email LIMIT 1');

  //print $query;
  $query_rs = $query->execute(array(':email' => $email));

  while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    $_POST['mode'] = 'save_email';
    $_POST['address'] = $row['email'];
    $_POST['replacement'] = $row['rid'];
    $_POST['day_begin'] = $row['day_begin'];
    $_POST['day_end'] = $row['day_end'];
    $_POST['format'] = $row['format'];
  }
  // Now we let all this data get picked up by the next if statment
  printf ("Trying to resend to %s<br>", $email);
}

if(isset($_POST['mode']) && $_POST['mode'] == 'save_email') {
  validateCSRF();

  $USER_INFO = $_SESSION['USER_INFO'];

  $email = trim($_POST['address']);
  $day_begin = $_POST['day_begin'];
  $day_end = $_POST['day_end'];
  $format = $_POST['format'];
  $rid = $_POST['replacement'];

  if( $rid != '' && $rid != 0 ) {
    $doreplace = 'Y';
  }
  else {
    $doreplace = 'N';
  }
  $confnum = rand(99, 999);
  $hashconf = md5($confnum);

  $uid = $USER_INFO['id'];

  // If the email contains spaces, give an error then break
  if( strpos($email, ' ') !== false ) {
    print '<h3 class="alert error">Error: Email address cannot contain spaces.</h3>';
    return;
  }

  // Check for valid email address
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    print '<h3 class="alert error">Error: Invalid email address format.</h3>';
    return;
  }

  if (!isValidMobileAddress($email)) {
    print '<h3 class="alert error">Error: Mobile phone number address must use 10-digit phone number.</h3>';
    return;
  }

  // First check that this email address isn't associated with another account
  $query_exists = $dbreadonly->prepare('
    SELECT
      *
    FROM
      mailaddresses
    WHERE
      email = :email
  ');

  $query_exists->execute(array(
    ':email' => $email
  ));

  $rs_exists = $query_exists->fetchAll(PDO::FETCH_ASSOC);

  // Make sure this address is not already associated with another account
  if (count($rs_exists) == 0) {

    // See if the first 10 digits are numbers, which means they are using a phone number
    $is_phone = isPhone($email);
    // Send the confirmation and put the request in the database
    $conf_address = sprintf("https://" . $_SERVER['HTTP_HOST'] .
          $CONFIG['MOUNT_PATH'] .
          "/regaddr?mode=confirm&amp;address=%s&amp;confnum=%s&amp;newaccount=y",
          $email, $confnum);
    $mailtext = sprintf($newaddremailconftext, $confnum);

    if(!$is_phone && $format != 'short') {
      $mailtext .= "<p><a href='$conf_address'>Click here to confirm this email address</a></p>";
    }
    $headers = "Mime-Version: 1.0\r\n";
    if( $is_phone ) {
      $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
    }
    else {
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    }

    $query_request = $database->prepare("INSERT INTO mailconfirm (uid,email,format,day_begin,day_end,hashconf,doreplace,rid)
    VALUES (:uid,:email,:format,:day_begin,:day_end,:hashconf,:doreplace,:rid)");


    $subject = $newaddremailsubjtext . ' ' . $confnum;
    $rs_query = $query_request->execute(array(
      ':uid' => $uid,
      ':email' => $email,
      ':format' => $format,
      ':day_begin' => $day_begin,
      ':day_end' => $day_end,
      ':hashconf' => $hashconf,
      ':doreplace' => $doreplace,
      ':rid' => $rid
    ));
    if($rs_query) {
      $confirmid = $database->lastInsertId($rs_query);
      admin_email ($email, $subject, $mailtext, $headers, $confirmid);
      printf("<h3 class=\"alert success\">Confirmation email sent to %s!</h3>\n", $email);
    } else {
      printf("<h3 class=\"alert error\">An error occured while generating the confirmation message.  Please try again.</h3>\n");
    }
  }  // end if $rs_exists
  else {
    print '<h3 class="alert error">Error: That email address is already registered with another account!</h3>';
  }
}


if(isset($_GET['mode']) && $_GET['mode'] == 'remove_pending') {
  validateCSRF();
  
  $email = urldecode($_GET['email']);

  $pending_query = $database->prepare('DELETE FROM mailconfirm WHERE email=:email');

  if ($pending_query->execute(array(':email' => $email))) {
    print 'success';
  } else {
    print "Could not remove unconfirmed email";
  }
}

if(isset($_GET['mode']) && $_GET['mode'] == 'add_email') {
?>
<link rel="stylesheet" type="text/css" href="css/userhome_map.css">
<h3>Register a new email address with ENS</h3>
<input type="hidden" id="ens_email_token" name="ens_email_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">

<form method="post" action="<?php print htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="add_address_form" class="ens_form">
<div>
  <label for="address">Email Address</label>
    <input type="text" name="address" size="30" />
</div>
<div>
  <label for="replacement">Replaces </label>
    <select name="replacement">

      <option value="0">This is a new address</option>
<?php foreach($USER_EMAILS as $email) {
  printf("<option value='%s'>%s</option>", $email['eid'], $email['email']);
}
?>
    </select>
</div>
<div>
  <label for="day_begin">Day Begins</a></label>
    <select name="day_begin">
      <option value="0">0:00</option>
      <option value="1">1:00</option>
      <option value="2">2:00</option>
      <option value="3">3:00</option>
      <option value="4">4:00</option>
      <option value="5">5:00</option>
      <option value="6">6:00</option>
      <option value="7">7:00</option>
      <option value="8" selected="selected">8:00</option>
      <option value="9">9:00</option>
      <option value="10">10:00</option>
      <option value="11">11:00</option>
      <option value="12">12:00</option>
      <option value="13">13:00</option>
      <option value="14">14:00</option>
      <option value="15">15:00</option>
      <option value="16">16:00</option>
      <option value="17">17:00</option>
      <option value="18">18:00</option>
      <option value="19">19:00</option>
      <option value="20">20:00</option>
      <option value="21">21:00</option>
      <option value="22">22:00</option>
      <option value="23">23:00</option>
    </select>
</div>
<div>
  <label for="day_end">Day Ends</a></label>
    <select name="day_end">
      <option value="0">0:00</option>
      <option value="1">1:00</option>
      <option value="2">2:00</option>
      <option value="3">3:00</option>
      <option value="4">4:00</option>
      <option value="5">5:00</option>
      <option value="6">6:00</option>
      <option value="7">7:00</option>
      <option value="8">8:00</option>
      <option value="9">9:00</option>
      <option value="10">10:00</option>
      <option value="11">11:00</option>
      <option value="12">12:00</option>
      <option value="13">13:00</option>
      <option value="14">14:00</option>
      <option value="15">15:00</option>
      <option value="16">16:00</option>
      <option value="17">17:00</option>
      <option value="18">18:00</option>
      <option value="19">19:00</option>
      <option value="20" selected="selected">20:00</option>
      <option value="21">21:00</option>
      <option value="22">22:00</option>
      <option value="23">23:00</option>
      <option value="24">24:00</option>
    </select>
</div>
<div>
  <label for="format">Message Format</a></label>
  <select name="format">
    <option value="HTML" selected="selected">HTML Format</option>
    <option value="long">Regular Email</option>
    <option value="short">Pager/Cell Phone</option>
    <option value="raw">Raw CUBE format</option>
  </select>
</div>

    <button name="submit" type="submit">Register Address</button>

    <p>To send notifications to your cell phone as a text message, use your 10-digit phone number, and <a href="http://www.makeuseof.com/tag/email-to-sms/" target="_blank">find your phone's address from this page</a>.

  </form>
<script type="text/javascript">

    $('#add_address_form').submit(function (event) {
        event.preventDefault();
        var address = $("#add_address_form [name='address']").val();
        var replacement = $("#add_address_form [name='replacement']").val();
        var day_begin = $("#add_address_form [name='day_begin']").val();
        var day_end = $("#add_address_form [name='day_end']").val();
        var format = $("#add_address_form [name='format']").val();
        
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: { mode: 'save_email', address: address, replacement: replacement, 
                   day_begin: day_begin, day_end: day_end, format: format },
            headers: getCSRFHeader($("#ens_email_token").val()),
            success: function(html) {
                $("#add_address_form").html(html);
                if( html.indexOf('error') == -1 && html.indexOf('Error') == -1 ) {
                    $.ajax({
                        type: "POST",
                        url: "inc/ajax/email_ajax.inc.php",
                        data: { mode: 'get_pending' },
                        headers: getCSRFHeader($("#ens_email_token").val()),
                        success: function (result) {
                            $('#confirm_wrapper').remove();
                            $("#map_header").before(result);
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown){
                        ensAlert("Error", "There was an error retrieving email addresses. Please try again later!");
                    });
                    
                    return true;
                } else {
                    return false;
                }
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown){
            ensAlert("Error", "There was an error saving your email address. Please try again later!");
        });
    });
</script>

<?php

}

if(isset($_POST['mode']) && $_POST['mode'] == 'edit_email_save') {
  validateCSRF();
    
  $eid = $_POST['eid'];
  $uid = $USER_INFO['id'];
  $allow = false;

  // make sure the user is only editing their own email addresses
  foreach($USER_EMAILS as $user_email) {
    if(in_array($eid, $user_email)) {
      $allow = true;
      break;
    }
  }

  if(!$allow) {
    print "You cannot edit this email address.";
    return;
  }

  $day_begin = intval($_POST['day_begin']);
  $day_end = intval($_POST['day_end']);
  $format = $_POST['format'];

  if($day_begin > $day_end) {
    $day_begin = $day_end = 0;
    print("<h3 class=\"alert warning\">Daytime hours must begin before ending.  Daytime was reset to all hours.</h3>\n");
  }
  $day_begin = timeresolve($day_begin - $USER_INFO['timezone']);
  $day_end = timeresolve($day_end - $USER_INFO['timezone']);

  $update_email = $database->prepare("
    UPDATE mailaddresses
    SET
      format=:format,
      day_begin=:day_begin,
      day_end=:day_end
    WHERE eid=:eid");

    $rs_email = $update_email->execute(array(
      ':format' => $format,
      ':day_begin' => $day_begin,
      ':day_end' => $day_end,
      ':eid' => $eid
    ));

  if ($rs_email) {
    print ("success");
  } else {
    print ("<h3 class=\"alert error\">Email updates failed.  Please try again.</h3>\n");
  }
}

if(isset($_GET['mode']) && $_GET['mode'] == 'delete_email_confirm') {
  $eid = $_GET['eid'];
?>
  <div id="delete_wrapper" style="text-align: center">
  <form action="inc/ajax/email_ajax.inc.php" method="get" id="delete_email_confirm">
    <h3>Are you sure you want to remove this email address?</h3>
    <p>You will no longer receive earthquake notifications with this email</p>
    <input type="hidden" id="ens_delete_token" name="ens_delete_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="thickbox" id="submit_delete_email" alt="inc/ajax/email_ajax.inc.php?mode=delete_email&eid=<?=$eid?>">Yes, delete it</button>
    <button onclick="tb_remove()">No, don't remove it</button>
  </form>
  </div>

  <script type="text/javascript">
    $("#delete_email_confirm").submit( function(e) { e.preventDefault(); });
    $("#submit_delete_email").click( function(e) {
        e.preventDefault();
      
        $.ajax({
            type: "GET",
            url: "inc/ajax/email_ajax.inc.php",
            data: {mode: "delete_email", eid: <?=$eid?>},
            headers: getCSRFHeader($("#ens_delete_token").val()),
            success: function(html) {
                $('#delete_wrapper').html(html);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown){
            ensAlert("Invalid Request", "Failed to remove email address. Please try again.");
        });
    });
  </script>

<?php
}


if(isset($_GET['mode']) && $_GET['mode'] == 'delete_email') {
    $isValidRequest = validateCSRF(null, true);
    $is_deleted = true;

    if (!$isValidRequest) {
        $is_deleted = false;
    } else {
        $eid = $_GET['eid'];
        $USER_INFO = $_SESSION['USER_INFO'];
        $USER_EMAILS = $_SESSION['USER_EMAILS'];
        $uid = $USER_INFO['id'];

        // First we need to disassociate the profiles it is with
        $query_bridge = $database->prepare("DELETE from email_param_bridge where emailid=:eid");

        if(!($rs_bridge = $query_bridge->execute(array(':eid' => $eid)))) {
            $is_deleted = true; // If we fail, just move on
        } // End of if query success

        // Now check to see if this left any orphaned profiles -sms
        //$query_profiles = $database->prepare("select distinct paramid from email_param_bridge where paramid IN (select pid from mailparams where userid=:userid)");
        $query_profiles = $database->prepare("SELECT pid from mailparams where userid=:userid and pid not in (select distinct paramid from email_param_bridge where paramid IN (select pid from mailparams where userid=:userid))");
        $profiles_rs = $query_profiles->execute(array(
          ':userid' => $uid
        ));

        while ( $row = $query_profiles->fetch(PDO::FETCH_ASSOC)) {
            $pid = $row['pid'];
            $query_delete = $database->prepare("DELETE from mailparams where pid=:pid");
            $query_delete->execute(array(':pid' => $pid));
        }

        // Now we delete the actual address
        #$delete_email = sprintf("DELETE FROM mailaddresses WHERE eid=%s AND uid=%s LIMIT 1", ($eid), ($uid)); // Do a limit just in case
    }

    if ($is_deleted) {
        $delete_email = $database->prepare("DELETE FROM mailaddresses WHERE eid=:eid AND uid=:uid LIMIT 1");
        $delete_email->execute(array(':eid'=>$eid, ':uid'=>$uid));
        print ("<h3 class=\"alert success\">Address Successfully deleted!</h3>\n");
        // Use javascript to remove the thickbox and remove the email
        ?>
          <script type="text/javascript">
          function remove_email( eid ) {
            var hidden_field = $("input[name='eid'][value='<?php print $eid; ?>']");
            var li = hidden_field.closest("li").hide();
            li.siblings('.ajax_result').hide();
            tb_remove();
          }
          setTimeout("remove_email(<?php print $eid; ?>)", 2500);
          </script>
        <?php

    } else {
        print ("<h3 class=\"alert error\">Email deletion process failed for some unknown reason.</h3>\n");
        print ("<p>Please notify the <a href=\"mailto:ensadmin@usgs.gov\">ENS Administrator</a> of the failure to have the problem solved.</p>\n");
    }

    // update the session info
    $_SESSION['USER_INFO'] = $USER_INFO;
    $_SESSION['USER_EMAILS'] = $USER_EMAILS;
}

?>
