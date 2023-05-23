<?php

    $IS_LOGGED_IN = false;
    $IS_ACCOUNT_LOCKED = false;
    $LOGIN_ERROR = false;

    // This page checks the username and password and ensures they match what is available in the database;
    $username = null;
    $hashpass = null;

    // We use if/else instead of param() so that a user does not hack the url
    if (isset($_POST['username'])) {
      $username = $_POST['username'];
    } else if (isset($_SESSION['USER_INFO']['username'])) {
      $username = $_SESSION['USER_INFO']['username'];
    } else {
      $IS_LOGGED_IN = false;
    }

    if (isset($_POST['textpass'])) {
      $hashpass = md5($_POST['textpass']);
    } else if (isset($_SESSION['USER_INFO']['hashpasswd'])) {
      $hashpass = $_SESSION['USER_INFO']['hashpasswd'];
    } else {
      $IS_LOGGED_IN = false;
    }

    $csrfReqHeader = filter_input(INPUT_POST, 'ens_login_token', FILTER_SANITIZE_STRING);
    $reqUser = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $reqPassword = filter_input(INPUT_POST, 'textpass', FILTER_SANITIZE_STRING);
    $isValidRequest = true;
    
    // Check login from login page
    if (isset($reqUser) && isset($reqPassword)) {
        // log in by user from Login page.
        $isValidRequest = validateCSRF($csrfReqHeader);
    }

    if (!$isValidRequest) {
        $IS_LOGGED_IN = false;
        $LOGIN_ERROR = true;
    } else {  
        // validate existing, or logging in user
        if ($username !== null && $hashpass !== null) {
          $userModel = new UserModel($database);    
          // Check if account exists. 
          $userAccount = $userModel->getUser($username);
          if (count($userAccount)> 0) {
            if ($userModel->isAccountLocked($userAccount['uid']) == false) {
              $query_validate = $database->prepare('
                SELECT *
                FROM mailusers
                WHERE
                  hashpasswd = :hashpass
                  AND (
                  username = :username1
                  OR EXISTS (
                    SELECT * FROM mailaddresses
                    WHERE
                      mailaddresses.uid = mailusers.id
                      AND (
                      mailaddresses.email = :username2
                      OR LEFT(mailaddresses.email,10) = :username3
                      )
                    )
                  )
                ');

              // Check if the user is logged in
              $query_validate->execute(array(
                ':hashpass' => $hashpass,
                ':username1' => $username,
                ':username2' => $username,
                ':username3' => $username      
              ));

              $rs_validate = $query_validate->fetchAll(PDO::FETCH_ASSOC);
              $query_validate->closeCursor();
              $query_validate = null;

              if (count($rs_validate) == 1) {
                $IS_LOGGED_IN = true;
                $USER_INFO = $rs_validate[0];

                if (isset($reqUser) && isset($reqPassword)) {
                    // log in by user from Login page. Refresh CSRF token.
                    getCSRFToken(true);
                }

                //clear account lock
                $userModel->clearAccountLock($rs_validate[0]['id']);
              } else {
                // Failed login. check if the current lock is old
                $userModel->setupAccountLock($userAccount['uid']); // no user id yet with the password
                $accountLock = $userModel->getAccountLockInfo($userAccount['uid']);
                if ($accountLock['elapsed_fail_seconds'] > $userModel::MAX_LOGIN_TIMEOUT_SECONDS) {
                  $userModel->clearAccountLock($userAccount['uid']);            
                }

                // invalid credential
                $userModel->setFailedTime($userAccount['uid']);
                $userModel->incrementFailure($userAccount['uid']);
              }

              //if (!$IS_LOGGED_IN) {
              if (!$rs_validate) {
                $LOGIN_ERROR = true;
              }
            } else {
              // Account locked - login not allowed 
              $LOGIN_ERROR = true;      
              $IS_ACCOUNT_LOCKED = true;
            }
          } else {
            // account does not exist. 
            $LOGIN_ERROR = true;      
          }
        }
    }

  // load user information
  if ($IS_LOGGED_IN) {
    $USER_EMAILS = array();

    $query_emails = '
      SELECT
        *
      FROM
        mailaddresses
      WHERE uid = :id
    ';

    $statement_emails = $database->prepare($query_emails);
    $statement_emails->bindValue(':id', $USER_INFO['id'], PDO::PARAM_INT);

    if ($statement_emails->execute()) {
      $result_emails = $statement_emails->fetchAll(PDO::FETCH_ASSOC);
      $statement_emails->closeCursor();
      $statement_emails = null;

      foreach ($result_emails as $email) {
        $USER_EMAILS[$email['eid']] = $email;
      }

      $email_count = count($USER_EMAILS);
    }

    $TEXT_DEFS = sprintf('textdefs%s.inc.php', $USER_INFO['language']);
    require_once dirname(__FILE__) . '/' . $TEXT_DEFS;

    // see if this is the first time they have logged in
    if ($USER_INFO['added'] == $USER_INFO['lastlogin']) {
      $firsttime = 1;
    }

    // Update the LastLogin field if they are logging in with this request
    if (isset($_POST['textpass'])) {
      $nowstr = date('Y-m-d H:i:s');
      $query_login = '
        UPDATE
          mailusers
        SET
          lastlogin = :last_login
        WHERE
          id = :id
        LIMIT 1
      ';

      $statement_login = $database->prepare($query_login);
      $statement_login->bindValue(':last_login', $nowstr, PDO::PARAM_STR);
      $statement_login->bindValue(':id', $USER_INFO['id'], PDO::PARAM_INT);

      // Not a huge deal if this doesn't update I suppose... (?)
      $statement_login->execute();

      $statement_login->closeCursor();
      $statement_login = null;
    }

    // set up the session variables so they can stay logged in
    $_SESSION['USER_INFO'] = $USER_INFO;
    $_SESSION['USER_EMAILS'] = $USER_EMAILS;
  } else if ($LOGIN_ERROR === true) {
    // Record failed attempts
    if ($username !== null) {
      error_log("ENS LOG - login failure, username: $username");        
    }
  }
?>
