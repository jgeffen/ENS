function Rectangle(corner1, corner2, regionid, color, strokeStyle, editable) {
	// set up some properties
	this.corner1	= corner1;
	this.corner2	= corner2;
	this.regionid	= regionid;
	this.color = color;
	this.border_color = this.color;
	//this.fill_opacity = 0.25;
	this.type	= "rectangle";

	// define some methods
	this.draw	= draw_rect;
	this.redraw	= redraw_rect;
	this.click	= click_rect;
	this.edit	= edit_rect;

	if(typeof strokeStyle !== "undefined" && strokeStyle !== null) {
		this.border_color = strokeStyle.color;
		this.border_thickness = strokeStyle.weight;
	}

	if(typeof editable != "undefined") {
		this.editable = editable;
	}
	else {
		this.editable = true;
	}

	// draw the rectangle
	this.draw();
}
Rectangle.prototype = new Shape();

/**
 *	Create a rectangular overlay using the points specified in point_array
 */
function draw_rect() {
	var rect = this, // used for the addEvent functions	
		pt1, pt2;

	this.leaflet = {}; // used to store drawControls
	this.points = [this.corner1, this.corner2];
	this.leaflet.feature = L.featureGroup().addTo(map);
	this.overlay = L.rectangle(this.points, {
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
		
		// Leaflet draw listeners
		map.on('draw:edited', function (e) {
			rect.edit();
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

function redraw_rect(points) {	
	if (typeof points != 'undefined') {
		//console.log('redraw_rect called with points');
                this.points = points;
		//console.log (this.points[0].lat);
		//console.log (this.points[1].lat);
                //console.log (this.points[0].lng);
                //console.log (this.points[1].lng);
        	var newBounds = L.latLngBounds(this.points[0], this.points[1]);
	} else {
//	this.points = points;
//	var newBounds = L.latLngBounds(this.points[0], this.points[1]);
		//console.log('redraw_rect called without points');
		var newBounds = L.latLngBounds(this.corner1, this.corner2);
	}
	this.overlay.setBounds(newBounds);
        this.corner1 = newBounds.getSouthEast();
        this.corner2 = newBounds.getNorthWest();
                $("#profile_form [name='lat1']").val(this.corner2.lat.toFixed(3));
                $("#profile_form [name='lat2']").val(this.corner1.lat.toFixed(3));
                $("#profile_form [name='lon1']").val(this.corner2.lng.toFixed(3));
                $("#profile_form [name='lon2']").val(this.corner1.lng.toFixed(3));
}

function click_rect() {
	if (this.editable) {
		map.addControl(this.leaflet.drawControl);
		this.leaflet.hasControl = true;
	}
}

function edit_rect() {
	var new_bounds = this.overlay.getBounds();	
	this.corner1 = new_bounds.getSouthEast();
	this.corner2 = new_bounds.getNorthWest();
		$("#profile_form [name='lat1']").val(this.corner2.lat.toFixed(3));
		$("#profile_form [name='lat2']").val(this.corner1.lat.toFixed(3));
		$("#profile_form [name='lon1']").val(this.corner2.lng.toFixed(3));
		$("#profile_form [name='lon2']").val(this.corner1.lng.toFixed(3));
}
