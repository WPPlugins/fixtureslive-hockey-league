<?php

/**
 * Base class
 */
if (!class_exists('Fixtures_Live_Base_Class')) {
	class Fixtures_Live_Base_Class {

		public $plugin_url;
		public $plugin_path;
		public $settings_page;
		public static $version = 1;
		private static $plugin_instance_version;
		protected $options;
		protected $stage;
		protected $leagues_to_delete;

		protected $data_types = array(
			0 => 'Fixtures Live Embed Code (Simple)',
			1 => 'Data Feeds (Advanced)'
		);

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_url = plugin_dir_url(__FILE__);
			$this->plugin_path = plugin_dir_path(__FILE__);
			$this->domain = 'fixtures_live_plugin';
			$this->options = get_option('plugin_options');
			$this->FL_KEY_METHODS = get_option('FL_KEY_METHODS');
			$this->stage = (get_option('fl_installation_stage')) ? get_option('fl_installation_stage') : 1;	
			self::$plugin_instance_version = get_option('fl_plugin_version');
			self::checkIfUpdatesAreRequired();
		}

		/***
		 * Install Function
		 * -------------------------
		 * Checks if pages exist for parents and if not will create them
		 **/
		public function install() {
		
			$opts = get_option('plugin_options');	
			// -- Check to see if options are there if already been installed
			if(!$opts['fl_league_page_id']) {
				$post_args = array(
					'post_title' => 'Leagues',
					'post_content' => '[list_league_divisions]',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_league_page_id'] = $post;
			} 

			if(!$opts['fl_fixtures_page_id']) {
				$post_args = array(
					'post_title' => 'Fixtures',
					'post_content' => '',
					'post_status' => 'publish',
					'post_type' => 'page'
				);	
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_fixtures_page_id'] = $post;
			}

			if(!$opts['fl_cups_page_id']) {
				$post_args = array(
					'post_title' => 'Cups',
					'post_content' => '[list_league_cups]',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_cups_page_id'] = $post;
			} 

			if(!$opts['fl_external_comp_page_id']) {
				$post_args = array(
					'post_title' => 'External Competition',
					'post_content' => '',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_external_comp_page_id'] = $post;
			} 

			if(!$opts['fl_team_page_id']) {
				$post_args = array(
					'post_title' => 'Teams',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_team_page_id'] = $post;
			}

			if(!$opts['fl_results_page_id']) {
				$post_args = array(
					'post_title' => 'Results',
					'post_content' => '',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_results_page_id'] = $post;	
			} 

			if(!$opts['fl_archives_page_id']) {
				$post_args = array(
					'post_title' => 'Archives',
					'post_content' => '[league_archives]',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_archives_page_id'] = $post;
			} 

			if(!$opts['fl_venue_page_id']) {
				$post_args = array(
					'post_title' => 'Venues',
					'post_content' => '',
					'post_status' => 'publish',
					'post_type' => 'page'
				);
				$post = wp_insert_post( $post_args, $wp_error );
				$opts['fl_venue_page_id'] = $post;
			} 


			if(!$opts['has_installed']) {
				$opts['plugin_include_styles'] = 1;
			}

			update_option('plugin_options', $opts);
			
			// -- If Plugin Is Already Installed:
			if(get_option('fl_plugin_version')) {
				self::checkIfUpdatesAreRequired();
			} else {
				// -- Set it to the core version
				update_option('fl_plugin_version', self::$version);
			}
			
		}
	
		/***
		 * Check if update is required
		 * -------------------------
		 * Checks if pages exist for parents and if not will create them
		 **/
		private static function checkIfUpdatesAreRequired() {
			if(self::$plugin_instance_version != self::$version) {
				self::updatePlugin();
			}
		}

		/***
		 * Update Functions
		 * -------------------------
		 * Checks if pages exist for parents and if not will create them
		 **/
		private static function updatePlugin() {
			/*self::$plugin_instance_version =  (int)get_option('fl_plugin_version');
			if(self::$plugin_instance_version < ) {
				update_option('fl_plugin_version', '1.1');
			}*/
		}

	}
}

/**
 * Admin class
 */
if (!class_exists('Fixtures_Live_Admin')) {
class Fixtures_Live_Admin extends Fixtures_Live_Base_Class
{
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		add_action('init', array($this, 'load_all_hooks'));
	}

	/**
	 * Load the hooks
	 */
	public function load_all_hooks() {	
		load_plugin_textdomain($this->domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		add_action('admin_init',  array( $this, 'register_settings'));  
		add_action('admin_print_styles', array( $this, 'add_styles' ) );
		add_action('admin_print_scripts', array( $this, 'add_scripts' ) );
		add_action('add_meta_boxes', array( $this, 'add_box' ) );
		add_action('admin_menu', array( $this, 'add_menu'));
		add_action('admin_notices',  array( $this, 'admin_messages'));  
	}

	/**
	 * Add the styles
	 */
	public function add_styles() {
		// -- Register Scripts & Styles
		wp_register_style( 'fl_admin_styles', $this->plugin_url . '/assets/css/admin.css' );
		wp_enqueue_style( 'fl_admin_styles' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Add the scripts
	 */
	public function add_scripts() {
		wp_register_script( 'fl_admin_js', $this->plugin_url . '/assets/js/admin.js', array( 'wp-color-picker' ), false, true  );
		wp_enqueue_script('jquery-ui-core'); 
		wp_enqueue_script('jquery-ui-tabs'); 
		wp_enqueue_script( 'fl_admin_js' );
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
	}

	/**
	 * Plugin Settings
	 */
	public function register_settings() {

		// -- Register Settings Fields
		register_setting( 'plugin_options', 'plugin_options',  array( $this, 'validate_settings') );

		// -- API Key Section
		add_settings_section('plugin_main', 'Configuration settings', array( $this, 'plugin_section_text'), $this->domain);
		add_settings_field('plugin_api_key', 'FixturesLive Data Key',  array( $this, 'plugin_api_string'), $this->domain, 'plugin_main');
		add_settings_field('plugin_league_id', 'FixturesLive LeagueID',  array( $this, 'plugin_league_id_string'), $this->domain, 'plugin_main');	
		// -- /API Key Section

		// -- Data Syndication Section
		add_settings_section('plugin_data_intergration', 'Select A Service Type you wish to integrate with your new pages', array( $this, 'plugin_data_integration_text'), $this->domain . 'data_method');
		add_settings_field('plugin_method', 'Data Intergration Methods',  array( $this, 'plugin_data_integration_type'), $this->domain . 'data_method', 'plugin_data_intergration');	
		// -- /Data Syndication Section

		// -- Leagues Setup Section
		add_settings_section('plugin_league_setup', 'Competitions to display', array( $this, 'plugin_league_section_text'), $this->domain . 'league_setup');	
		add_settings_field('plugin_league_cup_config', '',  array( $this, 'plugin_league_setup_assignments'), $this->domain . 'league_setup', 'plugin_league_setup');
		// -- /Leagues Setup Section

		// -- Template Functions
		add_settings_section('plugin_template_functions', 'Options', array( $this, 'plugin_template_section_text'), $this->domain . 'template_options');	
		add_settings_field('plugin_template_table_include_styles', 'Include FixturesLive CSS',  array( $this, 'plugin_include_styles'), $this->domain . 'template_options', 'plugin_template_functions');
		add_settings_field('plugin_template_table_header_color', 'FixturesLive header colour',  array( $this, 'plugin_color_picker_tables'), $this->domain . 'template_options', 'plugin_template_functions');
		add_settings_field('plugin_template_table_text_color', 'FixturesLive header text colour',  array( $this, 'plugin_color_picker_header_text'), $this->domain . 'template_options', 'plugin_template_functions');
		add_settings_field('plugin_template_table_alt_color', 'FixturesLive tertiary color',  array( $this, 'plugin_color_picker_alt_text'), $this->domain . 'template_options', 'plugin_template_functions');
		// -- /Template Functions

		// -- Advanced 
		add_settings_section('plugin_template_advanced', 'Core pages', array( $this, 'plugin_advanced_section_text'), $this->domain . 'advanced_options');	
		add_settings_field('plugin_template_select_team_page', 'Team page',  array( $this, 'plugin_template_select_team_page'), $this->domain . 'advanced_options', 'plugin_template_advanced');
		add_settings_field('plugin_template_select_external_page', 'External comp page',  array( $this, 'plugin_template_select_externalpage'), $this->domain . 'advanced_options', 'plugin_template_advanced');
		add_settings_field('plugin_template_select_archive_page', 'Archive page',  array( $this, 'plugin_template_select_archive_page'), $this->domain . 'advanced_options', 'plugin_template_advanced');
		add_settings_field('plugin_template_select_venue_page', 'Venue page',  array( $this, 'plugin_template_select_venue_page'), $this->domain . 'advanced_options', 'plugin_template_advanced');

		// -- Advanced
	}

	function plugin_section_text() {
		echo '<p>You need a FixturesLive Data Key, and a LeagueID</p>';
	} 

	function plugin_template_section_text() {
		echo '<p>These options will only apply if you are not using a FixturesLive Theme. </p>';
	}

	function plugin_league_section_text() {
		echo '<p></p>';
	}

	function plugin_data_integration_text() {
		echo '<p></p>';
	}

	function plugin_advanced_section_text() {
		echo '<p>These are pages are essential for users to be able to click from one page to another. They were installed when you activated the FixturesLive plugin.</p>

		<p><b>Warning</b> Changing these will have unforeseen consequences.</p>';
	}

	public function plugin_api_string() {
		
		echo "<input id='plugin_text_string' name='plugin_options[fl_apikey]' size='40' type='text' value='" . $this->options['fl_apikey'] . "' /><br/>
		<span class='description'>Enter your Data Key here</span>";
	}

	public function plugin_league_id_string() {
		echo "<input id='plugin_text_string' name='plugin_options[league_id]' size='40' type='text' value='" . $this->options['league_id'] . "' /><br/>
		<span class='description'>Enter your LeagueID. You can enter more than one LeagueID, seperated by a comma.</span>";
	}



	public function plugin_data_integration_type() {
		$selected = ($this->options['plugin_data_type']) ? $this->options['plugin_data_type'] : '';
		foreach(array_reverse($this->data_types, true) as $k => $v) {
			if($selected == $k) {
				echo "<input type='radio' checked='checked' value='".$k."' name='plugin_options[plugin_data_type]' /> {$v}<br/>";
			} else {
				echo "<input type='radio' value='".$k."' name='plugin_options[plugin_data_type]' /> {$v}<br/>";
			}
		}
		echo "<span class='description'>Select a data type for your League / Cup Pages</span>";
	}

	public function plugin_template_layout() {
		$selected = ($this->options['plugin_layout']) ? $this->options['plugin_layout'] : '';
		foreach($this->layouts as $k => $v) {
			if($selected == $k) {
				echo "<input type='radio' checked='checked' value='".$k."' name='plugin_options[plugin_layout]' /> {$v}<br/>";
			} else {
				echo "<input type='radio' value='".$k."' name='plugin_options[plugin_layout]' /> {$v}<br/>";
			}
		}
		echo "<span class='description'>Select a Theme Layout For Your League Pages</span>";
	}

	public function plugin_league_setup_assignments() {
		$installed_leagues = get_option('fl_installed_leagues');
		$local_config = $this->options['plugin_league_config'];
		$local_enabled = $this->options['plugin_league_enabled'];
		echo '<table width="100%" border="0">';
		foreach($installed_leagues as $league) {
			
			echo '<div class="league_block">
					<tr>
					<td>'.$league['name'].'</td>


					<td><input type="hidden" name="plugin_options[plugin_league_config][' . $league['name'] . ']" value="' . $league['comp_type'] . '" /></td>';


			if($local_enabled) {
				$checked = ($local_enabled[$league['name']] == 1) ? 'checked="checked"' : ''; 
			} else {
				$checked = 'checked="checked"';
			}
			
			echo '</td><td><input type="checkbox" value="1" ' . $checked . ' name="plugin_options[plugin_league_enabled][' . $league['name'] . ']" /> Enabled</td></tr></div>';
		}
		echo '</table>';
	}

	public function plugin_color_picker_tables() {
		echo '<input type="text" value="' . $this->options['plugin_table_colors'] . '"  name="plugin_options[plugin_table_colors]" class="color-picker" />';
	}

	public function plugin_color_picker_header_text() {

		echo '<input type="text"  value="' . $this->options['plugin_table_header_color'] . '" name="plugin_options[plugin_table_header_color]" class="color-picker" />';
	}

	public function plugin_color_picker_alt_text() {
		$default_colors = ($this->options['plugin_table_alt_color']) ? $this->options['plugin_table_alt_color'] : '#eee';
		echo '<input type="text"  value="' . $default_colors . '" name="plugin_options[plugin_table_alt_color]" class="color-picker" /><br/><p>This colour is used on any notes that appear on the fixtures / results pages</p>';	
	}

	public function plugin_include_styles() {
		$checked = null;
		if($this->options['plugin_include_styles'] == 1) {
			$checked = ' checked="checked" ';
		}
		echo '<input type="checkbox" ' . $checked . ' value="1"  name="plugin_options[plugin_include_styles]"  /><br/><p>Please leave this checked unless you are an advanced user and plan to use your own CSS';
	}

	public function plugin_allow_external_leagues() {
		$checked = null;
		if($this->options['plugin_include_styles'] == 1) {
			$checked = ' checked="checked" ';
		}
		echo '<input type="checkbox" ' . $checked . ' value="1"  name="plugin_options[plugin_allow_external_leagues]"  />';	
	}

	public function plugin_template_select_league_page() {
		echo '<select name="plugin_options[fl_league_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_league_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_cup_page() {
		echo '<select name="plugin_options[fl_cups_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_cups_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_fixtures_page() {
		echo '<select name="plugin_options[fl_fixtures_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_fixtures_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_results_page() {
		echo '<select name="plugin_options[fl_results_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_results_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_team_page() {
		echo '<select name="plugin_options[fl_team_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_team_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_externalpage() {
		echo '<select name="plugin_options[fl_external_comp_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_external_comp_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_archive_page() {
		echo '<select name="plugin_options[fl_archives_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_archives_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';
	}

	public function plugin_template_select_venue_page() {
		echo '<select name="plugin_options[fl_venue_page_id]">';
			$pages = get_pages();
			echo '<option value="">No Page Selected</option>';
			foreach($pages as $page) {
				if($this->options['fl_venue_page_id'] == $page->ID) {
					echo '<option selected="selected" value="' . $page->ID . '">' . $page->post_title . '</option>';
				} else {
					echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
				}
				
			}
		echo '</select>';	
	}


	/**
	 * Validate Settings
	 */
	public function validate_settings($input) {
		$options = get_option('plugin_options');
		$bApiCheck = true;
	    $valid = $this->options;
	    $fl_installation_stage = get_option('fl_installation_stage');


	    if ( isset($_POST['set_up_stage_1']) || isset($_POST['Update']) || isset($_POST['Refresh'])) {

	    	update_option('fl_api_is_valid',false);

	    	// -- Sanatize any naughty input
	   		$valid['fl_apikey'] = sanitize_text_field($input['fl_apikey']);
	  	    $valid['league_id'] = sanitize_text_field($input['league_id']);

	  	     	// -- Check API Key Isnt Blank
		    if (strlen($valid['fl_apikey']) == 0) {
		        add_settings_error(
		                'fl_apikey',                    // Setting title
		                'flapikey_texterror',            // Error ID
		                'Please enter an API Key',     // Error message
		                'error'                         // Type of message
		        );
		        // Set it to the default value
		        $valid['fl_apikey'] = '';
		        $bApiCheck = false;
		    } 

		    if (strlen($valid['league_id']) == 0) {
		        add_settings_error(
		                'league_id',                    // Setting title
		                'leagueid_texterror',            // Error ID
		                'Please enter a league ID',     // Error message
		                'error'                         // Type of message
		        );
		        // Set it to the default value
		        $valid['league_id'] = '';
		        $bApiCheck = false;
		    }

	    	// -- Check The API works and is valid
		    if(!get_option('fl_api_is_valid') || isset($_POST['Refresh'])) {
		    	if ($bApiCheck) {

			    	// - 1. Get the methods for an account
			    	$fl_webservice = new FixturesLiveTransportHelper($input['fl_apikey']);
			    	$response = $fl_webservice->GetWebServiceMethodsForAccount();
			    				    
			    	// - 2. If We have a valid key
			    	if($response) {
			    		// -- 3. Tell WP that the API Key is valid
			    		update_option('fl_api_is_valid',true);

			    		// -- 4. Assign the permissions to Wordpress
			    		$this->assignAPIPermissionsToWordpressOption($response);
			    		
			    		// -- 5. Next we need to check if we can divisions by league // -- This is the base level we want
						if(accountCanAccessMethod('GetDivisionsByLeague')) {

							// -- 5b -- Loop through leagues entereded.

							$leagues = explode(',', $valid['league_id']);
							foreach($leagues as $league) {
								// -- 6. We can, Do what we did before
								$response=$fl_webservice->GetDivisionsByLeague($league);
								if(!$response) {
						    		add_settings_error(
						                'fl_apikey',                    // Setting title
						                'flapikey_texterror',            // Error ID
						                'Transport Error! Either there was no data or an error on the server',     // Error message
						                'error'                         // Type of message
						       		 );
						    	} else {
						    		$this->getLeaguesFromAPI($response);
						    		if(!$this->options['has_installed']) {
						    			update_option('fl_installation_stage',2);
						    		}
				    			}
							}
						
						} else {
							add_settings_error(
			                'fl_apikey',                    
			                'flapikey_texterror',           
			                'Your account does not appear to have access to the GetDivisionsByLeague API method',     
			                'error'                       
			       		 );
					   }

			    	} else {
			    		add_settings_error(
			                'fl_apikey',                    
			                'flapikey_texterror',            
			                'We could not retrieve any permissions for this API key on the server',     
			                'error'                         
			       		 );
			    	}

			    	
			    }  else {
			    		add_settings_error(
			                'fl_apikey',                    
			                'flapikey_texterror',            
			                'Your API Key is inavlid',     
			                'error'                         
			       		 );
			    	}

		    }
	

	    }

	   // if( isset($_POST['set_up_stage_2']) || isset($_POST['Update']) || isset($_POST['Refresh']) ) {

		if( isset($_POST['set_up_stage_2'])  ) {

	    	$installed_leagues = get_option('fl_installed_leagues');
	    	
	    	$index_counter = 1;
	    	foreach($input['plugin_league_config'] as $k => $v) {

		    		// -- Check To See If The User Has Enabled This Divison
				if($input['plugin_league_enabled'][$k] == 1) {

					$post_type = ($v == 1) ? 'league' : 'cup';

					// -- Lets Create Some Pages
					$post_args = array(
						'post_title' => $k,
						'post_content' => '',
						'post_status' => 'publish',
						'post_type' => $post_type,
						'menu_order' => $installed_leagues[$k]['display_order']
					);

					global $wpdb;			
					$new_post = wp_insert_post( $post_args, $wp_error );
					update_post_meta($new_post, 'fixtures_live_id', $installed_leagues[$k]['divison_id']);
					update_post_meta($new_post, 'fixtures_live_key', $k);
					update_post_meta($new_post, 'season_name', $installed_leagues[$k]['season']);
				}
				$index_counter++;
			}


			$valid['plugin_league_config'] = $input['plugin_league_config'];
			$valid['plugin_league_enabled'] = $input['plugin_league_enabled'];

			if(!$this->options['has_installed']) {
    			update_option('fl_installation_stage',3);
    		}

    		// -- Do A Bit Of A Sanity Check For Leagues And Cups
    		if(isset($_POST['Refresh'])) {
    			// -- Lets check the leagues in the system as is
				$divisons = get_pages('child_of=' . $options['fl_league_page_id']);

				foreach($divisons as $divison) {
					// -- get the fixture live id for that division
					$FL_KEY = get_post_meta($divison->ID,'fixtures_live_key', true);
					// -- Dont Exist
					if(!array_key_exists($FL_KEY,$installed_leagues)) {
						wp_delete_post( $divison->ID );
					}					
				}

				$cups = get_pages('child_of=' . $options['fl_cups_page_id']);

				foreach($cups as $cup) {
					// -- get the fixture live id for that division
					$FL_KEY = get_post_meta($cup->ID,'fixtures_live_key', true);
					// -- Dont Exist
					if(!array_key_exists($FL_KEY,$installed_leagues)) {
						wp_delete_post( $cup->ID );
					}					
				}

    		}

    		$valid['plugin_data_type'] = 1;
   
	    }

	    /*if ( isset($_POST['set_up_stage_3']) || isset($_POST['Update']) ) {

	    	if(!$this->options['has_installed']) {
    			update_option('fl_installation_stage',4);
    		}

    		$valid['plugin_data_type'] = $input['plugin_data_type'];


	    }*/

	     if ( isset($_POST['set_up_stage_3']) || isset($_POST['Update']) ) {

	    	if(!$this->options['has_installed']) {
    			update_option('fl_installation_stage',4);
    		}

    		$valid['plugin_layout'] = $input['plugin_layout'];
    		$valid['plugin_table_colors'] = $input['plugin_table_colors'];
    		$valid['plugin_table_header_color'] = $input['plugin_table_header_color'];
    		$valid['plugin_table_alt_color'] = $input['plugin_table_alt_color'];
    		$valid['plugin_include_styles'] = $input['plugin_include_styles'];

	    }

	
	    if(isset($_POST['Update'])) {
	    	$valid['fl_cups_page_id'] = $input['fl_cups_page_id'];
	    	$valid['fl_league_page_id'] = $input['fl_league_page_id'];
	    	$valid['fl_fixtures_page_id'] = $input['fl_fixtures_page_id'];
	    	$valid['fl_results_page_id'] = $input['fl_results_page_id'];
	    	$valid['fl_team_page_id'] = $input['fl_team_page_id'];
	    	$valid['fl_external_comp_page_id'] = $input['fl_external_comp_page_id'];
	    	$valid['fl_archives_page_id'] = $input['fl_archives_page_id'];
	    	$valid['fl_venue_page_id'] = $input['fl_venue_page_id'];

	    	// -- Change Parents over
	    	$pages = get_pages();
	    	foreach($pages as $page) {
	    		$post_params = array();
	    		$post_params['ID'] = $page->ID;
	    		if($this->options['fl_cups_page_id'] == $page->post_parent) {
	    			$post_params['post_parent'] = $valid['fl_cups_page_id'];
	    			wp_update_post( $post_params );
	    		}  
	    		if($this->options['fl_league_page_id'] == $page->post_parent) {
	    			$post_params['post_parent'] = $valid['fl_league_page_id'];
	    			wp_update_post( $post_params );
	    		}  
	    		if($this->options['fl_league_page_id'] == $page->post_parent) {
	    			$post_params['post_parent'] = $valid['fl_league_page_id'];
	    			wp_update_post( $post_params );
	    		}  
	    		if($this->options['fl_results_page_id'] == $page->post_parent) {
	    			$post_params['post_parent'] = $valid['fl_results_page_id'];
	    			wp_update_post( $post_params );
	    		}  
	    		if($this->options['fl_team_page_id'] == $page->post_parent) {
	    			$post_params['post_parent'] = $valid['fl_team_page_id'];
	    			wp_update_post( $post_params );
	    		}  

	    	}
	    }
	    return $valid;

	}

	/**
	 * Admin Messaging
	 */
	public function admin_messages() {
		if(!$this->options['fl_apikey']) {
			echo $this->add_admin_message('FixturesLive API: there is no Data Key');
		}
	}


	/**
	 * Add Admin Message Helper
	 */
	public function add_admin_message($msg,$type="error") {
		return '<div id="message" class="'.$type.'"><p>'.$msg.'</p></div>';
	}

	/**
	 * Add the box
	 */
	public function add_box() {
	}

	/**
	 * Create the box content
	 */
	public function create_box_content() {
		
	}

	/**
	 * Add the menu
	 */
	public function add_menu() {
		// -- Add Main Menu
		 add_menu_page( 'FixturesLive', 'FixturesLive', 'manage_options', $this->domain, array($this, 'create_menu_content'), $this->plugin_url . '/assets/images/fl_icon_small.png', 6);
		 add_submenu_page( $this->domain, 'FixturesLive Settings', 'FixturesLive Settings', 'manage_options', $this->domain, array($this, 'create_menu_content'));
		 add_submenu_page( $this->domain, 'FixturesLive Support', 'FixturesLive Support', 'manage_options', $this->domain . '_support', array($this, 'create_support_menu_content'));
	}


	public function create_support_menu_content() {
		?>
			<div class="wrap">
				<h2>FixturesLive Support</h2>
				<iframe src="http://www.fixtureslive.com/external/wordpress_support/" width="100%" height="600"></iframe>
			</div>
		<?php
	}

	/**
	 * Create the menu content
	 */
	public function create_menu_content() {
		// Check the user capabilities
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		?>
		<div class="wrap">
		
		<?php settings_errors(); ?>	
		<?php if(get_option('has_installed')==1) : ?>
			<div>
				<div id="icon-fixtures_live_plugin" class="icon32"></div>
				<h2>FixturesLive Settings</h2>		
				<form action="options.php" method="post">	
					<div id="tabs">
					    <ul>
					        <li><a href="#box-1"><span>Configuration</span></a></li>
					        <li><a href="#box-4"><span>Default Colour Options</span></a></li>
					        <li><a href="#box-5"><span>Core Pages</span></a></li>
					        <li><a href="#box-6"><span>System Information</span></a></li>
					    </ul>
					    <div id="box-1">
							<!-- API Settings -->
							<?php settings_fields('plugin_options'); ?>
							<?php do_settings_sections($this->domain); ?>
							<!-- /API Settings -->
						</div>
						<div id="box-4">
							<!-- Template Settings -->
							<?php do_settings_sections($this->domain . 'template_options'); ?>
							<!-- /Template Settings -->
						</div>
						<div id="box-5">
							<!-- Template Settings -->
							<?php do_settings_sections($this->domain . 'advanced_options'); ?>
							<!-- /Template Settings -->
						</div>
						<div id="box-6">
							
								<h3>System Information</h3>
								<p>Use the information below when submitting technical support requests</p>
								<textarea style="width:100%;height:400px" readonly="readonly" id="system-info-textarea" title="To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).">
### FixturesLive Info ###

FixturesLive Plugin Version:    <?php echo get_option('fl_plugin_version') . "\n"; ?>
FixturesLive Data Key:			<?php echo $this->options['fl_apikey'] . "\n"; ?>
FixturesLive League ID:		    <?php echo $this->options['league_id'] . "\n"; ?>
FixturesLive Allowed Methods:	
<?php echo implode("\n",$this->FL_KEY_METHODS); ?>			

### Begin System Info ###

Multi-site:               <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

SITE_URL:                 <?php echo site_url() . "\n"; ?>
HOME_URL:                 <?php echo home_url() . "\n"; ?>

WordPress Version:        <?php echo get_bloginfo('version') . "\n"; ?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo mysql_get_server_info() . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

PHP Memory Limit:         <?php echo ini_get('memory_limit') . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get('post_max_size') . "\n"; ?>

WP_DEBUG:                 <?php echo defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

WP Table Prefix:          <?php global $wpdb; echo "Length: ". strlen($wpdb->prefix); echo " Status:"; if (strlen($wpdb->prefix)>16){echo " ERROR: Too Long";} else {echo " Acceptable";} echo "\n"; ?>

Session:                  <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:              <?php echo (ini_get('session.use_cookies') ? 'On' : 'Off'); ?><?php echo "\n"; ?>
Use Only Cookies:         <?php echo (ini_get('session.use_only_cookies') ? 'On' : 'Off'); ?><?php echo "\n"; ?>

UPLOAD_MAX_FILESIZE:      <?php if(function_exists('phpversion')) echo ini_get('upload_max_filesize'); ?><?php echo "\n"; ?>
POST_MAX_SIZE:            <?php if(function_exists('phpversion')) echo ini_get('post_max_size'); ?><?php echo "\n"; ?>
WordPress Memory Limit:   <?php echo (WP_MEMORY_LIMIT)/(1024*1024)."MB"; ?><?php echo "\n"; ?>
WP_DEBUG:                 <?php echo (WP_DEBUG) ? 'ON' : 'OFF'; ?><?php echo "\n"; ?>
DISPLAY ERRORS:           <?php echo (ini_get('display_errors')) ? 'On (' . ini_get('display_errors') . ')' : 'N/A'; ?><?php echo "\n"; ?>

ACTIVE PLUGINS:
<?php
$plugins = get_plugins();
$active_plugins = get_option('active_plugins', array());

foreach ( $plugins as $plugin_path => $plugin ):

//If the plugin isn't active, don't show it.
if ( !in_array($plugin_path, $active_plugins) )
continue;
?>
<?php echo $plugin['Name']; ?>: <?php echo $plugin['Version']; ?>

<?php endforeach; ?>

CURRENT THEME:
<?php
if ( get_bloginfo('version') < '3.4' ) {
$theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
echo $theme_data['Name'] . ': ' . $theme_data['Version'];
} else {
$theme_data = wp_get_theme();
echo $theme_data->Name . ': ' . $theme_data->Version;
}
?>


### End System Info ###
							</textarea>

								
						
						</div>
						<input class="fl_button"  name="Update" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" /> 
						<input class="fl_button"  name="Refresh" type="submit" value="<?php esc_attr_e('Refresh System'); ?>" /> 
				</form>
			</div>
		<?php else: ?>
			<div class="fl_install_wizard">
				<?php if($this->stage == 1) : ?>
					<h2>Welcome to the FixturesLive hockey league plugin</h2>
					<p>Installation will connect your Wordpress site to FixturesLive.</p>
						<form action="options.php" method="post" class="clear">
							<?php settings_fields('plugin_options'); ?>
							<?php do_settings_sections($this->domain); ?>
							<input name="set_up_stage_1" type="submit" class="fl_button" value="<?php esc_attr_e('Save Changes'); ?>" /> 
						</form>
				 <?php elseif($this->stage==2) : ?>
					<h2>Competition configuration</h2>
					<p>Below is a list of all of the current competitions for your League. For any competitions you do not want displayed on
						your website at this point untick the enabled box. Don't worry, you can change this later.</p>
						<form action="options.php" method="post" class="clear">
							<?php settings_fields('plugin_options'); ?>
						<?php do_settings_sections($this->domain . 'league_setup'); ?>
						<input name="set_up_stage_2" type="submit" class="fl_button" value="<?php esc_attr_e('Save Changes'); ?>" /> 
					</form>  
				<?php elseif($this->stage==3) : ?>
					<h2>Default colour options</h2>
					<form action="options.php" method="post" class="clear">
						<?php settings_fields('plugin_options'); ?>
						<?php do_settings_sections($this->domain . 'template_options'); ?>
						<input name="set_up_stage_3" type="submit" class="fl_button" value="<?php esc_attr_e('Save Changes'); ?>" /> 
					</form>
				<?php elseif($this->stage==4) : ?>
					<?php if(!get_option('has_installed')) { update_option('has_installed',1);	} ?>
					<h2>Congratulations</h2>
					<p>Your website is now connected to FixturesLive.</p>
				<?php endif; ?>

			</div>
		<?php endif; ?>
	</div><?php
	}



	public function getLeaguesFromAPI($xml_response) {

		// -- See If We Already Have Some Leagues

		$installed_leagues = get_option('fl_installed_leagues');
		if(!$installed_leagues) {
			$installed_leagues = array();
		}
		// -- Load Data from FL In
		$xml = simplexml_load_string($xml_response);
		// -- Make An Empty Array To Store The Data from FL In.
		$new_leagues = array();
		// -- Loop Through FL Data assigning each current instance to the new leagues array
		foreach($xml as $item) {
			// -- Check If Its The Current League
			if($item->Current == 'true') {
				$new_leagues[(string)$item->DivisionName] = array(
					'name' =>  (string)$item->DivisionName,
					'divison_id' => (string)$item->DivisionID,
					'season' => (string)$item->SeasonName,
					'season_id' => (string)$item->SeasonID,
					'display_order' => (int)$item->DisplayOrder,
					'comp_type' => (int)$item->CompetitionType
				);	
			}
		}

		$new_leagues = array_merge($installed_leagues, $new_leagues);

		// -- Update The Global Array in WP so we have References to id's etc
		update_option('fl_installed_leagues', $new_leagues);
	}


	// -- We are going to assign all the availbe methods to the options in an array
	private function assignAPIPermissionsToWordpressOption($xml_response) {

		// -- Load In The Response
		$xml = simplexml_load_string($xml_response);
		// -- Define an array
		$available_methods = array();
		// -- Loop Through FL Data assigning each current instance to the new leagues array
		foreach($xml as $item) {
			// --  Push the method name to the sting
			array_push($available_methods,(string)$item->Name);
		}

		// -- Set the option for it
		update_option('FL_KEY_METHODS', $available_methods);

		// -- Set The Property of the class for installation sake
		$this->FL_KEY_METHODS = $available_methods;

	}


	



}
}


?>