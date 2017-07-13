<?php
/**
 * Fixtures Live Widgets
 *
 * @package             Fixtures Live
 * @category            Cup
 * @author              Fixtures Live
 * @copyright           Copyright © 2013 Fixtures Live.
 */

require_once( 'widget-fixtures_live_nav.php' );
require_once( 'widgets-fixtures_live_leagueTable.php' );
require_once( 'widgets-fixtures_live_clubmap.php' );
require_once( 'widgets-fixtures_live_competitonList.php' );

function fl_register_widgets() {
	register_widget('Fixtures_Live_Navigation_Widget');
	register_widget('Fixtures_Live_Mini_League_Table_Widget');
	register_widget('Fixtures_Live_Mini_Map_Widget');
	register_widget('Fixtures_Live_Comp_List');
}
add_action('widgets_init', 'fl_register_widgets');
?>