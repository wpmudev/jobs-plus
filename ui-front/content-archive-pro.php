<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

wp_enqueue_script('masonry');
wp_enqueue_script('imagesloaded');
$no_pros = false;
global $Jobs_Plus_Core;
$page_id = get_the_ID();
if ( $page_id == $Jobs_Plus_Core->pro_empty_page_id ) {
	$no_pros = true;
}
?>
<div class="pro-archive-wrapper">

	<div class="group">
		<?php if(dynamic_sidebar('pro-archive-widget') ) : else: endif; ?>
	</div>
	
	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>
	<?php echo do_shortcode('[jbp-expert-search]'); ?>

	<?php if( have_posts() && $no_pros==false): ?>

	<div id="pro-grid-container">
		<div class="pro-grid-sizer"></div>
		<div class="pro-gutter-sizer"></div>

		<?php while( have_posts() ): the_post(); ?>

		<?php //if($wp_query->current_post > 4): ?>
		<?php //echo do_shortcode('[jbp-expert-archive size="small"]'); ?>

		<?php //elseif($wp_query->current_post > 2): ?>
		<?php //echo do_shortcode('[jbp-expert-archive size="medium"]'); ?>

		<?php //else: ?>
		<?php echo do_shortcode('[jbp-expert-archive size="large"]'); ?>
		<?php //endif; ?>

		<?php endwhile; ?><!-- have_posts -->
		<?php else: ?><!-- have_posts -->
		<h2><?php printf( __('No %s Found', JBP_TEXT_DOMAIN), $this->pro_labels->name ); ?></h2>

		<?php endif; ?><!-- have_posts -->
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($){
		var $container = $("#pro-grid-container");
		$container.imagesLoaded( function(){
			$container.masonry({
				itemSelector: ".pro-archive"
				,columnWidth: ".pro-grid-sizer"
				,gutter: ".pro-gutter-sizer"
				,containerStyle: null
				//,stamp: $(".pros-stamp")
			});
		});

		$(".pro-archive").click( function(){
			window.location = $(this).data('permalink');
		});

		$(".pro-archive-image").mouseover( function(){
			$(this).next(".pro-more").show();
		});

		$(".pro-more").mouseleave( function(){
			$(".pro-more").hide();
		});


	});
</script>

