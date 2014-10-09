<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Views_ExpertForm extends JobsExperts_Framework_Render
{

    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        $model = $this->model;
        $plugin = JobsExperts_Plugin::instance();

        $form = JobsExperts_Framework_ActiveForm::generateForm($model);
        $form->openForm('', 'POST', array('id' => 'experts-form'));
        $form->hiddenField($model, 'id');
        $edit_icon = '<i class="dashicons dashicons-edit"></i>';
        $is_edit = $this->is_edit;
        global $jbp_component_uploader;
        ?>
        <div class="jobs-expert-form">
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <?php $jbp_component_uploader->avatar_upload_render($model) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="tag-line">
                            <div class="panel panel-default" style="z-index: 0">
                                <div class="panel panel-heading">
                                    <?php _e('Tag line', JBP_TEXT_DOMAIN) ?>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $sdesc = new JobsExperts_Framework_EditableForm($model,
                                        '<p class="can-edit">{{short_description}}</p>',
                                        '<p class="can-edit">' . __('Short version of yourself, maximum 100 characters. This will be use to display at listing page', JBP_TEXT_DOMAIN) . '</p>', array(
                                            'short_description' => array(
                                                'type' => 'textArea',
                                                'id' => 'short_description',
                                                'class' => 'validate[maxSize[100]]'
                                            )
                                        ), 'hn-container jbp_text_popup', __('Short description about yourself', JBP_TEXT_DOMAIN));
                                    echo $sdesc->render();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 expert-form-container">
                <div class="page-header">
                    <?php
                    $empty_text = '<h2 class="can-edit">' . sprintf('Your Name (required)') . '</h2>';
                    $display_text = '<h2 class="can-edit">' . sprintf('{{first_name}} {{last_name}}') . '</h2>';
                    $mapped = array(
                        'first_name' => array(
                            'type' => 'textField',
                            'label' => 'First Name',
                            'id' => 'first_name',
                            'class' => 'validate[required]'
                        ),
                        'last_name' => array(
                            'type' => 'textField',
                            'label' => 'Last Name',
                            'id' => 'last_name',
                            'class' => 'validate[required]'
                        )
                    );

                    $fullname = new JobsExperts_Framework_EditableForm($model, $display_text, $empty_text,
                        $mapped, 'hn-container jbp_text_popup',
                        __('Your name', JBP_TEXT_DOMAIN)
                    );
                    echo $fullname->render();
                    ?>
                    <h4><?php echo sprintf(__('Member since %s', JBP_TEXT_DOMAIN), date("M Y", strtotime(get_the_author_meta('user_registered')))) ?></h4>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <label class="control-label">
                            <i class="glyphicon glyphicon-briefcase"></i> <?php _e('Company:', JBP_TEXT_DOMAIN) ?>
                        </label>
                    </div>
                    <div class="col-md-7">
                        <?php $company = new JobsExperts_Framework_EditableForm($model,
                            '<p class="can-edit"><a href="javascript::void(0)">{{company}}</a></p>',
                            '<p class="can-edit">' . __('Your Company', JBP_TEXT_DOMAIN) . '</p>', array(
                                'company' => array(
                                    'type' => 'textField',
                                    'label' => __('Company:', JBP_TEXT_DOMAIN),
                                    'id' => 'company'
                                ),
                                'company_url' => array(
                                    'type' => 'textField',
                                    'label' => __('Company Url:', JBP_TEXT_DOMAIN),
                                    'id' => 'company_url'
                                )
                            ), 'hn-container jbp_text_popup', __('Your Company', JBP_TEXT_DOMAIN));
                        echo $company->render();
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <label>
                            <i class="glyphicon glyphicon-map-marker"></i> <?php _e('Location:', JBP_TEXT_DOMAIN) ?>
                        </label>
                    </div>
                    <div class="col-md-7">
                        <?php $company = new JobsExperts_Framework_EditableForm($model,
                            '<p class="can-edit">{{location}}</p>',
                            '<p class="can-edit">' . __('Your Location', JBP_TEXT_DOMAIN) . '</p>', array(
                                'location' => array(
                                    'type' => 'countryDropdown',
                                    'id' => 'location',
                                    'class' => 'validate[required]'
                                )
                            ), 'hn-container jbp_text_popup', __('Where are you from?', JBP_TEXT_DOMAIN));
                        echo $company->render();
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <label>
                            <i class="fa fa-envelope"></i> <?php _e('Contact Email:', JBP_TEXT_DOMAIN) ?>
                        </label>
                    </div>
                    <div class="col-md-7">
                        <?php $company = new JobsExperts_Framework_EditableForm($model,
                            '<p class="can-edit">{{contact_email}}</p>',
                            '<p class="can-edit">' . __('Your Contact Email', JBP_TEXT_DOMAIN) . '</p>', array(
                                'contact_email' => array(
                                    'type' => 'textField',
                                    'id' => 'contact_email',
                                    'class' => 'validate[required,custom[email]]'
                                )
                            ), 'hn-container jbp_text_popup', __('Your email will not be published over the site', JBP_TEXT_DOMAIN));
                        echo $company->render();
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="active">
                                <a href="#biograhy" role="tab"
                                   data-toggle="tab"><?php _e('Biography', JBP_TEXT_DOMAIN) ?></a>
                            </li>
                            <li><a href="#profile" role="tab"
                                   data-toggle="tab"><?php _e('Social & Skill', JBP_TEXT_DOMAIN) ?></a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="biograhy">
                                <?php
                                $desc = new JobsExperts_Framework_EditableForm($model,
                                    '<div class="can-edit">{{biography}}</div>',
                                    '<p class="can-edit">' . __('Tell us about yourself (required, at least 200 characters)', JBP_TEXT_DOMAIN) . '</p>', array(
                                        'biography' => array(
                                            'type' => 'textArea',
                                            'id' => 'biography',
                                            'class' => 'validate[required,minSize[200]]'
                                        )
                                    ), 'hn-container jbp_text_popup', __('Tell us about yourself', JBP_TEXT_DOMAIN));
                                echo $desc->render();
                                ?>
                            </div>
                            <div class="tab-pane social-container" id="profile">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php $tmp = new JobsExperts_Components_Social_View(array(
                                            'model' => $model,
                                            'attribute' => 'social',
                                            'form' => $form
                                        ));
                                        $tmp->render();?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php $tmp = new JobsExperts_Components_Skill_View(array(
                                            'model' => $model,
                                            'attribute' => 'skills',
                                            'form' => $form
                                        ));
                                        $tmp->render();?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                $uploader = new JobsExperts_Components_Uploader_View(array(
                    'model' => $model,
                    'attribute' => 'portfolios',
                    'form' => $form
                ));
                $uploader->render();
                ?>
            </div>
        </div>
        <div class="row" style="margin-top: 40px">
            <div class="col-md-12" style="margin-left: 0">
                <?php echo wp_nonce_field('jbp_add_pro') ?>
                <?php if ($plugin->settings()->expert_new_expert_status == 'publish'): ?>
                    <button class="submit btn btn-small btn-primary" name="status" value="publish"
                            type="button"><?php _e('Publish', JBP_TEXT_DOMAIN) ?></button>
                <?php else: ?>
                    <button class="submit btn btn-small btn-primary" name="status"
                            type="button" value="pending"><?php _e('Submit for review', JBP_TEXT_DOMAIN) ?></button>
                <?php endif; ?>
                <?php if ($plugin->settings()->expert_allow_draft == 1): ?>
                    <button class="submit btn btn-small btn-info" name="status" value="draft"
                            type="button"><?php _e('Save Draft', JBP_TEXT_DOMAIN) ?></button>
                <?php endif; ?>
                <button onclick="location.href='<?php echo get_post_type_archive_link('jbp_pro') ?>'"
                        type="button"
                        class="btn btn-default btn-small pull-right"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
            </div>
            <div style="clear: both"></div>
        </div>

        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#experts-form').find('.submit').on('click', function () {
                    var that = $(this);
                    var form = that.closest('form');
                    //trigger validate
                    var old_text = '';
                    $.ajax({
                        type: 'POST',
                        data: {
                            'id': '<?php echo $model->id ?>',
                            'class': '<?php echo get_class($model) ?>',
                            'action': 'job_save',
                            'data': form.serializeArray(),
                            'status': that.val(),
                            '_nonce': '<?php echo wp_create_nonce('job_save') ?>'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            form.find('.submit').attr('disabled', 'disabled');
                            old_text = that.text();
                            that.text('Sending...');
                        },
                        success: function (data) {
                            data = $.parseJSON(data);
                            if (data.status == 1) {
                                var n = noty({
                                    text: '<?php _e('Expert profile '.($is_edit==true?'updated':'created').' successful, redirecting...',JBP_TEXT_DOMAIN) ?>',
                                    layout: 'center',
                                    type: 'success',
                                    timeout: 5000
                                });
                                location.href = data.url;
                            } else {
                                //rebind
                                form.find('.submit').removeAttr('disabled');
                                that.text(old_text);
                                //fill error
                                $('.expert-form-container').find('.alert').remove();
                                var error = $('<div/>').addClass('alert alert-danger');
                                $.each(data.errors, function (i, v) {
                                    error.append('<p>' + v + '</p>');
                                });
                                $('.expert-form-container').prepend(error);
                                //display noty
                                var n = noty({
                                    text: '<?php _e('Error happen, please check the form data',JBP_TEXT_DOMAIN) ?>',
                                    layout: 'center',
                                    type: 'error',
                                    timeout: 5000
                                });
                            }
                        }
                    })
                    return false;
                })
            })
        </script>
        <?php
        $form->endForm();
    }
}