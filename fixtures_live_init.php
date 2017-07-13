<?php
add_action( 'init', 'create_fl_cpt' );
function create_fl_cpt() {
    register_post_type( 'league',
        array(
            'labels' => array(
                'name' => 'Leagues',
                'singular_name' => 'League',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New League',
                'edit' => 'Edit',
                'edit_item' => 'Edit League',
                'new_item' => 'New League',
                'view' => 'View',
                'view_item' => 'View League',
                'search_items' => 'Search League',
                'not_found' => 'No Leagues',
                'not_found_in_trash' => 'No Leagues found in Trash',
                'parent' => 'Parent League'
            ),
 
            'public' => true,
            'menu_position' => 8,
            'supports' => array( 'title', 'page-attributes' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'assets/images/fl_icon_small.png', __FILE__ ),
            'has_archive' => true,
            'show_in_menu' => 'fixtures_live_plugin'
        )
    );


    register_post_type( 'cup',
        array(
            'labels' => array(
                'name' => 'Cups',
                'singular_name' => 'Cup',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Cup',
                'edit' => 'Edit',
                'edit_item' => 'Edit Cup',
                'new_item' => 'New Cup',
                'view' => 'View',
                'view_item' => 'View Cup',
                'search_items' => 'Search Cups',
                'not_found' => 'No Cups',
                'not_found_in_trash' => 'No Cups found in Trash',
                'parent' => 'Parent Cup'
            ),
 
            'public' => true,
            'menu_position' => 9,
            'supports' => array( 'title', 'page-attributes'  ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'assets/images/fl_icon_small.png', __FILE__ ),
            'has_archive' => true,
            'show_in_menu' => 'fixtures_live_plugin'
        )
    );



}

	add_action( 'admin_init', 'fl_custom_post_type' );
	// -- Init FL Metabox
	function fl_custom_post_type() {

        $types = array('league','cup');
        foreach($types as $type) {
            add_meta_box( 'fixtures_live_id_meta_box',
            'FixturesLive Identifer',
            'fixtures_live_id_meta_box_render',
            $type, 'normal', 'high'
          );
        }

         foreach($types as $type) {
            add_meta_box( 'fixtures_live_compfinder_meta_box',
            'FixturesLive Competition Finder',
            'fixtures_live_compfinder_meta_box_render',
            $type, 'normal', 'high'
          );
        }
	    
	}

	// -- Render FL Metabox
	function fixtures_live_id_meta_box_render( $post ) {
        $FLID = esc_html( get_post_meta( $post->ID, 'fixtures_live_id', true ) );
    ?>
    <table>
        <tr>
            <td>FixturesLive ID</td>
            <td><input type="text" name="fixtures_live_id" id="FLID" value="<?php echo $FLID; ?>" /></td>
        </tr>
    </table>
    <?php } 


    function fixtures_live_compfinder_meta_box_render( $post ) {
        global $fixtures_live_options;

        if(!get_option('fl_api_is_valid')) {
            echo '<p>You do not have a valid API key or permission to the required method for this process. Please check with FixturesLive.';
            return;
        } 
                       

        ?>
            <p>This box allows you to quickly add a new league or update an existing one (for example if this is a new season). Clicking the below find competitions button will show all current competitions
                which are active for the leagues you have entered ( Via FixturesLive installation - <a href="<?php echo admin_url( '?page=fixtures_live_plugin' ); ?>">update here</a> ).</p>
            <div id="comps">
                <ul id="comp_list"></ul>
            </div>
            <button id="find_comps_init" data-comp_type="<?php echo get_current_post_type(); ?>">Find Competitions</button>
        <?php
    }

	
	// -- Save CPT Values  
	add_action( 'save_post', 'add_fl_cpt_fields', 10, 2 );   
    function add_fl_cpt_fields( $post_id, $post ) {
	    // Check post type for movie reviews
	    if ( $post->post_type == 'league' || $post->post_type == 'cup') {
	        // Store data in post meta table if present in post data
	        if ( isset( $_POST['fixtures_live_id'] ) && $_POST['fixtures_live_id'] != '' ) {
	            update_post_meta( $post_id, 'fixtures_live_id', $_POST['fixtures_live_id'] );
	        }
	        
	    }
	}



    

    add_action( 'wp_ajax_flajaxfindcomps', 'fl_ajax_findcomps_callback' );
    function fl_ajax_findcomps_callback() {
        global $fixtures_live_options;   

        if(!get_option('fl_api_is_valid')) {
            echo '<p class="error">You do not have a valid API key or permission to the required method for this process. Please check with FixturesLive.';
            return;
        } 

        $comp_type = $_POST['comp_type'];
        $comp_type_identifier = ($comp_type == 'league') ? 1 : 2;
                       
        // -- This is a bit hacky and needs refactoring
        if(accountCanAccessMethod('GetDivisionsByLeague')) {
            $league_array = array();
            $leagues = explode(',', $fixtures_live_options['league_id']);
            foreach($leagues as $league) {
                $fl_webservice = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
                $response=$fl_webservice->GetDivisionsByLeague($league);
                if(!$response) {
                   echo '<p class="error">Error:: There was no response from the server</p>';
                } else {
                    $xml = simplexml_load_string($response);
                    // -- Loop Through FL Data assigning each current instance to the new leagues array
                    foreach($xml as $item) {
                        // -- Check If Its The Current League
                        if($item->Current == 'true' && (int)$item->CompetitionType == $comp_type_identifier) {
                            $league_array[(string)$item->DivisionName] = array(
                                'name' =>  (string)$item->DivisionName,
                                'divison_id' => (string)$item->DivisionID,
                                'season' => (string)$item->SeasonName,
                                'season_id' => (string)$item->SeasonID,
                                'display_order' => (int)$item->DisplayOrder
                            );  
                        }
                    }
                 }
            }
            echo json_encode($league_array);
        }
        // -- kill it
        die(); 
    }



