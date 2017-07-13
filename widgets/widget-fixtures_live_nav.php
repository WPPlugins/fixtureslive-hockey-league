<?php
/**
 * Adds Foo_Widget widget.
 */
class Fixtures_Live_Navigation_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'fixtures_live_navigation', // Base ID
			'Fixtures Live Cup & League Navigation Widget', // Name
			array( 'description' => __( 'Displays the navigation of leagues and cups relevant to the section you are in', 'fixtures_live' ), ) // Args
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

		// -- Find out if we are in a league/cup page or not
		if(get_post_type() == 'league' || get_post_type() == 'cup') {
			$parent = get_post_type();
			$child_pages = get_posts('post_type=' . get_post_type() . '&posts_per_page=-1&orderby=menu_order&order=DESC');
		} elseif($post->ID == $fixtures_live_options['fl_league_page_id'] || $post->ID == $fixtures_live_options['fl_cups_page_id']) {
			$parent = ($post->ID == $fixtures_live_options['fl_league_page_id']) ? 'league' : 'cup';
			$child_pages = get_posts('post_type=' . $parent . '&posts_per_page=-1&orderby=menu_order&order=DESC');
		}

		$divclass = ($parent=='Leagues') ? 'class="parent_enabled"' : '';
		$cupclass = ($parent=='Cups') ? 'class="parent_enabled"' : '';

		echo '<ul>';
		echo '<li ' . $divclass . '><a href="' . get_permalink($fixtures_live_options['fl_league_page_id']) . '">Leagues</a>';
		if($parent == 'league') {
			$this->buildNavigation($child_pages);
		}
		echo '</li>';
		echo '<li ' . $cupclass . '><a href="' . get_permalink($fixtures_live_options['fl_cups_page_id']) . '">Cups</a>';
		if($parent == 'cup') {
			$this->buildNavigation($child_pages);	
		}
		echo '</li>';
		echo '</ul>';
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
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];		
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	public function buildNavigation($arItems) {
		global $post;
		
		echo '<ul ' . $class . '>';
			foreach($arItems as $nav_item) {
				if($post->ID == $nav_item->ID) {
						echo '<li class="active"><a href="' . get_permalink($nav_item->ID) . '">' . $nav_item->post_title . '</a></li>';	
				} else {
						echo '<li><a href="' . get_permalink($nav_item->ID) . '">' . $nav_item->post_title . '</a></li>';
				}
			}
		echo '</ul>';
	}
} 