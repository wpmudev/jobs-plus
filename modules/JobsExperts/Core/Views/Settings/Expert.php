<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Views_Settings_Expert extends JobsExperts_Framework_Render
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        $form = $this->form;
        $model = $this->model;
        $plugin = JobsExperts_Plugin::instance();
        $job_labels = JobsExperts_Plugin::instance()->get_job_type()->labels;
        $pro_labels = JobsExperts_Plugin::instance()->get_expert_type()->labels;
        ?>
        <div class="page-header">
            <h3 class="hndle">
                <span><?php printf(esc_html__('%s Status Options', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?></span>
            </h3>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php printf(esc_html__('Maximum %s Records per User', JBP_TEXT_DOMAIN), $pro_labels->singular_name) ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'expert_max_records') ?>
                <p class="help-block"><?php printf(esc_html__('Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php printf(esc_html__('%s Records per Page', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'expert_per_page') ?>
                <p class="help-block"><?php printf(esc_html__('Maximum number of %s profiles for each user.', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php printf(esc_html__('Newly Created %s Status Options', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?>
            </label>

            <div class="col-md-9">
                <label><?php echo $form->radioButton($model, 'expert_new_expert_status', 'publish') ?>

                    <?php _e('Published', JBP_TEXT_DOMAIN); ?></label>

                <p class="help-block">
                    <?php printf(esc_html__('Allow members to publish %s themselves.', JBP_TEXT_DOMAIN), $pro_labels->name); ?>
                </p>
                <label> <?php echo $form->radioButton($model, 'expert_new_expert_status', 'pending') ?>
                    <?php _e('Pending Review', JBP_TEXT_DOMAIN); ?></label>

                <p class="help-block">
                    <?php printf(esc_html__('%s is pending review by an administrator.', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?>
                </p>
                <label>
                    <?php $form->hiddenField($model, 'expert_allow_draft', array('value' => 0)) ?>
                    <?php echo $form->checkBox($model, 'expert_allow_draft', array('value' => 1)) ?>
                    <?php _e('Draft', JBP_TEXT_DOMAIN); ?>
                </label>

                <p class="help-block">
                    <?php _e('Allow members to save Drafts.', JBP_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="page-header">
            <h3 class="hndle">
                <span><?php printf(esc_html__('%s Image Storage', JBP_TEXT_DOMAIN), $pro_labels->singular_name); ?></span>
            </h3>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php _e('Maximum Gallery Images', JBP_TEXT_DOMAIN) ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'expert_sample_size') ?>
                <p class="help-block">
                    <?php printf(esc_html__('Maximum number of images that can be uploaded to the %s portfolio gallery. Default is 4', JBP_TEXT_DOMAIN), $pro_labels->name); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="page-header">
            <h3 class='hndle'><span><?php _e('Notification Settings', JBP_TEXT_DOMAIN); ?></span></h3>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php _e('Disable Contact Form:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <label class="text-muted" style="font-weight: normal">
                    <?php $form->hiddenField($model, 'expert_contact_form', array('value' => 0)) ?>
                    <?php $form->checkBox($model, 'expert_contact_form', array('value' => 1)) ?>
                    <?php _e('disable contact form', JBP_TEXT_DOMAIN); ?>
                </label>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php _e('CC the Administrator:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <label class="text-muted" style="font-weight: normal">
                    <?php $form->hiddenField($model, 'expert_cc_admin', array('value' => 0)) ?>
                    <?php $form->checkBox($model, 'expert_cc_admin', array('value' => 1)) ?>
                    <?php _e('cc the administrator', JBP_TEXT_DOMAIN); ?>
                </label>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php _e('Email Subject:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'expert_email_subject', array('class' => 'large-text')); ?>
                <p class="help-block">
                    <?php _e('Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">
                <?php _e('Email Content:', JBP_TEXT_DOMAIN); ?>
            </label>

            <div class="col-md-9">
                <?php echo $form->textArea($model, 'expert_email_content', array('class' => 'large-text', 'rows' => 5)); ?>
                <p class="help-block">
                    <?php _e('Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', JBP_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php
    }
}