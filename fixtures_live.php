<?php
/*
	Plugin Name: FixturesLive Hockey League Plugin
	Description: Connects your site to FIxturesLive and provides a set of ad-free pages, plus a map of your clubs.
	Version: 1.1.9
	Author: FixturesLive
	License: GPL3
	
	Copyright 2014 FixturesLive
		
	This plugin is distributed under the 
	GNU General Public License; Version 3 (v3), 
	and is thus certified open source software. 
	See http://www.gnu.org/licenses/gpl.html.
*/

// Some Globals
$plugin_version = 1;

$fixtures_live_options = get_option('plugin_options');
$fixtures_live_api_permissions =  get_option('FL_KEY_METHODS');

include 'fixtures_live_init.php';
include 'fixtures_live_functions.php';
include 'classes/fltransportclass.php';
include 'fixtures_live_classes.php';
include 'shortcodes/fixtures_live_shortcodes.php';
include 'fixtures_live_template_functions.php';
include 'fixtures_live_template_actions.php';
include 'fixtures_live_templates.php';
include 'fixtures_live_actions.php';
include 'widgets/init.php';

// Create an admin instance
$FL_Admin = new Fixtures_Live_Admin();
// Create Reistration Hook For Installation
register_activation_hook( __FILE__, array('Fixtures_Live_Base_Class', 'install'));

// Load All Front End Shizzle
if (!defined('FIXTURESLIVE_TEMPLATE_URL')) define('FIXTURESLIVE_TEMPLATE_URL', 'fixtureslive/');
if (!defined('FIXTURES_LIVE_TEAM_PAGE')) define('FIXTURES_LIVE_TEAM_PAGE', $fixtures_live_options['fl_team_page_id']);
if (!defined('FIXTURES_LIVE_FIXTURES_PAGE')) define('FIXTURES_LIVE_FIXTURES_PAGE', $fixtures_live_options['fl_fixtures_page_id']);
if (!defined('FIXTURES_LIVE_RESULTS_PAGE')) define('FIXTURES_LIVE_RESULTS_PAGE', $fixtures_live_options['fl_results_page_id']);
if (!defined('FIXTURES_LIVE_DIVISON_PAGE')) define('FIXTURES_LIVE_DIVISON_PAGE', $fixtures_live_options['fl_league_page_id']);
if (!defined('FIXTURES_LIVE_EXTERNAL_COMP_PAGE')) define('FIXTURES_LIVE_EXTERNAL_COMP_PAGE', $fixtures_live_options['fl_external_comp_page_id']);
if (!defined('FIXTURES_LIVE_ARCHIVE_PAGE')) define('FIXTURES_LIVE_ARCHIVE_PAGE', $fixtures_live_options['fl_archives_page_id']);
if (!defined('FIXTURES_LIVE_VENUES_PAGE')) define('FIXTURES_LIVE_VENUES_PAGE', $fixtures_live_options['fl_venue_page_id']);

// -- Fixtures Live Body Classes
global $fixures_live_body_classes;
function fixures_live_add_body_class() {
	global $fixures_live_body_classes;
	$fixures_live_body_classes = (array) $fixures_live_body_classes;
}
add_action('wp_head', 'fixures_live_add_body_class');


// -- Enque Some Scripts
function fixtures_live_frontend_scripts() {
	
	wp_enqueue_script('jquery');
 	wp_enqueue_script('jquery-ui-core'); 
 	wp_enqueue_script('jquery-ui-tabs');	
 	if(get_page_type() == 'venue') {
 		wp_enqueue_script( 'gmaps', 'http://maps.google.com/maps/api/js?sensor=false', false, null, true);
	}
	wp_enqueue_script( 'fixtures_live_ui', plugins_url('/assets/js/fixtures_live_ui.js' , __FILE__), array('jquery'), false, true);
	wp_enqueue_script( 'fl_flexslider', plugins_url('/assets/js/flexslider.js' , __FILE__), array( 'jquery' ), false, true  );

}

add_action( 'template_redirect', 'fixtures_live_frontend_scripts', 0 );

// -- Add CSS to single pages
function fixtures_live_css() {
	global $fixtures_live_options;
	$css = '';
	if($fixtures_live_options['plugin_table_colors'] || $fixtures_live_options['plugin_table_header_color']) {
		$css .= '.fl_table th {'; 
			if($fixtures_live_options['plugin_table_header_color']) { $css .= ' background-color: ' . $fixtures_live_options['plugin_table_colors'] . ';'; }
			if($fixtures_live_options['plugin_table_colors']) { $css .= 'color: ' . $fixtures_live_options['plugin_table_header_color']; }
		$css .= '}';

		$css .= '.cup_viewer .slide_indexes li, #tabs li {';
			if($fixtures_live_options['plugin_table_header_color']) { $css .= ' background-color: ' . $fixtures_live_options['plugin_table_header_color'] . ';'; }	
		$css .= '}';

		$css .= '.fl_table tr td.walkover {';
			if($fixtures_live_options['plugin_table_alt_color']) { $css .= ' background-color: ' . $fixtures_live_options['plugin_table_alt_color'] . ';'; }	
		$css .= '}';

		$css .= '.cup_viewer .slide_indexes li > a, #tabs li a {';
			if($fixtures_live_options['plugin_table_colors']) { $css .= 'color: ' . $fixtures_live_options['plugin_table_colors']; }
		$css .= '}';

		$css .= '.cup_viewer .slide_indexes li.flex-active, #tabs li.ui-state-active  {';
			if($fixtures_live_options['plugin_table_colors']) { $css .= 'background-color: ' . $fixtures_live_options['plugin_table_colors']; };
		$css .= '}';

		$css .= '.cup_viewer .slide_indexes li.flex-active a, #tabs li.ui-state-active a {';
			if($fixtures_live_options['plugin_table_header_color']) { $css .= 'color: ' . $fixtures_live_options['plugin_table_header_color']; };
		$css .= '}';
	}
	if($css) {
		echo '<style type="text/css">' . PHP_EOL . $css . '</style>' . PHP_EOL;
	}
} 
add_action('wp_head', 'fixtures_live_css');

// -- Include Frontend.CSS
function include_fixtures_live_css() {
	global $fixtures_live_options;
	if($fixtures_live_options['plugin_include_styles']) {
		wp_register_style( 'frontend_styles', plugins_url('/assets/css/frontend.css' , __FILE__) );
		wp_enqueue_style( 'frontend_styles' );
		wp_register_style( 'font_awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');
		wp_enqueue_style( 'font_awesome' );
	}
}
add_action('wp_enqueue_scripts', 'include_fixtures_live_css' );
?>