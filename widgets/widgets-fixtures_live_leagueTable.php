<?php
/**
 * Adds Foo_Widget widget.
 */
class Fixtures_Live_Mini_League_Table_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'fixtures_live_leaguewidget', // Base ID
			'FixturesLive Mini Divison Widget', // Name
			array( 'description' => __( 'Displays a smaller version of the division', 'fixtures_live' ), ) // Args
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

		if(using_embeds()) {
			echo do_shortcode( '[fl_embed_league id="' . $instance['league'] . '"]' );
			echo $after_widget;
			return;
		}


		if($instance['league'] ) {
			$leagueObj = get_post($instance['league']);
			$data = get_raw_fl_division_data(get_fixtures_live_id($leagueObj));
			if($data) {
				$division_data = new SimpleXMLElement($data);


				if($division_data) {
					?>

				<?php if($instance['show_league_name']) : ?>
				<div class="league_details_header">
					<?php if($division_data->LeagueLogo != ''): ?>
						<figure>
							<img src="http://www.fixtureslive.com/uploads/logos/<?php echo $division_data->LeagueLogo; ?>" alt="<?php echo $division_data->LeagueName; ?>" />
						</figure>
					<?php endif; ?>
					<header>
						<h3><?php echo $division_data->LeagueName; ?></h3>
					</header>
				</div>
				<?php endif; ?>

				<?php if($instance['show_promotion_lines']) : ?>
					<?php
						$promotion = intval($division_data->Promotion) ? intval($division_data->Promotion) : 0;
						$promotion_possible = intval($division_data->PromotionPoss) ? intval($division_data->PromotionPoss) : 0;
						$relegation = intval($division_data->Relegation) ?  intval($division_data->Relegation) : 0;
						$relegation_possible = intval($division_data->RelegationPoss) ?intval($division_data->RelegationPoss) : 0;
					?>
				<?php else: ?>
					<?php $promotion=0;$promotion_possible=0;$relegation=0;$relegation_possible=0; ?>
				<?php endif; ?>

			<table border="1" class="fl_table division">
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th class="tac">P</th>
				<th class="tac">W</th>
				<th class="tac">D</th>
				<th class="tac">L</th>
				<th class="tac">F</th>
				<th class="tac">A</th>
				<th class="tac">+/-</th>
				<th class="tac">Pts</th>
			</tr>
		<?php $c=1; foreach($division_data->LeagueTableRows->LeagueTableRow as $standing) {  ?>
			<tr <?php if($promotion == $c || $relegation == $c): ?>class="promotion_line"<?php endif; ?> <?php if($promotion_possible == $c || $relegation_possible == $c): ?>class="promotion_possible_line"<?php endif; ?>>
				<td><?php echo $standing->Position; ?></td>
				<td class="tac">

					<?php if(intval($standing->PreviousPosition) > intval($standing->Position) && intval($standing->PreviousPosition) > 0) : ?>
						<i class="fa fa-caret-up"></i>
					<?php elseif(intval($standing->PreviousPosition) < intval($standing->Position) && intval($standing->PreviousPosition) > 0): ?>
						<i class="fa fa-caret-down"></i>
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
				<td><span class="team-color" style="background:<?php echo $standing->TeamColour; ?>"></span> <a href="<?php echo add_query_arg('item', str_replace(',', '', $standing->TeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE) ); ?>"><?php echo $standing->ClubTeamName; ?></a></td>
				
				<td class="tac"><?php echo $standing->Played; ?></td>
				<td class="tac"><?php echo $standing->Won; ?></td>
				<td class="tac"><?php echo $standing->Drawn; ?></td>
				<td class="tac"><?php echo $standing->Lost; ?></td>
				<td class="tac"><?php echo number_format((int)$standing->GoalsFor); ?></td>
				<td class="tac"><?php echo number_format((int)$standing->GoalsAgainst); ?></td>
				<td class="tac"><?php echo $standing->GoalDiff; ?></td>
				<td class="tac">
					<?php if((int)$standing->PointsDeducted>0) : ?>
						<?php echo number_format((int)$standing->NetPoints); ?> *
					<?php else: ?>
						<?php echo number_format((int)$standing->Points); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php $c++; } echo '</table>';
				}
			}
		}
		?>
			<p><a href="<?php echo get_permalink( FIXTURES_LIVE_DIVISON_PAGE ); ?>">View All Divisons &gt;</a></p>
		<?php 
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
		$instance['league'] = $new_instance['league'];
		$instance['show_league_name'] = $new_instance['show_league_name'];
		$instance['show_promotion_lines'] = $new_instance['show_promotion_lines'];
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
			$league = $instance[ 'league' ];
			$show_league_name = $instance[ 'show_league_name' ];
			$show_promotion_lines = $instance[ 'show_promotion_lines' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
			$league = '';
			$show_league_name = 1;
			$show_promotion_lines = 1;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<label for="<?php echo $this->get_field_id( 'league' ); ?>"><?php _e( 'League:' ); ?></label> 
		<select name="<?php echo $this->get_field_name( 'league' ); ?>" id="<?php echo $this->get_field_id( 'league' ); ?>" type="text" >
			<?php $child_pages =  get_posts('post_type=league&posts_per_page=-1&orderby=menu_order&order=DESC'); ?>
			<?php foreach($child_pages as $l) : ?>
				<?php if($league == $l->ID) : ?>
					<option selected="selected" value="<?php echo $l->ID; ?>"><?php echo $l->post_title; ?></option>
				<?php else: ?>
					<option value="<?php echo $l->ID; ?>"><?php echo $l->post_title; ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>

		



		<label for="<?php echo $this->get_field_id( 'show_league_name' ); ?>"><?php _e( 'Show League Name &amp; Logo:' ); ?></label> 
		<input <?php if($show_league_name) : ?>checked="checked"<?php endif; ?>  id="<?php echo $this->get_field_id( 'show_league_name' ); ?>" name="<?php echo $this->get_field_name( 'show_league_name' ); ?>" type="checkbox" value="1" />
		</p>

		<label for="<?php echo $this->get_field_id( 'show_promotion_lines' ); ?>"><?php _e( 'Show Promotion / Relegation Lines:' ); ?></label> 
		<input <?php if($show_promotion_lines) : ?>checked="checked"<?php endif; ?>  id="<?php echo $this->get_field_id( 'show_promotion_lines' ); ?>" name="<?php echo $this->get_field_name( 'show_promotion_lines' ); ?>" type="checkbox" value="1" />
		</p>

		<?php 
	}

	
} 