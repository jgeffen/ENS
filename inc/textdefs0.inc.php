<?php
# Text definitions for English
#$helptext = "help?";
$helptext = "";
$systemnametext = "USGS Earthquake Notification Service";
$welcometext = "Welcome";
$backtoprofiletext = "Back to Profiles";
$showonlyeventstext = "Show only events larger than magnitude";
$showonlynettext = "Show only events reported by net";
$filtereventstext = "Filter events";
$welcomemessagetext = "<p>Welcome to the Earthquake Notification Service (ENS).  <strong>Default notification 
profiles</strong> have been defined for you below, which you can edit or delete. You can also create your own additional custom 
notification profiles using the map tools below.</p>

<p>ENS is highly customizable. You can change account settings on the <a href=\"accountprefs\">Account Preferences</a> page. To change email address settings or add your cell phone, click the My Email Addresses button below.</p>

<p>Note that you are signing up for an <em>automated</em> service. If you employ any type of <strong>spam-blocker</strong> on your email account, you are responsible for 
allowing the ENS mail address to send to you. Mail is sent from the address 'ens@ens.usgs.gov'. Also, do not allow your account to auto-reply to ENS mail if you are on 
vacation. Failure to do so may result in your ENS account being disabled or deleted.</p>";

# Definitions for register.php
$regverificationtext = "Please enter the <a href=\"http://plus.maths.org/issue23/news/captcha/\" target=\"_blank\">verification code</a> below. This is five random upper-case letters. It is not case-sensitive.";

# Definitions for send_sample_email.inc.php
//$sampleemailtitletext = "Try out ENS";
$sampleemailentertext = "Enter your email";
$sampleemailsubmit = "Send sample email";


# Definitions for mailparams.php

$mailparamsbreadcrumbstext = "Earthquake Notification Service / Notification Profiles";
$profilesheadertext = "<h1>Notification Profiles Associated with %s's Account</h1>";
$editdaybegintext = "Day Begins";
$editdayendstext = "Day Ends";
$viewrecenttext = "View recent events sent to your account";
$profilescount1text = "%d mail profile found for your account:";
$profilescount2text = "%d mail profiles found for your account:";
$noprofilestext = "No profiles have been defined yet - please create a profile using one of the 4 buttons at the left.<br /><font size=\"+2\"<em>You will not receive earthquake notifications until you have done this!</em></font><br />Enter additional address(es) for notifications by selecting \"New Address\".<br />Your account information can be updated or deleted using \"My Account\".";
$noaddresstext = "<p class=\"alert error\">You have no confirmed email addresses in the database. You will not receive earthquake notifications until you confirm an address.</p><p>Check your email for a confirmation message. Otherwise, please <a href=\"regaddr\">register or confirm</a> at least one address to receive earthquake notifications.</p>";
$geoboundstext = "Geographic Bounds";
$placenametext = "Name";
$nametext = "Name";
$daymagtext = "Day Mag";
$nightmagtext = "Night Mag";
$allmagtext = "Mag";
$alltimestext = "All Times";
$networkstext = "Networks";
$addresstext = "Address";

$assocemailtext = "Email Addresses Associated With Your Account";
$addnewprofiletext = "Add a New Profile";

$profileedittext = "Edit This Profile";

# Definitions for mailparamsedit1.php

$edit1breadcrumbtext1 = "Earthquake Notification Service / Profiles / Edit an Existing Profile";
$edit1breadcrumbtext2 = "Earthquake Notification Service / Profiles / Add a New %s Profile";
$edit1headertext = "Earthquake Information Email Parameters";
$edit1predeftext = "Predefined";
$addresseslabeltext = "<b>Addresses:</b>";
$activelabeltext = "<b>Active:</b>";
$noaddresseslabeltext = "You do not have any addresses in the database. I cannot create a rule without an email address to associate with it!";
$editnetworkstext = "<b>Networks:</b>";
$definerectangletext = "Define a rectangular boundary<br />NOTE: Longitudes in the western hemisphere are negative";
$definerectanglenlat = "North";
$definerectangleslat = "South";
$definerectangleelon = "East";
$definerectanglewlon = "West";
$definecircletext = "<b>Define a circular boundary</b>";
$definecircleclat = "Center:Lat";
$definecircleclon = "Center:Lon";
$definecircleradius = "Radius (miles)";
$definecircleplacename = "Name for this place";
$definecircleselectplace = "<i>Or</i> select a place";
$definecirclepickpoints = "<i>Or</i> pick points on a map";
$definecustomregiontext = "Define a new custom geographic region";
$definecustomareatext = "Define a geographic region";
$definepolygontext = "Define a polygon boundary";
$createnewpolytext = "<b>Create a new polygon boundary</b>";
$pickpointstext = "Pick new points";
$usemaptext = "Pick Points On Map";
$usemapedittext = "Edit your polygon on the map";
$pickcircletext = "Define a circle on a map";
$uploadxmltext = "Upload an XML file";
$editcurrentpolytext = "Advanced Editing Options";
$basiceditoptionstext = "Basic Editing Options";
$polygoneditortext = "Polygon Editor";
$xmledittext = "Edit as XML";
$latloninputtext = "Input lat/lon values";
$cannedprofiletext = "Pick a Predefined Profile";
$cannedpreviewtext = "Click to see preview of selected area";
$cannedcurrentselectedtext = "Currently selected";
$cannedselecttext = "Select profile";
$editdaymagtext = "<b>Day Magnitude:</b>";
$editnightmagtext = "<b>Night Magnitude:</b>";
$editdepthtext = "Event Depth (km)";
$editprofilenametext = "<b>Name for this Profile:</b>";
$editsubmittext = "Submit Information";
$editdeleteprofiletext = "Delete this Profile";

# Definitions for mailparamsedit2.php

$edit2breadcrumbtext1 = "Earthquake Notification Service / Profiles / Edit an Existing Profile";
$edit2breadcrumbtext2 = "Earthquake Notification Service / Profiles / Add a New %s Profile";
$edit2headertext = "Earthquake Information Email Parameters";
$edit2predeftext = "Predefined";
$definecirclenoradius = "<br />Error! You cannot define a circle with a radius of 0!";
$edit2recordtext = "Database record updated";
$edit2inserttext = "New database record created";
$edit2submittext = "Continue";

# Definitions for mailparamsdelete.php

$profiledeletebreadcrumbtext = "Earthquake Notification Service / Profiles / Delete a Mail Profile";
$profiledeleteheadertext = "Delete notification profile";
$deletedaymagtext = "Day Magnitude";
$deletenightmagtext = "Night Magnitude";
$deletedaybegintext = "Day Begins";
$deletedayendstext = "Day Ends";
$profiledeleteconfirmtext = "Delete this profile?";
$profiledeletetext = "Profile database record deleted";
$profiledeletecontinuetext = "Continue";

# Definitions for emailedit.php

$emaildaybegintext = "<b>Day Begins:</b>";
$emaildayendstext = "<b>Day Ends:</b>";
$emaileditbreadcrumbtext = "Earthquake Notification Service / Profiles / Edit Your Email Address";
$emaileditheadertext = "<h2>Edit an email address</h2>";
$emaileditlabeltext = "Email Address";
$emaileditformattext = "Message Format";
$emaileditconfirmtext = "Submit Information";
$emaildeletetext = "Delete this email address";
$emailedittext = "Email database record updated";
$emaileditcontinuetext = "Continue";

# Definitions for deleteaddress.php

$emaildelbreadcrumbtext = "Earthquake Notification Service / Profiles / Delete an Email Address";
$emaildelheadertext = "<h2>Delete an email address</h2>";
$emaildelwarningtext = "This will delete address <em>%s</em>. It will also delete any mail profiles which have <em>%s</em> as their only address.";
$emaildelquerytext = "Are you sure you want to delete address <em>%s</em>?";
$emaildelconfirmtext = "Delete This Address";
$emaildeltext = "Address <em>%s</em> has been deleted";
$emaildelcontinuetext = "Continue";

# Definitions for passwd.php

$passwdbreadcrumbtext = "Earthquake Notification Service / Edit Your Account";
$passwdheadertext = "Edit Your Account";
$passwdnomatchtext = "Error: New passwords do not match!  Please try again.";
$passwdupdatetext = "Your account record has been updated";
$passwdcontinuetext = "Continue";
$passwdemailtext = "Email";
$passwdusernametext = "Username";
$passwdnametext = "Your Name";
$passwdaddresstext = "Address";
$passwdcitytext = "City";
$passwdstatetext = "State";
$passwdziptext = "ZIP Code";
$passwdphonetext = "Phone number";
$passwdtimezonetext = "Time Zone";
$whatsmytimezonetext = "<a href=\"http://aa.usno.navy.mil/faq/docs/world_tzones.html\" target=\"_blank\">What's my time zone?</a>";
$passwdlanguagetext = "Preferred Language";
$passwdpasswdtext = "Password";
$passwdconfirmtext = "Retype Password";
$passwdlanguagetext = "Preferred Language";
$passwduserclasstext = "User Class";
$passwdaftershocktext = "Aftershock Exclusion?";
$passwdupdatestext = "Receive updates for events?";
$passwddefertext = "Defer notifications during night hours?";
$passwdaffiliationtext = "Your Affiliation";
$passotherinteresttext = "If Other, specify here";
$passwdpasswdchangetext = "Leave these blank unless you want to change your password:";
$passwdsubmittext = "Submit Information";
$passwddeletetext = "Delete Your Account";
$passwdalertnotifications= "Additional Notification Products";

# Definitions for deleteaccount.php

$acctdelbreadcrumbtext = "Earthquake Notification Service / Profiles / Delete Your Account";
$acctdelheadertext = "Delete Your Account";
$acctdelwarningtext = "This will delete your account '%s' and all the email addresses and mail notification profiles associated with it.";
$acctdelquerytext = "<em>Are you sure you want to do this</em>?";
$acctdelconfirmtext = "Delete This Account";
$acctdelnoconfirmtext = "No, don't delete";
$acctdeltext = "Your account has been deleted";
$acctdelcontinuetext = "Continue";

# Definitions for newaddressreg.php

$newaddrbreadcrumbtext = "Earthquake Notification Service / Profiles / Register a New Address";
$newaddrheadertext = "Register a New Email Address";
$newaddremailtext = "Enter Your Address<br />(one address only)";
$newaddrreplacetext = "Replaces:";
$newaddrtext = "This is a new address";
$newaddrexpltext = "If the new address replaces an old one, select the address to replace here, otherwise leave it as $newaddrtext";
$newaddrformat0text = "HTML Format";
$newaddrformat1text = "Regular Email";
$newaddrformat2text = "Pager/Cell Phone";
$newaddrformat3text = "Raw CUBE Format";
$newaddrnotetext = "<font color=\"#CC0000\">A <strong>confirmation number</strong> will be sent to the address you entered. Please save this number to enter on the next page. There may be a short delay for pager/cell mail.<br /><br /><em>NOTE: If you have any sort of spam-blocker, you are responsible for configuring it to allow mail from 'ens@ens.usgs.gov'. If you do not do this, you will be unable to use this service.</em></font><br /><br />To find your address for messaging to cell phones, see <a href=\"http://www.lifehacker.com/software/cell-phones/send-sms-from-email-127033.php\" target=\"_blank\">http://www.lifehacker.com/software/cell-phones/send-sms-from-email-127033.php</a>";
$newaddrsubmittext = "Next";
$newaddrconftext = "For security purposes and to make sure you typed your address correctly, we have sent a message to:<br /><br /><em>%s</em><br ><br />This message contains a <strong>three-digit confirmation number</strong>. Enter this number in the space below.";
$newaddremailsubjtext = "USGS ENS Confirmation Code";
$newaddremailconftext = "Your confirmation number is %03d";
$newaddrnomatchtext = "Confirmation numbers do not match!";
$pendingtext = "You have addresses waiting to be confirmed!";


# Definitions for recentevents.php

$recenteventsbreadcrumb1text = "Earthquake Notification Service / Last Events Processed";
$recenteventsbreadcrumb2text = "Earthquake Notification Service / Profiles / Last Events Processed for User %s";
$recenteventsheadertext = "Recent Events Processed";
$recenteventsnoeventstext = "No events have been sent to your account in the last 10 days";

# Definitions for search.php

$searchbreadcrumbtext = "Earthquake Notification Service / Find Your Account or Password";
$searchheadertext = "";
$searchpwdresettext = "<br />We have reset your password and sent an email to your first email address with your new password.<br /> Please log on and change it.<br />";
$searchusernametext = "<br />We have reset the password on your ENS account and sent an email to %s with your username and new password.<br /> Please log on and change it.<br />";
?>
