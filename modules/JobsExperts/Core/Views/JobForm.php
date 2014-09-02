<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_JobForm extends JobsExperts_Framework_Render
{

    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        $model = $this->model;
        $plugin = JobsExperts_Plugin::instance();

        ?>
        <div class="row no-margin">
        <div class="col-md-12">
        <?php
        $form = JobsExperts_Framework_ActiveForm::generateForm($model);
        $form->openForm('', 'POST', array('class' => 'form-horizontal', 'id' => 'jbp_job_form'));
        $form->hiddenField($model, 'id');
        if (isset($_GET['job_title'])) {
            $model->job_title = $_GET['job_title'];
        }
        $edit_icon = '<i class="fa fa-pencil"></i>';
        ?>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('Choose a category', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php
                if (!$model->is_new_record() && !empty($model->categories)) {
                    if (is_array($model->categories)) {
                        $model->categories = wp_list_pluck($model->categories, 'term_id');
                    } else {
                        $model->categories = array($model->categories);
                    }
                }
                ?>
                <?php $form->dropDownList($model, 'categories',
                    array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name')),
                    array('class' => 'form-control validate[required]')
                ) ?>
                <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('Give your job a title', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php echo $form->textField($model, 'job_title', array('class' => 'form-control validate[required]')) ?>
                <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('Describe the work to be done', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php echo apply_filters('jbp_job_form_element', $form->textArea($model, 'description',
                    array('style' => 'height:150px', 'id' => 'job_description', 'class' => 'form-control validate[required]')), 'textarea', $model, 'description') ?>
                <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('What skills are needed?', JBP_TEXT_DOMAIN) ?></label>
            <?php
            $skills = $model->find_terms('jbp_skills_tag');
            if (is_array($skills) && count($skills)) {
                $skills = implode(',', wp_list_pluck($skills, 'name'));
            } else {
                $skills = '';
            }
            $model->skills = $skills;
            ?>
            <div class="col-md-9">
                <?php echo $form->hiddenField($model, 'skills', array('id' => 'jbp_skill_tag', 'style' => 'width:100%')) ?>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('#jbp_skill_tag').select2({
                            tags: <?php echo json_encode(get_terms('jbp_skills_tag', array('fields'=>'names', 'get' => 'all' ) ) ); ?>,
                            placeholder: "<?php esc_attr_e('Add a tag, use commas to separate'); ?>",
                            tokenSeparators: [","]
                        });
                    });
                </script>
                <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <?php if (!$plugin->settings()->job_budget_range): ?>
            <div class="form-group has-feedback">
                <label class="col-md-3 control-label"><?php _e('Budget', JBP_TEXT_DOMAIN) ?></label>

                <div class="col-md-9">
                    <div class="input-group pull-left" style="width: 40%">
                        <span class="input-group-addon">$</span>
                        <?php $form->textField($model, 'budget', array('class' => 'form-control validate[required,custom[number],min[1]]')) ?>
                    </div>
                    <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php else: ?>
            <div class="form-group has-feedback">
                <label class="col-md-3 control-label"><?php _e('Budget From', JBP_TEXT_DOMAIN) ?></label>

                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <?php $form->textField($model, 'min_budget',
                            array('class' => 'form-control validate[required,funcCall[checkMax],min[1],custom[number]]')) ?>
                        <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group has-feedback">
                <label class="col-md-3 control-label"><?php _e('Budget To', JBP_TEXT_DOMAIN) ?></label>

                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <?php  $form->textField($model, 'max_budget',
                            array('class' => 'form-control validate[required,funcCall[checkMax],min[1],custom[number]]')) ?>
                        <span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php endif; ?>
        <hr/>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('Contact Email', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <div class="input-group pull-left">
                    <span class="input-group-addon">@</span>
                    <?php $form->textField($model, 'contact_email', array(
                        'data-toggle' => "tooltip",
                        'title' => __('Contact email address for the job offer', JBP_TEXT_DOMAIN),
                        'data-placement' => 'bottom',
                        'class' => 'form-control validate[required,custom[email]]'
                    )) ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('Completion Date', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <div class="input-group pull-left">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                    <?php
                    $form->textField($model, 'dead_line', array(
                        'class' => 'form-control datepicker job-tool-tip validate[required]',
                        'data-toggle' => "tooltip",
                        'title' => __('When must this job be completed by? Or NA for not applicable.', JBP_TEXT_DOMAIN),
                        'data-placement' => 'bottom',
                    )) ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <script type="text/javascript">
                function no_past_date(field, rules, i, options) {
                    var value = new Date(field.val());
                    var current_date = new Date();
                    current_date.setHours(0, 0, 0, 0);

                    var diff = value - current_date;
                    if (diff < 0) {
                        return '<?php _e('*Must be a future date',JBP_TEXT_DOMAIN) ?>';
                    }
                }
            </script>
        </div>
        <hr/>
        <div class="form-group has-feedback">
            <label class="col-md-3 control-label"><?php _e('Job Open for', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-7">
                <?php $days = $plugin->settings()->open_for_days;
                $days = array_filter(explode(',', $days));
                $data = array();
                foreach ($days as $day) {
                    $data[$day] = $day . ' ' . __('Days', JBP_TEXT_DOMAIN);
                }

                $form->dropDownList($model, 'open_for', $data, array(
                    'prompt' => '--Select--', 'class' => 'form-control job-tool-tip',
                    'data-toggle' => "tooltip",
                    'title' => __('How long is this job open for from Today?', JBP_TEXT_DOMAIN),
                    'data-placement' => 'bottom',
                ));
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
        $uploader = new JobsExperts_Components_Uploader_View(array(
            'model' => $model,
            'attribute' => 'portfolios',
            'form' => $form
        ));
        $uploader->render();
        ?>
        <?php if ($plugin->settings()->job_new_job_status == 'publish'): ?>
            <button class="btn btn-primary submit" name="status" value="publish"
                    type="button"><?php _e('Publish', JBP_TEXT_DOMAIN) ?></button>
        <?php else: ?>
            <button type="button" class="btn btn-primary submit" name="status"
                    value="pending"><?php _e('Submit for review', JBP_TEXT_DOMAIN) ?></button>
        <?php endif; ?>
        <?php if ($plugin->settings()->job_allow_draft == 1): ?>
            <button class="btn btn-info submit" name="status" value="draft"
                    type="button"><?php _e('Save Draft', JBP_TEXT_DOMAIN) ?></button>
        <?php endif; ?>
        <?php echo wp_nonce_field('jbp_add_job') ?>

        <button onclick="location.href='<?php echo get_post_type_archive_link('jbp_job') ?>'" type="button"
                class="btn btn-default pull-right"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
        <div class="clearfix"></div>

        <?php
        echo $form->endForm();
        ?>
        </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.form-control-feedback').hide();
                $("#jbp_job_form").find(':input').blur(function () {
                    var parent = $(this).closest('div');
                    var top_parent = parent.parent();
                    if (parent.hasClass('input-group')) {
                        parent = parent.parent();
                        top_parent = parent.parent();
                    }
                    $.ajax({
                        type: 'POST',
                        data: {
                            'id': '<?php echo $model->id ?>',
                            'class': '<?php echo get_class($model) ?>',
                            'action': 'job_validate',
                            'key': $(this).attr('name'),
                            'data': $("#jbp_job_form").serializeArray(),
                            '_nonce': '<?php echo wp_create_nonce('job_validate') ?>'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            parent.find('.form-control-feedback').show();
                        },
                        success: function (data) {
                            parent.find('.form-control-feedback').hide();
                            data = jQuery.parseJSON(data);
                            if (data.status == 1) {
                                top_parent.removeClass('has-success has-error').addClass('has-success');
                                parent.find('.help-block').remove();
                            } else {
                                top_parent.removeClass('has-success has-error').addClass('has-error');
                                parent.find('.help-block').remove();
                                parent.append('<p class="help-block">' + data.error + '</p>');
                            }
                        }
                    })
                })
                $("#jbp_job_form").find('.submit').on('click', function () {
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
                                    text: '<?php _e('Job post successful, redirecting...',JBP_TEXT_DOMAIN) ?>',
                                    layout: 'center',
                                    type: 'success',
                                    timeout: 5000
                                });
                                location.href = data.url;
                            } else {
                                //rebind
                                form.find('.submit').removeAttr('disabled');
                                that.text(old_text);
                                //console.log(data);
                                //fill error
                                $.each(data.errors, function (i, v) {
                                    //build name
                                    var class_name = '<?php echo get_class($model) ?>';
                                    var name = class_name + '[' + i + ']';
                                    var input = form.find(':input[name="' + name + '"]');
                                    //get container
                                    var iparent = input.closest('div');
                                    var itop_parent = iparent.parent();
                                    if (iparent.hasClass('input-group')) {
                                        iparent = iparent.parent();
                                        itop_parent = iparent.parent();
                                    }
                                    itop_parent.removeClass('has-success has-error').addClass('has-error');
                                    iparent.find('.help-block').remove();
                                    iparent.append('<p class="help-block">' + v + '</p>');
                                });
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

                $('.datepicker').datepicker({
                    autoclose: true,
                    format: "M d, yyyy",
                    todayHighlight: true,
                    startDate: '<?php echo date('M d, Y') ?>'
                });
            })
        </script>
    <?php
    }
}