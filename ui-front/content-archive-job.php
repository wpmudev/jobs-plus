<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $CustomPress_Core, $Jobs_Plus_Core, $wp_query;


wp_enqueue_script('masonry');
wp_enqueue_script('jquery-ellipsis');

//Need to prevent infinite loop since you can't call have_posts in the loop
function job_have_posts(){
	global $wp_query;
	return $wp_query->current_post + 1 < $wp_query->post_count;
}

?>

<div class="job-archive-wrapper">

	<div class="group">
		<?php if(dynamic_sidebar('job-archive-widget') ) : else: endif; ?>
	</div>

	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>

	<?php echo do_shortcode('[jbp-job-search]'); ?>
	<?php echo do_shortcode('[jbp-job-price-search]'); ?>

	<?php if(have_posts()): ?>
	<div id="job-grid-container">
		<div class="job-grid-sizer"></div>
		<div class="job-gutter-sizer"></div>
		<?php while( have_posts() ): the_post(); ?>
		<?php echo do_shortcode('[jbp-job-excerpt]'); ?>
		<?php endwhile; ?>
	</div>
	<?php else: ?>
	<h2><?php printf(__('No %s Found', JBP_TEXT_DOMAIN), $this->job_labels->name ); ?></h2>
	<?php endif; ?>
	<?php echo $Jobs_Plus_Core->pagination(); ?>

</div><!-- .job-archive-wrapper -->

<?php if($wp_query->post_count > 0 ): ?>
<script type="text/javascript">
	jQuery(document).ready( function($){

		var currency_symbol = $.formatCurrency.regions['<?php echo $this->js_locale; ?>'].symbol;
		$('.currency_symbol').text(currency_symbol);

		$(".job-show").ellipsis({row: 3 });
		$(".ellipsis").ellipsis({row: 3, char: '<a> . . .Read more</a>' });

		var $container = $("#job-grid-container");
		$container.masonry({
			itemSelector: ".job-excerpt",
			columnWidth: ".job-grid-sizer",
			gutter: ".job-gutter-sizer"
		});

		$(window).resize( function(){ $container.masonry('layout'); } );

		$(".job-excerpt").click( function(){
			var $this = $(this);
			var permalink = $this.data('permalink');
			window.location = permalink;
		});
	});
</script>
<?php endif; ?>




