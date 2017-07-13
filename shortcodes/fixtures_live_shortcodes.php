<?php
function fl_embed_league_cb( $atts, $content = null ) {
	extract(shortcode_atts(array(
	      'id' => '',
     ), $atts));
	if($id) {
		return '<div id="fl"><!-- FL API data --></div>
		<p><script type="text/javascript" language="javascript">// <![CDATA[
		var fPassKey = ""; var fStartPage = "STATZONE_COMP:' . $id . ':1";
		// ]]&gt;</script><br />
		<script type="text/javascript" src="http://www.fixtureslive.com/api/api.js"></script>';
	}
}
add_shortcode('fl_embed_league', 'fl_embed_league_cb');


function fl_legacy_embed_league_cb( $atts, $content = null ) {
	extract(shortcode_atts(array(
	      'id' => '',
     ), $atts));
	if($id) {
		return '<div id="fl"><!-- FL API data --></div>
		<p><script type="text/javascript" language="javascript">// <![CDATA[
		var fPassKey = ""; var fStartPage = "STATZONE_COMP:' . $id . ':1";
		// ]]&gt;</script><br />
		<script type="text/javascript" src="http://www.fixtureslive.com/api/api.js"></script>';
	}
}
add_shortcode('fl_legacy_embed_league', 'fl_legacy_embed_league_cb');


function fl_legacy_embed_team_cb( $atts, $content = null ) {
	extract(shortcode_atts(array(
	      'id' => '',
     ), $atts));
	if($id) {
		return '<div id="fl"><!-- FL API data --></div>
		<p><script type="text/javascript" language="javascript">// <![CDATA[
		var fPassKey = ""; var fStartPage = "LEAGUEFIXTURES:' . $id . '";
		// ]]&gt;</script><br />
		<script type="text/javascript" src="http://www.fixtureslive.com/api/api.js"></script>';
	}
}
add_shortcode('fl_legacy_embed_team', 'fl_legacy_embed_team_cb');

function fl_legacy_comps_cb( $atts, $content = null ) {
	extract(shortcode_atts(array(
	      'id' => '',
     ), $atts));
	if($id) {
		return '<div id="fl"><!-- FL API data --></div>
		<p><script type="text/javascript" language="javascript">// <![CDATA[
		var fPassKey = ""; var fStartPage = "LEAGUEFIXTURES:' . $id . '";
		// ]]&gt;</script><br />
		<script type="text/javascript" src="http://www.fixtureslive.com/api/api.js"></script>';
	}
}
add_shortcode('fl_legacy_embed_competition_list', 'fl_legacy_comps_cb');


function list_league_divisions_cb( $atts ) {
	extract(shortcode_atts(array(
	      'type' => '',
     ), $atts));

	/*if(using_embeds()) {
		echo do_shortcode( '[fl_legacy_embed_competition_list id=""]');
	}*/


	global $fixtures_live_options;
		$child_pages = get_posts('post_type=league&posts_per_page=-1&orderby=menu_order&order=DESC');
		if($child_pages) {
		$tblString = '';
		$tblString .= '<table cellpadding="5" cellspacing="0" border="1" class="fl_table divison_list">';
		$tblString .=  '<tr><th>Division Name</th></tr>';
		foreach($child_pages as $page) {
			if($type == 'fixtures') {
				$tblString .=  '<tr><td><a href="' . add_query_arg('item', get_fixtures_live_id($page), get_permalink(FIXTURES_LIVE_FIXTURES_PAGE)) . '">' . $page->post_title . '</a></td></tr>';
			} elseif ($type == 'results') {
				$tblString .=  '<tr><td><a href="' . add_query_arg('item', get_fixtures_live_id($page), get_permalink(FIXTURES_LIVE_RESULTS_PAGE)) . '">' . $page->post_title . '</a></td></tr>';
			} else {
				$tblString .=  '<tr><td><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></td></tr>';
			}
		}
		$tblString .=  '</table>';
		return($tblString);
	}


}
add_shortcode('list_league_divisions', 'list_league_divisions_cb');

function list_league_cups_cb( $atts ) {
	extract(shortcode_atts(array(
	      'type' => '',
    ), $atts));

	/*if(using_embeds()) {
		echo do_shortcode( '[fl_legacy_embed_competition_list id=""]');
	}*/


	global $fixtures_live_options;
	$child_pages = get_posts('post_type=cup&posts_per_page=-1&orderby=menu_order&order=DESC');
	if($child_pages) {
		$tblString = '';
		$tblString .= '<table cellpadding="5" cellspacing="0" border="1" class="fl_table cups_list">';
		$tblString .=  '<tr><th>Competition Name</th></tr>';

		foreach($child_pages as $page) {
			if($type == 'fixtures') {
				$tblString .=  '<tr><td><a href="' . add_query_arg('item', get_fixtures_live_id($page), get_permalink(FIXTURES_LIVE_FIXTURES_PAGE)) . '">' . $page->post_title . '</a></td></tr>';
			} elseif ($type == 'results') {
				$tblString .=  '<tr><td><a href="' . add_query_arg('item', get_fixtures_live_id($page), get_permalink(FIXTURES_LIVE_RESULTS_PAGE)) . '">' . $page->post_title . '</a></td></tr>';
			} else {
				$tblString .=  '<tr><td><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></td></tr>';
			}
		}
		$tblString .=  '</table>';
		return($tblString);
	}
}
add_shortcode('list_league_cups', 'list_league_cups_cb');


/* = Archives 
================================== */
function league_archives_cb( $atts ) {
	$sHTML = "";
	extract(shortcode_atts(array(
	      'exclude' => '',
    ), $atts));


	// -- Check to see what ones to exclude from the stack.
    if($exclude) {
    	$excluded_ids = explode(',',$exclude);
    } else {
    	$excluded_ids = array();
    }

	global $fixtures_live_options;
	//print_r( $fixtures_live_options );
	if(accountCanAccessMethod('GetDivisionsByLeague')) {
		$local_leagues = array();
		$fl_webservice = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);

		$leagues = explode(',', $fixtures_live_options['league_id']);

		$sHTML .= '<table cellpadding="5" cellspacing="0" border="1" class="fl_table comp-archives">';

		foreach($leagues as $league) {

			if(!@in_array($league,$excluded_ids)) {

				if( get_transient('fixtures_live_archives_' . $league)) {
					$response = get_transient('fixtures_live_archives_' . $league);
				} else {
					$response=$fl_webservice->GetDivisionsByLeague($league);
					if($response) {
						set_transient('fixtures_live_archives_' . $league, $response,365*HOUR_IN_SECONDS);
					}
				}

				
				//print_r($fl_webservice->log);
				$xml = new SimpleXMLElement($response);
				//print_r($xml);
				foreach($xml->Division as $div) {
					if($div->Current == 'false') {
						// -- Break The Year Up
						$season_parts = explode('/', (string)$div->SeasonName);
						$season_number = ($season_parts) ? $season_parts[0] : (string)$div->SeasonName ;
						$local_leagues[(int)$season_number][] = $div;
					}
				}
			}


			

			//print_r($local_leagues);
			
		}


		krsort($local_leagues);

		if($local_leagues) {
			foreach($local_leagues as $k => $v) {
				$sHTML .= '<tr class="archive-toggler"><th>' . $k . '/' . ($k+1) . ' Season</th></tr>';
				foreach($v as $div) {
					$sHTML .= '<tr><td><a href="' . add_query_arg( array('external' => str_replace(',', '', $div->DivisionID),'external_name' => strtolower(str_replace(' ','-','Archive-' .$div->DivisionName . '-' . $div->SeasonName)) ) , get_permalink(FIXTURES_LIVE_EXTERNAL_COMP_PAGE) ) . '">' . $div->DivisionName . ' ' . $div->SeasonName . '</a></td></tr>';
				}

			}
		}

		$sHTML .= '</table>';
	}

	return $sHTML;
}
add_shortcode('league_archives', 'league_archives_cb');

function fixtureslive_league_cb( $atts ) {
	extract(shortcode_atts(array(
	      'fixtureslive_id' => '',
	      'show_league_header' => true
    ), $atts));
    if(!$fixtureslive_id) return;
    return render_division_table($fixtureslive_id, $show_league_header, false);
}
add_shortcode('fixtureslive_league', 'fixtureslive_league_cb');

function fixtureslive_cup_cb( $atts ) {
	extract(shortcode_atts(array(
	      'fixtureslive_id' => '',
    ), $atts));
    if(!$fixtureslive_id) return;
    return render_cup($fixtureslive_id, false);
}
add_shortcode('fixtureslive_cup', 'fixtureslive_cup_cb');

function fixtureslive_comp_fixtures_cb( $atts ) {
	extract(shortcode_atts(array(
	      'fixtureslive_id' => '',
    ), $atts));
    if(!$fixtureslive_id) return;
    return render_division_fixtures( $fixtureslive_id, false );
}
add_shortcode('fixtureslive_comp_fixtures', 'fixtureslive_comp_fixtures_cb');

function fixtureslive_comp_results_cb( $atts ) {
	extract(shortcode_atts(array(
	      'fixtureslive_id' => '',
    ), $atts));
    if(!$fixtureslive_id) return;
    return render_division_results( $fixtureslive_id, false );
}
add_shortcode('fixtureslive_comp_results', 'fixtureslive_comp_results_cb');


/* = League Shortcode Class Format
=================================== */
class LeagueShortcode {
	static $add_script;

	static function init() {
		add_shortcode('league_map', array(__CLASS__, 'league_map_cb'));
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}

	static function league_map_cb($atts) {
		self::$add_script = true;
		extract(shortcode_atts(array(
	      'map_height' => '',
	      'league_id' => ''
   		), $atts));

		if($league_id) { 
			if($map_height) {
   				return '<div style="height:' . $map_height . 'px" id="map"></div>';
	   		} else {
	   			return '<div id="map"></div>';		
	   		}
		}
   		

	}

	static function register_script() {
		wp_register_script('fixtures_live_map_script', str_replace('shortcodes/','', plugins_url('assets/js/fl_map_script.js', __FILE__)), array('jquery'), '1.0', true);
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;
		// -- Print Out Points
		$map_data = get_raw_fl_mapData(@$league_id);	
		$xml = simplexml_load_string($map_data);
		$points = array();
		// -- Loop Through FL Data assigning each current instance to the new leagues array
		$clubGPointData = array();
		foreach($xml as $item) {
			$clubGPointData[] = array(
				'club_id'=> (int) $item->ClubID,
				'club' => (string) $item->ClubName,
				'logo' => (string) $item->LogoLocation,
				'url' => (string) $item->Website,
				'contact_name' => (string) $item->ContactName,
				'contact_email' => (string) $item->ContactEmail,
				'zip' => (string) $item->BasedPostcode,
				'lng' => (string) $item->Longitude,
				'lat' => (string) $item->Latitude,
			);
		}
		echo '<script type="text/javascript"> var map_points = ' . json_encode($clubGPointData) . '</script><script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>';
		wp_enqueue_script( 'fixtures_live_map_script' );
	}
}

LeagueShortcode::init();



?>