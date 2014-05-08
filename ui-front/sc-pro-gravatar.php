<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post;
$rating = 0;
?>
<section class="pro-gravatar">
	<div class="pro-title"><?php the_title() ?></div>
	<div class="jbp-gravatar">
		<div class="jbp-gravatar-border group">
			<?php echo get_avatar_or_gravatar($post->post_author, do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]') ,160); ?>
		</div>
	</div>
	<div class="jbp-stats">
		<?php if (current_user_can( EDIT_PRO, $post->ID )) { ?>
		<a href="http://gravatar.com/emails/" target="_blank" class="gravatar_link">Change gravatar</a>
		<?php } ?>
		<div class="rating"><?php echo the_jbp_rating(); ?></div>
	</div>

	<div class="pro-certify group">
	</div>
</section>
