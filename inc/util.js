/* Functionality for the login page */
var TimeZoneOffset = -((new Date()).getTimezoneOffset()/60)
function setHiddenValues() {
	document.register.timezone.value=TimeZoneOffset
}

function newWindow(mapbox) {
	bigmapbox = window.open(mapbox, "stationinfoBox", "width=500,height=500,scrollbars=yes,toolbars=no,resizable=yes");
	bigmapbox.focus();
}


/* Functions from the profile edit page */
function handleContinent(dd1) {
	var idx = dd1.selectedIndex;
	var val = dd1[idx].value;
	var name = dd1[idx].text;
	var par = document.forms["mailrules"];
	var parelmts = par.elements;
	var countrysel = parelmts["country"];
	var regionsel = parelmts["region"];
	var continent = val;
	if (continent != 0) {
		document.mailrules.region.length = 1;
		document.mailrules.cannedrule.value=continent;
		document.mailrules.cannedrulename.value=name;
		document.getElementById('map_preview').href = "inc/map_preview.inc.php?mode=full&a=Y&pid="+continent;
		var directory = ""+document.location;
		directory = directory.substr(0, directory.lastIndexOf('/'));
		
		Http.get({
			url: "inc/getsubregions.inc.php?id=" +  continent,
			callback: fillCountry,
			cache: Http.Cache.Get
		}, [countrysel,regionsel]);
		
	}
}

function fillCountry(xmlreply, countryelmt, regionsel) {
	if (xmlreply.status == Http.Status.OK) {
		var countryresponse = xmlreply.responseText;
		var pairs = countryresponse.split("|");
		countryelmt.length = 1;
		countryelmt.disabled = false;
		countryelmt[0].value = 0;
		countryelmt[0].text = "Select country";
		countryelmt.length = pairs.length;
		for (o=1; o < pairs.length; o++) {
			var values = pairs[o].split(",");
			countryelmt[o].value = values[0];
			countryelmt[o].text = values[1];
		}
		cleansubregions(regionsel);
	} else if (!xmlreply.status) {
		//alert('There are no subregions for this selection.');
		// This is so if someone selects a country with subregions, 
		// then changes the country selection to one without subregions, 
		//the previous subregions do not remain available.
		cleansubregions(countryelmt);
		clearsubregions(regionsel);
	} else {
		alert(xmlreply.status + ": Error - Cannot handle the Ajax call.");
	}
}

function handleCountry(dd1) {
	var idx = dd1.selectedIndex;
	var val = dd1[idx].value;
	var name = dd1[idx].text;
	var par = document.forms["mailrules"];
	var parelmts = par.elements;
	var regionsel = parelmts["region"];
	var country = val;
	if (country != 0) {
		document.mailrules.cannedrule.value=country;
		document.mailrules.cannedrulename.value=name;
		document.getElementById('map_preview').href = "inc/map_preview.inc.php?mode=full&a=Y&pid="+country;
		var directory = ""+document.location;
		directory = directory.substr(0, directory.lastIndexOf('/'));
		
		Http.get({
			url: "inc/getsubregions.inc.php?id=" +  country ,
			callback: fillRegion,
			cache: Http.Cache.Get
		}, [regionsel]);
	}
}

function fillRegion(xmlreply, regionelmt) {
	if (xmlreply.status == Http.Status.OK) {
		var regionresponse = xmlreply.responseText;
		var pairs = regionresponse.split("|");
		regionelmt.length = 1;
		regionelmt.disabled = false;
		regionelmt[0].value = 0;
		regionelmt[0].text = "Select region";
		regionelmt.length = pairs.length;
		for (o=1; o < pairs.length; o++) {
			var values = pairs[o].split(",");
			regionelmt[o].value = values[0];
			regionelmt[o].text = values[1];
		}
	} else if (!xmlreply.status) {
		//alert('There are no subregions for this selection.');
		// This is so if someone selects a country with subregions, 
		// then changes the country selection to one without, the previous 
		// subregions do not remain available.
		cleansubregions(regionelmt); 
	} else {
		alert(xmlreply.status + ": Error - Cannot handle the Ajax call.");
	}
}

function handleRegion(dd1) {
	var idx = dd1.selectedIndex;
	var val = dd1[idx].value;
	var name = dd1[idx].text;
	var par = document.forms["mailrules"];
	var parelmts = par.elements;
	var region = val;
	if (region != 0) {
		document.mailrules.cannedrule.value=region;
		document.mailrules.cannedrulename.value=name;
		document.getElementById('map_preview').href = "inc/map_preview.inc.php?mode=full&a=Y&pid="+region;
	}
}

function cleansubregions(elmt) {
	elmt.length = 1;
	elmt[0].value = 0;
	elmt[0].text = "Currently Unavaliable";
	elmt.disabled = true;
}

function submitform() {
	url = document.getElementById('map_preview').href;
	bigmapBox = window.open(url, "mapPreview", "width=600,height=600,scrollbars=yes,toolbars=yes,resizable=yes");
	bigmapBox.focus()
	return false;
}

/* 
 * The following functions are credited as follows:
 * Original:  Pankaj Mittal (pankajm@writeme.com)
 * Web Site:  http://www.fortunecity.com/lavendar/lavender/21
 * 
 * This script and many more are available free online at
 * The JavaScript Source!! http://javascript.internet.com
*/
function small_window(myurl) {
	var newWindow;
	var props = 'scrollBars=yes,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,width=600,height=600';
	newWindow = window.open(myurl, "Add_from_Src_to_Dest", props);
}
function medium_window(myurl) {
	var newWindow;
	var props = 'scrollBars=yes,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,width=700,height=800';
	newWindow = window.open(myurl, "Add_from_Src_to_Dest", props);
}

function clearParentItems() {
	document.mailrules.parentList.value = '';
}
// Adds the list of selected items selected in the child
// window to its list. It is called by child window to do so.  
function addToParentList(sourceList) {
	document.mailrules.parentList.value = sourceList;
	//alert("Note: Map and database will not be updated until you click on the \"Submit Information\" button at the bottom of this page");
}
// Marks all the items as selected for the submit button.  
function selectList(sourceList) {
	sourceList = window.document.forms[0].parentList;
	for(var i = 0; i < sourceList.options.length; ++i) {
		if (sourceList.options[i] != null)
			sourceList.options[i].selected = true;
	}
	return true;
}
// Deletes the selected items of supplied list.
function deleteSelectedItemsFromList(sourceList) {
	var maxCnt = sourceList.options.length;
	for(var i = maxCnt - 1; i >= 0; --i) {
		if ((sourceList.options[i] != null) && (sourceList.options[i].selected == true))
			sourceList.options[i] = null;
	}
}
/* End of fortunecity functions */

/* This script validates the email */
/*
<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- V1.1.3: Sandeep V. Tamhankar (stamhankar@hotmail.com) -->
<!-- Original:  Sandeep V. Tamhankar (stamhankar@hotmail.com) -->
<!-- Changes:
*/
/* 1.1.4: Fixed a bug where upper ASCII characters (i.e. accented letters
international characters) were allowed.

1.1.3: Added the restriction to only accept addresses ending in two
letters (interpreted to be a country code) or one of the known
TLDs (com, net, org, edu, int, mil, gov, arpa), including the
new ones (biz, aero, name, coop, info, pro, museum).  One can
easily update the list (if ICANN adds even more TLDs in the
future) by updating the knownDomsPat variable near the
top of the function.  Also, I added a variable at the top
of the function that determines whether or not TLDs should be
checked at all.  This is good if you are using this function
internally (i.e. intranet site) where hostnames don't have to 
conform to W3C standards and thus internal organization e-mail
addresses don't have to either.
Changed some of the logic so that the function will work properly
with Netscape 6.

1.1.2: Fixed a bug where trailing . in e-mail address was passing
(the bug is actually in the weak regexp engine of the browser; I
simplified the regexps to make it work).

1.1.1: Removed restriction that countries must be preceded by a domain,
so abc@host.uk is now legal.  However, there's still the 
restriction that an address must end in a two or three letter
word.

1.1: Rewrote most of the function to conform more closely to RFC 822.

1.0: Original  */

function emailCheck (emailStr) {
	/* The following variable tells the rest of the function whether or not
	to verify that the address ends in a two-letter country or well-known
	TLD.  1 means check it, 0 means don't. */
	
	var checkTLD=1;
	
	/* The following is the list of known TLDs that an e-mail address must end with. */
	
	var knownDomsPat=/^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum|asia)$/;
	
	/* The following pattern is used to check if the entered e-mail address
	fits the user@domain format.  It also is used to separate the username
	from the domain. */
	
	var emailPat=/^(.+)@(.+)$/;
	
	/* The following string represents the pattern for matching all special
	characters.  We don't want to allow special characters in the address. 
	These characters include ( ) < > @ , ; : \ " . [ ] */
	
	var specialChars="\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
	
	/* The following string represents the range of characters allowed in a 
	username or domainname.  It really states which chars aren't allowed.*/
	
	var validChars="\[^\\s" + specialChars + "\]";
	
	/* The following pattern applies if the "user" is a quoted string (in
	which case, there are no rules about which characters are allowed
	and which aren't; anything goes).  E.g. "jiminy cricket"@disney.com
	is a legal e-mail address. */
	
	var quotedUser="(\"[^\"]*\")";
	
	/* The following pattern applies for domains that are IP addresses,
	rather than symbolic names.  E.g. joe@[123.124.233.4] is a legal
	e-mail address. NOTE: The square brackets are required. */
	
	var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	
	/* The following string represents an atom (basically a series of non-special characters.) */
	
	var atom=validChars + '+';
	
	/* The following string represents one word in the typical username.
	For example, in john.doe@somewhere.com, john and doe are words.
	Basically, a word is either an atom or quoted string. */
	
	var word="(" + atom + "|" + quotedUser + ")";
	
	// The following pattern describes the structure of the user
	
	var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
	
	/* The following pattern describes the structure of a normal symbolic
	domain, as opposed to ipDomainPat, shown above. */
	
	var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
	
	/* Finally, let's start trying to figure out if the supplied address is valid. */
	
	/* Begin with the coarse pattern to simply break up user@domain into
	different pieces that are easy to analyze. */
	
	var matchArray=emailStr.match(emailPat);
	
	if (matchArray==null) {
	
		/* Too many/few @'s or something; basically, this address doesn't
		even fit the general mould of a valid e-mail address. */
		
		alert("Email address seems incorrect (check @ and .'s)");
		return false;
	}
	
	var user=matchArray[1];
	var domain=matchArray[2];
	
	// Start by checking that only basic ASCII characters are in the strings (0-127).
	
	for (i=0; i<user.length; i++) {
		if (user.charCodeAt(i)>127) {
			alert("Ths username contains invalid characters.");
			return false;
		}
	}
	
	for (i=0; i<domain.length; i++) {
		if (domain.charCodeAt(i)>127) {
			alert("Ths domain name contains invalid characters.");
			return false;
		}
	}
	
	// See if "user" is valid 
	
	if (user.match(userPat)==null) {
		// user is not valid
		alert("The username doesn't seem to be valid.");
		return false;
	}
	
	/* if the e-mail address is at an IP address (as opposed to a symbolic
	host name) make sure the IP address is valid. */
	
	var IPArray=domain.match(ipDomainPat);
	if (IPArray!=null) {
		// this is an IP address
		for (var i=1;i<=4;i++) {
			if (IPArray[i]>255) {
				alert("Destination IP address is invalid!");
				return false;
			}
		}
		return true;
	}
	
	// Domain is symbolic name.  Check if it's valid.
	var atomPat=new RegExp("^" + atom + "$");
	var domArr=domain.split(".");
	var len=domArr.length;
	for (i=0;i<len;i++) {
		if (domArr[i].search(atomPat)==-1) {
			alert("The domain name does not seem to be valid.");
			return false;
		}
	}
	
	/* domain name seems valid, but now make sure that it ends in a
	known top-level domain (like com, edu, gov) or a two-letter word,
	representing country (uk, nl), and that there's a hostname preceding 
	the domain or country. */
	
	if (checkTLD && domArr[domArr.length-1].length!=2 && 
		domArr[domArr.length-1].search(knownDomsPat)==-1) {
		alert("The address must end in a well-known domain or two letter " + "country.");
		return false;
	}
	
	// Make sure there's a host name preceding the domain.
	
	if (len<2) {
		alert("This address is missing a hostname!");
		return false;
	}
	
	// If we've gotten this far, everything's valid!
	return true;
}

function setVal(elem, html) {
	elem.outerHTMLInput = html;
	var range = elem.ownerDocument.createRange();
	range.setStartBefore(elem);
	var docFrag = range.createContextualFragment(html);
	elem.parentNode.replaceChild(docFrag, elem);
}
	
function changeButtons() {
	var attr = "rel";
	var buttons = document.getElementsByTagName("input");
	var elem;
	for(var i = 0; i < buttons.length; ++i) {
		elem = buttons[i];
		if(elem.getAttribute(attr)) {
			var val = elem.getAttribute(attr);
			if(val.indexOf("ensnav") != -1 || t.toLowerCase() == "ensnav") {
				var params = getParams(val.substr(7,999));
				//alert('Changing html for element: ' + elem.getAttribute('value'));
				var newHTML = '<a href="#" onclick="' + params['target'] + 
					'.submit(); return false;" class="navitem">' + 
					params['display'] + '</a>'
				if(elem.outerHTML) {
					elem.outerHTML = newHTML;
				} else {
					setVal(elem, newHTML);
				}
			} // END: if(val.indexof(...)||...)
		} // END: if(elem.getAttribute())
	} // END: for(buttons)
	
	return false;
}

// Modeled after ibox functionality which was borrowed from thickbox
function getParams(str) {
	var Params = new Object();
	if(!str) return Params;
	var Pairs = str.split(/[;&]/);
	for(var i = 0; i < Pairs.length; ++i) {
		var KeyVal = Pairs[i].split('=');
		if(!KeyVal || KeyVal.length != 2) continue;
		var key = unescape(KeyVal[0]);
		var val = unescape(KeyVal[1]);
		val = val.replace(/\+/g,' ');
		Params[key] = val;
	}
	return Params;
}
