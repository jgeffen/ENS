<?php
/**
 *	Figure out if any of the tabs needs a style
 */

$accountStyle = $profileStyle = $emailStyle = '';
$userhome_link = "userhome_map";
$recent_link = "userhome_map?recent=true";

switch($page) {
	case 'accountprefs':
		$accountStyle = "selected";
	break;

	case 'myemail':
		$emailStyle = "selected";
	break;

	case 'regaddr':
	break;

	case 'userhome_map':
	default:
		$profileStyle = "selected";
		$userhome_link = '#';
		$recent_link = '#';
	break;
}

?>

<div id="map_header">
	<ul id="map_tabs">
		<li class="<?=$profileStyle; ?>" id="tab_my_profiles">
			<a href="<?= $userhome_link; ?>">My Profiles</a>
		</li>
		<li id="tab_recent">
			<a href="<?=$recent_link; ?>" >Recent Events</a>
		</li>
		<li class="<?=$emailStyle; ?>" id="tab_my_email">
			<a href="myemail" >My Email Addresses</a>
		</li>
		<li class="<?=$accountStyle; ?>" id="tab_account">
			<a href="accountprefs" >Account Preferences</a>
		</li>
		<li class="important" id="help">
			<a href="help" >Help</a>
		</li>
	</ul>
</div>
