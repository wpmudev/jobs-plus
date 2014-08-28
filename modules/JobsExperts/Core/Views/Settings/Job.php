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
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php printf(esc_html__('Maximum %s Records per User', JBP_TEXT_DOMAIN), $job_labels->singular_name) ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'job_max_records') ?>
                <p class="help-block"><?php printf(esc_html__('Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN), $job_labels->singular_name); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php printf(esc_html__('%s Records per Page', JBP_TEXT_DOMAIN), $job_labels->singular_name); ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'job_per_page') ?>
                <p class="help-block"><?php printf(esc_html__('Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN), $job_labels->singular_name); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php esc_html_e( 'Use Budget Range', JBP_TEXT_DOMAIN ); ?>
            </label>
            <div class="col-md-9">
                <?php $form->hiddenField( $model, 'job_budget_range', array( 'value' => 0 ) ) ?>
                <?php echo $form->checkBox( $model, 'job_budget_range', array( 'value' => 1 ) ) ?>
                <?php esc_html_e( 'Use Min and Max budget fields', JBP_TEXT_DOMAIN ); ?>
                <p class="help-block"><?php esc_html_e( 'Displays both minimum and maximum budget fields.', JBP_TEXT_DOMAIN ); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php printf(esc_html__('Newly Created %s Status Options', JBP_TEXT_DOMAIN), $job_labels->singular_name); ?>
            </label>

            <div class="col-md-9">
                <label><?php echo $form->radioButton($model, 'job_new_job_status', 'publish') ?>

                    <?php esc_html_e('Published', JBP_TEXT_DOMAIN); ?></label>

                <p class="help-block">
                    <?php printf(esc_html__('Allow members to publish %s themselves.', JBP_TEXT_DOMAIN), $job_labels->name); ?>
                </p>
                <label> <?php echo $form->radioButton($model, 'job_new_job_status', 'pending') ?>
                    <?php esc_html_e('Pending Review', JBP_TEXT_DOMAIN); ?></label>

                <p class="help-block">
                    <?php printf(esc_html__('%s is pending review by an administrator.', JBP_TEXT_DOMAIN), $job_labels->singular_name); ?>
                </p>
                <label>
                    <?php $form->hiddenField($model, 'job_allow_draft', array('value' => 0)) ?>
                    <?php echo $form->checkBox($model, 'job_allow_draft', array('value' => 1)) ?>
                    <?php esc_html_e('Draft', JBP_TEXT_DOMAIN); ?>
                </label>

                <p class="help-block">
                    <?php esc_html_e('Allow members to save Drafts.', JBP_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="page-header">
            <h3 class="hndle">
                <span><?php printf(esc_html__('%s Image Storage', JBP_TEXT_DOMAIN), $job_labels->singular_name); ?></span>
            </h3>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php esc_html_e('Maximum Gallery Images', JBP_TEXT_DOMAIN) ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'job_sample_size') ?>
                <p class="help-block">
                    <?php printf(esc_html__('Maximum number of images that can be uploaded to the %s portfolio gallery. Default is 4', JBP_TEXT_DOMAIN), $job_labels->name); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="page-header">
            <h3 class='hndle'><span><?php esc_html_e('Notification Settings', JBP_TEXT_DOMAIN); ?></span></h3>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php esc_html_e('Disable Contact Form:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <label class="text-muted" style="font-weight: normal">
                    <?php $form->hiddenField($model, 'job_contact_form', array('value' => 0)) ?>
                    <?php $form->checkBox($model, 'job_contact_form', array('value' => 1)) ?>
                    <?php esc_html_e('disable contact form', JBP_TEXT_DOMAIN); ?>
                </label>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php esc_html_e('CC the Administrator:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <label class="text-muted" style="font-weight: normal">
                    <?php $form->hiddenField($model, 'job_cc_admin', array('value' => 0)) ?>
                    <?php $form->checkBox($model, 'job_cc_admin', array('value' => 1)) ?>
                    <?php esc_html_e('cc the administrator', JBP_TEXT_DOMAIN); ?>
                </label>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php esc_html_e('Email Subject:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'job_email_subject', array('class' => 'large-text')); ?>
                <p class="help-block">
                    <?php esc_html_e('Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php esc_html_e('Email Content:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textArea($model, 'job_email_content', array('class' => 'large-text', 'rows' => 5)); ?>
                <p class="help-block">
                    <?php esc_html_e('Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
	<?php
	}
}