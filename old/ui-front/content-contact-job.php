<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $Jobs_Plus_Core;

$this->no_comments();

$params = stripslashes_deep($_POST);

$post_id = (int) empty($_POST['post_id']) ? $post->ID : $params['post_id'];
$post = get_post($post_id);

$user = wp_get_current_user();

wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('jquery-validate');

?>

<div id="post-jbp-job">
	<!--<h3><?php /*esc_html_e('Contact ', JBP_TEXT_DOMAIN); the_title()*/?></h3>-->

	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>


	<form action="#" method="post" id="jbp-job-contact">
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
		<input type="hidden" name="username" value="<?php echo esc_attr( $user->user_login ); ?>" />
		<input type="hidden" name="title" value="<?php echo esc_attr( $post->post_title ); ?>" />

		<div class="editfield">
			<label><?php esc_html_e('Your name: ', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="name" value="<?php echo esc_attr( $this->get_key('name','', $params) ); ?>" class="required"/>
		</div>

		<div class="editfield">
			<label><?php esc_html_e('Your email: ', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="email" value="<?php echo esc_attr( $this->get_key('email','', $params) ); ?>" class="required email" />
		</div>

		<div class="editfield">
			<label><?php esc_html_e('Content: ', JBP_TEXT_DOMAIN); ?></label>
			<textarea name="content" rows="5" class="required"><?php echo esc_textarea($this->get_key('name','', $params) ); ?></textarea>
		</div>

		<?php if( $this->get_setting('job->use_captcha', 0) ): ?>
		<div class="editfield">
			<label for="jbp_random_value"><?php esc_attr_e( 'Security image (required)', JBP_TEXT_DOMAIN ); ?></label>
			<p>
				<span class="captcha"><img src="<?php echo esc_attr(admin_url('admin-ajax.php?action=jbp-captcha') );?>" /></span>
				<span><input type="text" class="required" id="jbp_random_value" name ="jbp_random_value" value="" size="8" /></span>
				<br/><span class="description"><?php esc_html_e( 'Enter the characters from the image.', JBP_TEXT_DOMAIN ); ?></span>
			</p>
		</div>
		<?php endif;?>

		<div class="jobs-nav">
			<?php wp_nonce_field( 'verify' ); ?>
			<button class="jbp-button" type="submit" name="jbp-job-contact" value="1"><?php esc_html_e( 'Submit', JBP_TEXT_DOMAIN ); ?></button>
			<button class="jbp-button" type="button" onclick="location.href='<?php echo get_permalink(get_the_ID()); ?>'"><?php esc_html_e( 'Cancel', JBP_TEXT_DOMAIN ); ?></button>
		</div>
	</form>
	<script type="text/javascript"> jQuery(document).ready( function($){ $('#jbp-job-contact').validate(); }); </script>
</div>
