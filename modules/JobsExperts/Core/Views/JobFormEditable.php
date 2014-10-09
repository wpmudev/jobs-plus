<?php

/**
 * Author: WPMUDEV
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
        <div class="row">
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
        <div class="form-group">
            <label class="col-md-3"><?php _e('Choose a category', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php
                if (!$model->is_new_record() && !empty($model->categories)) {
                    $model->categories = wp_list_pluck($model->categories, 'term_id');
                }
                $data = array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name'));
                ?>
                <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<p class="can-edit text-info">{{categories}}</p>',
                    $edit_icon.'<p class="can-edit text-info">' . __(' Category (required)', JBP_TEXT_DOMAIN) . '</p>', array(
                        'categories' => array(
                            'type' => 'dropdown',
                            'id' => 'categories',
                            'class' => 'validate[required]',
                            'data' => $data
                        )
                    ), 'hn-container jbp_text_popup', __('Job Category', JBP_TEXT_DOMAIN));
                echo $tpl->render();
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('Give your job a title', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<p class="can-edit icon-pencil text-info">{{job_title}}</p>',
                    $edit_icon.'<p class="can-edit icon-pencil text-info">' . __('Job Title (required)', JBP_TEXT_DOMAIN) . '</p>', array(
                        'job_title' => array(
                            'type' => 'textField',
                            'id' => 'job_title',
                            'class' => 'validate[required]',
                        )
                    ), 'hn-container jbp_text_popup', __('Give your job a title', JBP_TEXT_DOMAIN));
                echo $tpl->render();
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('Describe the work to be done', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php
                $desc = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<div class="can-edit">{{description}}</div>',
                    $edit_icon.'<p class="can-edit">' . __('What need to be done (required)?', JBP_TEXT_DOMAIN) . '</p>', array(
                    'description' => array(
                        'type' => 'textArea',
                        'id' => 'description',
                        'class' => 'validate[required]'
                    )
                ), 'hn-container jbp_text_popup', __('Describe the work to be done', JBP_TEXT_DOMAIN));
                echo $desc->render();
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('What skills are needed?', JBP_TEXT_DOMAIN) ?></label>
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
                <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<p class="can-edit text-info">{{skills}}</p>',
                    $edit_icon.'<p class="can-edit text-info">' . __('What skill will be recommend for this job?', JBP_TEXT_DOMAIN) . '</p>', array(
                        'skills' => array(
                            'type' => 'tags',
                            'id' => 'skills',
                            'data' => json_encode(get_terms('jbp_skills_tag', array('fields' => 'names', 'get' => 'all')))
                        )
                    ), 'hn-container jbp_text_popup', __('Please enter skills, separate by comma.', JBP_TEXT_DOMAIN));
                echo $tpl->render();
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('Budget', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <div class="input-group pull-left" style="width: 40%">
                    <?php if (!$plugin->settings()->job_budget_range): ?>
                        <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                            $edit_icon.'<p class="can-edit text-info">${{budget}}</p>',
                            $edit_icon.'<p class="can-edit text-info">' . __('How much you want to pay (required) ?', JBP_TEXT_DOMAIN) . '</p>', array(
                                'budget' => array(
                                    'type' => 'textField',
                                    'label' => __('Budget:', JBP_TEXT_DOMAIN),
                                    'id' => 'job_budget',
                                    'class' => 'validate[required,custom[number],min[1]]'
                                )
                            ), 'hn-container jbp_text_popup');
                        echo $tpl->render();
                        ?>
                    <?php else: ?>
                        <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                            $edit_icon.'<p class="can-edit text-info">${{min_budget}} - ${{max_budget}}</p>',
                            $edit_icon.'<p class="can-edit text-info">' . __('How much you want to pay (required) ?', JBP_TEXT_DOMAIN) . '</p>', array(
                                'min_budget' => array(
                                    'type' => 'textField',
                                    'label' => __('Min budget:', JBP_TEXT_DOMAIN),
                                    'id' => 'min_budget',
                                    'class' => 'validate[required,custom[number],min[1]]'
                                ),
                                'max_budget' => array(
                                    'type' => 'textField',
                                    'label' => __('Max budget:', JBP_TEXT_DOMAIN),
                                    'id' => 'max_budget',
                                    'class' => 'validate[required,custom[number],min[1]]'
                                )
                            ), 'hn-container jbp_text_popup', __('How much you want to pay?', JBP_TEXT_DOMAIN));
                        echo $tpl->render();
                        ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('Contact Email', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-7">
                <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<p class="can-edit text-info">{{contact_email}}</p>',
                    $edit_icon.'<p class="can-edit text-info">' . __('Please input your email (required)', JBP_TEXT_DOMAIN) . '</p>', array(
                        'contact_email' => array(
                            'type' => 'textField',
                            'id' => 'contact_email',
                            'class' => 'validate[required,custom[email]]'
                        )
                    ), 'hn-container jbp_text_popup', __('We need email for communicate'));
                echo $tpl->render();
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('Completion Date', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-9">
                <?php $tpl = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<p class="can-edit text-info">{{dead_line}}</p>',
                    $edit_icon.'<p class="can-edit text-info">' . __('Job complete day.', JBP_TEXT_DOMAIN) . '</p>', array(
                        'dead_line' => array(
                            'type' => 'calendar',
                            'id' => 'dead_line'
                        )
                    ), 'hn-container jbp_text_popup', __('Please select a day'));
                echo $tpl->render();
                ?>
            </div>
            <div class="clearfix"></div>
            <script type="text/javascript">
                function no_past_date(field, rules, i, options) {
                    var value = new Date(field.val());
                    var current_date = new Date();
                    current_date.setHours(0, 0, 0, 0);

                    var diff = value - current_date;
                    if (diff < 0) {
                        return '<?php echo esc_js(__('*Must be a future date',JBP_TEXT_DOMAIN) )?>';
                    }
                }
            </script>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-md-3"><?php _e('Job Open for', JBP_TEXT_DOMAIN) ?></label>

            <div class="col-md-7">
                <?php $days = $plugin->settings()->open_for_days;
                $days = array_filter(explode(',', $days));
                $data = array();
                foreach ($days as $day) {
                    $data[$day] = $day . ' ' . __('Days', JBP_TEXT_DOMAIN);
                }
                $tpl = new JobsExperts_Framework_EditableForm($model,
                    $edit_icon.'<p class="can-edit text-info">{{open_for}}</p>',
                    $edit_icon.'<p class="can-edit text-info">' . __('How long you want this job open?', JBP_TEXT_DOMAIN) . '</p>', array(
                        'open_for' => array(
                            'type' => 'dropdown',
                            'id' => 'open_for',
                            'class' => 'validate[required]',
                            'data' => $data
                        )
                    ), 'hn-container jbp_text_popup', __('How long you want this job open?', JBP_TEXT_DOMAIN));
                echo $tpl->render();
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
            <button class="btn btn-primary" name="status" value="publish"
                    type="submit"><?php _e('Publish', JBP_TEXT_DOMAIN) ?></button>
        <?php else: ?>
            <button class="btn btn-primary" name="status"
                    value="pending"><?php _e('Submit for review', JBP_TEXT_DOMAIN) ?></button>
        <?php endif; ?>
        <?php if ($plugin->settings()->job_allow_draft == 1): ?>
            <button class="btn btn-info" name="status" value="draft"
                    type="submit"><?php _e('Save Draft', JBP_TEXT_DOMAIN) ?></button>
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
                $("#jbp_job_form").validationEngine('attach', {
                    binded: false,
                    scroll: false
                });
                $('.job-tool-tip').tooltip();
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