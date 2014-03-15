<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

?>

<div class="wrap">
	<?php screen_icon('jobs-plus'); ?>
	<h2><?php printf( __('Jobs+ Settings %s', JBP_TEXT_DOMAIN), JOBS_PLUS_VERSION );?></h2>
	<?php $this->render_tabs(); ?>
	<form action="#" method="post">
		<br />
		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php _e( 'General Options', JBP_TEXT_DOMAIN ) ?></span></h3>
			<div class="inside">
				<table class="form-table">

					<tr>
						<th>
							<label><?php printf( __('Enable %s Certification', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ); ?></label>
						</th>
						<td>
							<input type="hidden" name="jbp[general][use_certification]" value="0" />
							<label><input type="checkbox" name="jbp[general][use_certification]" value="1" <?php checked( $this->get_setting('general->use_certification', '0') ) ?> /> <?php printf( __('Enable %s Certification', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ); ?></label>
							<br /><span class="description"><?php _e('Enable the certification checkbox on the users profile page so tha the Administrator can mark them Certified.', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label for="moderation"><?php _e('Certification Label', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<input type="text" name="jbp[general][certification]" value="<?php echo $this->get_setting('general->certification', __('Jobs+ Certified', JBP_TEXT_DOMAIN) );?>" size="60"/>
							<br /><span class="description"><?php printf(__('%s Certification Label.', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="moderation"><?php _e('Currency Symbol', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<input type="text" name="jbp[general][currency]" value="<?php echo $this->get_setting('general->currency', __('$', JBP_TEXT_DOMAIN) );?>" size="10"/>
							<br /><span class="description"><?php _e('Currency symbol for this site. Default "$"', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field('jobs-plus-settings'); ?>
			<input type="hidden" name="jobs-plus-settings" value="1" />
			<input type="submit" class="button-primary" name="general-settings" value="<?php _e('Save Changes', JBP_TEXT_DOMAIN);?>">
		</p>
	</form>
</div>

