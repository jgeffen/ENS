function Polygon(point_array, center, regionid, color, strokeStyle, editable) {
	// set up some properties
	this.points =  point_array;
	this.center = center;
	this.regionid = regionid;
	this.color = color;
	this.border_color = this.color;
  //this.fill_opacity = 0.25;
	this.type = "polygon";

	// define some methods
	this.draw = draw_poly;
	this.redraw = redraw_poly;
	this.click = click_poly;
	this.edit	= edit_poly;
//	this.makexml    = make_xml;

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

	// draw the polygon
	this.draw();
}
Polygon.prototype = new Shape();

/**
 *	Create a polygon overlay using the points specified in point_array
*/
function draw_poly() {
	var poly = this; // used for the addEvent functions	
	this.leaflet = {}; // used to store drawControls
	this.leaflet.feature = L.featureGroup().addTo(map);
	this.overlay = L.polygon(this.points, {
		color: this.border_color, 
		weight: this.border_thickness, 
		opacity: this.border_opacity, 
		fillColor: this.color, 
		fillOpacity: this.fill_opacity
	}).addTo(this.leaflet.feature);
	
	// Leaflet draw options
	if (this.editable) {
		// Leaflet draw somehow tramps on L.latLng values anywere in the profile obj for polygons, 
		// so store coord pairs as arrays instead.
		this.origPts = [];
		for (var i = 0; i < this.points.length; i ++) {
			var lat_lng = [this.points[i].lat, this.points[i].lng];
			this.origPts.push(lat_lng);
		}
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
			poly.edit();
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

function redraw_poly(points) {
	if (typeof points != 'undefined') {
		this.points = points;
	}
	
	var newlatlngs = [];
	for (var i = 0; i < this.points.length; i ++) {
		if (typeof this.points[i] != 'undefined') {
			newlatlngs[i] = this.points[i];
	  }
	}
	this.overlay.setLatLngs(newlatlngs);
}

function click_poly() {
	if (this.editable) {
		map.addControl(this.leaflet.drawControl);
		this.leaflet.hasControl = true;
	}
}

function edit_poly() {
	var new_points = this.overlay.getLatLngs(),
		new_center = this.overlay.getBounds().getCenter();
	this.points = new_points;
	this.center = new_center;
}