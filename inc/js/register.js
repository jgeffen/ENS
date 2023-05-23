// only used for the register pagei

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

    // Converted to Ajax, but does not seem to be used. This will not pass CSRF check.
    $.ajax({
        type: "POST",
        url: "inc/ajax/check_username_ajax.php",
        data: {username: username, ajax: true},
        success: function(data) {
            // if the ajax told us the username was valid, display a green check
            if(data == 'valid') {
                error_message(user_error, true, "username");
            }
            else if(data == 'exists') {
                error_message(user_error, false, "username");
            }
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown){
        ensAlert("Error", "There was an error checking username. Please try again later.");
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
// Also, we run a quick AJAX request to make sure that no one else is using this email
function check_email() {
    var email = $("#email").val();
    var email_error = "Email address appears to be invalid";
    var email_duplicate_error = "Email address is used by another account.";
    var email_phone_error = "Your primary email address cannot be a phone number";

    // Check that the email is in the form xxxxx@xxxx.xxxx where x is any character except white space
    if(email.match(/^[^\s]+@[^\s]+\.[^\s]+$/)) {

        var email_re =/^[0-9]{10}@/;
        isPhone( email );
        if( isPhone( email ) ) {
            error_message(email_duplicate_error, true, "email");
            error_message(email_error, true, "email");
            error_message(email_phone_error, false, "email");
            return;
        }
        // if the address isn't a phone, and the message had a phone error, clear the message
        else {
            error_message(email_duplicate_error, true, "email");
            error_message(email_phone_error, true, "email");
            error_message(email_error, true, "email");
        }
    }
    else {
        error_message(email_duplicate_error, true, "email");
        error_message(email_phone_error, true, "email");
        error_message(email_error, false, "email");
        return;
    }

    // Converted to Ajax, but does not seem to be used. This will not pass CSRF check.
    $.ajax({
        type: "POST",
        url: "inc/ajax/register.ajax.php",
        data: {check : 'email', email: email},
        success: function(result) {
            // If the email is being used, give an error
            if(result == 'invalid') {
                error_message(email_duplicate_error, false, "email");
            }
            // Otherwise remove any existing errors
            else {
                error_message(email_duplicate_error, true, "email");
            }
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown){
        ensAlert("Error", "There was an error checking your email address. Please try again later!");
    });  
}

/**
 * Return true if the email address looks like a phone. The qualifiers are:
 *   - only 10 digits before the '@'
 *   - domain is one of the known cell phone gateways
 */
function isPhone( email ) {
	var gateways = ['vtext.com', 'message.alltel.com', 'txt.att.net', 
			'mms.att.net', 'cingularme.com', 'myboostmobile.com',
			'messaging.nextel.com', 'messaging.sprintpcs.com', 
			'tmomail.net', 'vzwpix.com', 'vmobl.com'];
	var domain = email.split('@');
	domain = domain[1];
	var email_re = /^[0-9]{10}@/;	

	if( gateways.indexOf(domain) != -1 || email.match(email_re) ) {
		return true;
	}
	return false;
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

addEvent(window, "load", function() {

	$("#confirm").blur( check_password );
	$("#confirm, #password").keyup( function() {
		setTimeout(check_password, 500);
	});

	$("#username").typeWatch( user_options );
	$("#email").blur( check_email );

});
