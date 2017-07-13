<?php
/**
 * Adds Foo_Widget widget.
 */
class Fixtures_Live_Comp_List extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'fixtures_live_comp_list', // Base ID
			'Fixtures Live Competition List', // Name
			array( 'description' => __( 'A list of selected competitons in the league', 'fixtures_live' ), ) // Args
		);

		$this->opts = array(1 => 'Divisions',2 => 'Cups');
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
		$list_to_show = $this->opts[$instance['type']];
		$queryArg = ($list_to_show == 'Divisions') ? 'league' : 'cup' ;
		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		$child_pages = get_posts('post_type=' . $queryArg . '&posts_per_page=-1&orderby=menu_order&order=DESC');
		$this->buildNavigation($child_pages);
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
		$instance['type'] = strip_tags( $new_instance['type'] );
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
			$type = $instance[ 'type' ];	
		}
		else {
			$title = __( 'New title', 'text_domain' );
			$type = 1;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Competition Type:' ); ?></label> 
		<select name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>" type="text" >
			<?php foreach($this->opts as $k => $v) : ?>
				<?php if($type == $k ) : ?>
					<option selected="selected" value="<?php echo $k ; ?>"><?php echo $v; ?></option>
				<?php else: ?>
					<option value="<?php echo $k ; ?>"><?php echo $v; ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<?php 
	}

	public function buildNavigation($arItems) {
		global $post;
		$class = '';
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