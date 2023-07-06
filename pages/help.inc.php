<?php

  $TITLE = "ENS Documentation";
  $HEAD = "<style type=\"text/css\"><!--\n" .
    ".keywords {\n" .
      "font-family: Arial, Helvetica, sans-serif;\n" .
      "font-weight: bold;\n" .
      "clear: both;\n" .
      "display: block;\n" .
    "}\n" .
    ".imgborders {\n" .
      "border: 1px solid #336699;\n" .
      "margin-right: 5px;\n" .
      "padding-bottom: 5px;\n" .
      "padding-right: 5px;\n" .
      "padding-top: 5px;\n" .
    "}\n" .
    "-->\n" .
    "</style>\n";

  if(!isset($_GET['ispopup'])) {
    // Add style to hide "Close Window" links (which won't work anyway if the
    // window is not a pop-up.
    /*$HEAD .= "<style type=\"text/css\"><!--\n" .
      ".popup_control { display: none; }\n" .
      "--></style>";
*/


    $HEAD .= '<script type="text/javascript">
      $(document).ready(function() {
        $(".popup_control").click(function(e) {
          window.close();
        });
      });
      </script>';
  }

if (!isset($TEMPLATE)) {
  // template functions
  include_once 'functions.inc.php';

  // defines the $CONFIG hash of configuration variables
  include_once '../conf/config.inc.php';

  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/index.css"/>';
  $FOOT = '<script src="js/index.js"></script>';

  ###session_start();

  include ("inc/validate.inc.php");
  include 'template.inc.php';
}

?>

<h2>Table of Contents</h2>
<ul>
  <li><a href="#intro">Introduction</a></li>
  <li><a href="#registration">Subscribe</a></li>
  <li><a href="#navigate">Navigating the ENS Webpages </a></li>
  <li><a href="#profiles">Managing Your ENS Profiles</a></li>
  <li><a href="#emails">Managing Your Email Addresses</a></li>
 <li><a href="#phone">Receiving ENS on Your Phone</a></li>
  <li><a href="#format">Reading Your Notifications</a></li>
  <li><a href="#glossary">ENS Glossary of Terms</a></li>
  <li><a href="#disclaimer">Disclaimer</a></li>
</ul>

<h2><a name="intro"></a>Introduction</h2>
<p>
  The U.S. Geological Survey Earthquake Hazards Program Earthquake Notification
  Service (ENS) is a customizable system provided free to everyone. New
  accounts receive, by default, all earthquakes with magnitude 6.0 or greater;
  you can subsequently customize these settings to better fit your needs.
  You can receive earthquake notifications for any earthquakes located by the
  ANSS/NEIC (Advanced National Seismic System/National Earthquake Information
  Center) in the U.S. and around the World. Information for earthquakes in the
  U.S. is generally available within 5 minutes; information for earthquakes
  elsewhere in the World is generally available within 30 minutes. Within the
  U.S. we locate earthquakes down to about M2.0, and about M4.0 for the rest
  of the world. Please read
  the <a href="#disclaimer">DISCLAIMER</a> at the bottom of this document.
</p>

<h3>Customizable Options:</h3>
<ul>
  <li>Functional in English and Spanish</li>
  <li>Specify your local time zone</li>
  <li>Specify affiliation</li>
  <li>Aftershock exclusion option</li>
  <li>Update Notifications option</li>
  <li>Defer Notifications option</li>
  <li>Add, remove, or change multiple email addresses (up to 15) </li>
  <li>Define multiple notification profiles</li>
  <li>Activate/Deactivate each profile individually</li>
  <li>Set notification magnitude thresholds for night and day hours</li>
  <li>
    Receive emails in 4 formats: HTML, long, short (for pagers and cell phones),
    and raw CUBE format messages. </li>
  <li>
    Create a notification profile region from a list of predefined regions  </li>
  <li>
    Create a custom profile region by drawing it on a map</li>
  <li>
    Custom profile regions can be rectangles, circles, or polygons</li>
  <li>
  <li>View the most recent events sent to your account</li>
  <li>
    Select seismic networks to receive events from (scientists only; default
    is to receive from all networks)  </li>
  <li>Remove profiles</li>
  <li>Manage/Delete account</li>
</ul>



<h2><a name="registration"></a>Subscribe</h2>
<h3>Subscribe Step 1 - Set up Account</h3>
<p><img src="images/helpdoc/subscribe2.jpg"
  alt="Register page" width="568" height="370" border="1" class="imgborders" /></p>
<p>A Spanish version of the form is available via
  a link at the top of the page.</p>
<p><strong>Email </strong>This email address will be the primary address associated with your account.  If you clicked directly on the Sign Up for ENS link, you must enter an email. After clicking Subscribe, you will need to check your email to receive your confirmation code. You will have an opportunity to enter
  additional email addresses using the My Email Addresses tab once you register. </p>
<p>
  <span class="keywords"><strong>Username</strong></span>
  This can be any username you want to use. If it is already taken, a red X will appear next to the field, along with a message notifying you of the error. If you ignore the error, you will
  see a message when you submit the form, and will be given the opportunity to
  choose another username. </p>
<p>
<span class="keywords"><strong>Password/Confirm</strong></span>
  Enter a password twice, once in each box. This will allow you to login and
  manage your account.</p>
<p><span class="keywords"><strong>Subscribe</strong></span> Please read the Disclaimer in the box below the Subscribe button. Clicking Subscribe will create your account then either log you in to ENS, or display a message instructing you to check your email for a confirmation code. </p>
<p>In order to send event notifications at the proper hours,  the system will attempt to guess your time zone. After registering, you can check if it is correct by going to the Account Preferences tab. <a href="http://aa.usno.navy.mil/faq/docs/world_tzones.html"
      target="_blank">What's my time zone?</a></p>
<p>ENS will remember the language (English or Spanish) you used when filling out the form, and present content in this language whenever possible. Your language preference, along with many other account settings, can be changed in the Account Preferences tab.</p>
<p>&nbsp;</p>
<h3>Subscribe Step 2 - Confirm Email</h3>
<img src="images/helpdoc/subscribe3.jpg"
  alt="Confirmation email message" width="507" height="89" border="1" class="imgborders" />
<p>After subscribing, you need to confirm that you own the email address you used to sign up. ENS will send you an email with a 3 digit confirmation code, which you have to enter after logging in.</p>
<p>Confirming your email:</p>
<ul>
  <li>Ensures that you did not mistype your address</li>
  <li>Prevents other people/spam bots from signing up with your address</li>
</ul>
<p>&nbsp;</p>
<p><strong>
  At this point your ENS account is set up and ready to receive event notifications. </strong></p>


<h2><a name="navigate"></a>Navigating the ENS Webpages</h2>
<img src="images/helpdoc/navigating1.jpg"
  alt="ENS tabbed navigation" width="725" height="39" border="1" class="imgborders" />
<p>
  The yellow tabs at the top of the ENS user page allow you to manage your profiles, see recent events sent to you, manage your email addresses, edit your
   account information, and obtain ENS
  documentation. The Logout link can be found in the
  upper-right corner of every page.</p>
<p>Note that Javascript is required to use the map functionality of ENS. If you don't know what this means, you can most likely ignore this fact because over 95% of browsers already have Javascript enabled. </p>
<p>It is highly recommended that you use a modern browser such as Internet Explorer 7+, Firefox 2+, or Safari 3.1+. </p>
<hr style="clear:both" />
<h2 style="clear: left;"><a name="profiles"></a>Managing Your ENS Profiles</h2>
<p style="clear: left;">The ENS homepage uses a <a href="http://leafletjs.com/">Leaflet Maps</a> based interface to display all your profiles in one convenient place. The map allows you to manipulate existing profiles, add new profiles, change profile settings, and delete profiles. </p>
<p style="clear: left;"><img src="images/helpdoc/profiles_map.jpg"
  alt="Default map" width="800" height="298" border="1" class="imgborders" /></p>
<p style="clear: left;">The first time you log in, you will see a map with two default Predefined profiles for the World and the United States. The United States profile appears as a group of 3 blue polygons, while the default World appears as the green shaded rectangle. You may edit or delete one
or both of these. </p>
<p style="clear: left;">In Leaflet maps, you can change the view by clicking and dragging to pan, and by using the control on the left. The map automatically wraps horizontally, so, depending on your zoom level, you may see more than the entire world.</p>
<p style="clear: left;"><img src="images/helpdoc/profiles_list.jpg"
  alt="Profile list" width="157" height="153" border="1" class="imgborders" /></p>
<p style="clear: left;">The My ENS Profiles box on the right side of the map shows a list of all of your profiles. The colors on the list correspond to the colors of the profile on the map. Click the name of a profile to edit that profile. <a href="#editing_profiles">Read more about editing profiles.</a></p>
<p style="clear: left;">&nbsp;</p>
<h3 style="clear: left;"><a name="profile_info_panel" id="profile_info_panel"></a>Profile Information Panel</h3>
<p style="clear: left;">Below the map is a blue bar, which expands when a profile is clicked or added. This is the Profile Information Panel. </p>
<p style="clear: left;"><img src="images/helpdoc/profiles_add_pre2.jpg"
  alt="Profile information panel" width="800" height="181" border="1" class="imgborders" /></p>
<p style="clear: left;">The Profile Information Panel allows you to set the following options:</p>
<p style="clear: left;"><strong>Profile Name </strong>You may give this profile a name. This is to help you keep track of your profiles.</p>
<p style="clear: left;"><strong>Send emails to: </strong>All email addresses associated with your account will be listed here. Place a check mark next to all addresses you would like to use to receive event notifications for this profile. </p>
<p style="clear: left;"><strong>Magnitude </strong>These values are the minimum magnitudes for which you want to receive notifications. Read about <a href="#mag">Daytime Magnitude</a> and <a href="#mag_night">Night Magnitude</a>.</p>
<p style="clear: left;"><strong>Active </strong>Should this profile send event notifications? Check 'Yes' to allow notifications. Check 'No' to suspend/disable notifications. </p>
<p style="clear: left;">&nbsp;</p>
<h3 style="clear: left;"><strong>Add New Profile</strong></h3>
<p style="clear: left;"><img src="images/helpdoc/profiles_add.jpg"
  alt="Add New Region control" width="155" height="56" border="1" class="imgborders" /></p>
<p style="clear: left;">To add a new ENS profile, you must use the control in the top right corner of the screen. The two types of profiles are (from left to right) Predefined, and Custom. </p>
<p style="clear: left;"><strong>Adding a Predefined profile</strong></p>
<p style="clear: left;">Clicking on the predefined profile button <img src="images/helpdoc/profiles_add_button_pre.jpg"
  alt="Predefined region button" width="35" height="28" border="1" align="middle" class="imgborders" /> will hide all your existing profiles and show a blank map of the world. The Add New Region control will expand and display a list of the available predefined regions. </p>
<!--
<p style="clear: left;"><img src="images/helpdoc/profiles_add_pre.jpg"
  alt="Add new region control expanded" width="350" height="56" border="1" class="imgborders" /></p>
-->
<p style="clear: left;"><img src="images/helpdoc/ens-choosing-a-region.jpg"
        alt="Add new region control expanded" width="780" height="376" border="1" class="imgborders" /></p>
<p style="clear: left;">Picking a region from the drop down menu will add the region to the map.</p>
<p style="clear: left;">When the predefined region is added to the map, the <a href="#profile_info_panel">Profile Information Panel</a> below the map was expanded.</p>
<p style="clear: left;"><img src="images/helpdoc/ens-after-choosing-a-region.jpg"
        alt="Add new region control expanded" width="780" height="354" border="1" class="imgborders" /></p>
<p style="clear: left;">Note that the default magnitude threshold for new profiles is M6.0. Earthquakes this large happen somewhere in the world about once every three days on
average, although it's not uncommon to go for a week or more without
having one. If you are setting up a notification profile for any place other than the most active parts of the world, you will probably want to set the threshold lower than M6.0.  Appropriate values are generally something like
M4.0-M4.5, or M1.5-M2.0 for less-active areas like Europe or the eastern U.S. <br><br>The new profile will not be saved until you click Save in the Profile Information Panel.</p>
<p style="clear: left;"><img src="images/helpdoc/ens-setting-the-magnitude.jpg"
        alt="Add new region control expanded" width="780" height="507" border="1" class="imgborders" /></p>

<p style="clear: left;"><strong>Adding a Custom profile</strong></p>
<p style="clear: left;">  Click the Custom profile button <img src="images/helpdoc/profiles_add_button_poly.jpg"
  alt="Polygon region button" width="35" height="28" border="1" align="middle" class="imgborders" /> to start drawing a custom region. The toolbar on the left below the zoom buttons controls drawing. The three buttons are for drawing polygons, rectangles, and circles. To begin drawing, click on the one of the three draw buttons. Note that all other profiles are hidden until you finish drawing.</p>
<p style="clear: left;"><img src="images/helpdoc/profiles_draw_toolbar.jpg"
  alt="Drawing a custom region" width="352" height="265" border="1" class="imgborders" /></p>
<p style="clear: left;"><strong>Drawing a polygon</strong></p>
<p style="clear: left;">Click the 'draw a polygon' button on the toolbar:</p>
<p style="clear: left;"><img src="images/helpdoc/profiles_draw_polygon.jpg"
        alt="Drawing a polygon" width="121" height="95" border="1" class="imgborders" /></p>
<p style="clear: left;">Click the map to draw the polygon. Click the last point to close the polygon.</p>
<p style="clear: left;"><img src="images/helpdoc/profiles_add_poly.jpg"
  alt="Drawing a polygon" width="786" height="350" border="1" class="imgborders" /></p>
<p style="clear: left;">The new profile will not be saved until you click Save in the Profile Information Panel.</p>
<p style="clear: left;"><strong>Adding a Rectangular profile</strong></p>
<p style="clear: left;">Click the 'draw a rectangle' button:</p>
<p style="clear: left;"><img src="images/helpdoc/profiles_draw_rectangle.jpg"
        alt="Drawing a polygon" width="121" height="95" border="1" class="imgborders" /></p>
<p style="clear: left;">Click the map to start drawing a rectangle. Note that all other profiles are hidden until you finish drawing. A rectangular profile is defined by clicking one corner and then dragging the pointer to the opposite corner. </p>
<p style="clear: left;"><img src="images/helpdoc/profiles_add_rect.jpg"
  alt="Drawing a rectangular region" width="786" height="352" border="1" class="imgborders" /></p>
<p style="clear: left;">The new profile will not be saved until you click Save in the Profile Information Panel.</p>
<p style="clear: left;"><strong>Adding a Circular profile</strong></p>
<p style="clear: left;">Click the circular profile button:</p>
<p style="clear: left;"><img src="images/helpdoc/profiles_draw_circle.jpg"
  alt="Circular region button" width="121" height="95" border="1" align="middle" class="imgborders" /></p>
<p style="clear: left;">Click the map to start drawing a circle. Note that all other profiles are hidden until you finish drawing. First, click the point you want to be the center of the circle. Then drag the pointer to define the radius. </p>
<p style="clear: left;">Circles will become distorted near the poles. This is because Leaflet maps uses a <a href="http://en.wikipedia.org/wiki/Mercator_projection">mercator projection.</a> In general, circles work best for defining relatively small areas. A circle with a radius over a 250 miles or 400km will be distorted by the curvature of the Earth. </p>
<p style="clear: left;"><img src="images/helpdoc/profiles_add_circle.jpg"
  alt="Drawing a circular region" width="785" height="351" border="1" class="imgborders" /></p>
<p style="clear: left;">The new profile will not be saved until you click Save in the Profile Information Panel.</p>
<h3 style="clear: left;"><a name="editing_profiles" id="editing_profiles2"></a>Editing A Profile</h3>
<p>Parameters for any ENS profile, including the two default profiles, can be modified.
On custom profiles that you created, you can also change the profile's coverage area. You cannot change the coverage area of pre-defined profiles.</p>
<p>Click the profile on the map or the profile name in the My ENS Profiles list to edit the profile. </p>
<p>You will notice that the <a href="#profile_info_panel">Profile Information Panel</a> expands below the map. Here you can set any of the parameters discussed above. </p>
<p>All other profiles will be hidden when editing a profile.</p>
<p><strong>Custom profiles</strong> have a control button on the map, indicating that the custom region is editable.</p>
<p><span style="clear: left;"><img src="images/helpdoc/profiles_edit_poly.jpg"
  alt="Editing a polygon region" width="786" height="384" border="1" class="imgborders" /></span></p>
<p style="clear: left;">To edit a custom polygon, click on the edit control button. Then, each corner of the polygon will have a small white box, and each line segment of the polygon also has a transparent white box at its middle. Dragging these transparent boxes allows you to add new points to the polygon. To remove a point, double-click on the box at the vertex you want to remove.</p>
<p><span style="clear: left;"><img src="images/helpdoc/profiles_edit_polygon_points.jpg"
        alt="Editing a polygon region" width="482" height="349" border="1" class="imgborders" /></span></p>
<p style="clear: left;">You can click the Cancel button before saving to undo all changes to the current profile. When you are done editing your polygon, click the OK button to finish editing. Changes will be saved when you click the Save Changes button at the bottom of the profile form.</p>
<p>Click Save Changes when you are finished editing.</p>
<p><strong>Rectangular profiles </strong>show four boxes at the corners, and one at the center when the rectangle is editable.</p>
<p><span style="clear: left;"><img src="images/helpdoc/profiles_edit_rect.jpg"
  alt="Editing a rectangular region" width="524" height="349" border="1" class="imgborders" /></span></p>
<p>Drag the corner icons to reshape the rectangle. Click and drag the center icon to move the rectangle without changing its shape. Clicking Cancel or Discard Changes will undo all modifications to the current profile.</p>
<p>Click Save Changes when you are finished editing.</p>
<p><strong>Circular profiles</strong> show an icon at the center and an icon on the radius when the circle is editable.</p>
<p><span style="clear: left;"><img src="images/helpdoc/profiles_edit_circle.jpg"
  alt="Editing a circular region" width="562" height="352" border="1" class="imgborders" /></span></p>
<p>Drag the center icon to relocate the circle without changing its size.</p>
<p>Drag the icon on the circle's radius to resize the circle. </p>
<p>Click Save Changes when you are finished editing.</p>
<p>&nbsp;</p>
<h3>Notes:</h3>
<ul>
  <li>All underlined field tags are links to Help for that tag.</li>
  <li>
    You may turn any individual profile off and on without deleting it by
    clicking on the &ldquo;Edit Profile&rdquo; button on the My ENS Profiles
    page, and then checking &ldquo;Yes&rdquo; or &ldquo;No&rdquo; for the
    &ldquo;Active&rdquo; option.
  </li>
  <li>
    If your account affiliation is classified as Scientist/Network Operator,
    you can also specify the network(s) to receive events from, and the depth
    range of events as well.
  </li>
</ul>


<h3 style="clear: left;">
    <a id="xmlsyntax"></a>
    Using XML to Define a Region
</h3>
<p>
    ENS accepts raw XML to define a region instead of selecting an area on the map. To use the raw XML, the XML must be in the following format:

    <textarea readonly rows="7" style="height: auto;">
        <region>
            <point><lat>latitude1</lat><lon>longitude1</lon></point>
            <point><lat>latitude2</lat><lon>longitude2</lon></point>
            <point><lat>latitude3</lat><lon>longitude3</lon></point>
            ... add more coordinates as needed
        </region>
    </textarea>

    <br>

    For example, the following is a valid XML data:

    <textarea readonly rows="7" style="height: auto;">
        <region>
        <point><lat>35.101</lat><lon>-480.709</lon></point>
        <point><lat>35.011</lat><lon>-474.601</lon></point>
        <point><lat>32.49</lat><lon>-474.645</lon></point>
        <point><lat>32.767</lat><lon>-480.621</lon></point>
        </region>
    </textarea>

    <br>
    Please check that your XML meets the following requirements:

    <ul>
        <li>There must be at least 3 coordinates</li>
        <li>Each "&ltpoint&gt" element must be on the same line with latitude and longitude</li>
        <li>Only one "&ltregion&gt" element</li>
        <li>Latitude and longitude may use up to 3 decimal places</li>
        <li>XML header should not be used</li>
        <li>Maximum of 20 latitude/longitude points</li>
        <li>UTF-8 encoded</li>
    </ul>
</p>


<h2><a name="emails"></a>Managing Your Email Addresses</h2>
<p>ENS allows you to associate multiple email addresses with your account. Click on the My Email Addresses tab to show a list of the addresses you currently have registered with ENS.</p>
<p> <img src="images/helpdoc/email_list.jpg"
  alt="Email address list" width="617" height="144" border="1" class="imgborders" /></p>
<p>Each email address has several options you can customize:</p>
<p><strong>Message Format </strong>ENS messages are available in four different formats. The standard HTML format displays the event message in a nice webpage-like format, with a link to the main earthquake web site page for that earthquake. Most users will want to choose HTML format. For information on the other formats, see <a href="#format">Message Format details </a>below.</p>
<p><strong>Day Begins </strong>and <strong>Day Ends </strong> Day and night time windows are associated with each individual email address. Day and night hours are only really relevant for pagers or cellphones, where you might want to designate a night-time where you would receive fewer notifications. For an email account, set the start and end times
both to 00:00 in order to have your daytime magnitude limit in place 24 hours.</p>
<p>You need to specify hours to be considered 'daytime'. Times are based on a <a href="http://en.wikipedia.org/wiki/24-hour_clock">24-hour clock</a>. The 'end' value must be equal to or greater than the 'begin' value, and both must be in the range of 0-23 hours. If the day and night hours are both set to 00, then all hours will be considered 'daytime' and you will get all notifications for events greater than your specified daytime magnitude threshold. Note that the hours are specified in local <a href="http://en.wikipedia.org/wiki/Standard_time">standard time in your time zone</a> that you specified when you opened your ENS account. If you need to change your time zone, use the Account Preferences tab on the main page. </p>
<p>The purpose of having your time zone in the system is so that messages sent to you can include the time the earthquake occurred in your time zone. If the time
is off by one hour, it is probably because your time zone
observes Daylight Saving or Summer Time. The time zone setting in your
account is based on how far east or west you are from the Prime Meridian
in Greenwich, England. The adoption of Summer Time is a politically
determined thing. Since our system does not know your address or even
what country you are in, it has no way of knowing whether or not your
area observes Summer Time. If your area observes Summer Time, you can compensate
for this by changing your time zone one hour to the east during Summer Time.</p>
<p>Using this list, you can change the options for the address. You <strong>must</strong> click Save after making changes.</p>
<p>To remove an address from your account, simply click the Delete button. You will be presented with a dialog box asking you if you really want to delete the address.</p>
<h3>Register a new address</h3>
<p>To associate another email address with your ENS account, click the Add Another Email Address button located below the list of addresses. You will be presented with a popup message like the following:</p>
<p><img src="images/helpdoc/email_add_new.jpg"
  alt="Register new address dialog box" width="538" height="458" border="1" class="imgborders" /></p>
<p><strong>Replaces </strong>This option is only necessary if you want to replace an existing address.</p>
<p>The options in this dialog box are explained above under the <a href="#emails">Managing Your Email Addresses</a> header.</p>
<p>Click Register Address to submit the form. You will need to check your email to receive your confirmation number. </p>
<p><img src="images/helpdoc/email_confirm.jpg"
  alt="Confirm new address box" width="652" height="145" border="1" class="imgborders" /></p>
<p>This number needs to be entered in the box on the main page. <strong>You may need to refresh the page to see the confirmation box.</strong> If you cannot get this box to appear, you can confirm the email with <a href="regaddr">this confirmation page.</a></p>
<p>&nbsp;</p>

<h2><a name="phone"></a>Receiving ENS on Your Phone</h2>
<p>ENS can send to mobile phones as long as they are capable of receiving SMS (short message service) messages
via email. Most phone providers have an email-to-SMS gateway, and the address
for your phone will be something like:</p>

<pre>
1234567890@yourphonecompany.net
</pre>

<p>There is a pretty comprehensive list of these gateways at:</p>

<ul>
<li><a href="https://www.lifewire.com/sms-gateway-from-email-to-sms-text-message-2495456">https://www.lifewire.com/sms-gateway-from-email-to-sms-text-message-2495456</a></li>
<li><a href="https://freesmsgateway.info">https://freesmsgateway.info</a></li>
<li><a href="https://smsemailgateway.com">https://smsemailgateway.com</a></li>
</ul>

<p>When you find the address to send SMS to your phone, you can register
it in ENS as an additional email address on your account. Be sure to select
the 'short' format for the messages to a phone.</p>

<p>One other thing to consider for registering a phone is to set your day
and night hours. That way, you can have a lower magnitude threshold
for the day, and have it only wake you up at night for larger quakes.
If you want to know about every quake, but don't want to be disturbed
at night, set the 'Defer' option under the 'Account Preferences' tab. This
will set the system so that it will still wake you up in the night for
the big earthquakes, but it will hold nighttime messages about small
ones until morning for earthquakes that are above your day threshold but
below your nighttime notification threshold.</p>

<p>Also, due to the way our system works, the addresses to send any
particular earthquake to come out of the database in alphabetical order.
Because of this, messages to phones come out first, since numbers sort
ahead of letters. So phone messages will typically come faster than email.</p>

<h2><a name="account" id="account"></a>Managing Your Account</h2>
<p><img src="images/helpdoc/accounts.jpg"
  alt="Account preferences panel" width="394" height="400" border="1" class="imgborders" /></p>
<p>ENS allows you to customize several settings related to your account. Click on the Account Preferences tab to see the list of settings. They are described below: </p>
<p><span class="keywords"><strong>Your Name</strong></span> First and last name. </p>
<p> <span class="keywords"><strong>Time Zone</strong></span> Select your time zone, based on your offset from GMT. For areas where
  daylight saving time is implemented, select your time zone based on the
  offset for Standard Time. <a href="http://wwp.greenwichmeantime.com/info/timezone.htm"
      target="_blank">What's my time zone?</a> </p>
<p> <span class="keywords"><strong>Preferred Language</strong></span> English or Spanish. </p>
<p> <span class="keywords"><strong>Your Affiliation</strong></span> Please indicate your interest in earthquake notification. If it isn't listed,
  write it into the &ldquo;Other&rdquo; box. </p>
<p> <span class="keywords"><strong>Aftershock Exclusion</strong></span> <img src="images/helpdoc/subscribe2.gif"
      alt="aftershock exclusion" width="400" height="58" /> </p>
<p>
  Large earthquakes generally will have many aftershocks. You may not wish
  to receive notification of all of them. This option will reduce the amount
  of mail you get after a large event. The ENS system will automatically
  define an aftershock zone and a magnitude threshold below which an
  earthquake will be considered an aftershock as follows: For events over
  M5.5 we will define an exclude region. The size of the aftershock zone is
  calculated according to the formula given by Wells and Coppersmith (1994).
</p>
<p>
  The length is then multiplied by 2 and that number is used as the radius
  around the epicenter for the aftershock zone. We used a hexagon to
  approximate a circle.  The magnitude limit is M-2 (2 units below the
  mainshock magnitude) within that region.
</p>
<p>
  The length of time for the exclusion is based on the mainshock magnitude
  as follows&hellip;
</p>
<ul>
  <li>M5.5-M6.0: 10 days</li>
  <li>M6.0-M6.5: 20 days</li>
  <li>M6.5-M7.0: 30 days</li>
  <li>M7.0-M8.0: 60 days</li>
  <li>M8.0+: 120 days</li>
</ul>
<p> This option can be turned on or off at any time on the &ldquo;Account Preferences&rdquo; tab. </p>
<p><strong>NOTE:</strong> Aftershock exclusion is turned on by default for new ENS accounts. If you do not want to use this feature, you must turn it off.</p>
<p> <span class="keywords"><strong> Receive updates  for events?</strong></span></p>
<img src="images/helpdoc/receive_updates.jpg" alt="event updates" style="width:383px;height:47px" />
<p>Occasionally ENS will send messages with an incorrect magnitude. In cases where the magnitude changes by more than 0.4 or the location changes by more than 0.5 degrees of latitude or longitude, the system will send out an update or deletion message to all users who received the original message. If you don't want to receive these updates, choose "No". If an event is deleted you will always receive the delete notification if the original magnitude meets your other setting thresholds.</p>

<p><span class="keywords"><strong>Defer notifications during night hours? </strong></span></p>
<img src="images/helpdoc/defer_night.jpg" alt="event updates" style="width:383px;height:45px" />
<p>Each profile has an associated day and night magnitude threshold. If an earthquake happens at night above the 'night' threshold, a message will be sent to you. If the magnitude is above the 'day' threshold, but below the 'night' level, having this option set will
cause the system to generate a message to you, which will be held until the following morning. This setting also applies to event updates. Select "Yes" to delay notifications.</p>
<p><strong>NOTE:</strong> This option is intended for profiles that generate messages sent to phones or pagers.</p>

<p> <span class="keywords"><strong>Password/Retype Password</strong></span> To change your password, enter a new password twice, once in each box. </p>
<p>Click Save Changes to store your new settings. A success message should appear below the button when your changes have been saved.</p>
<p>Note that you cannot change your Username.</p>
<h3>Vacation Option</h3>
<p><img src="images/helpdoc/accounts_vacation.jpg"
    alt="Vacation options" width="246" height="148" border="1" class="imgborders" /></p>
<p>ENS allows you to disable event notifications for a period of time. This feature is a simple way to suspend notifications for all profiles and all addresses associated with your account. Note that ENS does not save messages for you during your vacation, so you will not receive a backlog of notifications when your vacation ends.</p>
<h3>Unsubscribe</h3>
<p>
You can Unsubscribe at any time from the Account Preferences page.  Additionally, you can unsubscribe individual email addresses by replying to an earthquake message with a <strong>one-line, plain-text message containing the word 'STOP'</strong>. The system will automatically remove that address from the system. If your message contains extra text, or is a non-plain-text character encoding, the administrator will have to remmove it by hand, which may take several days.</p>
<img src="images/helpdoc/accounts_unsubscribe.jpg"
    alt="Unsubscribe button" width="178" height="74" border="1" class="imgborders" />
<p>
  If you click the &ldquo;Unsubscribe&rdquo; button, the following message will
  appear:</p>
<img src="images/helpdoc/accounts_unsubscribe2.jpg"
    alt="Confirm unsubscribe" width="438" height="308" border="1" class="imgborders" />
<p>
  If you click &ldquo;Yes, unsubscribe&rdquo;, you will get the following confirmation message:
</p>
<img src="images/helpdoc/unsubscribeconfirm.gif"
    alt="confirm unsubscribe message" width="300" height="141" />
<p>Click OK to return to the ENS homepage.</p>
<p>If you are having trouble unsubscribing, please contact the ENS administrator: <a href="mailto:ensadmin@ens.usgs.gov">ensadmin@ens.usgs.gov</a></p>
<p><strong>NOTE:</strong> All ENS earthquake messages contain a numeric identifier code associated with the email address they are being sent to. Long-form messages have an identifier code at the end of the subject like:<br>
<pre>2010-03-17 17:01:01 (M3.9) BAJA CALIFORNIA, MEXICO 32.3 -115.3 (31b86)</pre>
<p>Short-form ENS messages have an identifier code at the end, like
this:<br>
<pre>M6.2 21:44 5/29 -30.2 -178.1 105km SSW of Raoul Island, Kermadec Islands US c000yra7 31b86</pre>
<p>The '31b86' is the identifier code.</p>
<p>If you need assistance from the ENS administrator,
please forward one of these identifier codes so that the administrator can find your
account in the system.
</p>


<h2><a name="glossary"></a>ENS Glossary of Terms </h2>

<h3><a name="active"></a>Active</h3>
<p>
  Use this to mark this profile as active or inactive. This is useful for
  temporarily turning off email for vacations and such if you want to be able
  to reactivate it again without having to re-input the mail criteria. You will
  NOT receive any notifications that you missed after an Inactive account is
  set back to Active.
</p>

<h3><a name="address"></a>Address</h3>
<p>
  Displayed are the email address(es) that are currently registered to your
  account. You may have up to 15 addresses associated with your account. To add
  a new address, use the 'Add New Email Address' button in the right column
  under the &ldquo;My Email Addresses&rdquo; section. This will prompt you for
  the address and what format messages you want to receive on it. It will then
  send you a confirmation message. Addresses are confirmed by sending you a
  random three-digit confirmation number to ensure that the address is working.
  Enter that number in the form provided. Addresses are not given or sold by
  the USGS to any outside organization. Note that for a new profile, all
  addresses are selected by default. If there is an address you <em>don't</em>
  want associated with this profile, then uncheck it.
  <a href="http://www.usgs.gov/privacy.html">Privacy Policy</a>
</p>

<h3><a name="affiliation"></a>Affiliation</h3>
<p>Please indicate your interest in earthquake notification.</p>

<h3><a name="aftershock"></a>Aftershock Exclusion</h3>
<p>
  Large earthquakes generally will have many aftershocks. You may not wish
  to receive notification of all of them. This option will reduce the amount
  of mail you get after a large event. The ENS system will automatically
  define an aftershock zone and a magnitude threshold below which an
  earthquake will be considered an aftershock as follows: For events over
  M5.5 we will define an exclude region. The size of the aftershock zone is
  calculated according to the formula given by Wells and Coppersmith (1994).
</p>
<p>
  The length is then multiplied by 2 and that number is used as the radius
  around the epicenter for the aftershock zone. We used a hexagon to
  approximate a circle.  The magnitude limit is M-2 (2 units below the
  mainshock magnitude) within that region.
</p>
<p>
  The length of time for the exclusion is based on the mainshock magnitude
  as follows&hellip;
</p>
<ul>
  <li>M5.5-M6.0: 10 days</li>
  <li>M6.0-M6.5: 20 days</li>
  <li>M6.5-M7.0: 30 days</li>
  <li>M7.0-M8.0: 60 days</li>
  <li>M8.0+: 120 days</li>
</ul>
<p> This option can be turned on or off at any time on the &ldquo;Account Preferences&rdquo; tab. </p>

<h3><a name="mag"></a>Day Magnitude</h3>
<p>
  This is your magnitude cutoff for daytime hours. You will be notified of any
  events greater than this magnitude during your specified daytime hours. See
  also <a href="#mag_night">Night Magnitude</a>.
</p>

<h3><a name="day_begin"></a>Day Begins/Day Ends </h3>
<p>
  You need to specify hours to be considered 'daytime'. Times are based on a
  <a href="http://en.wikipedia.org/wiki/24-hour_clock" target="_blank">24-hour
  clock</a>. The 'end' value must be equal to or greater than the 'begin'
  value, and both must be in the range of 0-23 hours. If the day and night
  hours are equal, then all hours will be considered 'daytime' and you will get
  all notifications for events greater than your specified daytime magnitude.
  Note that the hours are specified in local <a
  href="http://en.wikipedia.org/wiki/Standard_time" target="_blank">standard
  time in your time zone</a> that you specified when you opened your ENS
  account. If you need to change your time zone, use the 'Edit your account'
  function on the main page.
</p>
<p>
  For an email account, start and end times of 00:00 are appropriate. Day and
  night hours are only really relevant for pagers or cellphones, where you
  might want to designate a night-time where you would receive fewer
  notifications.
</p>

<h3><a name="defer" id="defer"></a>Defer notifications during night hours</h3>
<p>
  If a notification or an update occurs during your night hours that does not
  meet the magnitude threshold for nighttime, you will get the message at the
  start of
  the next day if you select &ldquo;Yes&rdquo;, and you will not get the
  notification or update if you select &ldquo;No&quot;.
</p>

<h3><a name="depth" id="depth"></a>Event Depth </h3>
<p>
  This option can be seen only if you indicate an affiliation of
  Scientist/Network Operator. Use this if you want to be notified about events
  in a certain area only within a certain depth range.
</p>

<h3><a name="language"></a>Language</h3>
<p>Select your language. English and Spanish are currently the only choices.</p>

<h3><a name="pager"></a>PAGER</h3>
<p>"If selected, ENS will send a short summary containing the summary PAGER summary 
alert level (Green, Yellow, Orange, or Red) and a URL link to the USGS event page 
where the detailed PAGER report can be accessed.    PAGER reports come about 10 
minutes after U.S. earthquakes and 20 minutes for those outside the U.S.  
Occasionally a PAGER report is updated if a significant change is made to earthquake 
magnitude or the affected location.</p> 

<p>PAGER (Prompt Assessment of Global Earthquakes for Response) estimates earthquake 
impacts by combining population density, shaking intensity, and models of economic 
and fatality losses.  Loss models reflect the regional differences in building 
inventory and vulnerability.</p> 

<p>The PAGER Green summary predicts minor or no economic impact and fatalities are not expected.  
Red predicts severe economic impact, and/or many casualties.  
Yellow and Orange are applied to intermediate degrees of estimated losses.</p> 

<p>Please see the PAGER project page at <a href="https://earthquake.usgs.gov/data/pager/" target="_new">https://earthquake.usgs.gov/data/pager/</a>  
for a more complete explanation of PAGER. Information there explains how to read a
 onePAGER report, the scientific background for PAGER, and a searchable archive of past 
 PAGER reports. The searchable archive of past PAGER events can be used to estimate 
 how often ENS might send a PAGER message.   For reference, globally, for all 
 magnitudes, PAGER develops about one green alert per day and one to two red alerts per year."</p> 
<p>&nbsp;</p>

<h3><a name="shakemap"></a>ShakeMap</h3>
<p>"If selected, ENS will send a short summary containing the ShakeMap peak shaking intensity and a 
link to the associated USGS event page where the detailed ShakeMap report can be accessed.</p> 

<p>ShakeMaps show shaking and ground motion in maps generally centered on the earthquake location.
 Shaking intensity is shown on an intensity scale, where intensity I means imperceptible shaking 
 and intensity IX can cause damage to well-built structures (<a href="https://www.usgs.gov/programs/earthquake-hazards/modified-mercalli-intensity-scale" target="_new">https://www.usgs.gov/programs/earthquake-hazards/modified-mercalli-intensity-scale</a>). 
 At intensity V and above shaking has some potential to cause damage.</p> 

 <p>Shaking intensities in map view are estimated from earthquake magnitude, depth, and distance from the source. 
ShakeMap also considers local geologic conditions that can amplify or deamplify shaking relative to the average, 
such as whether a site is on weak soil or firm rock, respectively.  Measurements from seismic stations are used, 
where available, to improve the map relative to the estimate from magnitude and depth alone. ShakeMaps are 
periodically updated as intensity reports from the USGS “Did You Feel It?” system data are received.</p> 

<p>ShakeMap also reports peak ground acceleration (PGA), peak ground velocity (PGV) and spectral acceleration 
(SA) at multiple periods. Please see the ShakeMap Manual for additional information.  A searchable archive 
is available to see past ShakeMaps and to evaluate how often ShakeMaps are made.  PAGER includes information 
from ShakeMap, so subscribing just to PAGER may meet many user's needs.  For further information about 
ShakeMap, please see <a href="https://earthquake.usgs.gov/data/shakemap/" target="_new">https://earthquake.usgs.gov/data/shakemap/</a>".</p> 
<p>&nbsp;</p>

<h3><a name="shakealert"></a>ShakeAlert&reg; Earthquake Early Warning System</h3>
A few minutes after an earthquake of magnitude 4 or larger in California, Oregon, or Washington, 
ENS subscribers to the ShakeAlert&reg; message will receive a short message. The message will say 
when the earthquake occurred and what was its peak estimated magnitude, and give a short URL 
to the appropriate USGS event web page. Subscribing to ENS messages about ShakeAlert&reg; will not 
provide timely earthquake early warning.

The ShakeAlert&reg; Earthquake Early Warning System rapidly detects potentially damaging 
earthquakes in or near the three west-coast states and estimates shaking intensities. 
ShakeAlert&reg; quickly sends messages for technical partners who actually provide early warning to end users.

A few minutes after the earthquake, a review is made of ShakeAlert&reg; performance. The review 
compares ShakeAlert&reg; magnitude and location estimates to normally more accurate estimates 
from a regional seismic network. The ENS message is based on the ShakeAlert&reg; System performance report.

For further explanation and details, please see: <a href="https://www.usgs.gov/programs/earthquake-hazards/science/early-warning" target="_new">https://www.usgs.gov/programs/earthquake-hazards/science/early-warning</a> </p>

<h3><a name="oaf"></a>OAF: Operational Aftershock Forecast</h3>
<p>Aftershocks are expected to occur following most moderate and large earthquakes.  Larger mainshocks tend to 
cause more aftershocks and have a higher chance of triggering a sizeable aftershock.   
The rate of aftershocks slows over time after the mainshock, but can continue at a low level for months or 
sometimes years.  The USGS updates its aftershock forecasts periodically as more information becomes available.</p>   

<p>For additional information on past forecasts and background on how forecasts are made, see:  <a href="https://earthquake.usgs.gov/data/oaf/" target="_new">https://earthquake.usgs.gov/data/oaf/</a></p> 
  <p>&nbsp;</p>
 
<h3><a name="format"></a>Message Format</h3>
<p>
  The message format is related to the type of account. The 'HTML' and 'long' formats are
  suitable for email, while the 'short' format is more appropriate for cell
  phones, pagers, and PDAs. The 'raw' format is a machine-readable compact
  format.
</p>
<p style="font-weight: bold;">Example of Short Format:</p>
<pre>
  M6.2 21:44 5/29 -30.2 -178.1 105km SSW of Raoul Island, Kermadec Islands US yra7 31b86
</pre>

<p>The information included is:
</p>
<p>magnitude time (UTC) date latitude longitude descriptive location event_id email_id</p>

<p style="font-weight: bold;">Example of Long Format:</p>
<pre>
  == PRELIMINARY EARTHQUAKE REPORT ==
  **This event supersedes event US hsal.

  Region:                     SOUTHERN QUEBEC, CANADA
  Geographic coordinates:     45.026N, 73.881W
  Magnitude:                  3.7 Ml
  Depth:                      12 km
  Universal Time (UTC):       9 Jan 2006 15:35:40
  Time near the Epicenter:    9 Jan 2006 10:35:40
  Local time in your area:    9 Jan 2006 08:35:40

  Location with respect to nearby cities:
    19 km (12 miles) NE (54 degrees) of Chateaugay, NY
    23 km (15 miles) NW (310 degrees) of Altona, NY
    24 km (15 miles) WNW (287 degrees) of Mooers, NY
    60 km (37 miles) SSW (192 degrees) of Laval, Qu&eacute;bec, Canada
    60 km (37 miles) SSW (204 degrees) of Montr&eacute;al, Qu&eacute;bec, Canada

  ADDITIONAL EARTHQUAKE PARAMETERS
  __________________________________________________
  event ID                     : LD 2006010900
  version                      : 1
  number of phases             : 24
  rms misfit                   : 0.24 seconds
  horizontal location error    : 0.4 km
  vertical location error      : 1.1 km
  maximum azimuthal gap        : 75 degrees
  distance to nearest station  : 31.3 km

  Flinn-Engdahl Region Number = 447
  This is a computer-generated message and has not yet been reviewed by a seismologist.
  For subsequent updates, maps, and technical information, see:
  https://earthquake.usgs.gov/earthquakes/eventpage/ld2006010900#executive
  or
  https://earthquake.usgs.gov/

  CISN Southern California Management Center
  Caltech Seismological Laboratory
  U.S. Geological Survey
  http://www.cisn.org/scmc.html
  ________________________________

  DISCLAIMER: http://earthquake.usgs.gov<?php echo $CONFIG['MOUNT_PATH'] ?>/disclaimer.html

  This email was sent to lisa@usgs.gov

  You requested mail for events within the 'East Coast' region
  for M2.5 between 08:00 and 23:00 and M4.0 other times.

  To change your parameters or unsubscribe, go to:

  http://earthquake.usgs.gov<?php echo $CONFIG['MOUNT_PATH'] ?>
  ---
    </pre>

<p style="font-weight: bold;">Example of HTML Format:</p>
<p><img src="images/HTML_format.png" width="825" height="652" border="1" class="imgborders" alt="HTML format" /></p>
<p>Most of the information is self-explanatory, but the information included in the  PARAMETERS
section is:</p>
<p>Nph - number of phases<br />
  Dmin - distance to nearest station
     (km)<br />
Rmss - rms misfit (seconds)<br />
Gp - azimuthal gap (degrees)<br />
M-type - magnitude type</p>
<p>
  </pre>

</p>
<h3><a name="networks"></a>Networks</h3>
<p>
  You will only see these if you have indicated an affiliation of
  Scientist/Network Operator. These are the seismic <a
  href="http://quake.geo.berkeley.edu/anss/anss-catalog-source-codes.html"
  >networks</a> to accept events from. You will <strong>only</strong> be
  notified of events declared by these networks. By default, all networks are
  selected (except AT, which provides preliminary solutions that are always
  superseded by NEIC or regional network locations within a few minutes). If
  you know that you want to exclude one or more, then uncheck them. Otherwise,
  leave them all checked. The choices are:
</p>

<ul>
  <li><strong>CI</strong>: Southern California</li>
  <li><strong>NC</strong>: Northern California</li>
  <li><strong>NN</strong>: Nevada</li>
  <li><strong>UU</strong>: Utah</li>
  <li><strong>UW</strong>: Pacific Northwest</li>
  <li><strong>AK</strong>: Alaska</li>
  <li><strong>NM</strong>: New Madrid - University of Memphis</li>
  <li><strong>HV</strong>: Hawaii Volcano Observatory</li>
  <li><strong>PR</strong>: Puerto Rico Seismic Network</li>
  <li><strong>US</strong>: NEIC worldwide network</li>
  <li><strong>SE</strong>: Southeastern U.S.</li>
  <li><strong>LD</strong>: Lamont-Doherty - Columbia University, New York</li>
  <li><strong>MB</strong>: Montana Bureau of Mines and Geology</li>
  <li><strong>WY</strong>: Yellowstone Seismic Network - Wyoming</li>
  <li><strong>AR</strong>: Arizona Seismic Network</li>
  <li><strong>AT</strong>: Alaska Tsunami Warning Center</li>
  <li><strong>TX</strong>: Texas Seismological Network</li>
</ul>

<h3><a name="mag_night"></a>Night Magnitude</h3>
<p>
  This is your magnitude cutoff for non-daytime hours. You will be notified of
  any events greater than this magnitude during these hours. Typically your
  night magnitude will be larger than your day magnitude so that you're less
  likely to get notifications while you're sleeping, but there is no reason why
  night can't be the same as the daytime setting.
   It all depends you your personal schedule
  and when you want to receive alerts. This is only really applicable if you
  are having alerts sent to a pager or cell phone that can wake you up in the
  middle of the night. If you are having alerts sent to an email account that
  won't wake you up in the middle of the night, then you will probably want the
  night magnitude to be the same as the day magnitude. You will NOT receive any
  notifications that you missed after the Night Magnitude time period is over
  and the Day Magnitude period begins. See also <a href="#mag">Day
  Magnitude</a>.
</p>

<h3><a name="profilename"></a>Profile Name</h3>
<p>
  This is an optional field where you can enter a name for your profile. The
  name is used to identify the profile on your account display, and also in the
  footer of any messages sent to you.
</p>

<h3><a name="timezone"></a>Time Zone</h3>
<p>
  Select your time zone, based on your offset from GMT. For areas where
  daylight saving time is implemented, select your time zone based on the
  offset for Standard Time. <a target="_blank"
  href="http://wwp.greenwichmeantime.com/info/timezone.htm">What's my time
  zone?</a>
</p>

<h3><a name="updates" id="updates"></a>Updates and deletions for events</h3>
<p>
  Updates are sent for previous notifications if the magnitude changes by more
  than 0.4 or the location changes by more than 0.5 degrees of latitude or
  longitude. Deletion messages are sent if the system previously sent a notification
  for a nonexistent event. If you do not want to receive any updates or deletions, select
  &ldquo;No&rdquo;.
</p>



<h3><a name="disclaimer"></a>DISCLAIMER</h3>
<p>
  The earthquake information delivered through the Earthquake Notification
  Service (ENS) is preliminary. Subsequent review usually results in some
  revision to the data, and all users are advised to check the USGS earthquake
  program pages at <a href="https://earthquake.usgs.gov"
  >https://earthquake.usgs.gov</a> for updates. Data users are cautioned to
  consider carefully the provisional nature of the information before using it
  for decisions that concern personal or public safety or the conduct of
  business that involves substantial monetary or operational consequences.
  Earthquakes are a common occurrence, and many are either not large enough to
  cause damage or not located sufficiently close to populations centers to
  produce damage. E-mail alerts sent through ENS do not imply an impending
  threat.
</p>
<p>
  ENS is an informational tool and NOT a robust earthquake or tsunami warning
  system. The USGS does not produce tsunami warnings. For the information about
  tsunamis, please refer to the information given in the NOAA website
  <a href="http://tsunami.gov/">http://tsunami.gov/</a>.
</p>
<p>
  On a global basis, earthquakes of magnitude 5.0 or greater are generally
  reviewed and distributed by ENS within 2 hours or their occurrence. Some
  events of magnitude 5.0 to 6.0 in remote parts of the world, especially on
  mid-ocean ridges in parts of the Southern Hemisphere, may not be distributed
  until 24 hours after their occurrence. Within the US, widely felt earthquakes
  are generally distributed within 5 minutes. Additionally, processing and
  sending the messages typically takes 30 minutes. The USGS cannot guarantee
  the receipt or timeliness of an e-mail message after it has been sent.
</p>
<p>
  If numerous bounced messages are received from your account, the automated
  system will not remove your address from the ENS mailing list. If that should
  happen, your mail profiles will be marked 'inactive' and the system will stop
  sending you mail. If you fix your address and you still want to receive
  e-mails, just log in and mark your profiles active.
</p>
<p>
  The events which have been located by the USGS and contributing agencies
  should not be considered to be a complete list of ALL events M2.5+ in the US
  and adjacent areas and especially should not be considered to be complete
  lists of ALL events M4.0+ in the World. The World Data Center for Seismology,
  Denver (a part of the USGS National Earthquake Information Center) continues
  to receive data from observatories throughout the world for several months
  after the events occurred, and using those data, adds new events and revises
  existing events in later publications. For a description of these later
  publications and the data available, see <a
  href="/research/index.php?areaID=13">Scientific Data</a>.
</p>
<p>
  Please address unresolved mailing list issues to:
  <a href="mailto:ensadmin@ens.usgs.gov">ensadmin@ens.usgs.gov</a>.
</p>

<h3><a name="PRA"></a>PRA - Privacy Statement</h3>
<p>
  This form is subject to the Privacy Act of 1974.
</p>
<p>
  <strong>Authority</strong><br />
  The National Earthquake Hazards Reduction Program (NEHRP), which was first
  authorized in 1977, Public Law (PL) 95–124), and most recently reauthorized
  in 2004 (NEHRP Reauthorization Act of 2004, PL 108–360
</p>
<p>
  <strong>Principal Purpose</strong><br />
  The Earthquake Hazards Program provides rapid, authoritative information on
  earthquakes and their impact to emergency responders, governments, facilities
  managers and researchers across the country.
</p>
<p>
  <strong>Routine Use</strong><br />
  Used to allow users to report shaking intensity of earthquake events, to
  allow users to receive notifications of earthquake events, and to allow
  users to volunteer to have seismic instrumentation installed on their
  property.
</p>
<p>
  <strong>Disclosure is Voluntary</strong><br />
  If the individual does not furnish the information requested, there will be
  no adverse consequences. However, if you do not provide contact information
  we may be unable to contact you for additional information to verify your
  responses.
</p>
<p>
  <strong>Privacy Act Statement</strong><br />
  You are not required to provide your personal contact information in order to
  submit your survey. However, if you do not provide contact information, we
  may be unable to contact you for additional information to verify your
  responses. If you do provide contact information, this information will only
  be used to initiate follow-up communications with you. The records for this
  collection will be maintained in the appropriate Privacy Act System of
  Records identified as Earthquake Hazards Program Earthquake Information.
  (INTERIOR/USGS-2) published at 74 FR 34033 (July 14,2009).
</p>
<p>
  <strong>Paperwork Reduction Act Statement</strong><br />
  The Paperwork Reduction Act of 1995 (44 U.S.C. 3501 et. seq.) requires us to
  inform you that this information is being collected to supplement
  instrumental data and to promote public safety through better understanding
  of earthquakes. Response to this request is voluntary. Public reporting for
  this form is estimated to average 6 minutes per response, including the time
  for reviewing instructions and completing the form. A Federal agency may not
  conduct or sponsor, and a person is not required to respond to, a collection
  of information unless it displays a currently valid OMB Control Number.
  Comments regarding this collection of information should be directed to:
  Bureau Clearance officer, U.S. Geological Survey, 807 National Center,
  Reston, VA 20192.
</p>
