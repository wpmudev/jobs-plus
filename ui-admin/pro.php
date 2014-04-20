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
	<?php $this->render_tabs('pro'); ?>
	<form action="#" method="post">
		<br />
		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php printf(__( '%s Status Options', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<input type="hidden" name="jbp[pro][edit_button]" value="1" />

					<!--
					<tr>
					<th>
					<label><?php printf(__('Show Edit Button on %s Form', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name) ?></label>
					</th>
					<td>
					<input type="hidden" name="jbp[pro][edit_button]" value="0" />
					<label><input type="checkbox" name="jbp[pro][edit_button]" value="1" <?php checked( $this->get_setting( 'pro->edit_button', '0' ) ) ?> /> <?php _e('Show edit button', JBP_TEXT_DOMAIN); ?></label>
					<br /><span class="description"><?php _e('Displays an edit button on the Pro form and Archive list. Some themes already have an edit button.', JBP_TEXT_DOMAIN); ?></span>
					</td>
					</tr>
					-->
					<tr>
						<th>
							<label><?php printf(__('Maximum %s Records per User', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name) ?></label>
						</th>
						<td>
							<input type="text" name="jbp[pro][max_records]" value="<?php echo intval($this->get_setting('pro->max_records', 1 ) );?>" size="2" />
							<br /><span class="description"><?php printf(__('Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php printf(__('%s Records per Page', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></label>
						</th>
						<td>
							<input type="text" name="jbp[pro][per_page]" value="<?php echo intval($this->get_setting('pro->per_page', 48 ) );?>" size="2" />
							<br /><span class="description"><?php printf(__('Maximum number of %s profiles per archive page.', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php printf(__('Newly Created %s Status Options', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></label>
						</th>
						<td>
							<label>
								<input type="hidden" name="jbp[pro][moderation][publish]" value="0" />
								<input type="checkbox" name="jbp[pro][moderation][publish]" value="1" <?php checked( $this->get_setting('pro->moderation->publish') ) ?> /> <?php _e('Published', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php printf(__('Allow members to publish %s themselves.', JBP_TEXT_DOMAIN), $this->pro_labels->name); ?></span>
							<br />

							<label>
								<input type="hidden" name="jbp[pro][moderation][pending]" value="0" />
								<input type="checkbox" name="jbp[pro][moderation][pending]" value="1" <?php checked( $this->get_setting('pro->moderation->pending') ) ?> /> <?php _e('Pending Review', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php printf(__('%s is pending review by an administrator.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></span>
							<br />

							<label>
								<input type="hidden" name="jbp[pro][moderation][draft]" value="0" />
								<input type="checkbox" name="jbp[pro][moderation][draft]" value="1" <?php checked( $this->get_setting('pro->moderation->draft') ) ?> /> <?php _e('Draft', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php _e('Allow members to save Drafts.', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e('Show Ratings in Comments', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<input type="hidden" name="jbp[pro][comment_ratings]" value="0" />
							<label><input type="checkbox" name="jbp[pro][comment_ratings]" value="1" <?php checked( $this->get_setting('comment_ratings') ) ?> /> <?php _e('Show comment authors rating of the pro', JBP_TEXT_DOMAIN); ?></label>
							<br /><span class="description"><?php printf(__('Displays the rating given by the author of the comment for a %s.', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></span>
						</td>
					</tr>

				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class="hndle"><span><?php printf(__( '%s Image Storage', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label><?php printf(__('Base path for %s image uploads', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></label>
						</th>
						<td>
							<?php echo untrailingslashit(WP_CONTENT_DIR); ?><input class="jbp-full" type="text" name="jbp[pro][upload_path]" value="<?php esc_attr_e($this->get_setting('pro->upload_path') );?>" size="40" />
							<br /><span class="description"><?php printf(__('Base disk path where any file uploaded with a %1$s entry are stored. The post id will be added to create individual directories for each user. Default is %2$s', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name,WP_CONTENT_DIR . '/uploads/pro/') ; ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e('Maximum Gallery Images', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<input type="text" name="jbp[pro][max_gallery]" value="<?php esc_attr_e($this->get_setting('pro->max_gallery', 4) );?>" size="2" />
							<br /><span class="description"><?php printf(__('Maximum number of images that can be uploaded to the %s portfolio gallery. Default is 4', JBP_TEXT_DOMAIN), $this->pro_labels->name); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php printf(__('%s image size', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></label>
						</th>
						<td>
							<input type="text" name="jbp[pro][thumb_width]" value="<?php esc_attr_e($this->get_setting('pro->thumb_width', '160'), '' );?>" size="2" /> <?php _e('width', JBP_TEXT_DOMAIN); ?>
							<input type="text" name="jbp[pro][thumb_height]" value="<?php esc_attr_e($this->get_setting('pro->thumb_height', '120') );?>" size="2" /> <?php _e('height', JBP_TEXT_DOMAIN); ?>
							<br /><span class="description"><?php printf(__('Size of the %s thumb-image. Defaults to 160x120.', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Notification Settings', JBP_TEXT_DOMAIN ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="disable_contact_form"><?php _e( 'Disable Contact Form:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[pro][disable_contact_form]" value="0" />
							<input type="checkbox" id="disable_contact_form" name="jbp[pro][disable_contact_form]" value="1" <?php checked( $this->get_setting('pro->disable_contact_form') ); ?> />
							<span class="description"><?php _e( 'disable contact form', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="cc_admin"><?php _e( 'CC the Administrator:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[pro][cc_admin]" value="0" />
							<input type="checkbox" id="cc_admin" name="jbp[pro][cc_admin]" value="1" <?php checked( $this->get_setting('pro->cc_admin') ); ?> />
							<span class="description"><?php _e( 'cc the administrator', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="cc_sender"><?php _e( 'CC the Sender:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[pro][cc_sender]" value="0" />
							<input type="checkbox" id="cc_sender" name="jbp[pro][cc_sender]" value="1" <?php checked( $this->get_setting('pro->cc_sender') ); ?> />
							<span class="description"><?php _e( 'cc the sender', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="email_subject"><?php _e( 'Email Subject:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input class="jbp-full" type="text" id="email_subject" name="jbp[pro][email_subject]" value="<?php echo $this->get_setting('pro->email_subject'); ?>" />
							<br />
							<span class="description"><?php _e( 'Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="field_image_req"><?php _e( 'Email Content:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<textarea class="jbp-full" id="email_content" name="jbp[pro][email_content]" rows="12" wrap="hard" ><?php
								echo esc_textarea( $this->get_setting('pro->email_content') );
							?></textarea>
							<br />
							<span class="description"><?php _e( 'Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class='hndle'><span><?php _e( 'Terms of Service Text', JBP_TEXT_DOMAIN ) ?></span></h3>
			<div class="inside">

				<table class="form-table">
					<tr>
						<th>
							<label for="tos_txt"><?php _e('Terms of Service Text', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<textarea class="jbp-full" name="jbp[pro][tos_txt]" id="tos_txt" rows="15" placeholder="<?php esc_attr_e('Terms of Service', JBP_TEXT_DOMAIN); ?>" ><?php echo esc_textarea( $this->get_setting('pro->tos_txt','') ); ?></textarea>
							<br />
							<span class="description"><?php _e( 'Text for "Terms of Service"', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field('jobs-plus-settings'); ?>
			<input type="hidden" name="jobs-plus-settings" value="1" />
			<input type="submit" class="button-primary" name="pro-settings" value="<?php _e('Save Changes', JBP_TEXT_DOMAIN);?>">
		</p>
	</form>
</div>

