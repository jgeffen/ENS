<?php
#include_once $_SERVER["REDIRECT_APP_WEB_DIR"] . 'inc/config.inc.php';
class PolyProfile {

	function __construct( $regionid, $database, $type, $name = 'polygon', $color="#3333ff", $xmltext = '') {
		$this->regionid = $regionid;
		$this->database = $database;
		$this->name = $name;
		$this->color = $color;
		$this->xmltext = $xmltext;
		$this->geometry = $type;

		// If the initialization didn't tell us what type this region was, we need to look it up
		if($this->geometry == 'undefined') {
			$type_query = "SELECT geo_type FROM mailregions WHERE geo_id = :geo_id LIMIT 1";
			$type_rs = $database->prepare($type_query);
			$type_rs->execute(array(
				':geo_id' => $this->regionid
			));
			while($row = $type_rs->fetch(PDO::FETCH_ASSOC)) {
				$this->geometry = $row['geo_type'];
			}	
			$type_rs = null;
		} 

		if($this->color == 'undefined') {
			$this->color = '#3333ff';
		}

		$this->poly_ordered_points = array();

		if($this->xmltext != '') {
			$this->create_ordered_from_xml();
		} else {
			switch($this->geometry) {
				case 'circle':
					$this->circle_array = array(); 
					$this->create_circle_array();
				break;
				
				case 'rectangle':
					$this->rect_array = array();
					$this->create_rect_array();
				break;
				
				case 'polygon':
				default: 
					$this->create_triangle_array();
					$this->create_polygons();
					$this->create_polygons_ordered();
				break;
			}

		}
	}
	
	function create_triangle_array() {
		$this->raw_triangles = array();		

		#$query = sprintf("SELECT * FROM mailgeography WHERE ruleid=%d AND drawlines != '12,23,31'", $this->regionid);
		$query = sprintf("SELECT * FROM mailgeography WHERE ruleid=%d", $this->regionid);
		$query_rs = $this->database->query($query);
		//print mysql_num_rows($query_rs) . ";";
		$x = 0;
		while($row = $query_rs->fetch(PDO::FETCH_ASSOC)) {
			array_push($this->raw_triangles, $row);
		}
		$query_rs = null;
	}

	// find all the triangles that border eachother and add them to the multidimensional poly_array
	function create_polygons() {
		$this->poly_array = array();
		// loop through the triangles
		foreach( $this->raw_triangles as $tri_index => $tri) {

			$borders = array();
			$this->find_bordering( $tri_index, $borders );
			if(!empty($borders)) {
				array_push($this->poly_array, $borders);
			}
		}
	}	

	function find_bordering( $tri_index, &$borders ) {
		if(!isset($this->raw_triangles[$tri_index])) return $borders;	
		$current = $this->raw_triangles[$tri_index];
		if(!empty($current)){
			array_push($borders, $current);
			unset($this->raw_triangles[$tri_index]);
			foreach( $this->raw_triangles as $index => $tri ) {
				if($this->are_bordering( $current, $tri )){
					//print "found some bordering<br />";
					$this->find_bordering( $index, $borders );
				}
			}
		}
		return $borders;	
	}

	function are_bordering( $tri1, $tri2 ) {
		$bordering = false;

	
		$tri1_lines = $tri1['drawlines'];
		$tri2_lines = $tri2['drawlines'];

		if($tri1_lines == '' || $tri2_lines == '' ) {
			return false;
		}

		$tri1_points = array( array($tri1['v1y'], $tri1['v1x']) , array($tri1['v2y'], $tri1['v2x']), array($tri1['v3y'], $tri1['v3x']));
		$tri2_points = array( array($tri2['v1y'], $tri2['v1x']) , array($tri2['v2y'], $tri2['v2x']), array($tri2['v3y'], $tri2['v3x']));
	
		$tri1_contains = array();
		$tri2_contains = array();

		foreach($tri1_points as $index => $point) {
			if($tri2_points[0] == $point || $tri2_points[1] == $point || $tri2_points[2] == $point) {
				array_push($tri1_contains, $index + 1);
				array_push($tri2_contains, array_search($point, $tri2_points) + 1);
			}
		}
		//print_r($tri1_contains);
		//print_r($tri2_contains);

		if(count($tri1_contains) == 2) {
			//there were 2 matched points, now check if they were on a drawline
			$bordering = true;
		//	print $tri1_lines;
			if(strpos($tri1_lines, $tri1_contains[0]) !== false && strpos($tri1_lines, $tri1_contains[1] !== false)) {
		//		print "tri 1 failed";
				$bordering = false;
			}
			
			if(strpos($tri2_lines, $tri2_contains[0]) !== false && strpos($tri2_lines, $tri2_contains[1] !== false)) {
		  //		  print "tri2 failed";
				$bordering = false;
			}

		}
		
/*
		// This part is much more complicated than it should be because 
		// triangles bordering on a drawline aren't considered to be bordering.
		if(strpos($tri1_lines, '12') === false && in_array( $tri1_points[0], $tri2_points) && in_array($tri1_points[1], $tri2_points)) {
			return true;
		}
		
		if(strpos($tri1_lines, '23') === false && in_array( $tri1_points[1], $tri2_points) && in_array($tri1_points[2], $tri2_points)) {
			return true;
		}

		if(strpos($tri1_lines, '31') === false && in_array( $tri1_points[0], $tri2_points) && in_array( $tri1_points[2], $tri2_points)) {
			return true;
		}
*/		

		return $bordering;	
	}

	function create_polygons_ordered() {
	//	print "Poly array:<br />\n";
		//print_r($this->poly_array);
		$this->poly_ordered_points = array();
		foreach($this->poly_array as $poly_index => $polygon) {
			// at this point, we know that all the triangles in $polygon are bordering
			$this->ordered_points_temp = array();

			//print_r($this->poly_array);
			
			$starting_pt = $this->find_starting_pt($polygon);
			$this->ordered_points_temp[] = $starting_pt;
		//	print "starting at $starting_pt[0], $starting_pt[1] <br /><br />";
		       $this->find_next_pt($starting_pt, $this->starting_tri, $poly_index);
			if(count($this->ordered_points_temp) > 1) {
				if(end($this->ordered_points_temp) != $starting_pt) {
					$this->ordered_points_temp[] =	$starting_pt; //close the poly
				}
				//print "ordered_points_temp: ";
				array_push($this->poly_ordered_points, $this->ordered_points_temp);
			}



		}
		
	}

	function create_circle_array() {
		$query = sprintf("SELECT v1y, v1x, v2y, v2x FROM mailgeography WHERE ruleid=%d", $this->regionid);	
		$query_rs = $this->database->query($query);
		$x = 0;
		while($circle = $query_rs->fetch(PDO::FETCH_ASSOC) ) {
			// create an array ['lat', 'lon', 'radius'] for each circle
			$this->circle_array[$x] = array($circle['v1y'], $circle['v1x'], $circle['v2y']);
			$x++;		
		}
		$query_rs = null;
	}

	function create_rect_array() {
		//$query = sprintf("SELECT v1y, v1x, v2y, v2x FROM mailgeography WHERE ruleid=%d", $this->regionid);	
		$query = sprintf("SELECT v1y, v1x, v2y, v2x FROM mailgeography WHERE ruleid=%d and (drawlines IS NULL or NOT (drawlines='12,23,31'))", $this->regionid);
		$query_rs = $this->database->query($query);
		$x = 0;
		while($rect = $query_rs->fetch(PDO::FETCH_ASSOC) ) {
			// create an array ['lat1', 'lon1', 'lat2', 'lon2'] for each rect
			$this->rect_array[$x] = array($rect['v1x'], $rect['v1y'], $rect['v2x'], $rect['v2y']);
			$x++;
		}
		$query_rs = null;
	}

	function find_starting_pt($polygon) {
		// this foreach loop will most often return on the first $tri
		foreach( $polygon as $index => $tri ) {
			if(strpos($tri['drawlines'], '12') === false || strpos($tri['drawlines'], '31') == false) {
				$this->starting_tri = $index;
				return array($tri['v1x'], $tri['v1y']);
			}
			if(strpos($tri['drawlines'], '23') !== false || strpos($tri['drawlines'], '12') == false) {
				$this->starting_tri = $index;
				return array($tri['v2x'], $tri['v2y']);
			}
			if(strpos($tri['drawlines'], '31') !== false || strpos($tri['drawlines'], '23') == false) {
				$this->starting_tri = $index;
				return array($tri['v3x'], $tri['v3y']);
			}
		}
		return 'error';
	}

	function find_next_pt($point, $tri_index, $poly_index) {
		$tri = $this->poly_array[$poly_index][$tri_index];
	
		$point_x = array_search($point[0], $tri);
		$point_y = array_search($point[1], $tri);
		$point_num = $this->get_point_num($point, $tri);
		unset($this->poly_array[$poly_index][$tri_index]['v'.$point_num.'x']);
		unset($this->poly_array[$poly_index][$tri_index]['v'.$point_num.'y']);

		$next_pt = array();	
		switch( $point_num ) {
			case 1:
				if( isset($tri['v2x']) && isset($tri['v2y']) && strpos($tri['drawlines'], '12') === false){			   
					$next_pt = array($tri['v2x'], $tri['v2y']);
				}
				else if ( isset($tri['v3x']) && isset($tri['v3y']) && strpos($tri['drawlines'], '31') === false) {
					$next_pt = array($tri['v3x'], $tri['v3y']);
				}

			break;

			case 2:
				if( isset($tri['v1x']) && isset($tri['v1y']) && strpos($tri['drawlines'], '12') === false){			   
					$next_pt = array($tri['v1x'], $tri['v1y']);
				}
				else if ( isset($tri['v3x']) && isset($tri['v3y']) && strpos($tri['drawlines'], '23') === false) {
					$next_pt = array($tri['v3x'], $tri['v3y']);
				}

			break;
			
			case 3:
				//print "in point 3<br />";
				if( isset($tri['v1x']) && isset($tri['v1y']) && strpos($tri['drawlines'], '31') === false){			   
					$next_pt = array($tri['v1x'], $tri['v1y']);
				}
				else if ( isset($tri['v2x']) && isset($tri['v2y']) && strpos($tri['drawlines'], '23') === false) {
					$next_pt = array($tri['v2x'], $tri['v2y']);
				}

			break;
			
			default:
				
				return;
			break;
	
		}

		if($next_pt != array()) {
			//print "added $next_pt[0], $next_pt[1] ";
			$this->ordered_points_temp[] =	$next_pt;
			//print_r($next_pt);
			//print $point_num . "<br />";
			$this->find_next_pt($next_pt, $tri_index, $poly_index);
		}
		// we've taken care of all points in this triangle, lets go to the next one

		foreach( $this->poly_array[$poly_index] as $tri_index_other => $tri_other) {
			if( $this->has_point($point, $tri_other) ) {
				//print "going to tri $tri_index_other<br />";
				$this->find_next_pt($point, $tri_index_other, $poly_index);
			}
		}
	
		/*

		$border_pt = $this->has_border_pt($point, $tri);
		if($border_pt){
		   
			if($border_pt == array('','')) {
				$this->find_next_pt($point, $tri_index, $poly_index);
			}	
			else {
				$this->find_next_pt($border_pt, $tri_index, $poly_index);	
			}
			
		}
		else {
			// we need to look for a bordering point in the other triangles
			foreach($this->poly_array[$poly_index] as $index => $tri) {
				
				if($index != $tri_index) { // make sure we're not looking at the same pt
					//$point_x = array_search($point[0], $tri);
					//$point_y = array_search($point[1], $tri);
					//$point_num = substr($point_x, 1, 1);
					$border_pt = $this->has_border_pt($point, $tri);
					if($border_pt) {
					
						$this->unset_point($point, $index, $poly_index);	
	
						$this->find_next_pt($border_pt, $index, $poly_index);
						break;	
					}	
				}

			}
		}
		*/
	}// end find_next_pt

	// returns the number of the point if found
	function has_point($point, $tri) {
		/*if(!(is_array($tri) && isset($tri['v1x']) && isset($tri['v1y']) && isset($tri['v2x'])&& isset($tri['v2y']) && isset($tri['v3x']) && isset($tri['v3y']))) {

		}
		*/

		if(!is_array($tri)) return false;
	
		if(isset($tri['v1x']) && isset($tri['v1y']) && $tri['v1x'] == $point[0] && $tri['v1y'] == $point[1]) {
			return 1;
		}
		
		if(isset($tri['v2x']) && isset($tri['v2y']) && $tri['v2x'] == $point[0] && $tri['v2y'] == $point[1]) {
			return 2;
		}

		if(isset($tri['v3x']) && isset($tri['v3y']) && $tri['v3x'] == $point[0] && $tri['v3y'] == $point[1]) {
			return 3;
		}

		return false;
	}

	function get_point_num($point, $tri) {
		$tri_x = array();
		$tri_y = array();
		if(isset($tri['v1x']) && isset($tri['v1y'])) {
			$tri_x['1'] = $tri['v1x'];
			$tri_y['1'] = $tri['v1y'];
		}		
		if(isset($tri['v2x']) && isset($tri['v2y'])) {		
			$tri_x['2'] = $tri['v2x'];
			$tri_y['2'] = $tri['v2y'];
		}   
		if(isset($tri['v3x']) && isset($tri['v3y'])) {		
			$tri_x['3'] = $tri['v3x'];
			$tri_y['3'] = $tri['v3y'];
		}   

		foreach($tri_x as $index => $x ) {
			if($point[0] == $x) {
				if($point[1] == $tri_y[$index]) {
					return $index;
				}
			}
		}
		return -1;
	}

	function unset_point($point, $tri_index, $poly_index) {
		$point_x_keys = array_keys($this->poly_array[$poly_index][$tri_index], $point[0]);

		foreach( $point_x_keys as $point_x ) {
			$point_num_x = substr($point_x, 1, 1);
			if($this->poly_array[$poly_index][$tri_index]['v'.$point_num_x.'y'] == $point[1]) {
				$point_num = $point_num_x;
				unset($this->poly_array[$poly_index][$tri_index]['v'.$point_num.'x']);
				unset($this->poly_array[$poly_index][$tri_index]['v'.$point_num.'y']);
				break;
			}
	       }
		
	}

	function has_border_pt($point, $tri) {
		if( in_array($point[0], $tri) === false || in_array($point[1], $tri) === false) {
			// if the point isn't in the tri it cant have a bordering point
			return false;
		}

		$point_x_keys = array_keys($tri, $point[0]);
		foreach( $point_x_keys as $point_x ) {
			$point_num_x = substr($point_x, 1, 1);
			if($tri['v'.$point_num_x.'y'] == $point[1]) {
				$point_num = $point_num_x;

		if($point_num == 1) {
				if(strpos($tri['drawlines'], '12') !== false && array_key_exists('v2x', $tri) && array_key_exists('v2y', $tri) ) {
					$border_pt = array($tri['v2x'], $tri['v2y']);
				}
				else if(strpos($tri['drawlines'], '31') !== false && array_key_exists('v3x', $tri) && array_key_exists('v3y', $tri) ) {
					$border_pt = array($tri['v3x'], $tri['v3y']);
				}
		}
		else if($point_num == 2) {
				if(strpos($tri['drawlines'], '12') !== false && array_key_exists('v1x', $tri) && array_key_exists('v1y', $tri) ) {
					$border_pt = array($tri['v1x'], $tri['v1y']);
				}
				else if(strpos($tri['drawlines'], '23') !== false && array_key_exists('v3x', $tri) && array_key_exists('v3y', $tri) ) {
					$border_pt = array($tri['v3x'], $tri['v3y']);
				}
		}
		else if($point_num == 3) {
		       if(strpos($tri['drawlines'], '31') !== false && array_key_exists('v1x', $tri) && array_key_exists('v1y', $tri) ) {
					$border_pt = array($tri['v1x'], $tri['v1y']);

			}
			else if(strpos($tri['drawlines'], '23') !== false && array_key_exists('v2x', $tri) && array_key_exists('v2y', $tri) ) {
					$border_pt = array($tri['v2x'], $tri['v2y']);

				}
		 }
		if($border_pt == array('','')) {
			return false;
		}
		else {
			//print "had a border point<br />";
			return $border_pt;		
		}

			} // end if($tri['v$point_numy'] == $point[1])
		}// end foreach
		return false;
	}


	function create_ordered_from_xml() {
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $this->xmltext, $vals);
		xml_parser_free($parser);

		$this->poly_ordered_points[0] = array();

		foreach($vals as $xml_tag) {
			if( strtoupper($xml_tag['tag']) == 'LAT') {
				$lat = $xml_tag['value'];
			}
			if( strtoupper($xml_tag['tag']) == 'LON') {
				$lon = $xml_tag['value'];
				array_push($this->poly_ordered_points[0], array($lon, $lat));
			}
		}
	}

	/**
	 * Print the javascript required to draw the region on the map.
	 * @param $editable Boolean indicating if the region should be editable or not. Default true
	 */
	function javascript($editable = true) {
		if( $editable ) {
			$editableString = "true";
		}
		else {
			$editableString = "false";
		}

		switch($this->geometry) {
			case 'circle':
				$output = '';
				$output .= "var " . $this->name . " = new Array(0);\n ";
				foreach( $this->circle_array as $index => $circle ) {
					$output .= "var point_array_$index = new Array(0);\n";
					$output .= "var " . $this->name ."_center = L.latLng(parseFloat(".$circle[0]."), parseFloat(".$circle[1]."));\n";

					$output .= $this->name."[$index] = new Circle(".$this->name ."_center , ".$circle[2] ." , '" . 
						htmlspecialchars($this->regionid, ENT_QUOTES, 'UTF-8') ."',  \"" . 
						htmlspecialchars($this->color, ENT_QUOTES, 'UTF-8') . "\", null, " . $editableString . " );\n";
				}

				if(isset($circle)) 
					$output .= "var center = L.latLng(".$circle[1].",".$circle[0].");\n";


				break;

			case 'rectangle':
				$output = '';
				$output .= "var " . $this->name . " = new Array(0);\n ";
				foreach( $this->rect_array as $index => $rect ) {
					$center_x = ($rect[1]+$rect[3])/2;
					$center_y = ($rect[0]+$rect[2])/2;
					$output .= "var point_array_$index = new Array(0);\n";
					$output .= "var " . $this->name ."_center = L.latLng(parseFloat(".$center_x."), parseFloat(".$center_y."));\n";

					$output .= "var corner_1_$index = L.latLng(parseFloat(".$rect[1]."), parseFloat(".$rect[0]."));\n";
					$output .= "var corner_2_$index = L.latLng(parseFloat(".$rect[3]."), parseFloat(".$rect[2]."));\n";
					$output .= $this->name."[$index] = new Rectangle(corner_1_$index , corner_2_$index, '" . 
						htmlspecialchars($this->regionid, ENT_QUOTES, 'UTF-8') ."',  \"" . 
						htmlspecialchars($this->color, ENT_QUOTES, 'UTF-8') . "\", null, " . $editableString . " );\n";
				}

				if(isset($center_x) && isset($center_y)) 
					$output .= "var center = L.latLng(".$center_x.",".$center_y.");\n";

				break;

			case 'polygon':
			default:

				$output = '';
				$output .= "var " . $this->name . " = new Array(0);\n ";
				foreach( $this->poly_ordered_points as $index => $poly_ordered ) {
					$output .= "var point_array_$index = new Array(0);\n";
					$center = $this->find_center($index);
					$output .= "var " . $this->name ."_center = L.latLng(parseFloat(".$center[1]."), parseFloat(".$center[0]."));\n";
					foreach($poly_ordered as $point) {
						$output .= sprintf( "point_array_%s.push( L.latLng(parseFloat(%3.4f), parseFloat(%3.4f)) );\n", $index,  $point[1], $point[0]);
					}
					$output .= $this->name."[$index] = new Polygon(point_array_$index, " . $this->name . "_center , '" . 
						htmlspecialchars($this->regionid, ENT_QUOTES, 'UTF-8') ."',  \"" . 
						htmlspecialchars($this->color, ENT_QUOTES, 'UTF-8') . "\", null, " . $editableString . " );\n";
				}

				if(isset($center)) {
					$output .= "var center = L.latLng(".$center[1].",".$center[0].");\n";
				}
	
		} // end switch
		return $output;
		
	}

	function geo() {
		foreach( $this->poly_ordered_points as $index => $poly_ordered ) {
			$output = 'INSERT INTO mailgeography_spatial (ruleid, geometry) VALUES ( ' . $this->regionid.', GeomFromText("POLYGON((';
			foreach( $poly_ordered as $point ){
				$output .= $point[0] . ' ' . $point[1] . ", ";
			}
			$output = substr($output, 0, strlen($output) - 2);
			$output .= '))"));';
		}
		return $output;
	}

	// returns '(x,y)'
	function find_center($poly_index) {
		$points = $this->poly_ordered_points[$poly_index];
		$x = array();
		$y = array();
		foreach($points as $point) {
			array_push($x, $point[0]);
			array_push($y, $point[1]);
		}

		if(!empty($x) && !empty($y)) {
			$center_x = (max($x)+min($x)) / 2;
			$center_y = (max($y)+min($y)) / 2;
		}
		else {
			$center_x = 0;
			$center_y = 0;
		}

		return array($center_x, $center_y);		
	}

}// end class

function get_subregion($id, $database, &$output = '') {
	$sub_query = "
		SELECT geo_id, mailruleid, placename		     
		FROM mailregions 
		WHERE 
			geo_flag='canned' 
		ORDER BY placename ASC, mailruleid";

	#$sub_rs = mysql_query($sub_query, $database);
	$sub_rs = $database->prepare($sub_query);
	$sub_rs->execute();

	$profile_array = array();
	$sorted_array = array();

	#while($row = mysql_fetch_assoc($sub_rs)) {
	while ($row = $sub_rs->fetch(PDO::FETCH_ASSOC)) {
		array_push($profile_array, $row);
	}

	#mysql_free_result($sub_rs);
	$sub_rs = null;

	$output = '';
	$sorted_array = get_children(0, 0, $profile_array, $output);

	return $output;
}

// Create the $output html for the predefined regions box by recursively finding children of $parent_id
function get_children($parent_id, $level, &$profile_array, &$output) {

	for($i=0; $i<count($profile_array); $i++) {
		//print "\t" . count($profile_array) . "\n";
		if(isset($profile_array[$i]) && $profile_array[$i] != 0 && $profile_array[$i]['mailruleid'] == $parent_id) {
			$id = $profile_array[$i]['geo_id'];
			$name = $profile_array[$i]['placename'];
			$output .= sprintf("<option value=\"%s\">%s</option>", $id, str_repeat("&#160;", ($level)*3). $name);
			unset($profile_array[$i]);
			$profile_array[$i] = 0;
			get_children($id, $level+1,  $profile_array, $output);	   
		}
	}
}

?>
