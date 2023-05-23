/**
 *
 *
 */

// Quick reference to 'active' profile
var focus_profile = null;
// Used when 'undoing' changes
var originalPts = null;
var originalProfile = null;
// Used when adding new profiles
var new_poly = null;
var new_profile = null;
// This is an 'associative array' used to store the user's profiles.
// (JS doesn't have associative arrays, so it is an object with the profiles stored as properties)
// The 'array' elements are all Profile objects
var profiles_array = {};

// This multi-dimensional array is used to store the profile colors
// The first element in the sub array is the color that shows up as the background on the profile_list
// The second element is the color used for the GPolygon
// The third element is a boolean that helps keep track of what color to use for the next new profile
var color_array =	[];
color_array[0] =	["#00ea1d", "#77fe87", false];
color_array[1] =	["#1071de", "#80bbfd", false];
color_array[2] =	["#5315e1", "#a883fd", false];
color_array[3] =	["#e700a7", "#fd77d8", false];
color_array[4] =	["#ff4900", "#ff9e77", false];
color_array[5] =	["#ffd500", "#ffe877", false];
color_array[6] =	["#c0fb00", "#dfff77", false];
color_array[7] =	["#00e45c", "#77fdad", false];
color_array[8] =	["#fa0023", "#ff778a", false];
color_array[9] =	["#ff9e88", "#ffcabd", false];
color_array[10] =	["#ffe388", "#ffeaa5", false];
color_array[11] =	["#58a692", "#9cd2c5", false];
color_array[12] =	["#6871b0", "#a7add7", false];
var next_color = color_array[0][0]; // Stores the value of the next available color

// Store the GMaps click handlers for the GPolygons here
// This is mostly used when adding new profiles
var clickHandle = [];

if(typeof default_net_list == "undefined") {default_net_list = '';}
// All new profiles will initially have this information
var default_info = {
	mag_min: 6.0,
	mag_night: 6.0,
	active: 'Y',
	depth_min: -100,
	depth_max: 800,
	networks: default_net_list
};
// Note that default_net_list is defined by the userhome_gmaps.inc.php page

var highlighted_stroke = {color: '#666666', weight: 2};

// #############################################
// Create a Profile object that stores all of the information about the profile
// #############################################
function Profile(id) {
	// ___ Set up some data members ___
	this.id = id;
	this.info = {};
	// Array to store all of the shapes associated with the profile.
	// Most profiles will only have one element in this.regions because they only have one shape
	this.regions = [];
	this.listeners = [];
	this.focus = false;

	// ___ Define some methods for the Profile object ___

	// These deal with the display of the profile
	//this.zoom = zoom;
	this.getExtent = getExtent;
	this.setExtent = setExtent;
	this.show = show_profile;
	this.hide = hide_profile;
	this.hide_others = hide_others;
	this.fill_info_box = fill_info_box;

	// Thes methods handle tasks like discarding changes and
	//   preventing other profiles from stealing focus.
	this.activate = activate;
	this.revert = revert;
	this.deactivate = deactivate_profile;
	this.deactivate_others = deactivate_others;
	this.disable_listeners = disable_listeners;
	this.convert_to_custom = convert_to_custom;
	this.save = save_profile;

	// Methods for geometric purposes
	this.get_points = get_points;
	this.update_points = update_points;
	this.set_bounding_box = set_bounding_box;
}

// ###################################################
// Methods that deal with the display of the profile
// ###################################################

/**
 *	Get (and set) the map bounds for the current profile.
 *	Get method accomodates profiles that have multiple regions
**/

function getExtent() {
	var bounds, bounds_all = new L.LatLngBounds();
	for (var i = 0; i < this.regions.length; i ++) {
		bounds = this.regions[i].overlay.getBounds();
		bounds_all.extend(bounds);
	}
	return bounds_all;
}

function setExtent() {
	var bounds = this.getExtent(),
		zoom = map.getBoundsZoom(bounds),
		center = bounds.getCenter();
	if (zoom === 0) zoom = 1;
	map.setView(center, zoom);
}

/**
 *	Show all the regions in this profile
 *
**/
function show_profile() {
	for(var shape in this.regions) {
		map.addLayer(this.regions[shape].overlay);
	}
}

/**
 *	Hide all the regions in this profile. All markers are also hidden.
 *	If there are unsaved changes to this profile, they will be discarded.
 *
**/
function hide_profile() {
	if(focus_profile == this) {
		$("#discard").click();
	}
	for(var shape in this.regions) {
		map.removeLayer(this.regions[shape].overlay);
	}
}

/**
 * Hide all other profiles; used when user clicks on a profile.
**/
function hide_others() {
	for( var i in profiles_array ) {
		if( this != profiles_array[i]) {
			profiles_array[i].hide();
			$("#hide_profile_"+i).text("Show");
		}
	}
}

/**
 * Extract the data from the Profile object and put it into the profile_info form that
 * appears beneath the map. This function will show the profile_info form if it is hidden
**/
function fill_info_box() {
	var profile = this;
	toggle_profile_info( 'show' );
	if(this.info.regionname == "") {
		$("#profile_name").val("(Enter a region name)");
	}
	else {
		$("#profile_name").val( htmlDecode(jQuery.trim(this.info.regionname)) );
	}
	$("input[name='active'][value='"+this.info.active+"']").attr("checked", "checked");
	$("#mag_min").val(this.info.mag_min);
	$("#mag_night").val(this.info.mag_night);

	// Check all of the emails necessary for this profile
	$("input[name='email[]']").each( function(i) {
		var email_num = this.value;
		if( profile.info.emaillist.indexOf(email_num) != -1 || profile.id == -1 ) {
			this.checked = true;
		}
	});

	// Select the proper networks
	$("input[name='networks[]']").each( function(i) {
		var network = this.value;

		if( profile.info.networks.indexOf(network) != -1 ) {
			this.checked = true;
		}
	});

	// Fill in the depth fields
	$("input[name='depth_min']").val(this.info.depth_min);
	$("input[name='depth_max']").val(this.info.depth_max);

  // display note since many users email asking why they're not getting alerts
  $('.magnote').remove(); // first remove any existing notes
  if (new_profile) {
  	$('.mag_label').first().before('<p class="magnote"><strong>Note:</strong> The default magnitude is 6, and many regions do not have earthquakes this large.</p>');
	}

	if( this.info.canned == 'canned') {
		$("#predefined_region").empty();
		$("#predefined_region").append(predefined_regions_text);
		$("#predefined_region_wrapper").show('normal');

		$("#predefined_region option[value='"+profile.info.regionid+"']").attr("selected", "selected");

		$("#more_options fieldset").empty();
		$("#no_more_options").show();
		$("#show_more_options").hide();

/*	2014-02-18, SAH: commented out b/c it doesn't make sense to switch profile here; user can just select a new one
											using the 'new' pre-defined region button
		// Predefined regions get a select box that allows users to switch regions.
		// We'll copy the existing box
		$("#type").text("(Predefined region: ");
		var predefinedSelect = $('<select id="predefinedSwitch"></select>').html(predefined_regions_text);
		$('option[value="' + focus_profile.info.regionid + '"]', predefinedSelect).attr("selected", "true");
		$("#type").append(predefinedSelect);
		$("#type").append(")");
*/

	$("#type").text("(Pre-defined region)");

	}
	else {
// This is a custom region.
		$("#no_more_options").hide();
		$("#show_more_options").show();
		// Create the html for the more_options div

		// Each type of profile will have different things to go in the "More Options" box
		switch(profile.info.type) {
			case 'polygon':
				var contents = '<button id="edit_xml">Edit as XML</button>';
				break;

			case 'custom_xml':
				var contents = '<button id="new_xml">Input XML</button>';
                                break;
			case 'rectangle':
				var lat1 = profile.regions[0].corner1.lat.toFixed(3);
				var lat2 = profile.regions[0].corner2.lat.toFixed(3);
				var lng1 = profile.regions[0].corner1.lng.toFixed(3);
				var lng2 = profile.regions[0].corner2.lng.toFixed(3);

				// Get the max and min values to fill in the more options box
				var minlat = Math.min(lat1, lat2);
				var maxlat = Math.max(lat1, lat2);
				var minlng = Math.min(lng1, lng2);
				var maxlng = Math.max(lng1, lng2);

				var contents = '<fieldset>';
				contents += '<strong>Latitude:</strong>';
				contents += '<label for="lat1">North</label> <input type="text" id="lat1" name="lat1" size="6" maxlength="9" value="'+maxlat+'" />';
				contents += '<label for="lat2">South</label> <input type="text" id="lat2" name="lat2" size="6" maxlength="9" value="'+minlat+'" />';
				contents += '</fieldset><fieldset>';
				contents += '<strong>Longitude:</strong>';
				contents += '<label for="lon1">West</label> <input type="text" id="lon1" name="lon1" size="6" maxlength="9" value="'+minlng+'" />';
				contents += '<label for="lon2">East</label> <input type="text" id="lon2" name="lon2" size="6" maxlength="9" value="'+maxlng+'" />';
				contents += '</fieldset>';
				contents += '<button id="update_rect" class="button_small">Update</button>';
				break;

			case 'circle':
				var radius = profile.regions[0].radius.toFixed(3);
				var places;
				var lat = profile.regions[0].center.lat.toFixed(3);
				var lon = profile.regions[0].center.lng.toFixed(3);

				// get the predefined placenames
				var contents = '<strong>Place Name</strong> <select id="place" name="place"><option value="">Pick a place</option>'+predefined_places_text+'</select>';
				contents += '<label for="radius" class="circle_label">Radius</label> <input type="text" name="radius" id="radius" size="6" maxlength="9" value="'+radius+'" />';
				contents += '<label for="lat" class="circle_label">Center Lat </label><input type="text" name="clat" id="clat" size="6" maxlength="9" value="'+lat+'" />';
				contents += '<label for="lon" class="circle_label">Center Lon </label><input type="text" name="clon" id="clon" size="6" maxlength="9" value="'+lon+'" />';
				contents += '<button id="update_circle" class="button_small">Update</button>';
				break;

			default:
				var contents = '';
				break;

		}
		$("#more_options div").empty(); // Clear the div (in case the user had edited a different profile before this one)
		$("#more_options div").append(contents);

		var custom_type = ' region';
		if (profile.info.type) {
			custom_type = ' ' + profile.info.type;
			if (profile.info.type == 'custom_xml') {
				$('#more_options').show();
				$('#show_more_options').hide();
			}
		}
		$("#type").text("(Custom" + custom_type + ")");
		$("#predefined_region_wrapper").hide();
		$('#show_more_options').on('click', function() {
			$('#more_options').show();
			$('#show_more_options').hide();
		});
	}
}

/**
 * Show or hide the profile_info box
 * @param expand Boolean value indicating if we should show or hide the box
 *        If the parameter isn't supplied, the box is toggled
 */
function toggle_profile_info( expand ) {
	var profile_info = $('#profile_info');
	profile_info.stop();
	if( typeof(expand) != 'undefined' ) {
		if( expand == 'show' ) {
			if( profile_info.is(':hidden') ) {
				profile_info.show();
			}
		}
		else if( expand == 'hide') {
			if( profile_info.is(':visible') ) {
				profile_info.hide();
			}
		}
	}
	else {
		profile_info.toggle();
	}
}

function selectProfile (profile) {
    profile.disable_listeners();

    resetValidationError("#profile_form");

    if (focus_profile === null) {
        profile.deactivate_others();

        // call the click event for every region in the profile
        for (region in profile.regions) {
            profile.regions[region].click();
        }
        profile.focus = true;
        focus_profile = profile;
        profile.fill_info_box();
        profile.hide_others();
        profile.setExtent();

        for (r in profile.regions) {
            if (originalPts === null) {
                originalPts = []; // Make an array
            }

            if (profile.regions[r].type == 'polygon') {
                var temp = jQuery.extend(false, {}, profile.regions[r].points);
            } else if (profile.regions[r].type == 'circle') {
                var ctr = jQuery.extend(false, {}, profile.regions[r].center);
                var temp = {radius: profile.regions[r].radius, center: ctr};
            } else if (profile.regions[r].type == 'rectangle') {
                var corner1 = jQuery.extend(false, {}, profile.regions[r].corner1);
                var corner2 = jQuery.extend(false, {}, profile.regions[r].corner2);
                var temp = [corner1, corner2];
            }
            originalPts.push(temp);
        }
    }
};

// ###################################################
// Methods for handling tasks like setting up the profile,
// handling clicks, discarding changes, and preventing other
// profiles from stealing focus
// ###################################################

/**
 * Enables profile interaction by setting up click and mouseover listeners
 * The click listener will copy the necessary information into the 'original' variable
 * which is used later to revert changes.
 * This function will always make the profile visible
**/
function activate() {
	var profile = this;
	profile.show();
	// add some listeners for mouse events
		originalPts = []; // Clear the array
		for( region in profile.regions ) {
			//var click = GEvent.addListener(profile.regions[region].overlay, 'click', function() {
			var click = profile.regions[region].overlay.on('click', function() {
        selectProfile(profile);
			});

			//var mouseover = GEvent.addListener(profile.regions[region].overlay, 'mouseover', function() {
			var mouseover = profile.regions[region].overlay.on('mouseover', function() {
				if(focus_profile === null) {
					//profile.regions[0].center_marker.show();
					//map.addLayer(profile.regions[0].center_marker);

					for(region in profile.regions) {
						profile.regions[region].mouseOver();
					}
				}
			});

			//var mouseout = GEvent.addListener(profile.regions[region].overlay, 'mouseout', function() {
			var mouseout = profile.regions[region].overlay.on('mouseout', function() {
				if(focus_profile === null) {
					for(region in profile.regions) {
						profile.regions[region].mouseOut();
					}
					profile.deactivate();
				}
			});

			/*
			var changePoly = GEvent.addListener(profile.regions[region].overlay, 'lineupdated', function() {
				if(profile.info.canned == 'canned' && focus_profile != null && profile.focus == true) {
					profile.convert_to_custom();
				}
			});
			*/

			profile.listeners.push(click);
			profile.listeners.push(mouseover);
		}
}

/**
 * This "discards changes" by calling the redraw() method for each region of the profile.
**/
function revert() {

	// redraw shape - only necessary for editable profiles
	if (this.regions[0].editable) {
		var revertedPts = jQuery.extend(false, {}, originalPts);

		for (region in this.regions) {
			// Leaflet draw somehow tramps on L.latLng values anywere in the profile obj for polygons,
			// so coord pairs are stored as arrays instead...convert back to L.latLng obj
			if (this.info.type === 'polygon') {
				var orig_points = this.regions[region].origPts,
					latlngs = [];

				for (var i = 0; i < orig_points.length; i ++) {
					var latlng = L.latLng(parseFloat(orig_points[i][0]), parseFloat(orig_points[i][1]));
					latlngs.push(latlng);
				}
				this.regions[region].redraw(latlngs);
			} else {
				this.regions[region].redraw( revertedPts[region] );
			}
		}

		// Reset the stroke color
		var color = this.regions[region].color;
		this.regions[region].overlay.setStyle({weight: 1, color: color});

	}

	// If the profile was converted to a custom from a predefined, it will have changed its id to 0
	if (this.info.regionid == 0) {
		this.id = originalProfile.info.regionid;
		this.info.regionid = originalProfile.info.regionid;
		this.info.canned = originalProfile.info.canned;
		originalProfile = null;
	}
	originalPts = null;
}


/**
 * Make this profile uneditable by removing Leaflet draw controls
**/
function deactivate_profile() {
	// remove any leaflet draw controls that were added for editable profiles
	// only single-region profiles are editable, so it should be safe just looking at first region in array
	var leaflet_props = this.regions[0].leaflet;
	if (leaflet_props.hasControl) {
		map.removeControl(leaflet_props.drawControl);
		leaflet_props.hasControl = false;
	}
}


/**
 * Remove the listeners from all the other profiles. This is useful when clicking on a profile.
**/
function deactivate_others() {
	for( var i in profiles_array ) {
		if( this != profiles_array[i]) {
			profiles_array[i].deactivate();
		}
	}
}


/**
 * Remove the click and mousemove event handlers for the current profile.
**/
function disable_listeners() {
	var profile = this;
	for(listener in profile.listeners) {
		//GEvent.removeListener(profile.listeners[listener]);
		profile.listeners[listener].off('click');
	}
}

/**
 * NOTE: THIS FUNCTION IS UNUSED!
 * If a profile is made up of predefined regions, this method can convert it to a custom region.
 * All regions except region[0] will be discarded to prevent users from being able to
 * have custom regions made out of multiple polygons. Discarding extra regions is useful
 * for profiles like California Cities, which is composed of three circles.
**/
function convert_to_custom() {
	var profile = this;

	originalProfile = new Object();
	originalProfile.info = jQuery.extend(false, {}, profile.info);
	originalProfile.color = profile.regions[0].color;

	profile.info.canned = 'custom';

	// When converting to a custom profile, we need to make sure there is only one shape
	// This is only necessary for a few profiles like default US and california cities
	for(var i = (profile.regions.length-1); i>0; i--) {

		//profile.regions[i].overlay.disableEditing();
		//profile.regions[i].overlay.hide();
		map.removeLayer(profile.regions[i].overlay);
		profile.regions[i] = null;

	}
	profile.info.regionid = 0;
	profile.regions = [profile.regions[0]];
	profile.fill_info_box();
}

/**
 * This function submits the profile to be stored in the database. It makes an ajax post
 * which sends all of the information about the profile to profile_ajax.inc.php
 * After posting, the profile_list will be redrawn (incase any of the profiles have been renamed)
 * Any errors returned by the profile_ajax.inc.php page should be displayed with alert()
**/
function save_profile() {
    var profile = this;
    profile.set_bounding_box();

    var id = this.id;
    var options = this.info;
    options.mode = 'save';
    options.active = $("input[name='active']:checked").val();


    if (options.active == 'undefined') {
        options.active = 'Y';
    }
    options.mag_min = $("#mag_min").val();
    options.mag_night = $("#mag_night").val();
    if ($("#profile_name").val() != "(Enter a region name)") {
        options.regionname = $("#profile_name").val();
    } else {
        options.regionname = 'Unnamed';
    }

    profile.info.emaillist = '';
    // Get all of the checked email addresses
    options['addressflags[]'] = []; // make a new array
    $("#profile_form [name='email[]']:checked").each(function () {
        options['addressflags[]'].push(this.value);
        if (profile.info.emaillist != '')
            profile.info.emaillist += "," + this.value;
        else
            profile.info.emaillist += this.value;
    });

    // Get all of the checked networks
    profile.info.networks = '';
    $("#profile_form [name='networks[]']:checked").each(function () {
        if (profile.info.networks != '')
            profile.info.networks += "," + this.value;
        else
            profile.info.networks += this.value;
    });

    //Get the min/max depth
    options.depth_min = $("#profile_form [name='depth_min']").val();
    options.depth_max = $("#profile_form [name='depth_max']").val();

    //alert (this.info.type);

    if (this.info.type == 'circle') {
        options.clat = this.regions[0].center.lat;
        options.clon = this.regions[0].center.lng;
        options.radius = this.regions[0].radius;

        // if the user has changed the "more options", we have to account for that
        if ($("#profile_form [name='radius']").length > 0 && $("#profile_form [name='radius']").val() != options.radius) {
            options.radius = $("#profile_form [name='radius']").val();
        }
        if ($("#profile_form [name='clat']").length > 0 && $("#profile_form [name='clat']").val() != options.clat) {
            options.clat = $("#profile_form [name='clat']").val();
        }
        if ($("#profile_form [name='clon']").length > 0 && $("#profile_form [name='clon']").val() != options.clat) {
            options.clon = $("#profile_form [name='clon']").val();
        }

    } else if (this.info.type == 'rectangle') {
        options.lat_min = this.regions[0].corner2.lat;
        options.lat_max = this.regions[0].corner1.lat;
        options.lon_min = this.regions[0].corner2.lng;
        options.lon_max = this.regions[0].corner1.lng;

        // If we happened to pick the wrong points, swap them
        if (options.lon_max < options.lon_min) {
            var temp = options.lon_min;
            options.lon_min = options.lon_max;
            options.lon_max = temp;
        }
        if (options.lat_max < options.lat_min) {
            var temp = options.lat_min;
            options.lat_min = options.lat_max;
            options.lat_max = temp;
        }
    } else if (this.info.type == 'polygon') {
        //this.update_points();
        var points = this.get_points();
        //this.normalize_points();
        //this.info.points_json = $.toJSON(this.info.points); // use the jquery.json.js plugin
        this.info.points_json = $.toJSON(points);
    } else if (this.info.type == 'custom_xml') {
        //alert ("trying to save a new xml-defined region");
        var points = this.get_points();
        this.info.points_json = $.toJSON(points);
        this.info.type = 'polygon';
    }

    // We've finished formatting all of the data, so let's submit it
    $.ajax({
        type: "POST",
        url: "inc/ajax/profile_ajax.inc.php",
        data: options,
        headers: getCSRFHeader($("#ens_profile_token").val()),
        dataType: "json",
        success: function(result) {
            // Returned data is the id of the profile
            // If the id was 'new', add the profile to profiles_array and to the side list

            profile.info.regionname = htmlEncode(profile.info.regionname); //escape input
            if (id == -1 && typeof result.profileid != 'undefined') {
                profile.info.regionid = result.regionid;
                profile.info.profileid = result.profileid;
                profile.id = result.profileid;
                profiles_array[result.profileid] = profile;
                profiles_array[result.profileid] = profile;
                redraw_profile_list();
                next_color.used = true;
                fit_all();
                originalPts = null;
            } else if (typeof result.profileid != 'undefined') {
                redraw_profile_list();
                // we saved correctly, so we can get rid of originalPts
                fit_all();
                originalPts = null;

            } else {
                // If there was an error, show it
                ensAlert("Invalid Data", result);
            }
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown){
        // Error returned from the server is in HTML format
        ensAlert("Invalid Data", jqXHR.responseText + "\n\nPlease try again!");
    });

    $('#more_options').hide();
    toggle_profile_info('hide');
}

// ###################################################
// Methods for geometric tasks
// ###################################################


/**
 *	Looks at all the regions for this profile and their points to one big array
**/
function get_points() {
	var points = [],
		points_latlng = this.regions[0].points;
	for (var i = 0; i < points_latlng.length; i ++) {
		points.push([points_latlng[i].lat, points_latlng[i].lng]);
	}
	return points;
}

/**
 *	Update the profile's list of its regions' points by
 *	looking at all the points in the GPolygon (stored in .overlay) of the region
**/
function update_points(  ) {
	this.info.points = new Array();
	for( region in this.regions) {
		this.regions[region].points = new Array();
		var count = this.regions[region].overlay.getVertexCount();
		for(var i=0; i<count; i++) {
			this.regions[region].points[i] = this.regions[region].overlay.getVertex(i);
	       }
	}
}

/**
 *	set_bounding_box sets this.info.lat_max, this.info.lon_min, etc to be the corners of the
 *	bounding box of the region.
**/
function set_bounding_box() {
	var bounds = this.getExtent(); // bounds of all regions in specific profile
	this.info.lat_max = bounds.getNorth();
	this.info.lat_min = bounds.getSouth();
	this.info.lon_max = bounds.getEast();
	this.info.lon_min = bounds.getWest();
}




// ###################################################
// Functions that deal with all profiles, so they aren't
// a part of the Profile() object
// ###################################################


/**
 *	Zoom map to extent of all profiles
 **/
function fit_all() {
    var zoom, center, bounds, bounds_all = new L.LatLngBounds;
    for (var i in profiles_array) {
        bounds = profiles_array[i].getExtent();
        bounds_all.extend(bounds);
    }

    if (bounds_all.isValid()) {
        // Customize map view if there is at least one profile
        zoom = map.getBoundsZoom(bounds_all);
        center = bounds_all.getCenter();
        if (zoom === 0) {
            zoom = 1;
        }
        map.setView(center, zoom);
    } else {
        // Display the default map
        map.fitWorld();
    }
}

/**
 *	Make every profile in profiles_array visible
**/
function show_all() {
	for( var i in profiles_array ) {
		profiles_array[i].show();
		$("#hide_profile_"+i).text("Hide");
	}
}

/**
 *	Hide all profiles in profiles_array
**/
function hide_all() {
	for( var i in profiles_array ) {
		profiles_array[i].hide();
		$("#hide_profile_"+i).text("Show");
	}
}

/**
 *	Disable the listeners for all profiles in profiles_array
**/
function deactivate_all() {
	for( var i in profiles_array ) {
	       profiles_array[i].deactivate();
	}
	active_profile = 'none';
	map.closePopup();

	for(var i=1; i<=5; i++) {
		if(typeof clickHandle[i] != 'undefined') {
			//GEvent.removeListener(clickHandle[i]);
			clickHandle[i].off('click');
		}
	}
}

/**
 *	For each profile in profiles_array, an entry will be made in the profile_list sidebar/menu
 *	with the correct background color. This function also sets up the next_color variable which
 *	is used when adding new profiles to the map.
**/
function redraw_profile_list() {

	var linktext ='';
	var append_text = '';

	for( profile in profiles_array) {

		// If this profile doesn't have any geography, then we're going to have problems
		if( profiles_array[profile].regions[0] === undefined ) {
			continue;
		}
		var profile_color = profiles_array[profile].regions[0].color;
		//if (profiles_array[profile].regions[0].overlay.isHidden()) {
		if (map.hasLayer(profiles_array[profile].regions[0].overlay)) {
			linktext = 'Show';
		}
		else {
			linktext = 'Hide';
		}
		var color;

		// Look up the correct background color for the entry in the profile list
		// The background color is different from the profile color
		for(var i =0; i<color_array.length; i++) {
			if(profile_color == color_array[i][0]) {
				color = color_array[i][1];
				color_array[i][2] = true;
				// set up the next color
				var k = i+1;
				if(k < color_array.length) {
					next_color = color_array[k][0];
				}
				// if we're at the end of the color_array, mark them all as unset
				else {
					for(var j=0; j<color_array.length; j++) {
						color_array[j][2] = false;
					}
					next_color = color_array[0][0];

				}
				break;
			}
		}

		append_text += "<li style=\"background: "+color+"\" id=\""+profile+"\">" + profiles_array[profile].info.regionname +" ";
		append_text += "<a id=\"hide_profile_"+profile+"\" href=\"#\">"+linktext+"</a> ";
		append_text += "</li>";
	}

	$("#profile_wrapper > ul").html(append_text);

	$('#profile_wrapper > ul > li').tipTip({
		content: 'Click the name of a profile to edit',
		defaultPosition: 'left'
	});
};

/**
 * Allow the user to switch a predefined region to a different region
 */
$("#predefinedSwitch").on("change", function() {
	var newId = $("option:selected", $(this)).val();

	// Get the geometry of this new profile
	var options = {
		'mode': 'get_predefined',
		'id': newId,
		'color': focus_profile.regions[0].color
	};
    
	// Below POST method may not be used anymore. May have been linked from removed code. CSRF not added.
	$.post("inc/ajax/profile_ajax.inc.php", options, function(data) {
		// Lets get rid of the existing region
		for(var i = 0, length = focus_profile.regions.length; i < length; i ++) {
			map.removeLayer(focus_profile.regions[i].overlay);
		}

		focus_profile.regions = null;

    // Jquery will automatically detect that I want to add a script and run the content
    // It won't actually create the script element
    $("body").append("<script type=\"text/javascript\" >"+data+"</script>");
		focus_profile.regions = new_poly;
		focus_profile.info.regionid = newId;
  });
});

$("#profile_wrapper > ul").delegate("a[id^='hide_profile_']", 'click', function(event) {
	event.preventDefault();
	var profile_num = $(this).attr('id').substring(13);
	//if(profiles_array[profile_num].regions[0].overlay.isHidden()) {
	if (map.hasLayer(profiles_array[profile_num].regions[0].overlay)) {
		profiles_array[profile_num].show();
		$(this).text('Hide');
	}
	else {
		profiles_array[profile_num].hide();
		$(this).text('Show');
	}

	return false;
});

$("#profile_wrapper > ul").delegate('li', 'click', function() {
	// deselect current and show all profiles
	var id = $(this).attr("id");
	profiles_array[id].show();

	// If we've clicked on the same profile, do nothing
	if( focus_profile != null && id == focus_profile.info.profileid ) {
		return;
	}
	$("#discard").click();
	profiles_array[id].regions[0].overlay.fire('click');

	//profiles_array[id].setExtent();

	return false;
});

$("#profile_wrapper > ul").delegate('li', 'mouseover', function() {
	var id = $(this).attr("id");
	//GEvent.trigger(profiles_array[id].regions[0].overlay, "mouseover");
	profiles_array[id].regions[0].overlay.fire('mouseover');
});

$('#profile_wrapper > ul').delegate('li', 'mouseout', function() {
	var id = $(this).attr("id");
	//GEvent.trigger(profiles_array[id].regions[0].overlay, "mouseout");
	profiles_array[id].regions[0].overlay.fire('mouseout');
});

function size(obj) {
	var size = 0, key;
	for (key in obj) {
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};
