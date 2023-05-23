<?php
    include_once('inc/functions.inc.php');
    session_start();

    $TITLE = "Unsubscribe from ENS";

    print '<h2>Unsubscribe from ENS</h2>';

    if( isset($_POST['username']) && isset($_POST['hashpass']) && $_POST['username'] != '' && $_POST['hashpass'] != '') {
        $csrfReqHeader = filter_input(INPUT_POST, 'ens_unsub_token', FILTER_SANITIZE_STRING);
        $isValidRequest = validateCSRF($csrfReqHeader);
        
        if ($isValidRequest) {
            login_and_unsubscribe( $_POST['username'], md5($_POST['hashpass'])) ;
        } else {
            print '<h3 class="alert error">Invalid Request. Please try again.</h3>';
        }
    } else {
        if( isset($_POST['username']) && isset($_POST['hashpass']) && ($_POST['username'] == '' || $_POST['hashpass'] == '')) {
            print '<h3 class="alert error">You must enter your username and password.</h3>';
        }
?>

<div class="ens_form" id="unsubscribe_form">
<form method="post" action="" name="unsubscribe">
    <p>To unsubscribe from notifications, please provide your username and password. 
    If forgot your login information, you can <a href="recover">recover it</a>.</p> 

    <fieldset>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" />
    </div>

    <div>
        <label for="hashpass">Password</label>
        <input type="password" name="hashpass" id="hashpass" />
    </div>
    </fieldset>
    <hr />
    <input type="hidden" id="ens_unsub_token" name="ens_unsub_token" value="<?php echo htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">

    <button type="submit" name="submit" class="ens_btn" id="unsubscribe_btn" onclick="return confirm('!!Warning!! This action cannot be undone. Are you sure you want to proceed?');" >Unsubscribe</button>
    <p class="note">
    Note: Please allow up to 3 days to unsubscribe during a significant earthquake sequence. You will continue to receive notifications during that time. 
    </p>
</form>
</div>

<?php 
    } // END:: if(isset($_POST['username']))
?>
