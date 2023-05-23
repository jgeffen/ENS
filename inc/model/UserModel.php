<?php

/**
 * Mailusers and login related database access methods
 */
class UserModel {
    
    private $database;
    
    const MAX_LOGIN_TIMEOUT_SECONDS = 60 * 15; // 15 minute timeout
    const MAX_LOGIN_RETRY = 20; // max retries
    const DEFAULT_FAIL_TIME = '1970-01-01 00:00:00'; 

    function __construct($database) {
        $this->database = $database;
    }
    
    // True if username exists in user_login_info table, false if not
    function isAccountLockExist($userId) {
        $user = $this->getAccountLockInfo($userId);
        if (count($user) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Account is locked if more than 20 attempts within last 15 minutes
     */
    function isAccountLocked($userId) {
      $accountLockInfo = $this->getAccountLockInfo($userId);      
      if (count($accountLockInfo) > 0) {
        if ($accountLockInfo['num_fail'] >= self::MAX_LOGIN_RETRY && $accountLockInfo['elapsed_fail_seconds'] < self::MAX_LOGIN_TIMEOUT_SECONDS) {
          return true;
        } else {
          return false;
        }        
      } else {
        // If login info does not exist, account is not locked
        return false;
      }
    }

    /*
     * Add account lock info if one does not exist
     */
    function setupAccountLock($userId) {
      if ($this->isAccountLockExist($userId) == false) {
        // no record for user login in user_login_info table. Create a new record. 
        $this->insertUserLoginInfo($userId);
      }
    }
    
    // Get user info for a username. Only the first row.
    function getUser($username) {
        $result = array();
        
        // ENS allows login using username, email address, and phone number. PHP recommends not using duplicate named properties
        $sql = "SELECT mu.id, mu.username
                FROM mailusers mu
                    LEFT JOIN mailaddresses ma on mu.id = ma.uid 
                WHERE username = :username1
                    OR (ma.email = :username2 OR left(ma.email,10) = :username3)"; 
        $query = $this->database->prepare($sql);
        $query->execute(array(':username1' => $username,
                                    ':username2' => $username,
                                    ':username3' => $username));
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Finds the first row. Multiple user accounts may exist, but those are intentional or needs to be fixed        
            $result = array(
                'uid' => $row['id'], 
                'username' => $row['username']);
        }
        return $result;
    }

    // Get user login info for a user. Only the first row.
    function getAccountLockInfo($userId) {
        $result = array();
        $sql = "SELECT uid, num_fail, last_fail_time, TIMESTAMPDIFF(SECOND, last_fail_time, CURRENT_TIMESTAMP) as elapsed_fail_seconds 
                FROM user_login_info 
                WHERE uid = :uid";
        $query = $this->database->prepare($sql);
        $query->execute(array(':uid' => $userId));
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $result = array(
                'uid' => $row['uid'], 
                'num_fail' => $row['num_fail'],
                'last_fail_time' => $row['last_fail_time'],
                'elapsed_fail_seconds' => $row['elapsed_fail_seconds']);
        }
        return $result;
    }
    
    // Create a new login info record
    function insertUserLoginInfo($userId) {
        $sql = "INSERT INTO user_login_info (uid, num_fail, last_fail_time) VALUES (:uid, 0, :last_fail_time)";
        $query = $this->database->prepare($sql);
        $recordset = $query->execute(array(':uid' => $userId, ':last_fail_time' => self::DEFAULT_FAIL_TIME));
        if ($recordset) {
          return true;
        } else {
          return false;
        }
    }

    /**
     * Increment failure count and update failed timestamp
     */
    function incrementFailure($userId) {
      $sql = "UPDATE user_login_info SET num_fail=num_fail+1 WHERE uid=:uid";
      $query = $this->database->prepare($sql);
      $recordset = $query->execute(array(':uid' => $userId));           
      if ($recordset) {
        return true;
      } else {
        return false;
      }
    }

    function setFailedTime($userId) {
      $sql = "UPDATE user_login_info SET last_fail_time=CURRENT_TIMESTAMP WHERE uid=:uid";
      $query = $this->database->prepare($sql);
      $recordset = $query->execute(array(':uid' => $userId));           
      if ($recordset) {
        return true;
      } else {
        return false;
      }
    }

    /*
     * Clear account lock by setting num_fail to 0 and time to default
     */
    function clearAccountLock($userId) {
      $sql = "UPDATE user_login_info SET num_fail=0, last_fail_time=:last_fail_time WHERE uid=:uid";
      $query = $this->database->prepare($sql);
      $recordset = $query->execute(array(':uid' => $userId, ':last_fail_time' => self::DEFAULT_FAIL_TIME));           
      if ($recordset) {
        return true;
      } else {
        return false;
      }      
    }
}

?>
