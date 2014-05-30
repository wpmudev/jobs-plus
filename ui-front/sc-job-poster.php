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
<div class="job-poster" data-eq-pts=" break: 420">
	<h2 style="text-align: center"><?php echo esc_html($text); ?></h2>
	<hr />
	<?php while( $jbp_query->have_posts() ): ?>
	<div class="poster" >
		<?php
		$jbp_query->the_post();

		echo do_shortcode('[jbp-job-poster-excerpt]');

		?>
	</div>
	<?php endwhile; ?>
	<div class="jobs-link">
		<span><a href="<?php echo get_post_type_archive_link('jbp_job'); ?>"><?php echo esc_html( sprintf(__('Browse More %s ...', JBP_TEXT_DOMAIN), $this->job_labels->name) ); ?></a></span>
	</div>
	<div>
		[jbp-job-post-btn img="false"]
	</div>
	<?php wp_reset_postdata(); ?>
</div>
