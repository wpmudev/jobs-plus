<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $CustomPress_Core;

?>
<div class="wrap">
	<?php $this->render_tabs('job'); ?>
	<h2><?php printf( esc_html__('Jobs+ %s Settings', JBP_TEXT_DOMAIN), $this->job_labels->singular_name );?></h2>
	<form action="#" method="post">
		<br />
		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php printf(__( '%s Status Options', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<input type="hidden" name="jbp[job][edit_button]" value="1" />
					<!--
					<tr>
					<th>
					<label><?php printf(__('Show Edit Button on %s Form', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></label>
					</th>
					<td>
					<input type="hidden" name="jbp[job][edit_button]" value="0" />
					<label><input type="checkbox" name="jbp[job][edit_button]" value="1" <?php checked( $this->get_setting('job->edit_button', '0') ) ?> /> <?php esc_html_e('Show edit button', JBP_TEXT_DOMAIN); ?></label>
					<br /><span class="description"><?php printf(__('Displays an edit button on the %s form and Archive list. Some themes already have an edit button.', JBP_TEXT_DOMAIN), $this->job_labels->singular_name); ?></span>
					</td>
					</tr>
					-->
					<tr>
						<th>
							<label><?php printf(esc_html__('Maximum %s Records per User', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></label>
						</th>
						<td>
							<input type="text" name="jbp[job][max_records]" value="<?php echo intval($this->get_setting('job->max_records', 1) );?>" size="2" />
							<br /><span class="description"><?php printf(esc_html__('Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN), $this->job_labels->singular_name); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php printf(esc_html__('%s Records per Page', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></label>
						</th>
						<td>
							<input type="text" name="jbp[job][per_page]" value="<?php echo intval($this->get_setting('job->per_page', 20) );?>" size="2" />
							<br /><span class="description"><?php printf(esc_html__('Maximum number of %s records per archive page.', JBP_TEXT_DOMAIN), $this->job_labels->singular_name); ?></span>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php esc_html_e('Use Budget Range', JBP_TEXT_DOMAIN ); ?></label>
						</th>
						<td>
							<label>
								<input type="hidden" name="jbp[job][use_budget_range]" value="0" />
								<input type="checkbox" name="jbp[job][use_budget_range]" value="1" <?php checked( $this->get_setting('job->use_budget_range') ) ?> /> <?php esc_html_e('Use Min and Max budget fields', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php esc_html_e('Displays both minimum and maximum budget fields.', JBP_TEXT_DOMAIN); ?></span>
							<br />
						</td>
					</tr>	
					<tr>
						<th>
							<label><?php printf(esc_html__('Newly Created %s Status Options', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></label>
						</th>
						<td>
							<label>
								<input type="radio" name="jbp[job][moderation][publish]" value="1" <?php checked( $this->get_setting('job->moderation->publish', 1) ) ?> /> <?php esc_html_e('Published', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php printf(esc_html__('Allow members to publish %s themselves.', JBP_TEXT_DOMAIN), $this->job_labels->name); ?></span>
							<br />
							<br />

							<label>
								<input type="radio" name="jbp[job][moderation][publish]" value="0" <?php checked( !$this->get_setting('job->moderation->publish', 1) ) ?> /> <?php esc_html_e('Pending Review', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php printf(esc_html__('%s is pending review by an administrator.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></span>
							<br />
							<br />

							<label>
								<input type="hidden" name="jbp[job][moderation][draft]" value="0" />
								<input type="checkbox" name="jbp[job][moderation][draft]" value="1" <?php checked( $this->get_setting('job->moderation->draft', 1) ) ?> /> <?php esc_html_e('Draft', JBP_TEXT_DOMAIN); ?>
							</label>
							<br /><span class="description"><?php esc_html_e('Allow members to save Drafts.', JBP_TEXT_DOMAIN); ?></span>
						</td>
					</tr>

				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class="hndle"><span><?php printf(esc_html__( '%s Image Storage', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label><?php printf(esc_html__('Base path for %s image uploads', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></label>
						</th>
						<td>
							<?php echo untrailingslashit(WP_CONTENT_DIR); ?><input class="jbp-full" type="text" name="jbp[job][upload_path]" value="<?php echo esc_attr($this->get_setting('job->upload_path') );?>" size="60" />
							<br /><span class="description"><?php printf(esc_html__('Base disk path where any file uploaded with a %s entry are stored. The post id will be added to create individual directories for each user. Default is %s', JBP_TEXT_DOMAIN), $this->job_labels->singular_name, WP_CONTENT_DIR . '/uploads/job/'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php esc_html_e('Maximum Gallery Images', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<input type="text" name="jbp[job][max_gallery]" value="<?php echo esc_attr($this->get_setting('job->max_gallery', '4') );?>" size="2" />
							<br /><span class="description"><?php printf(esc_html__('Maximum number of images that can be uploaded to the $s portfolio gallery. Default is 4', JBP_TEXT_DOMAIN), $this->job_labels->name); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php printf(esc_html__('%s image size', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name); ?></label>
						</th>
						<td>
							<input type="text" name="jbp[job][thumb_width]" value="<?php echo esc_attr($this->get_setting('job->thumb_width', '160'), '' );?>" size="2" /> <?php esc_html_e('width', JBP_TEXT_DOMAIN); ?>
							<input type="text" name="jbp[job][thumb_height]" value="<?php echo esc_attr($this->get_setting('job->thumb_height', '120') );?>" size="2" /> <?php esc_html_e('height', JBP_TEXT_DOMAIN); ?>
							<br /><span class="description"><?php printf(esc_html__('Size of the %s thumb-image. Defaults to 160x120.', JBP_TEXT_DOMAIN), $this->job_labels->singular_name); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
<!-- Capabilities settings -->		
		
		<div class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'Role Capabilities Settings', $this->text_domain ) ?></h3>
			<div class="inside">
			<table class="form-table">
				<tr>
					<th>
						<label for="roles"><?php esc_html_e( 'Assign Capabilities', $this->text_domain ) ?></label>
						<img id="ajax-loader" alt="ajax-loader" src="<?php echo set_url_scheme(admin_url() ) . 'images/loading.gif' ?>" />
					</th>
					<td>

						<select id="roles" name="roles">
							<?php
							global $wp_roles;
							$role_obj = $wp_roles->get_role( 'administrator' );
							foreach ( $wp_roles->role_names as $role => $name ): ?>
							<option value="<?php echo $role; ?>"><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
						<br /><span class="description"><?php echo sprintf(__('Select a role to which you want to assign %s capabilities.', $this->text_domain), $this->job_obj->capability_type ); ?></span>
						<br /><br />
						<div id="capabilities">
							<?php

							$all_caps = $CustomPress_Core->all_capabilities('jbp_job');
							if( ! empty($all_caps) ){
								sort($all_caps);
								foreach ( $all_caps as $capability ):
									$description = ucfirst(str_replace('_', ' ',$capability));
									?>
									<input type="checkbox" name="capabilities[<?php echo $capability; ?>]" id="<?php echo $capability; ?>" value="1" />
									<label for="<?php echo $capability; ?>"><span class="description"><?php echo $description; ?></span></label>
									<br />
									<?php
								endforeach;
							}
							?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					</th>
					<td>
						<input type="hidden" name="action" value="jbp_save_caps" />
						<input type="hidden" id="post_type" name="post_type" value="jbp_job" />
						<input type="button" class="button" id="save_roles" name="save_roles" value="Save this Role's Changes" />
					</td>
				</tr>
			</table>
		</div>
		</div>
		

		<div class="postbox">
			<h3 class='hndle'><span><?php esc_html_e( 'Notification Settings', JBP_TEXT_DOMAIN ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="disable_contact_form"><?php esc_html_e( 'Disable Contact Form:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[job][disable_contact_form]" value="0" />
							<input type="checkbox" id="disable_contact_form" name="jbp[job][disable_contact_form]" value="1" <?php checked( $this->get_setting('job->disable_contact_form', 0) ); ?> />
							<span class="description"><?php esc_html_e( 'disable contact form', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="use_captcha"><?php esc_html_e( 'Use CAPTCHA:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[job][use_captcha]" value="0" />
							<input type="checkbox" id="use_captcha" name="jbp[job][use_captcha]" value="1" <?php checked( $this->get_setting('job->use_captcha', 0) ); ?> />
							<span class="description"><?php esc_html_e( 'use the captcha image on the email form', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="cc_admin"><?php esc_html_e( 'CC the Administrator:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[job][cc_admin]" value="0" />
							<input type="checkbox" id="cc_admin" name="jbp[job][cc_admin]" value="1" <?php checked( $this->get_setting('job->cc_admin', 0) ); ?> />
							<span class="description"><?php esc_html_e( 'cc the administrator', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="cc_sender"><?php esc_html_e( 'CC the Sender:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input type="hidden" name="jbp[job][cc_sender]" value="0" />
							<input type="checkbox" id="cc_sender" name="jbp[job][cc_sender]" value="1" <?php checked( $this->get_setting('job->cc_sender', 0) ); ?> />
							<span class="description"><?php esc_html_e( 'cc the sender', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="email_subject"><?php esc_html_e( 'Email Subject:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<input class="jbp-full" type="text" id="email_subject" name="jbp[job][email_subject]" value="<?php echo esc_attr( $this->get_setting( 'job->email_subject' ) ); ?>" />
							<br />
							<span class="description"><?php esc_html_e( 'Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="field_image_req"><?php esc_html_e( 'Email Content:', JBP_TEXT_DOMAIN ); ?></label></th>
						<td>
							<textarea class="jbp-full" id="email_content" name="jbp[job][email_content]" rows="12" wrap="hard" ><?php
								echo esc_textarea( $this->get_setting( 'job->email_content' ) );
							?></textarea>
							<br />
							<span class="description"><?php esc_html_e( 'Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
<!--
		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class='hndle'><span><?php esc_html_e( 'Terms of Service Text', JBP_TEXT_DOMAIN ) ?></span></h3>
			<div class="inside">

				<table class="form-table">
					<tr>
						<th>
							<label for="jbp[job][tos_txt]"><?php esc_html_e('Terms of Service Text', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<textarea class="jbp-full" name="jbp[job][tos_txt]" id="tos_txt" rows="15" cols="125" placeholder="<?php echo esc_attr( 'Terms of Service', JBP_TEXT_DOMAIN); ?>" ><?php echo esc_textarea($this->get_setting('job->tos_txt', '' ) ); ?></textarea>
							<br />
							<span class="description"><?php esc_html_e( 'Text for "Terms of Service"', JBP_TEXT_DOMAIN ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
-->
		<p class="submit">
			<?php wp_nonce_field('jobs-plus-settings'); ?>
			<input type="hidden" name="jobs-plus-settings" value="1" />
			<input type="submit" class="button-primary" name="job-settings" value="<?php echo esc_attr('Save Changes', JBP_TEXT_DOMAIN);?>">
		</p>
	</form>
</div>

