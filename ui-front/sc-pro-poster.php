<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

/**
* Do not use raw shortcodes( [xxx] ) inside the loop. Use echo do_shortcode('[xxx]');
*/
global $post, $jbp_query;
$jbp_query = new WP_Query(array('post_type' => 'jbp_pro', 'posts_per_page' => 3, 'post_status' => 'publish') );

wp_enqueue_style('jobs-plus');
wp_enqueue_script('element-query');

?>
<div class="pro-poster">
	<h2 class="h2_title"><?php echo esc_html( $text ); ?></h2>


</div>
