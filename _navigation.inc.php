<?php

if ($IS_LOGGED_IN) {
  echo navGroup(navItem($CONFIG['MOUNT_PATH'] . '/index.php',
      'Earthquake Notification Service'),
    navItem($CONFIG['MOUNT_PATH'] . '/userhome_map', 'My Profiles') .
    navItem($CONFIG['MOUNT_PATH'] . '/userhome_map?recent=true',
        'Recent Events') .
    navItem($CONFIG['MOUNT_PATH'] . '/myemail', 'My Email Addresses') .
    navItem($CONFIG['MOUNT_PATH'] . '/accountprefs', 'Account Preferences') .
    navItem($CONFIG['MOUNT_PATH'] . '/help', 'Help' )
  );

  echo navItem('logout', 'Log out');
} else {
  echo navItem($CONFIG['MOUNT_PATH'] . '/login', 'Login');
}
