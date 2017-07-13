<?php
/**
 * Adds Foo_Widget widget.
 */
class Fixtures_Live_Mini_Map_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'fixtures_live_clublocations', // Base ID
			'FixturesLive Club Mini Map', // Name
			array( 'description' => __( 'Displays a map with all the clubs located', 'fixtures_live' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $post, $fixtures_live_options;
		extract( $args );
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;

		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		echo '<div class="map">' . do_shortcode( '[league_map league_id="' . $instance['league_to_show'] . '" map_height="' . $instance['map_height'] . '"]' ) . '</div>';
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['map_height'] = $new_instance['map_height'];
		$instance['league_to_show'] = $new_instance['league_to_show'];
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		global $post, $fixtures_live_options;

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];		
			$map_height = $instance[ 'map_height' ];
			$league_to_show = $instance[ 'league_to_show' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
			$map_height = 200;
			$league_to_show = null;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'map_height' ); ?>"><?php _e( 'Map Height (i.e. 200):' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'map_height' ); ?>" name="<?php echo $this->get_field_name( 'map_height' ); ?>" type="text" value="<?php echo esc_attr( $map_height ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'league_to_show' ); ?>"><?php _e( 'League Map To Show (Fixtures Live ID)' ); ?></label> 
		<select name="<?php echo $this->get_field_name( 'league_to_show' ); ?>">
			<?php if($this->getLeagues()) : foreach($this->getLeagues() as $league) : ?>
				<?php $activeClass = ($league_to_show == $league) ? 'selected="selected"' : ''; ?>
				<option <?php echo $activeClass; ?> value="<?php echo $league; ?>"><?php echo $league; ?></option>
			<?php endforeach; endif; ?>
		</select>
		
		</p>

		<?php 
	}

	public function getLeagues() {
		global $fixtures_live_options;
		print_r(explode(',',$fixtures_live_options['league_id']));
		return explode(',',$fixtures_live_options['league_id']);
	}

	
} 