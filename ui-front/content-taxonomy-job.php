<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $CustomPress_Core, $Jobs_Plus_Core, $wp_query;

wp_enqueue_script('jquery-masonry');
wp_enqueue_script('jquery-ellipsis');

//Need to prevent infinite loop since you can't call have_posts in the loop
function job_have_posts(){
	global $wp_query;
	return $wp_query->current_post + 1 < $wp_query->post_count;
}

?>

<div class="job-archive-wrapper">

	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>

<?php $this->job_search_form(); ?>

	<?php if(have_posts()): ?>
	<div id="job-grid-container">
		<div class="job-grid-sizer"></div>
		<?php while( have_posts() ): the_post(); ?>
		<?php echo do_shortcode('[jbp-job-excerpt]'); ?>
		<?php endwhile; ?>
	</div>
	<?php else: ?>
	<h2>No Jobs Entered Yet</h2>
	<?php endif; ?>
	<?php echo $Jobs_Plus_Core->pagination(); ?>

</div><!-- .job-archive-wrapper -->

<?php if($wp_query->post_count > 0 ): ?>
<script type="text/javascript">
	jQuery(document).ready( function($){
		var $container = $("#job-grid-container");
		$container.masonry({
			itemSelector: ".job-excerpt",
			columnWidth: ".job-grid-sizer",
			gutter: 0
		});

		$(".job-show").ellipsis({row: 3 });
		$(".ellipsis").ellipsis({row: 4, char: '<a> . . .Read more</a>' });

		$(".job-excerpt").click( function(){
			var $this = $(this);
			var permalink = $this.data('permalink');
			window.location = permalink;
		});

	});
</script>
<?php endif; ?>




