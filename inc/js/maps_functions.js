addEvent(window, "load", function () {
	// All of the jquery event handlers need to be defined inside the wrapper to
	// ensure that the jquery.js library has been loaded



// #########################################
// The next group of functions provides functionality
// for the My Email Addresses tab
// #########################################

	$(".add_email").click( function(e) {
		e.preventDefault();
		tb_show("Register a new email address with ENS", "inc/ajax/email_ajax.inc.php?mode=add_email&height=520&width=500", false);
	});

	$("[id^='delete_email_']").click( function(e) {
		e.preventDefault();
		var emailid = $(this).attr("id").substring(13);

		tb_show("Really delete email address?", "inc/ajax/email_ajax.inc.php?mode=delete_email_confirm&height=250&width=400&eid="+emailid, false);
	});

    $("[id^='save_email_']").click( function(e) {
        e.preventDefault();
        var form = $(this).parents("form");
        var day_begin = form.find("[name='day_begin']").val();
        var day_end = form.find("[name='day_end']").val();
        var format = form.find("[name='format']").val();
        var eid = form.find("[name='eid']").val();
        var statusImg = $("#email_result_"+eid).html("<img src='images/loading_sm.gif' alt=\"\" />");

         $.ajax({
            type: "POST",
            url: "inc/ajax/email_ajax.inc.php",
            data: { mode: 'edit_email_save', eid: eid, day_begin: day_begin, day_end: day_end, format: format },
            headers: getCSRFHeader($("#ens_email_token").val()),
            success: function(html){
                if(html != 'success') {
                    statusImg.find("img").attr("src", "images/minus.png");
                    $("#email_result_"+eid).html(html);
                }
                else {
                    statusImg.find("img").attr("src", "images/ok.png");
                    statusImg.find("img").attr("alt", "Saved");
                    statusImg.append(" Saved Successfully");
                }
            },
            error: function(html) {
                statusImg.find("img").attr("src", "images/error.png");
                statusImg.append(" Error Saving Email");
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown){
            ensAlert("Invalid Request", "Failed to save email address. Please try again.");
        });

    });

// #########################################
// The next group of functions provides functionality
// for the Account Preferences tab.
// #########################################
	$("#account_form").submit( function(e) {
		e.preventDefault();

		// Get the values the user has entered
		var name = 		$(this).find("[name='name']").val();
		var timezone =  	$(this).find("[name='timezone']").val();
		var language =  	$(this).find("[name='language']").val();
		var affiliation =	$(this).find("[name='affiliation']").val();
		var otherinterest =	$(this).find("[name='otherinterest']").val();
		var aftershock = 	$(this).find("[name='aftershock']:checked").val();
		var updates =		$(this).find("[name='updates']:checked").val();
		var defer = 		$(this).find("[name='defer']:checked").val();

		var data = {
			name: name,
			timezone: timezone,
			language: language,
			affiliation: affiliation,
			otherinterest: otherinterest,
			aftershock: aftershock,
			updates: updates,
			defer: defer,
			mode: 'update'
		};

		$("#account_result").slideUp();
		$("#account_result").html("");

		$.ajax({
			type: "POST",
			url: "inc/ajax/account_prefs_ajax.inc.php",
			data: data,
			headers: getCSRFHeader($("#ens_profile_token").val()),
			success: function(html) {
				$("#account_result").html(html);
				$("#account_result").slideDown();
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown){
			$("#account_result").html('<h3 class=\"alert error\">Failed to update account. Please try again later.</h3>');
			$("#account_result").slideDown();
		});
	});

	$("#password_form").submit( function(e) {
		e.preventDefault();

		// Get the values the user has entered
		var password =		$(this).find("[name='password']").val();
		var confirm_pass =	$(this).find("[name='confirm']").val();

		var data = {
			password: password,
			confirm: confirm_pass,
			mode: 'change_pass'
		};

		$("#password_result").slideUp();
		$("#password_result").html("");

		$.ajax({
			type: "POST",
			url: "inc/ajax/account_prefs_ajax.inc.php",
			data: data,
			headers: getCSRFHeader($("#ens_passwd_token").val()),
			success: function(html) {
				$("#password_result").html(html);
				$("#password_result").slideDown();
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown){
			$("#password_result").html('<h3 class=\"alert error\">Failed to change password. Please try again later.</h3>');
			$("#password_result").slideDown();
		});
	});

	$("#vacation").submit( function(e) {
		e.preventDefault();

		// Get the values the user has entered
		var lveDate =	$(this).find("[name='lveDate']").val();
		var rtnDate =	$(this).find("[name='rtnDate']").val();

		var data = {
			lveDate: lveDate,
			rtnDate: rtnDate,
			mode: 'vacation_add'
		};

		$("#vacation_result").slideUp();
		$("#vacation_result").html("");

		$.ajax({
			type: "POST",
			url: "inc/ajax/account_prefs_ajax.inc.php",
			data: data,
			headers: getCSRFHeader($("#ens_vacation_token").val()),
			success: function(html) {
				if(html == 'success' ) {
					currentVacations();
				}
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown){
			$("#vacation_result").html('<h3 class=\"alert error\">Failed to update vacation. Please try again later.</h3>');
			$("#vacation_result").slideDown();
		});
	});

	$("#unsubscribe_confirm").submit( function(e) {
		e.preventDefault();

		tb_show("Confirm unsubscribe", "inc/ajax/account_prefs_ajax.inc.php?mode=unsubscribe_confirm&height=250&width=400", false);

	});


// #########################################
// The next group of functions handles general interface functionality
// such as expanding and hiding components
// #########################################
	$("#expand_profile").click( function(e) {
		e.preventDefault();
		toggle_profile_info();
	});

	$("#show_more_options").click( function(event) {
		event.preventDefault();
		$("#more_options").toggle("normal");
	});

	$("#control_add_wrapper").delegate("#profile_list", "click", function(event) {
		event.preventDefault();
    var profileId,
        profile;

    profileId = event.target.id;
    profile = profiles_array[profileId];

    if (focus_profile !== null) {
      $("#discard").click();
    }

    selectProfile(profile);
	});

	$("#map_tabs li").click( function(e) {
		var a = $(this).find('a');
		if(a.length == 0 || a.attr('href') == '#' || a.attr('href') == 'userhome_gmaps#') {
			e.preventDefault();
			change_view($(this));
		}
	});


// #########################################
// These functions handle map/profile panel functionality
// #########################################

	$('#profile_info').delegate("#edit_xml", "click", function(event) {
		event.preventDefault();
		var profile_num = focus_profile.info.profileid;
		tb_show("Edit region xml", "inc/ajax/xml_ajax.inc.php?mode=pid&pid="+profile_num+"&height=480&ens_xml_token=" + 
                $("#ens_profile_token").val(), false);
	});

        $('#profile_info').delegate("#new_xml", "click", function(event) {
                event.preventDefault();
                //var profile_num = focus_profile.info.profileid;
                tb_show("Edit region xml", "inc/ajax/xml_ajax.inc.php?mode=pid&pid=0&height=480", false);
        });

	// Reposition the circle when the user changes the place field
	$('#profile_info').delegate("#place", "change", function(event) {
		var selected = $("#place :selected");
		if(selected.val() == '') return;
		var latlon = selected.val();
		var pieces = latlon.split(",");
		var lat = pieces[0];
		var lon = pieces[1];
		$("#clat").val(lat);
		$("#clon").val(lon);
		$("#profile_name").val( jQuery.trim( selected.text()) );

		focus_profile.regions[0].center = L.latLng(lat, lon);
		focus_profile.regions[0].redraw();
		focus_profile.setExtent();
		//focus_profile.regions[0].border_marker.setLatLng( L.latLng( lat, lon+(focus_profile.regions[0].radius_degrees * focus_profile.regions[0].circle_squish) ));
	});


	$('#profile_info').delegate("#update_circle", "click", function(event) {
		event.preventDefault();
		var profile = focus_profile;
		var redraw = false;

		if($("#profile_form [name='radius']").val() != profile.regions[0].radius) {
				profile.regions[0].radius = $("#profile_form [name='radius']").val();
		profile.regions[0].radius = parseFloat(profile.regions[0].radius);
				redraw = true;
		}
		if($("#profile_form [name='clat']").val() != profile.regions[0].center.lat) {
				profile.regions[0].center = L.latLng($("#profile_form [name='clat']").val(), profile.regions[0].center.lng);
				redraw = true;
		}
		if($("#profile_form [name='clon']").val() != profile.regions[0].center.lng) {
				 profile.regions[0].center = L.latLng(profile.regions[0].center.lat, $("#profile_form [name='clon']").val());
			redraw = true;
		}

		if( redraw ) {
			profile.regions[0].redraw();
                        focus_profile.setExtent();
			//var border_pos = L.latLng( profile.regions[0].center.lat, profile.regions[0].center.lng + (profile.regions[0].radius_degrees * profile.regions[0].circle_squish) );
			//profile.regions[0].border_marker.setLatLng( border_pos );
		}
	});

	$('#profile_info').delegate("#update_rect", "click", function(event) {
		event.preventDefault();
		var lat1 = $("#lat1").val();
		var lat2 = $("#lat2").val();
		var lon1 = $("#lon1").val();
		var lon2 = $("#lon2").val();
		focus_profile.regions[0].corner1 = L.latLng( lat1, lon1);
		focus_profile.regions[0].corner2 = L.latLng( lat2, lon2);
		focus_profile.regions[0].redraw();
		focus_profile.setExtent();
	});

	// Display a confirmation dialog box if the user asks to delete the profile
	$('#profile_info').delegate("#delete_btn", "click", function(event) {
		event.preventDefault();

		if(focus_profile !== null) {

			var profile_num = $(this).attr("id").substring(15);
			var name = escape(focus_profile.info.regionname);

			if(profile_num == '') {
				profile_num = focus_profile.info.profileid;
			}

			// confirm that we want to delete the profile
			tb_show("Really delete profile?", "inc/ajax/profile_ajax.inc.php?mode=delete_profile_confirm&height=250&width=400&pid="+profile_num+"&name="+name, false);
		}
		return false;
	});

/*
	$('body').on('click', '#submit_delete_profile', function() {
		fit_all();
	});
*/

	$("#profile_name").focus( function() {
		if($(this).val() == "(Enter a region name)") {
			$(this).val('');
		}
	});

	$("#profile_name").blur( function() {
		if($(this).val() == '') {
			$(this).val('(Enter a region name)');
		}
	});

	/**
	 * Give a warning when users enter a low magnitude threshold.
	 */
	$('#profile_info').delegate('#mag_min, #mag_night', 'change', function(event) {
		var parentNode,
				minNode,
				value = $(this).val();

		if( value <= 3 ) {
			if( $('.mag_warning').length > 0 ) {
				$('.mag_warning').show();
			}
			else {
				// create warning element
				warning = document.createElement('p');
				warning.classList.add('mag_warning');

				// place warning inside mag fieldset
				parentNode = document.querySelector('#mag_min').parentNode;
				parentNode.insertAdjacentElement('afterbegin', warning);

				// set content for mag warning
				warning.innerHTML = '<strong style="display: inline">Warning:</strong> This magnitude threshold may cause ENS to send a large number of notifications. See the <a href="https://www.usgs.gov/programs/earthquake-hazards/lists-maps-and-statistics" target="_blank">earthquake facts page</a> for the increase in numbers of earthquakes with decreasing magnitude threshold.';
			}
		}
		else {
			$('.mag_warning').hide();
		}
	});

	// !IMPORTANT FUNCTION
	// When the Save Changes button is clicked, this will
	// save the current profile (using the Profile.save() function)
	// This will also convert a newly added profile to a regular profile
	$("#save").click( function(event) {
		event.preventDefault();

        if (jQuery("#profile_form").validate().form()) {
            // user adding new custom profile - set up properties and clean up leaflet leftovers
            if (new_profile && new_profile.info.canned === 'custom') {
                // convert leaflet draw layer to "native" ENS layer
                var type = new_profile.info.type,
                    layer = new_profile.leaflet.layer;
                switch(type) {
                    case 'rectangle':
                        var cnr1 = layer.getBounds().getSouthEast(),
                            cnr2 = layer.getBounds().getNorthWest();
                        new_poly = new Rectangle(cnr1, cnr2, -1, next_color, highlighted_stroke, true);
                        break;
                    case 'circle':
                        var center = layer.getLatLng(),
                            radius = layer.getRadius() / 1609.344, // radius in miles;
                        new_poly = new Circle(center, radius, -1, next_color, highlighted_stroke, true);
                        break;
                    case 'polygon':
                        var center = layer.getBounds().getCenter(),
                            points = layer.getLatLngs();
                        new_poly = new Polygon(points, center, -1, next_color, highlighted_stroke, true);
                        break;
                }
                // Clean up leaflet draw leftovers
                map.removeControl(new_profile.leaflet.drawControl);
                map.removeLayer(new_profile.leaflet.feature);
                delete new_profile.leaflet; // this prop. causes an error when saving profile via ajax

                // set regions prop. to ENS layer
                new_profile.regions = [new_poly];
                focus_profile = new_profile;
            }
            if (new_profile && new_profile.info.canned === 'custom_xml') {
                // convert leaflet draw layer to "native" ENS layer
                var type = new_profile.info.type;
                // set regions prop. to ENS layer
                //new_profile.regions = [new_poly];
                focus_profile = new_profile;
            }

            // do this in add and edit mode
            if(focus_profile != null) {
                focus_profile.save();

                new_poly = null;
                new_profile = null;

                // reenable the listeners
                focus_profile.activate();
                focus_profile.focus = false;
                focus_profile = null;

                deactivate_all();
                // fit_all(); // profiles_array is not complete. Moved to ajax success so that we try to fit all shapes after ajax is complete
                show_all();
                originalPts = null;
                document.getElementById("profile_form").reset();
            }
        }
	});

	// ! ANOTHER IMPORTANT FUNCTION
	// When the user clicks Discard Changes or Deselect, this
	// function will revert the current profile to its
	// pristine state
	$("#discard").click( function(event) {
		var layer_to_remove; // ugly hack; need to store canned / custom_xml layers because they get added back to the map after they're removed (and remove them again)
		event.preventDefault();
		$("#add_predefined_select").hide(); // hide predefined pulldown if open

		// remove any leaflet draw layers, controls that were added for new "custom" regions
		if (new_profile && new_profile.info.canned === 'custom') {
			if (new_profile.leaflet.drawControl) {
				map.removeControl(new_profile.leaflet.drawControl);
				map.removeLayer(new_profile.leaflet.feature);
			}
		}
    if (new_profile && new_profile.info.canned === 'custom_xml') {
			if (new_profile.regions[0]) {
				layer_to_remove = new_profile.regions[0].overlay;
				map.removeLayer(layer_to_remove);
			}
    }
		// remove any leftover pre-defined region layers
		if (new_profile && new_profile.info.canned === 'canned') {
			for(var i = 0, length = new_profile.regions.length; i < length; i ++) {
				layer_to_remove = new_profile.regions[i].overlay;
				map.removeLayer(layer_to_remove);
			}
		}

		if(focus_profile !== null) {
			toggle_profile_info( 'hide' );
			document.getElementById("profile_form").reset();
			$("#more_options").hide();
			$("#type").text('');

			// if this is a newly added profile delete it
			var pid = focus_profile.info.profileid;
			if(pid === -1) {
				new_profile = null;
			}
			// Otherwise revert it
			else {
				focus_profile.revert();
			}
			focus_profile.activate();
			focus_profile.focus = false;
			focus_profile = null;

			deactivate_all();
			fit_all();
			show_all();
			originalPts = null;

			if (layer_to_remove) {
				map.removeLayer(layer_to_remove);
			}

			// reset save button to enable it (disabled out by Leaflet draw when editing)
			$('#save').prop('disabled', false);
		}
	});


// #########################################
// Display the proper tooltips for the various parts
// of the interface
// #########################################
	$('#tab_my_profiles').tipTip({
		content: 'Manage my ENS profiles'
	});

	$('#tab_recent').tipTip({
		content: 'Show recent events sent to me'
	});

	$('#tab_my_email').tipTip({
		content: 'Manage my email addresses'
	});

	$('#tab_account').tipTip({
		content: 'Change my account preferences'
	});

	$('#help').tipTip({
		content: 'Get help with ENS'
	});

	$('#btn_pre').tipTip({
		content: 'Add a pre-defined region'
	});

	$('#btn_custom').tipTip({
		content: 'Create a new custom region'
	});

	$('#btn_custom_adv').tipTip({
		content: 'Create a new custom region using XML'
	});

}); // end of addEvent(window, "load")

/**
 * Switch the view based on which tab is clicked.
 * @param tab A jquery object for the tab clicked
**/
function change_view(tab) {
	var id = tab.attr('id');

	$("#map_tabs li").each(function() {
		if($(this) != tab) {
			hide_view($(this));
		}
	});

	tab.addClass('selected');

	switch(id) {
		default:
		case 'tab_my_profiles':
			$("#map").show();
			$("#map_info").show();
			if($("#recent_legend").is(":visible")) {
				$("#recent_legend").hide();
			}
			$('#discard').click();
			hide_all();
			fit_all();
			show_all();

		break;

		case 'tab_recent':
			$("#map").show();
			$("#map_info").show();
			add_recent_events();
		break;
	}
}

/**
 * Hide the div's associated with this tab
 * @param tab A jquery object for the tab clicked
**/
function hide_view(tab) {
	tab.removeClass('selected');
		var id = tab.attr('id');
		switch(id) {
			case 'tab_my_profiles':
				$("#map").hide();
				$("#map_info").hide();
			break;

			case 'tab_recent':
				clear_recent_events();
				$("#map").hide();
				$("#map_info").hide();
			break;

			case 'tab_my_email':
				$("#main_email_wrapper").hide();
			break;

			case 'tab_account':
				$("#account_prefs").hide();
			break;
        }
}

/**
 * Add the overlays for recent events sent to this user
 * If the user added the events, hid them, then added them again,
 * we save an AJAX call by just showing the ones that were already there.
**/
function add_recent_events() {
    if(typeof recent_events == 'undefined' ||
            (typeof recent_events == 'object' && recent_events.length == 0)) {
        $("#recent_legend").show();
        //map.addControl(new RecentLegendControl());
        
        $.ajax({
            type: "POST",
            url: "inc/ajax/recevents_ajax.inc.php",
            dataType: "script",
            headers: getCSRFHeader($("#ens_recent_token").val()),
            success: function(html) {
                // Nothing goes here because the returned value is javascript
                // that jQuery automatically executes
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown){
            ensAlert("Error", "There was an error displaying recent events. Please try again later!");
        });          
    }
    else {
        // Show the legend if it is hidden
        if($("#recent_legend").is(":hidden")) {
            //GEvent.trigger(btn_hide, "click");
            $("#recent_legend").show();
        }

        for(i in recent_events) {
            map.addLayer(recent_events[i]);
        }
    }
}

/**
 * The recent event icons aren't actually deleted, they are all just removed from the map.
 * This behavior is handy if the user wants to show the recent events later
**/
function clear_recent_events() {
	if(typeof recent_events != 'undefined') {
		for(i in recent_events) {
			map.removeLayer(recent_events[i]);
		}
	}
}

function currentVacations() {
	var data = {
		mode: 'vacation_current'
	};
	
	$.ajax({
		type: "POST",
		url: "inc/ajax/account_prefs_ajax.inc.php",
		data: data,
		headers: getCSRFHeader($("#ens_vacation_token").val()),
		success: function(html) {
			$('#vacation_current').html(html);
			$('.remove_vacation').on('click', removeVacation);				
		}
	})
	.fail(function (jqXHR, textStatus, errorThrown){
		$("#vacation_result").html('<h3 class=\"alert error\">Failed to retrieve vacation. Please try again later.</h3>');
	});
}

function removeVacation (e) {
	e.preventDefault();
	var link = $(this);
	var vid = $(this).attr('rel');
  
	$.ajax({
		type: "POST",
		url: "inc/ajax/account_prefs_ajax.inc.php",
		data: {mode: 'vacation_delete', id: vid},
		headers: getCSRFHeader($("#ens_vacation_token").val()),
		success: function(html) {
			if( html == 'success' ) {
				link.parent().slideUp();
			} else {
				$('#vacation_result').html(
					'<h3 class="alert error">Could not delete vacation.</h3>');
			}
		}
	})
	.fail(function (jqXHR, textStatus, errorThrown){
		$("#vacation_result").html('<h3 class=\"alert error\">Could not delete vacation.</h3>');
		$("#vacation_result").slideDown();
	});  
}
