<?php
/**
 * Actions used in template files
 *
 * DISCLAIMER
 *
 * Do not edit or add directly to this file if you wish to upgrade Jigoshop to newer
 * versions in the future. If you wish to customise Jigoshop core for your needs,
 * please use our GitHub repository to publish essential changes for consideration.
 *
 * @package             Jigoshop
 * @category            Core
 * @author              Jigoshop
 * @copyright           Copyright © 2011-2013 Jigoshop.
 * @license             http://jigoshop.com/license/commercial-edition
 */

/* Content Wrappers */
add_action( 'fixtures_live_before_main_content', 'jigoshop_output_content_wrapper'    , 10);
add_action( 'fixtures_lafter_before_main_content' , 'jigoshop_output_content_wrapper_end', 10);

/* Shop Messages */
//add_action( 'fixtures_live_before_singleton', '', 10);
//add_action( 'fixtures_live_after_singleton'  '', 10);

/* Sidebar */
add_action( 'fixtures_live_sidebar', 'fixtures_live_get_sidebar', 10);
?>