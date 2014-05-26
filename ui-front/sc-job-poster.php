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
$jbp_query = new WP_Query(array('post_type' => 'jbp_job', 'posts_per_page' => 3, 'post_status' => 'publish') );

wp_enqueue_style('jobs-plus');
wp_enqueue_script('element-query');

?>
<div class="job-poster" data-eq-pts=" small:320, medium: 480, large: 960">
	<h2>Recently</h2>
	<?php while( $jbp_query->have_posts() ): ?>
	<div class="poster" >
		<?php
		$jbp_query->the_post();
		$terms = wp_get_post_terms( get_the_ID(), 'jbp_category', array('fields' => 'ids') );
		?>
		<div class="job-poster-img">
			<?php echo do_shortcode(	sprintf('[ti term="%s"]', $terms[0]) ); ?>
		</div>

		<?php echo do_shortcode('[jbp-job-poster-excerpt]'); ?>
	</div>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
</div>