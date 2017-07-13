<?php
/**
 * Actions used in template files
 *
 * @package             Fixtures Live
 * @category            Cup
 * @author              Fixtures Live
 * @copyright           Copyright © 2013 Fixtures Live.
 */

/* Content Wrappers */
add_action( 'fixtures_live_before_main_content', 'fixtures_live_output_content_wrapper' , 10);
add_action( 'fixtures_live_after_main_content' , 'fixtures_live_output_content_wrapper_end', 10);

/*  Messages */
//add_action( 'fixtures_live_before_singleton', '', 10);
//add_action( 'fixtures_live_after_singleton'  '', 10);

/* Data Tabs */
add_action( 'fixtures_live_division_tabs', 'fixtures_live_output_data_tabs', 10);
add_action( 'fixtures_live_tabs', 'fixtures_live_results_tab' , 10 );
add_action( 'fixtures_live_tabs', 'fixtures_live_fixtures_tab', 20 );
add_action( 'fixtures_live_tabs', 'fixtures_live_top_scorers_tab', 20 );

add_action( 'fixtures_live_tab_panels', 'fixtures_live_fixtures_panel', 10 );
add_action( 'fixtures_live_tab_panels', 'fixtures_live_results_panel' , 20 );
add_action( 'fixtures_live_tab_panels', 'fixtures_live_top_scorers_panel' , 20 );


/* Sidebar */
add_action( 'fixtures_live_sidebar', 'fixtures_live_get_sidebar', 10);


?>