<?php
/**
 * Single Cup template
 *
 * @package             Fixtures Live
 * @category            Cup
 * @author              Fixtures Live
 * @copyright           Copyright © 2013 Fixtures Live.
 */
 ?>

<?php get_header('fixtures_live'); ?>

<?php do_action('fixtures_live_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<?php do_action('fixtures_live_before_singleton', $post); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header>
				<h1><?php the_title(); ?> <?php fl_season_name(); ?></h1>
			</header>
				<?php fixtues_live_content(); ?>	

			
		</div>	
		<?php do_action('fixtures_live_after_singleton', $post); ?>

	<?php endwhile; ?>

<?php do_action('fixtures_live_after_main_content'); // </div></div> ?>

<?php do_action('fixtures_live_sidebar'); ?>

<?php get_footer('fixtures_live'); ?>