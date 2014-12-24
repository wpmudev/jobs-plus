<div class="page-header">
    <h3 class="hndle">
        <span><?php printf(esc_html__('%s Status Options', je()->domain), $pro_labels->singular_name); ?></span>
    </h3>
</div>
<?php $form = new IG_Active_Form($model);
$form->open(array("attributes" => array("class" => "form-horizontal")));?>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php printf(esc_html__('Maximum %s Records per User', je()->domain), $pro_labels->singular_name) ?>
    </label>

    <div class="col-md-9">
        <?php $form->text('expert_max_records') ?>
        <p class="help-block"><?php printf(esc_html__('Maximum number of %s profiles for each user.', je()->domain), $pro_labels->singular_name); ?></p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php printf(esc_html__('%s Records per Page', je()->domain), $pro_labels->singular_name); ?>
    </label>

    <div class="col-md-9">
        <?php $form->text('expert_per_page') ?>
        <p class="help-block"><?php printf(esc_html__('Maximum number of %s profiles for each page.', je()->domain), $pro_labels->singular_name); ?></p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php printf(esc_html__('Newly Created %s Status Options', je()->domain), $pro_labels->singular_name); ?>
    </label>

    <div class="col-md-9">
        <label><?php $form->radio('expert_new_expert_status', array(
                'value' => 'publish'
            )) ?>

            <?php _e('Published', je()->domain); ?></label>

        <p class="help-block">
            <?php printf(esc_html__('Allow members to publish %s themselves.', je()->domain), $pro_labels->name); ?>
        </p>
        <label> <?php $form->radio('expert_new_expert_status', array(
                'value' => 'pending'
            )) ?>
            <?php _e('Pending Review', je()->domain); ?></label>

        <p class="help-block">
            <?php printf(esc_html__('%s is pending review by an administrator.', je()->domain), $pro_labels->singular_name); ?>
        </p>
        <label>
            <?php $form->hidden('expert_allow_draft', array('value' => 0)) ?>
            <?php $form->checkbox('expert_allow_draft', array('attributes' => array('value' => 1))) ?>
            <?php _e('Draft', je()->domain); ?>
        </label>

        <p class="help-block">
            <?php _e('Allow members to save Drafts.', je()->domain); ?>
        </p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="page-header">
    <h3 class="hndle">
        <span><?php printf(esc_html__('%s Image Storage', je()->domain), $pro_labels->singular_name); ?></span>
    </h3>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php _e('Maximum Gallery Images', je()->domain) ?>
    </label>

    <div class="col-md-9">
        <?php $form->text('expert_sample_size') ?>
        <p class="help-block">
            <?php printf(esc_html__('Maximum number of images that can be uploaded to the %s portfolio gallery. Default is 4', je()->domain), $pro_labels->name); ?></p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="page-header">
    <h3 class='hndle'><span><?php _e('Notification Settings', je()->domain); ?></span></h3>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php _e('Disable Contact Form:', je()->domain); ?>
    </label>

    <div class="col-md-9">
        <label class="text-muted" style="font-weight: normal">
            <?php $form->hidden('expert_contact_form', array('value' => 0)) ?>
            <?php $form->checkbox('expert_contact_form', array('attributes' => array('value' => 1))) ?>
            <?php _e('disable contact form', je()->domain); ?>
        </label>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php _e('CC the Administrator:', je()->domain); ?>
    </label>

    <div class="col-md-9">
        <label class="text-muted" style="font-weight: normal">
            <?php $form->hidden('expert_cc_admin', array('value' => 0)) ?>
            <?php $form->checkbox('expert_cc_admin', array('attributes' => array('value' => 1))) ?>
            <?php _e('cc the administrator', je()->domain); ?>
        </label>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php _e('Email Subject:', je()->domain); ?>
    </label>

    <div class="col-md-9">
        <?php $form->text('expert_email_subject', array('attributes' => array('class' => 'large-text'))); ?>
        <p class="help-block">
            <?php _e('Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', je()->domain); ?>
        </p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">
        <?php _e('Email Content:', je()->domain); ?>
    </label>

    <div class="col-md-9">
        <?php $form->text_area('expert_email_content', array('attributes' => array('class' => 'large-text', 'rows' => 5))); ?>
        <p class="help-block">
            <?php _e('Variables: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', je()->domain); ?>
        </p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <div class="col-sm-10">
        <?php wp_nonce_field('je_settings', '_je_setting_nonce') ?>
        <button type="submit" class="btn btn-primary"><?php _e("Submit", je()->domain) ?></button>
    </div>
</div>
<?php $form->close() ?>