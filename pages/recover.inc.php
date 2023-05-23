<?php
    include_once('inc/functions.inc.php');
    session_start();
    
    $TITLE = "Recover login information";
    if(isset($_POST['mode']) && $_POST['mode'] == "recover") {
        
        $csrfReqHeader = filter_input(INPUT_POST, 'ens_recover_token', FILTER_SANITIZE_STRING);
        $isValidRequest = validateCSRF($csrfReqHeader);

        // Some error messages
        $notFound = '<p class=alert error">%s was not found in the ENS database. Are you
                sure you have an ENS account?</p> <p>If you are already receiving
                messages from ENS, you can find your account as follows:</p>
                <ul><li>Long-form ENS messages will have the address they were
                sent to in a line at the bottom, like this: <br />
                "This email was sent to you@yourinternet.com"</li>
                <li><p>ENS messages contain a 5 character alpha-numeric identifier
                code at the end of the subject. For example, the ID number
                might look like "31b86" or "26be4".</p>
                <p>If you are currently receiving earthquake messages from
                the USGS, please forward one of these identifier codes, or
                a sample notification message to <a href="mailto:ensadmin@ens.usgs.gov">
                ensadmin@ens.usgs.gov</a> so we can see how it got to you.</p></li></ul>
                ';

        $notFoundPhone= '<p class=alert error">Try <a href="http://www.makeuseof.com/tag/email-to-sms/"
                target="_blank">looking up</a> your carrier\'s
                SMS gateway and enter the full address below.</p>';

        $multipleAccounts= '<p class=alert warning">%s is associated with multiple accounts. Your
                information must be recovered manually.</p> <p class="alert error">A request
                has been sent to the <a href="mailto:ensadmin@ens.usgs.gov">admin</a>.
                You will receive a reply as soon as possible.</p><p><strong>Please
                don\'t submit multiple requests; we will email your password as soon
                as we can.</strong></p>
                ';

        $sentInfo =	'<p class="alert uccess">We have reset the password on your ENS account
                and sent an email to %s with your username and new password. Please
                <a href="' . $CONFIG['MOUNT_PATH'] . '">log on</a> and change it.';

        $mailFailed=    '<p class=alert error">Your password could not be recovered. Please
                email the <a href="mailto:ensadmin@ens.usgs.gov">admin</a> about
                the problem.</p>';

        $phoneMsgText = 'ENS Username: %s Password: %s';

        $emailMsgText = 'This is an automatically generated email being sent because someone
                (possibly you) has requested Earthquake Notification Service account
                information.  We have reset your login information to the following:
                <br /><br />Username: %s <br />Password: %s <br /><br />Note:
                Passwords are case sensitive. <br /><br />You can log
                in to your account by going to https://earthquake.usgs.gov' .
        $CONFIG['MOUNT_PATH'] . '/' . ' and
                entering the above information. Once you log in, please change your
                password to something you can remember.';

        $recoverEmail = "The following user tried to recover their account information, but
                failed because they have multiple accounts associated with the same
                address. <br /><br /> Email: %s <br /><br /> Associated usernames: %s
                <br /><br /> You should reset their password and, if applicable,
                remove one of the accounts. Also, you might want to check for other
                password recovery requests by this user, so we don't reply to them
                twice.<br /> This request was generated at %s";

        if (!$isValidRequest) {
            print '<p class="alert error">Invalid Request. Please try again.</p>';
        } else {

            if( isset($_POST['email']) &&  $_POST['email'] != '') {

                $email = $_POST['email'];
                $isPhone = isPhone($email);

                if( $isPhone && strpos($email, '@') === false) {
                    $query = $dbreadonly->prepare("
                        SELECT
                            mu.id,
                            mu.username,
                            ma.email
                        FROM
                            mailusers AS mu,
                            mailaddresses AS ma
                        WHERE
                            ma.uid=mu.id AND
                            LEFT(ma.email,10) = :email");
                }
                else {
                    $query = $dbreadonly->prepare("
                        SELECT
                            mu.id,
                            mu.username,
                            ma.email
                        FROM
                            mailusers AS mu,
                            mailaddresses AS ma
                        WHERE
                            ma.uid=mu.id AND
                            ma.email= :email");
                }
                $result = $query->execute(array(':email' => $email));
                $rowCount = $query->rowCount();

                $emailAlertText = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                // If we can't find their email/phone, tell them
                if( !$result || $rowCount == 0 ) {
                    printf( $notFound, $emailAlertText);
                    if( $isPhone ) {
                        print $notFoundPhone;
                    }
                }
                // If they have multiple emails, send an email to ensadmin
                else if($rowCount > 1 ) {
                    $accounts = '';
                    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $accounts .= $row['username'] . ', ';
                        $email = $row['email'];
                    }
                    $accounts = substr($accounts, 0, -1); // Strip the trailing comma

                    $emailsubtext = 'ENS Login Reset - ' . $email;
                    $headers = "Mime-Version: 1.0\r\n";
                    // To send HTML mail, the Content-type header must be set
                    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $msgText = sprintf($recoverEmail, $email, $accounts, date('F j, Y, G:i a'));

                    mail('ensadmin@ens.usgs.gov', $emailsubtext, $msgText, $headers);

                    printf($multipleAccounts, $emailAlertText);
                }
                // We found their email/phone, so send them a new password
                else {
                    $row = $query->fetch(PDO::FETCH_ASSOC);

                    // The email we send is different for phones and emails
                    // The headers also change slightly.
                    if( $isPhone ) {
                        $msgText = $phoneMsgText;
                        $email = $row['email'];
                        $headers = "Mime-Version: 1.0\r\n";
                        $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
                    }
                    else {
                        $msgText = $emailMsgText;
                        // To send HTML mail, the Content-type header must be set
                        $headers = "Mime-Version: 1.0\r\n";
                        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    }

                    $newpass = generate_password(10);
                    $hashpass = md5($newpass);
                    $msgText = sprintf($msgText, $row['username'], $newpass);
                    $emailsubtext = 'ENS Account Login Information';
                    $emailAlertText = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

                    $queryUpdate = $database->prepare("
                        UPDATE
                            mailusers
                        SET
                            hashpasswd=:hashpasswd
                        WHERE
                            id=:id
                    ");

                    $result = $queryUpdate->execute(array(
                        ':id' => $row['id'],
                        ':hashpasswd' => $hashpass
                    ));

                    if($result) {
                        admin_email($email, $emailsubtext, $msgText, $headers, $row['id']);
                        printf( $sentInfo, $emailAlertText );
                    }
                    else {
                        print $mailFailed;
                    }
                }
            }
            // If they didn't type anything in the box
            else {
                print '<p class="alert error">No email or phone number provided.</p>';
            }
        }
    } // End: if($_POST['mode']
?>
    <h3>Password recovery</h3>
    <p>To recover your login information, enter the information related to your account.</p>
    <div class="ens_form" id="recover_form">
        <form method="post" action="" name="recover">
            <div style="display: block">
                <label for="email">Email Address or Ten Digit Phone Number</label><input type="text" name="email" id="email" />
            </div>
            <input type="hidden" name="mode" value="recover" />
            <input type="hidden" id="ens_recover_token" name="ens_recover_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" name="submit" class="ens_btn">Recover Account</button>
        </form>
	</div>
