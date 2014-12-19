<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Views_Backend_JobForm extends JobsExperts_Framework_Render
{

    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function  _to_html()
    {
        $model = $this->model;
        $plugin = JobsExperts_Plugin::instance();
        $form = JobsExperts_Framework_ActiveForm::generateForm($model);
        ?>
        <div class="wrap">
        <div class="hn-container">
        <div class="page-header">
            <?php if (!isset($_GET['id'])): ?>
                <h2><?php _e(sprintf('Add new %s', $plugin->get_job_type()->labels->singular_name)) ?></h2>
            <?php else: ?>
                <h2><?php _e(sprintf('Edit %s', $plugin->get_job_type()->labels->singular_name)) ?></h2>
            <?php endif; ?>
        </div>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'add-success'): ?>
            <div class="alert alert-success"><strong><?php _e('Job saved!', JBP_TEXT_DOMAIN) ?></strong></div>
        <?php endif; ?>
        <?php $form->openForm('', 'POST', array('class' => 'form-horizontal')) ?>
        <div class="row">
            <?php $form->hiddenField($model, 'id') ?>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><?php _e('General Information', JBP_TEXT_DOMAIN) ?></strong>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Job title', JBP_TEXT_DOMAIN) ?></label>

                            <div class="col-sm-8">
                                <?php $form->textField($model, 'job_title', array('class' => 'regular-text')) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Description', JBP_TEXT_DOMAIN) ?></label>

                            <div class="col-sm-8">
                                <?php wp_editor($model->description, 'job_description', array(
                                    'name' => $this->buildFormElementName($model, 'description'),
                                    'textarea_rows' => '8'
                                )) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Job Owner', JBP_TEXT_DOMAIN) ?></label>

                            <div class="col-sm-8">
                                <?php wp_dropdown_users(array(
                                    'name' => $this->buildFormElementName($model, 'owner'),
                                    'selected' => $model->owner
                                )) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Job Status', JBP_TEXT_DOMAIN) ?></label>

                            <div class="col-sm-8">
                                <?php $form->dropDownList($model, 'status', array(
                                    'publish' => 'Publish',
                                    'draft' => 'Draft'
                                )) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-6" style="margin-left: 0">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><?php _e('Job Meta', JBP_TEXT_DOMAIN) ?></strong>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Budget ($)', JBP_TEXT_DOMAIN) ?>
                            </label>

                            <div class="col-sm-8">
                                <?php if (!$plugin->settings()->job_budget_range): ?>
                                    <?php echo $form->textField($model, 'budget') ?>
                                <?php else: ?>
                                    <?php echo $form->textField($model, 'min_budget') ?>

                                    <?php echo $form->textField($model, 'max_budget') ?>
                                <?php endif; ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Contact Email', JBP_TEXT_DOMAIN) ?>
                            </label>

                            <div class="col-sm-8">
                                <?php $form->textField($model, 'contact_email', array(
                                    'class' => 'regular-text'
                                )) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Completion Date', JBP_TEXT_DOMAIN) ?>
                            </label>

                            <div class="col-sm-8">
                                <?php
                                $form->textField($model, 'dead_line', array(
                                    'class' => 'datepicker regular-text'
                                )) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Job Open for', JBP_TEXT_DOMAIN) ?>
                            </label>

                            <div class="col-sm-8">
                                <?php $days = $plugin->settings()->open_for_days;
                                $days = array_filter(explode(',', $days));
                                $data = array();
                                foreach ($days as $day) {
                                    $data[$day] = $day . ' ' . __('Days', JBP_TEXT_DOMAIN);
                                }

                                $form->dropDownList($model, 'open_for', $data, array(
                                    'prompt' => '--Select--', 'class' => 'validate[required] job-tool-tip',
                                ));
                                ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><?php _e('Category & Skill', JBP_TEXT_DOMAIN) ?></strong>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Category', JBP_TEXT_DOMAIN) ?>
                            </label>

                            <div class="col-sm-8">
                                <?php
                                //parse categories
                                if ($model->categories) {
                                    //check if model is string
                                    if (!is_array($model->categories)) {
                                        $model->categories = explode(',', $model->categories);
                                    } else {
                                        $model->categories = wp_list_pluck($model->categories, 'term_id');
                                    }
                                }

                                ?>
                                <?php $form->dropDownList($model, 'categories',
                                    array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name')),
                                    array('class' => 'regular-text')) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-sm-2 control-label"><?php _e('Skills', JBP_TEXT_DOMAIN) ?>
                            </label>

                            <div class="col-sm-8">
                                <?php
                                $skills = $model->find_terms('jbp_skills_tag');
                                if (is_array($skills) && count($skills)) {
                                    $skills = implode(',', wp_list_pluck($skills, 'name'));
                                } else {
                                    $skills = '';
                                }
                                $model->skills = $skills;
                                ?>
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
                            </div>
                            <div class="clearfix"></div>
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

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12">
                <button class="button button-primary"><?php _e('Save Changes', JBP_TEXT_DOMAIN) ?></button>
            </div>
        </div>
        <?php wp_nonce_field('jbp_admin_save_job', 'jbp_admin_save_job') ?>
        <?php $form->endForm(); ?>
        </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
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

    private function buildFormElementName($model, $attribute)
    {
        $model_class_name = get_class($model);
        $frm_element_name = $model_class_name . "[$attribute]";

        return $frm_element_name;
    }
}