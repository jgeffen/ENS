<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

#$FILE_PATH = $CONFIG['APP_WEB_DIR'];
$FILE_PATH = '.';


// include guard so configuration and include only happen once
if(!isset($TEMPLATE)) {
  // configure the template
  $TITLE = 'Earthquake Notification Service';
  $NAVIGATION = true;
  $HEAD = '
    <link rel="stylesheet" href="/lib/leaflet-0.7.7/leaflet.css"/>
    <link rel="stylesheet" href="/lib/leaflet-0.7.7/leaflet-draw/leaflet.draw.css"/>
    <link rel="stylesheet" href="css/index.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <link rel="stylesheet" href="css/userhome_map.css"/>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="/lib/jquery-1.11.0/jquery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="js/lib/jquery.plugins.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-1.4.1.js"></script>
    <script src="/lib/leaflet-0.7.7/leaflet.js"></script>
    <script src="/lib/leaflet-0.7.7/leaflet-draw/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="js/lib/ens.js?v=' . filemtime('js/lib/ens.js') . '"></script>
    <script src="inc/request.js"></script>
  ';

  $FOOT = '
    <script src="inc/js/maps_functions.js?v=' . filemtime('inc/js/maps_functions.js') . '"></script>
  	<script src="inc/js/Map_Controls.js?v=' . filemtime('inc/js/Map_Controls.js') . '"></script>
  	<script src="inc/js/Profile.js?v=' . filemtime('inc/js/Profile.js') . '"></script>
  	<script src="inc/js/Shape.js"></script>
  	<script src="inc/js/Rectangle.js"></script>
  	<script src="inc/js/Circle.js"></script>
  	<script src="inc/js/Polygon.js"></script>
  ';

  // template functions
  include_once 'functions.inc.php';

  // ens functions
  include_once 'inc/config.inc.php';
  include_once 'inc/functions.inc.php';
  include_once 'inc/common-functions.inc.php';

  // start session
  session_start();

  // clear session if logging out
  if (param('page') === 'logout') {
    $_SESSION = array();
    header('Location: login');
    exit();
  }

  include_once ("inc/model/UserModel.php");
  include ("inc/validate.inc.php");

  include 'template.inc.php';
}


// check whether in maintenance mode
if (file_exists('../conf/maintenance')) {
  echo 'ENS user pages are currently down for maintenance, please try again soon';
  return;
}

if (!$IS_LOGGED_IN) {
  if ($LOGIN_ERROR) {
    $page = param('page', 'login');
    if(!in_array($page, $nologinok)) {
      $page = 'login';
    }
  } else {
    $page = param('page', 'login');
    if(!in_array($page, $nologinok)) {
      $page = 'login';
    }
  }

} else {
  $page = param('page','userhome_map');
  // if user visits "/ens/" redirect to userhome_map
  if ($page === '') {
    header('Location: userhome_map');
    exit();
  }
}


// We need to store the confirmation info in the $_SESSION array,
// so the user can click the confirm link, log in, and not have to retype the code
$confnum = param('confnum', '');
$address = param('address', '');
$newaccount = param('newaccount', '');
if ($confnum != '') {
  $_SESSION['confnum'] = $confnum;
  $_SESSION['address'] = $address;
  $_SESSION['newaccount'] = $newaccount;
  if ($page == "login") {
    print '<p class="alert warning">You must log in to confirm your email address.</p>';
  }
}

$FILE_TO_INCLUDE = sprintf("%s/pages/%s.inc.php", $FILE_PATH, $page);
if(file_exists($FILE_TO_INCLUDE)){
  include $FILE_TO_INCLUDE;
} else {
  $_POST['errorid'] = "404";
  include $FILE_PATH . "/pages/error.inc.php";
}

// Let's check to see if an error occured while processing the include file
if(strstr($ERROR_MESSAGE, "<li>")) {
  $_POST['errorid'] = "500"; // Set to a server error
  include $FILE_PATH . "/pages/error.inc.php";
}

?>
