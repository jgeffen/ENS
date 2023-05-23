function Circle(center, radius, regionid, color, strokeStyle, editable) {
	// set up some properties
	this.center = center;
	this.radius = radius; // in miles
	this.regionid = regionid;
	this.color = color;
	this.border_color = this.color;
	//this.fill_opacity = 0.25;
	this.type = "circle";

	// define some methods
	this.draw = draw_circle;
	this.redraw = redraw_circle;
	this.click = click_circle;
	this.edit = edit_circle;
	
	if(typeof strokeStyle !== "undefined" && strokeStyle !== null) {
		this.border_color = strokeStyle.color;
		this.border_thickness = strokeStyle.weight;
	}

	if(typeof editable != "undefined" ) {
		this.editable = editable;
	}
	else {
		this.editable = true;
	}

	// draw the circle
	this.draw();
}
Circle.prototype = new Shape();

/**
 *	Create a circular map overlay
*/
function draw_circle() {
	var circle = this; // used for the addEvent functions	
	this.radiusm = this.radius * 1609.344;	// convert miles to meters

	this.leaflet = {}; // used to store drawControls
	this.leaflet.feature = L.featureGroup().addTo(map);
	this.overlay = L.circle(this.center, this.radiusm, {
		color: this.border_color, 
		weight: this.border_thickness, 
		opacity: this.border_opacity, 
		fillColor: this.color,
		fillOpacity: this.fill_opacity
	}).addTo(this.leaflet.feature);

	// Leaflet draw options
	if (this.editable) {
		this.leaflet.drawControl = new L.Control.Draw({
			draw: false,
			edit: {
				remove: false,
				featureGroup: this.leaflet.feature
			}
		});
		L.drawLocal.edit.toolbar.actions.save.text = 'Ok';
		L.drawLocal.edit.toolbar.actions.save.title = 'Accept changes.';
		
		// Leaflet draw listener
		map.on('draw:edited', function (e) {
			circle.edit();
		});
		map.on('draw:editstart', function() {
			$('#save').prop('disabled', true);
		});
		map.on('draw:editstop', function () { // covers both saving/canceling edits
			$('#save').prop('disabled', false);
		});
	}
	
	return this.overlay;
}

function redraw_circle(new_params) {
	if (typeof new_params != 'undefined') {
		this.radius = new_params.radius;
		this.center = new_params.center;
	}
	
	this.radiusm = this.radius * 1609.344; // convert miles to meters

	this.overlay.setRadius(this.radiusm);
	this.overlay.setLatLng(this.center);
}

function click_circle() {
	if (this.editable) {
		map.addControl(this.leaflet.drawControl);
		this.leaflet.hasControl = true;
	}
}

function edit_circle() {
	var new_radius = this.overlay.getRadius() / 1609.344, // radius in miles;
		new_center = this.overlay.getLatLng();
	this.radius = new_radius;
	this.center = new_center;
	$("#profile_form [name='radius']").val(this.radius.toFixed(3));
	$("#profile_form [name='clat']").val(this.center.lat.toFixed(3));
	$("#profile_form [name='clon']").val(this.center.lng.toFixed(3));
}
