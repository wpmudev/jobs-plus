<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_Settings_Job extends JobsExperts_Framework_Render {
	public function __construct( $data ) {
		parent::__construct( $data );
	}

	public function _to_html() {
		$form  = $this->form;
		$model = $this->model;
		$plugin      = JobsExperts_Plugin::instance();
		$job_labels  = JobsExperts_Plugin::instance()->get_job_type()->labels;
		$pro_labels  = JobsExperts_Plugin::instance()->get_expert_type()->labels;
		?>
		<div class="page-header">
			<h3 class="hndle">
				<span><?php printf( __( '%s Status Options', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></span>
			</h3>
		</div>
		<table class="form-table">
			<tr>
				<th>
					<label><?php printf( esc_html__( 'Maximum %s Records per User', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></label>
				</th>
				<td>
					<?php echo $form->textField( $model, 'job_max_records' ) ?>
					<br /><span class="description"><?php printf( esc_html__( 'Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></span>
				</td>
			</tr>

			<tr>
				<th>
					<label><?php printf( esc_html__( '%s Records per Page', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></label>
				</th>
				<td>
					<?php echo $form->textField( $model, 'job_per_page' ) ?>
					<br /><span class="description"><?php printf( esc_html__( 'Maximum number of %s records per archive page.', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></span>
				</td>
			</tr>

			<tr>
				<th>
					<label><?php esc_html_e( 'Use Budget Range', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<label>
						<?php $form->hiddenField( $model, 'job_budget_range', array( 'value' => 0 ) ) ?>
						<?php echo $form->checkBox( $model, 'job_budget_range', array( 'value' => 1 ) ) ?>
						<?php esc_html_e( 'Use Min and Max budget fields', JBP_TEXT_DOMAIN ); ?>
					</label>
					<br /><span class="description"><?php esc_html_e( 'Displays both minimum and maximum budget fields.', JBP_TEXT_DOMAIN ); ?></span>
					<br />
				</td>
			</tr>
			<tr>
				<th>
					<label><?php printf( esc_html__( 'Newly Created %s Status Options', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></label>
				</th>
				<td>
					<label>
						<?php echo $form->radioButton( $model, 'job_new_job_status', 'publish' ) ?>
						<?php esc_html_e( 'Published', JBP_TEXT_DOMAIN ); ?>
					</label>
					<br /><span class="description"><?php printf( esc_html__( 'Allow members to publish %s themselves.', JBP_TEXT_DOMAIN ), $job_labels->name ); ?></span>
					<br />
					<br />

					<label>
						<?php echo $form->radioButton( $model, 'job_new_job_status', 'review' ) ?>
						<?php esc_html_e( 'Pending Review', JBP_TEXT_DOMAIN ); ?>
					</label>
					<br /><span class="description"><?php printf( esc_html__( '%s is pending review by an administrator.', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></span>
					<br />
					<br />

					<label>
						<?php $form->hiddenField( $model, 'job_allow_draft', array( 'value' => 0 ) ) ?>
						<?php echo $form->checkBox( $model, 'job_allow_draft', array( 'value' => 1 ) ) ?>
						<?php esc_html_e( 'Draft', JBP_TEXT_DOMAIN ); ?>
					</label>
					<br /><span class="description"><?php esc_html_e( 'Allow members to save Drafts.', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>

		</table>
		<div class="page-header">
			<h3 class="hndle">
				<span><?php printf( esc_html__( '%s Image Storage', JBP_TEXT_DOMAIN ), $job_labels->singular_name ); ?></span>
			</h3>
		</div>
		<table class="form-table">
			<tr>
				<th>
					<label><?php esc_html_e( 'Maximum Gallery Images', JBP_TEXT_DOMAIN ) ?></label>
				</th>
				<td>
					<?php echo $form->textField( $model, 'job_sample_size' ) ?>
					<br /><span class="description"><?php printf( esc_html__( 'Maximum number of images that can be uploaded to the $s portfolio gallery. Default is 4', JBP_TEXT_DOMAIN ), $job_labels->name ); ?></span>
				</td>
			</tr>
		</table>

		<div class="page-header">
			<h3 class='hndle'><span><?php esc_html_e( 'Notification Settings', JBP_TEXT_DOMAIN ); ?></span></h3>
		</div>
		<table class="form-table">
			<tr>
				<th>
					<label for="disable_contact_form"><?php esc_html_e( 'Disable Contact Form:', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $form->hiddenField( $model, 'job_contact_form', array( 'value' => 0 ) ) ?>
					<?php $form->checkBox( $model, 'job_contact_form', array( 'value' => 1 ) ) ?>
					<span class="description"><?php esc_html_e( 'disable contact form', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="use_captcha"><?php esc_html_e( 'Use CAPTCHA:', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $form->hiddenField( $model, 'job_contact_form_captcha', array( 'value' => 0 ) ) ?>
					<?php $form->checkBox( $model, 'job_contact_form_captcha', array( 'value' => 1 ) ) ?>
					<span class="description"><?php esc_html_e( 'use the captcha image on the email form', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>

			<tr>
				<th>
					<label for="cc_admin"><?php esc_html_e( 'CC the Administrator:', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $form->hiddenField( $model, 'job_cc_admin', array( 'value' => 0 ) ) ?>
					<?php $form->checkBox( $model, 'job_cc_admin', array( 'value' => 1 ) ) ?>
					<span class="description"><?php esc_html_e( 'cc the administrator', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="cc_sender"><?php esc_html_e( 'CC the Sender:', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $form->hiddenField( $model, 'job_cc_sender', array( 'value' => 0 ) ) ?>
					<?php $form->checkBox( $model, 'job_cc_sender', array( 'value' => 1 ) ) ?>
					<span class="description"><?php esc_html_e( 'cc the sender', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="email_subject"><?php esc_html_e( 'Email Subject:', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php echo $form->textField( $model, 'job_email_subject', array( 'class' => 'large-text' ) ); ?>
					<br />
					<span class="description"><?php esc_html_e( 'Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label for="field_image_req"><?php esc_html_e( 'Email Content:', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php echo $form->textArea( $model, 'job_email_content', array( 'class' => 'large-text','rows'=>5 ) ); ?>

					<br />
					<span class="description"><?php esc_html_e( 'Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>
		</table>
	<?php
	}
}