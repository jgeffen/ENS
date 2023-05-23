// JavaScript Document


//addEvent method from John Resig
// http://ejohn.org/projects/flexible-javascript-events/
function addEvent( obj, type, fn )
{
	if (obj.addEventListener)
		obj.addEventListener( type, fn, false );
	else if (obj.attachEvent)
	{
		obj["e"+type+fn] = fn;
		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
	}
}


function get_containing_form(el) {
	var f = el;
	while (f && f.nodeName != 'FORM') { f = f.parentNode; }

	return (f);
}


var replace_ens_formlinks = function () {
	if (!document.getElementsByTagName) return;
	var inputs = document.getElementsByTagName('INPUT');

	//ie ONLY works with the className method...
	for (var i = 0; i < inputs.length; i++) {
		var el = inputs[i];

        if (el.getAttribute('class') == 'nav_left' || el.className == 'nav_left') {
			setOuterHTML(el, '<a href="' + get_containing_form(el).action + '" onclick="get_containing_form(this).submit(); return false;">' + el.value + '</a>');
           // el.outerHTML = '<a href="' + el.value + '" onclick="get_containing_form(this).submit(); return false;">' + el.value + '</a>';
        }
	}
}

/** HTML Element Prototype setter
	Author: Erik Arvidsson
	URL: http://www.webfx.nu/dhtml/mozInnerHTML/mozInnerHtml.html
*/
/*HTMLElement.prototype.outerHTML setter = function(str) {
	var r = this.ownerDocument.createRange();
	r.setStartBefore(this);
	var df = r.createContextualFragment(str);
	this.parentNode.replaceChild(df, this);
	return str;
}*/

function setOuterHTML(elmt, str) {
	if(elmt.outerHTML) {
		elmt.outerHTML = str;
	} else {
		var r = elmt.ownerDocument.createRange();
		r.setStartBefore(elmt);
		var df = r.createContextualFragment(str);
		elmt.parentNode.replaceChild(df, elmt);
	}
	return str;
}


/*
//this function requires jquery
function hide_disclaimer() {
	$(".disclaimer_message a").click( function(event) {
		event.preventDefault();
		$(".disclaimer").css("display", "block");
	});
}

//try to get the user's timezone
function timezone() {
        var d = new Date();
        var timezone = (1+d.getTimezoneOffset()/60)*(-1);
        document.getElementById("timezone").value=timezone;
}

// Use a simple ajax call to check if the username is in the database
// This also inserts a small image to the right of the username field
// this function also requires jquery
function check_username() {
	var username = $("#username").val();
	var user_error = "Username is taken";

	$.post("inc/check_username_ajax.php", {username: username, ajax: true}, function(data) {
		// if the ajax told us the username was valid, display a green check
		if(data == 'valid') {
			error_message(user_error, true, "username");
		}
		else if(data == 'exists') {
			error_message(user_error, false, "username");
		}
	});
}

// give a little message if the two passwords don't match
function check_password() {
	var pass = $("#password").val();
	var confirm_pass = $("#confirm").val();
	var pass_error = "Passwords don't match";
	if(pass != confirm_pass) {
			error_message(pass_error, false, "confirm");

	}
	else {
			error_message(pass_error, true, "confirm");
	}

}

// This function looks at the input of the email field and tries to see if it is a phone number
// If it is, print an error message
function check_email() {
	var email = $("#email").val();
	var email_error = "Email address appears to be invalid";
	if(email.match(/.+@.+\..+/)) {

		var email_re =/^[0-9]{10}@/;
		var email_phone_error = "Your primary email address cannot be a phone number";
		if( email.match(email_re) ) {
			error_message(email_phone_error, false, "email");
		}
		// if the address isn't a phone, and the message had a phone error, clear the message
		else {
			error_message(email_error, true, "email");
			error_message(email_phone_error, true, "email");
		}
	}
	else {
		error_message(email_error, false, "email");
	}

}

function error_message( message, remove, field_id ) {

	if(remove == true) {
		var source = "images/ok.png";
	}
	else {
		var source = "images/error.png";
	}

	if($("#"+field_id).val() <= 0) {
		remove = true;
		source = '';
	}

	// if the little error icon already exists, just change the source, otherwise create it
	if( $("#error_img_"+field_id).length>0) {
		$("#error_img_"+field_id).attr("src", source);
	}
	else {
		var image = document.createElement("img");
        	image.src=source;
        	image.id="error_img_"+field_id;
        	image.alt="";
        	$("#"+field_id).after( image );
        	$("#error_img_"+field_id).css("margin", "2px").css("vertical-align", "middle");
	}

	var error_heading = "<strong class='alert error'>Error creating your account</strong>";
        // if there is no error header already there, and they aren't trying to remove stuff, create the header
	if(!($("#error_list").length > 0) && remove != true) {
		$("#message").append(error_heading);
		var error_list = document.createElement("ul");
        	error_list.id="error_list";
		$("#message").append( error_list );
	}

	// add the message if it isn't already there
	if( remove != true) {
		var exists = false;
		 $("#error_list").children().each( function(i) {
                        if($(this).text() == message) {
                                exists=true;;
                        }
                });
		if(!exists)
			$("#error_list").append("<li>"+message+"</li>");
	}
	else {
		$("#error_list").children().each( function(i) {
			if($(this).text() == message) {
				$(this).remove();
			}
		});
	}

	// if there are no messages left, remove the heading and ul
	if( $("#error_list").children().size() == 0 ) {
		$("#error_list").remove();
		$("#message").text('');
	}


}

function change_preview(obj) {
	var profile_id;

	var type=obj.val();
	if(type == "world") {
		profile_id = 332089;
	}
	else {
		profile_id = $("#select_"+type+" option:selected").val();
	}
	$("#preview_img").attr("src", "inc/makemap.inc.php?mode=thumb&a=Y&pid="+$(this).val());
}
*/
//run script automatically
$(document).ready( replace_ens_formlinks);

/*
// only used for the register page
addEvent(window, "load", hide_disclaimer);
addEvent(window, "load", timezone);
var user_options = {
    callback: check_username,
    wait:750,
    highlight:true,
    captureLength:2
};
var pass_options = {
    callback: check_password,
    wait:750,
    highlight:true,
    captureLength:2
};

var email_options = {
    callback: check_email,
    wait:750,
    highlight:true,
    captureLength:2
};

$("#confirm").blur( check_password );
$("#username").typeWatch( user_options );
$("#email").blur( check_email );
*/

/** jQuery UI dialog for alert messages **/
function ensAlert(title, msgContent, options) {
    options = jQuery.extend(
        {
            width: 400,
            height: 'auto'
        },
        options || {});

    var divDialog = jQuery(document.createElement("div"));
    jQuery(divDialog).append(msgContent);

    jQuery(divDialog).dialog({
        modal: true,
        title: title,
        resizable: false,
        width: options.width,
        height: options.height,
        open: function() {
        },
        close: function() {
            jQuery(this).dialog('destroy');
        },
        buttons: [{
             text: "OK",
             click: function() {
                jQuery(this).dialog("close");
             }
        }]
    });
}

// Reset validation errors and styling on the input field.
function resetValidationError(formId) {
    jQuery(formId).validate().resetForm(); //clear any errors
    jQuery(formId + ' input.error').removeClass('error');
}

// Check for strings rejected by firewall rules
function isValidString(value) {
    // Reject - "<?xml ", "@#"
    var pattern = /\<\?xml |\@\#/g;
    return pattern.test(value) ? false : true;
}

function ENS_HTMLCODE() {}
ENS_HTMLCODE.FORBIDDEN = "403";

function htmlDecode(encodedString) {
  var textArea = document.createElement('textarea');
  textArea.innerHTML = encodedString;
  return textArea.value;
}

function htmlEncode(input) {
  const textArea = document.createElement("textarea");
  textArea.innerText = input;
  return textArea.innerHTML.split("<br>").join("\n");
}

function getCSRFHeader(value) {
	return {'X-CSRF-TOKEN': value};
}
