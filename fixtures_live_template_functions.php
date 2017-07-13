<?php
/**
 * Functions used in template files
 *
 * @package             Fixtures Live
 * @category            Cup
 * @author              Fixtures Live
 * @copyright           Copyright © 2013 Fixtures Live.
 */

 if(!function_exists('fixtures_live_output_content_wrapper')) {
 	function fixtures_live_output_content_wrapper() {
 		if ( get_option('template') === 'twentyfourteen' ) echo '<div id="primary" class="content-area"><div id="content" class="site-content" role="main">';
		elseif ( get_option('template') === 'twentythirteen' ) echo '<div id="primary" class="content-area"><div id="content" class="site-content" role="main">';
		elseif ( get_option('template') === 'twentytwelve' ) echo '<div id="primary" class="site-content"><div id="content" role="main">';
		elseif ( get_option('template') === 'twentyeleven' ) echo '<section id="primary"><div id="content" role="main">';
		else echo '<div id="container"><div id="content" role="main">';  
 	}
 }

 if (!function_exists('fixtures_live_output_content_wrapper_end')) {
	function fixtures_live_output_content_wrapper_end() {
		if ( get_option('template') === 'twentyfourteen' ) echo '</div></div>';
		elseif ( get_option('template') === 'twentythirteen' ) echo '</div></div>';
		elseif ( get_option('template') === 'twentytwelve' ) echo '</div></div>';
		elseif ( get_option('template') === 'twentyeleven' ) echo  '</div></section>';
		else echo '</div></div>'; /* twenty-ten */
	}
}

if (!function_exists('fixtures_live_get_sidebar')) {
	function fixtures_live_get_sidebar() {
		dynamic_sidebar('fixtureslivesidebar');
	}
}

if (!function_exists('fixtures_live_content')) {
	function fixtues_live_content($echo=true) {

		global $post, $fixtures_live_options;

			$fl_id = (is_numeric(@$_GET['item'])) ? @$_GET['item'] : ''; 

			$page_type = get_page_type($post); 

			if($page_type == 'division') {

				if( using_embeds() ) {
				 	do_shortcode('[fl_legacy_embed_league id="' . get_post_meta($post->ID,'fixtures_live_id',true) . '"]'); 
				} else {
					render_division_table();
				}

			} elseif($page_type == 'cup' ) {
				if( using_embeds() ) {		
				    do_shortcode('[fl_legacy_embed_league id="' . get_post_meta($post->ID,'fixtures_live_id',true) . '"]');
				} else {			
					render_cup();
				}
			} elseif($page_type == 'results') {
				if(!$fl_id) {
					the_content();
				} else {
					render_division_results($fl_id);
				}
			} elseif($page_type == 'fixtures') {
				if(!$fl_id) {
					the_content();
				} else {
					render_division_fixtures($fl_id);
				}
			} elseif($page_type == 'external-comp') {
				render_external_comp($_GET['external']);
			} elseif($page_type == 'venue') {
				render_venue($_GET['venue']);
			}

	}
}

if (!function_exists('fl_season_name')) {
	function fl_season_name($echo=true) {
		global $post;
		if($echo) {
			echo get_post_meta( $post->ID, 'season_name', true );
		}  else {
			return get_post_meta( $post->ID, 'season_name', true );
		}
	}
}


/**
 * page tabs
 **/
if (!function_exists('fixtures_live_output_data_tabs')) {
	function fixtures_live_output_data_tabs( $comp_id ) {
		if (isset($_COOKIE["current_tab"])) $current_tab = $_COOKIE["current_tab"]; else $current_tab = '#tab-description';	
		if(!$comp_id) {
			global $post;
			$comp_id = $post->ID;
		}
		?>
		<div id="tabs">
			<ul class="tabs">
				<?php do_action('fixtures_live_tabs', $current_tab); ?>
			</ul>
			<?php do_action('fixtures_live_tab_panels', $comp_id); ?>
		</div>
		<?php
	
	}
}

if (!function_exists('fixtures_live_fixtures_tab')) {
	function fixtures_live_fixtures_tab( $current_tab ) {
		?>
		<li <?php if ($current_tab=='#tab-fixtures') echo 'class="active"'; ?>><a href="#tab-fixtures">Fixtures</a></li>
		<?php
	}
}
if (!function_exists('fixtures_live_results_tab')) {
	function fixtures_live_results_tab( $current_tab ) {
		?>
		<li <?php if ($current_tab=='#tab-results') echo 'class="active"'; ?>><a href="#tab-results">Results</a></li>
		<?php 
	}
}

if(!function_exists('fixtures_live_top_scorers_tab')) {
	function fixtures_live_top_scorers_tab( $current_tab ) {
		?>
		<li <?php if ($current_tab=='#tab-scorers') echo 'class="active"'; ?>><a href="#tab-scorers">Top Scorers</a></li>
		<?php 
	}
}

/**
 * page tabs panels
 **/
if (!function_exists('fixtures_live_fixtures_panel')) {
	function fixtures_live_fixtures_panel( $fixtures_live_id ) {
		
		echo '<div class="panel" id="tab-fixtures">';
		
		 render_division_fixtures( $fixtures_live_id ); 
		echo '</div>';
	}
}

if (!function_exists('fixtures_live_results_panel')) {
	function fixtures_live_results_panel( $fixtures_live_id ) {
		echo '<div class="panel" id="tab-results">';
		 render_division_results( $fixtures_live_id );
		echo '</div>';
	}
}


if(!function_exists('fixtures_live_top_scorers_panel')) {
	function fixtures_live_top_scorers_panel( $fixtures_live_id ) {
		echo '<div class="panel" id="tab-scorers">';
		 render_competition_top_scorers( $fixtures_live_id );
		echo '</div>';
	}
}


 ?>