/**
 *	This method creates a shape object based on the type specified by the parameter.
 *	Most of the object's methods are overridden depending on which type of shape the 
 *	page has requested.
 *	@param type The type of shape to create. 'polygon', 'circle', 'rectangle'. 
 *	@param map The Map object to put the shape onto. 
*/

function Shape(color, border_thickness, border_opacity, fill_opacity) {
	// set up some default arguments
	if(typeof color == 'undefined') {
		color = '#0000ff';
	}
	if(typeof border_thickness == 'undefined') {
		border_thickness = 1;
	}
	if(typeof border_opacity == 'undefined') {
		border_opacity = 1;
	}
	if(typeof fill_opacity == 'undefined') {
		fill_opacity = 0.35;
	}

	// assign some variables for this object
	this.color = color;
	this.border_thickness = border_thickness;
	this.border_opacity = border_opacity;
	this.fill_opacity = fill_opacity;
	this.points = [];

	// associate some methods
	this.show = show_shape;
	this.hide = hide_shape;
	this.set_center = set_center;
	this.mouseOver = mouseOver_shape;
	this.mouseOut = mouseOut_shape;

}

function show_shape() {
	this.overlay.addTo(map);
}

function hide_shape() {
	map.removeLayer(this.overlay);
}

function set_center() {
	// For some reason, getBounds() and this bounds.getCenter() doesn't work with
	// my large rectangle fix, so we need to find the center manually
	// var bounds = this.overlay.getBounds();
	// var ctr = bounds.getCenter();
	// this.center = ctr;

	var latMin = 365;
	var latMax = -365;
	var lonMin = 180;
	var lonMax = -180;
	for(var x in this.points) {
		if( this.points[x].lat > latMax ) {
			latMax = this.points[x].lat;
		}
		if( this.points[x].lat < latMin) {
			latMin = this.points[x].lat;
		}
		if( this.points[x].lng > lonMax ) {
			lonMax = this.points[x].lng;
		}
		if( this.points[x].lng < lonMin ) {
			lonMin = this.points[x].lng;
		}
	}
	this.center = L.latLng( (latMax+latMin)/2, (lonMax+lonMin)/2 );
}

function mouseOver_shape() {
	this.overlay.setStyle({color: highlighted_stroke.color, weight: highlighted_stroke.weight});
}

function mouseOut_shape() {
	this.overlay.setStyle({color: this.color, weight: 1});
}