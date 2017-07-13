<?php

/* = Template Loader
--------------------- */
function fixtureslive_template_loader( $template ) {

	// -- Reference The Global Post Object
	global $post,$fixtures_live_options;

	if(!$post) return $template;

	// -- Determine if it is a fixtures live page
	if(get_post_meta($post->ID,'fixtures_live_id',true)) {
		// -- This is a Divison / Cup Singleton Page
		// --- Set up some body classes
		$child_class = strtolower(get_post_type($post->ID));
		if($child_class == 'league') $child_class = 'division';
		fixures_live_add_body_class( array('fixtures_live'));
		// -- Now Find the template / We will look in the theme directory first of all and roll back to the template in the plugin directory.
		$template = locate_template( array( 'single-'.$child_class.'.php', FIXTURESLIVE_TEMPLATE_URL . 'single-'.$child_class.'.php' ) );
		if ( ! $template ) $template = plugin_dir_path(__FILE__) . 'templates/single-'.$child_class.'.php';
	 /*}elseif($post->ID == FIXTURES_LIVE_FIXTURES_PAGE || $post->post_parent == FIXTURES_LIVE_FIXTURES_PAGE) {
		fixures_live_add_body_class( array('fixtures_live','single-fixtures'));
		// -- This is the Fixtures Portal Page
		$template = locate_template( array( 'single-fixtures.php', FIXTURESLIVE_TEMPLATE_URL . 'single-fixtures.php' ) );
		if ( ! $template ) $template = plugin_dir_path(__FILE__) . 'templates/single-fixtures.php'; */
	 /* } elseif($post->ID == FIXTURES_LIVE_RESULTS_PAGE || $post->post_parent == FIXTURES_LIVE_RESULTS_PAGE) {
		fixures_live_add_body_class( array('fixtures_live','single-results'));
		// -- This is the Cup Portal Page
		$template = locate_template( array( 'single-results.php', FIXTURESLIVE_TEMPLATE_URL . 'single-results.php' ) );
		if ( ! $template ) $template = plugin_dir_path(__FILE__) . 'templates/single-results.php'; */
	} elseif($post->ID == FIXTURES_LIVE_TEAM_PAGE && isset($_GET['item'])) {
		fixures_live_add_body_class( array('fixtures_live','single-team'));
		$template = locate_template( array( 'single-team.php', FIXTURESLIVE_TEMPLATE_URL . 'single-team.php' ) );
		if ( ! $template ) $template = plugin_dir_path(__FILE__) . 'templates/single-team.php';
	} elseif(isset($_GET['venue'])) {
		fixures_live_add_body_class( array('fixtures_live','single-venue'));
		$template = locate_template( array( 'single-venue.php', FIXTURESLIVE_TEMPLATE_URL . 'single-venue.php' ) );
		if ( ! $template ) $template = plugin_dir_path(__FILE__) . 'templates/single-venue.php';
	} elseif(isset($_GET['external'])) {
		fixures_live_add_body_class( array('fixtures_live','external-comp'));
		$template = locate_template( array( 'single-external.php', FIXTURESLIVE_TEMPLATE_URL . 'single-external.php' ) );
		if ( ! $template ) $template = plugin_dir_path(__FILE__) . 'templates/single-external.php';
	} elseif($post->ID == FIXTURES_LIVE_ARCHIVE_PAGE) {
		fixures_live_add_body_class( array('fixtures_live','league-archive'));
	}  elseif($post->ID == FIXTURES_LIVE_VENUES_PAGE) {
		fixures_live_add_body_class( array('fixtures_live','venue-page'));
	}
	return $template;
}
add_filter( 'template_include', 'fixtureslive_template_loader' );

/* = Template Loader
--------------------- */
function fixtureslive_return_template( $template_name ) {
	$template = locate_template( array( $template_name, FIXTURESLIVE_TEMPLATE_URL . $template_name ), false );
	if ( !$template)
		$template = plugin_basename(__FILE__) . '/templates/' . $template_name ;
	return $template;
}

/* = Template Loader
--------------------- */
function fixtureslive_get_template( $template_name, $require_once = true ) {
	load_template( fixtureslive_return_template( $template_name ), $require_once );
}

?>