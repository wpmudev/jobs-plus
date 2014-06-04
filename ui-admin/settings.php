<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

?>
<div class="wrap">
	<?php $this->render_tabs(); ?>
	<h2><?php esc_html_e('Jobs+ General Settings', JBP_TEXT_DOMAIN);?></h2>
	<form action="#" method="post">
		<br />
		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php esc_html_e( 'General Options', JBP_TEXT_DOMAIN ) ?></span></h3>
			<div class="inside">
				<table class="form-table">

					<tr>
						<th>
							<label><?php printf( esc_html__('Enable %s Certification', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ); ?></label>
						</th>
						<td>
							<input type="hidden" name="jbp[general][use_certification]" value="0" />
							<label><input type="checkbox" name="jbp[general][use_certification]" value="1" <?php checked( $this->get_setting('general->use_certification', '0') ) ?> /> <?php printf( esc_html__('Enable %s Certification', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ); ?></label>
							<br /><span class="description"><?php esc_html_e('Enable the certification checkbox on the users profile page so that the Administrator can mark them Certified.', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label for="jbp[general][certification]"><?php esc_html_e('Certification Label', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<input type="text" name="jbp[general][certification]" value="<?php echo esc_attr($this->get_setting('general->certification', __('Jobs+ Certified', JBP_TEXT_DOMAIN) ) );?>" size="60"/>
							<br /><span class="description"><?php printf(esc_html__('%s Certification Label.', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php esc_html_e( 'New User Registration Options', JBP_TEXT_DOMAIN ) ?></span></h3>
			<div class="inside">
				<table class="form-table">

					<tr>
						<th>
							<label><?php esc_html_e('Enable Fast Registration', JBP_TEXT_DOMAIN ); ?></label>
						</th>
						<td>
							<input type="hidden" name="jbp[general][use_fast_register]" value="0" />
							<label><input type="checkbox" name="jbp[general][use_fast_register]" value="1" <?php checked( $this->get_setting('general->use_fast_register', '0') ) ?> /> <?php esc_html_e('Enable Fast Registration', JBP_TEXT_DOMAIN ); ?></label>
							<br /><span class="description"><?php esc_html_e('Enable Fast Registration, displays a popup registration form which allows either Login or Registration. When registering, no email confirmation is needed and they are immediately logged in.', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php esc_html_e('CAPTCHA in Registration', JBP_TEXT_DOMAIN ); ?></label>
						</th>
						<td>
							<input type="hidden" name="jbp[general][use_register_captcha]" value="0" />
							<label><input type="checkbox" name="jbp[general][use_register_captcha]" value="1" <?php checked( $this->get_setting('general->use_register_captcha', '0') ) ?> /> <?php esc_html_e('Enable CAPTCHA in Registration', JBP_TEXT_DOMAIN ); ?></label>
							<br /><span class="description"><?php esc_html_e('Enable a CAPTCHA image on the Fast Registration form.', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>

				</table>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field('jobs-plus-settings'); ?>
			<input type="hidden" name="jobs-plus-settings" value="1" />
			<input type="submit" class="button-primary" name="general-settings" value="<?php echo esc_attr('Save Changes', JBP_TEXT_DOMAIN);?>">
		</p>
	</form>
</div>

