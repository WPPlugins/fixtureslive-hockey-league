<?php
/**
 * Core WP API Actions for Fixtures Live 
 *
 * @package             Fixtures Live
 * @category            Cup
 * @author              Fixtures Live
 * @copyright           Copyright © 2013 Fixtures Live.
 */

/**
*	Cron to remove Transient Garbage	
*/
add_action( 'wp', 'prefix_setup_schedule' );
function prefix_setup_schedule() {
	if ( ! wp_next_scheduled( 'fixtureslive_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'fixtureslive_daily_event');
	}
}

add_action( 'fixtureslive_daily_event', 'delete_expired_db_transients' );
function delete_expired_db_transients() {
    global $wpdb, $_wp_using_ext_object_cache;
    if( $_wp_using_ext_object_cache )
        return;
    $time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int)$_SERVER['REQUEST_TIME'] : time() ;
    $expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < {$time};" );
    foreach( $expired as $transient ) {

        $key = str_replace('_transient_timeout_', '', $transient);
        delete_transient($key);
    }
}


?>