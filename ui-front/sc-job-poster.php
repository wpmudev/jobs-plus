<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

/**
Variables passed by  shortcode
		$title
		$legend
		$link
		$view
		$class
		$count
*/
global $post, $jbp_query, $Term_Images;

$jbp_query = new WP_Query(array(
'post_type' => 
'jbp_job', 
'posts_per_page' => $count, 
'post_status' => 'publish',
) 
);

wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('element-query');
wp_enqueue_script('jquery-format-currency-i18n');

$use_cat_img = empty($Term_Images) ? false : $Term_Images->get_setting('jbp_category->use', false);
if( $use_cat_img ) {
	$break = 'data-eq-pts=" break: 420"';
} else {
	$break = 'data-eq-pts=" break: 1024"';
}

?>
<div class="job-poster" <?php echo $break; ?> >
	<h2 style="text-align: center"><?php echo esc_html($title); ?></h2>
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
		<span><a href="<?php echo get_post_type_archive_link('jbp_job'); ?>"><?php echo esc_html( $link ); ?></a></span>
	</div>
	<div>
		[jbp-job-post-btn text="<?php echo $legend; ?>" img="false"]
	</div>
	<?php wp_reset_postdata(); ?>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($){
		var currency_symbol = $.formatCurrency.regions['<?php echo $this->js_locale; ?>'].symbol;
		$('.currency_symbol').text(currency_symbol);
	});
</script>