// ___________ Define some event handlers for the ProfileControls menu _________

$('#btn_pre').click( function(e) {
	e.preventDefault();
	hidetiptip = false;
	add_predefined();
});
  
$('#btn_custom').click( function(e) {
	e.preventDefault();
	hidetiptip = false;
	add_custom();
});

$('#btn_custom_adv').click( function(e) {
        e.preventDefault();
        hidetiptip = false;
        add_custom_adv();
});
  

// #########################################
// The next four functions handle the drawing functionality of the interface.
// As the names imply, they are used to add new profiles to the map.
// #########################################

/**
 * add_predefined handles adding a predefined profile to the map. The user first chooses a predefined profile
 * from the list. This function then uses $.post to get the javascript required to create the new shape. 
 * Note that this function does not save the profile to the database
**/
function add_predefined() {
	$("#discard").click();
	hide_all();

	var text;

	$("#add_predefined_select").css("display", "inline");
	// insert the select box for predefined profiles
	$("#add_predefined_select").html('');
	if(typeof predefined_regions_text != 'undefined') {
		text = "<option>Choose a region</option>" + predefined_regions_text;
	}
	else {
		text = '';
	}
	$("#add_predefined_select").html(text);

}

$('#control_add_wrapper').delegate("#add_predefined_select", 'change',  function() {
	var selected = $("#add_predefined_select :selected");
	var id = selected.val();
	var name = selected.text();
	if( id !== '' ) {
	$.post("inc/ajax/profile_ajax.inc.php", {mode: "get_predefined", id: id, color: next_color}, function(data) {
		if(new_poly !== null && new_poly !== 'polygon') {
			// the script and therefore the predefined profile already exist, so remove the profile and update the script
			for(var i = 0, length = new_poly.length; i < length; i ++) {
				map.removeLayer(new_poly[i].overlay);
			}
			new_poly = null;
		}

		// Jquery will automatically detect that I want to add a script and run the content
		// It won't actually create the script element
		$("body").append("<script type=\"text/javascript\">"+data+"</script>"); 
		$("add_predefined_select").text("");	

		new_profile = new Profile();
		new_profile.id = -1;		
		new_profile.info = jQuery.extend(false, {}, default_info);
		new_profile.info.profileid = new_profile.id;
		new_profile.info.regionid = new_poly[0].regionid;
		new_profile.info.regionname = name; 
		new_profile.info.canned = 'canned';			
		new_profile.info.emaillist = '1,2,3';	
		new_profile.info.type = new_poly[0].type;
		new_profile.regions = new_poly;

		focus_profile = new_profile;
		focus_profile.fill_info_box();			
		focus_profile.setExtent();
		$("#add_predefined_select").hide();
	});	
	} // end if(id!='')
});


/**
 *	add_custom handles the drawing of circles, rectangles and polygons using Leaflet draw
**/
function add_custom() {
	$("#discard").click();
	hide_all();
	
	// start out w/ save button disabled so user can't save an "empty" region (enabled by leaflet draw when region defined)
	$('#save').prop('disabled', true);
		
	new_profile = new Profile();
	new_profile.id = -1;
	new_profile.info = jQuery.extend(false, {}, default_info);
	new_profile.info.profileid = new_profile.id;
	new_profile.info.regionid = -1;
	new_profile.info.regionname = '';
	new_profile.info.canned = 'custom';
	new_profile.info.emaillist = '1,2,3';
	new_profile.leaflet = {};	// use store leaflet draw options, etc. on profile obj

	focus_profile = new_profile;		
	focus_profile.fill_info_box();
	
	// Leaflet draw options, controls
	new_profile.leaflet.feature = L.featureGroup().addTo(map);

	var drawOptions = {
		draw: {
			polyline: false,
			marker: false
		},
		edit: {
			featureGroup: new_profile.leaflet.feature
		}
	},
	drawOptionsDisabled = {
		draw: false,
		edit: {
			featureGroup: new_profile.leaflet.feature
		}
	};

	new_profile.leaflet.drawControl = new L.Control.Draw(drawOptions);	
	L.drawLocal.edit.toolbar.actions.save.text = 'Ok';
	L.drawLocal.edit.toolbar.actions.save.title = 'Accept changes.';

	map.addControl(new_profile.leaflet.drawControl);
	
	// Leaflet draw listeners
	map.on('draw:created', function (e) {
		var layer = e.layer,
			type = e.layerType;
			
		layer.addTo(new_profile.leaflet.feature);
		new_profile.leaflet.layer = layer;
		new_profile.info.type = type;

		$('#save').prop('disabled', false);
		swapControl(drawOptionsDisabled);
	});
	map.on('draw:editstart draw:drawstart draw:deletestart', function() {
		$('#save').prop('disabled', true);
	});
	map.on('draw:editstop draw:deletestop', function () { // covers both saving & canceling edits/deletes
		$('#save').prop('disabled', false);
	});
	// draw:deleted must be last so that deletestop doesn't enable save button when region is deleted
	map.on('draw:deleted', function() {
		$('#save').prop('disabled', true);
		swapControl(drawOptions);
	});

	// swap leaflet draw control between add / edit only modes (so only one shape can be drawn at once)
	function swapControl(options) {
		map.removeControl(new_profile.leaflet.drawControl);
		new_profile.leaflet.drawControl = new L.Control.Draw(options);		
		map.addControl(new_profile.leaflet.drawControl);
	}
}

/**
 *      add_custom_adv handles the drawing of polygons using XML
**/
function add_custom_adv() {
        $("#discard").click();
        hide_all();

        // start out w/ save button disabled so user can't save an "empty" region (enabled by leaflet draw when region defined)
        $('#save').prop('disabled', true);

        new_profile = new Profile();
        new_profile.id = -1;
        new_profile.info = jQuery.extend(false, {}, default_info);
        new_profile.info.profileid = new_profile.id;
        new_profile.info.regionid = -1;
        new_profile.info.regionname = '';
        new_profile.info.canned = 'custom_xml';
        new_profile.info.emaillist = '1,2,3';
	new_profile.info.type = 'custom_xml';
        new_profile.leaflet = {};       // use store leaflet draw options, etc. on profile obj

        focus_profile = new_profile;
        focus_profile.fill_info_box();

}

// #########################################
// The RecentLegendControl isn't a truly a map control
// in the sense that it doesn't control anything, but it
// is defined as a control as a way to promote consistency.
// #########################################

function RecentLegendControl() {

}

// To "subclass" the GControl, we set the prototype object to
// an instance of the GControl object
//RecentLegendControl.prototype = new GControl();

// Creates a one DIV for each of the buttons and places them in a container// DIV which is returned as our control element. We add the control to
// to the map container and return the element for the map class to
// position properly.
RecentLegendControl.prototype.initialize = function(map) {
	var container = document.createElement("div");
	container.id = "recent_legend";
	var legend = document.createElement("img");
	legend.src = 'images/legend.png';
	legend.alt = 'Recent Events legend';
	container.appendChild(legend);
	map.getContainer().appendChild(container);

	return container; 
};
