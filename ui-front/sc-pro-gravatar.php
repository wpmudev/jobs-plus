<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post;
$rating = 0;
?>
<div class="pro-gravatar">
	<div class="jbp-gravatar">
		<div class="jbp-gravatar-border jbp-clear">
			<?php echo get_avatar_or_gravatar($post->post_author, do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]') ,160); ?>
		</div>
	</div>
	<div class="jbp-stats">
		<div class="pro-title"><?php the_title() ?></div>
		<div class="rating"><?php echo the_rating(); ?><br /><?php echo $rating; ?></div>
	</div>

	<div class="pro-certify clearfix">
	</div>
</div>
