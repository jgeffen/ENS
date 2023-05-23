<?php
include_once("../config.inc.php");
session_start();
/**
 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />
 * File:        form.php<br /><br />
 *
 * This is a very simple form sending a username and password.<br />
 * It demonstrates how you can integrate the image script into your code.<br />
 * By creating a new instance of the class and passing the user entered code as the only parameter, you can then immediately call $obj->checkCode() which will return true if the code is correct, or false otherwise.<br />
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or any later version.<br /><br />
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.<br /><br />
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA<br /><br />
 * 
 * Any modifications to the library should be indicated clearly in the source code 
 * to inform users that the changes are not a part of the original software.<br /><br />
 *
 * If you found this script useful, please take a quick moment to rate it.<br />
 * http://www.hotscripts.com/rate/49400.html  Thanks.
 *
 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA
 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version
 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation
 * @copyright 2007 Drew Phillips
 * @author drew010 <drew@drew-phillips.com>
 * @version 1.0.3.1 (March 23, 2008)
 * @package Securimage
 *
 */ ?>
<html>
<head>
  <title>Securimage Test Form</title>
  <script type="text/javascript">
	function switch_captcha( address ) {
		
	}
  </script>
</head>

<body>

<?php

  include("securimage.php");

if(isset($_POST['submit'])){
	$img = unserialize($_SESSION['img']);
	$valid = $img->check($_POST['code']);

	if($valid == true) {
	echo "<center>Thanks, you entered the correct code.</center>";
	} else {
		echo "<center>Sorry, the code you entered was invalid.  <a href=\"javascript:history.go(-1)\">Go b
ack</a> to try again.</center>";
	}

}
else { 

  $img = new Securimage();
$_SESSION['img'] = serialize($img);
?>
<form method="POST">
Username:<br />
<input type="text" name="username" /><br />
Password:<br />
<input type="text" name="password" /><br />

<!-- pass a session id to the query string of the script to prevent ie caching -->
<div id="captcha_div">
<img id="captcha" src="securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>"><br />
</div>
<a href="#" onclick="document.getElementById('captcha').src = 'securimage_show.php?' + Math.random(); return false">
		<img src="images/arrow_refresh.png" alt="Refresh CAPTCHA image" style="border: 0px none" />
		Reload Image
	</a>
<a onclick="switch_captcha('<?php echo md5(uniqid(time())); ?>')" href="securimage_play.php?sid=<?php echo md5(uniqid(time())); ?>">
		<img src="images/sound.png" alt="Play CAPTCHA audio" style="border: 0px none" />
		Audio
	</a><br />
<input type="text" name="code" /><br />

<input type="submit" name="submit" value="Submit Form" />
</form>
<embed src="securimage_play.php?sid=<?php echo md5(uniqid(time())); ?>" autostart="true" loop="false"  width="175" /> 
<embed type="application/x-shockwave-flash" src="http://www.google.com/reader/ui/3247397568-audio-player.swf?audioUrl=securimage_play.php?sid=<?php echo md5(uniqid(time())); ?>" width="400" height="27" allowscriptaccess="never" quality="best" bgcolor="#ffffff" wmode="window" flashvars="playerMode=embedded" />
<object type="audio/x-wav" data="data/test.wav" width="200" height="20">
  <param name="src" value="securimage_play.php">
  <param name="autoplay" value="false">
  <param name="autoStart" value="0">
  alt : <a href="securimage_play.php">test.wav</a>
</object>


<?php
}
?>

</body>
</html>
