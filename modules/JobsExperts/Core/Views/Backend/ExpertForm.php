<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Views_Backend_ExpertForm extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    function _to_html()
    {
        $model = $this->model;
        $plugin = JobsExperts_Plugin::instance();
        $form = JobsExperts_Framework_ActiveForm::generateForm($model);

        ?>
        <div class="wrap">
            <div class="hn-container">
                <div class="page-header">
                    <?php if (!isset($_GET['id'])): ?>
                        <h2><?php _e(sprintf('Add new %s', $plugin->get_expert_type()->labels->singular_name)) ?></h2>
                    <?php else: ?>
                        <h2><?php _e(sprintf('Edit %s', $plugin->get_expert_type()->labels->singular_name)) ?></h2>
                    <?php endif; ?>
                </div>
                <?php if (isset($_GET['status']) && $_GET['status'] == 'add-success'): ?>
                    <div class="alert alert-success"><strong><?php _e('Expert profile saved!', JBP_TEXT_DOMAIN) ?></strong></div>
                <?php endif; ?>
                <?php $form->openForm('', 'POST', array('class' => 'form-horizontal')) ?>
                <?php $form->hiddenField($model, 'id') ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong><?php _e('General Information', JBP_TEXT_DOMAIN) ?></strong>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('First Name', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php $form->textField($model, 'first_name', array('class' => 'regular-text')) ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Last Name', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php $form->textField($model, 'last_name', array('class' => 'regular-text')) ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Biography', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php wp_editor($model->biography, 'biography', array(
                                            'name' => $this->buildFormElementName($model, 'biography'),
                                            'textarea_rows' => '8',
                                            'media_buttons' => false
                                        )) ?>
                                        <?php if ($model->get_error('biography')): ?>
                                            <span class="error_message"><p
                                                    class="error_msg"><?php echo $model->get_error('biography') ?></p></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Profile Owner', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php wp_dropdown_users(array(
                                            'name' => $this->buildFormElementName($model, 'user_id')
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
                </div>
                <div class="row">
                    <div class="col-md-7" style="margin-left: 0">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong><?php _e('Addition Information', JBP_TEXT_DOMAIN) ?></strong>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Contact Email', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php $form->textField($model, 'contact_email', array('class' => 'regular-text')) ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Location', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php $form->countryDropDown($model, 'location') ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Company Name', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php $form->textField($model, 'company', array('class' => 'regular-text')) ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Company Url', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php $form->textField($model, 'company_url', array('class' => 'regular-text')) ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"><?php _e('Short Description', JBP_TEXT_DOMAIN) ?></label>

                                    <div class="col-sm-8">
                                        <?php wp_editor($model->short_description, 'short_description', array(
                                            'name' => $this->buildFormElementName($model, 'biography'),
                                            'textarea_rows' => '8',
                                            'media_buttons' => false
                                        )) ?>
                                        <?php if ($model->get_error('short_description')): ?>
                                            <span class="error_message"><p
                                                    class="error_msg"><?php echo $model->get_error('short_description') ?></p></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <?php $tmp = new JobsExperts_Components_Social_View(array(
                            'model' => $model,
                            'attribute' => 'social',
                            'form' => $form
                        ));
                        $tmp->render();?>
                        <?php $tmp = new JobsExperts_Components_Skill_View(array(
                            'model' => $model,
                            'attribute' => 'skills',
                            'form' => $form
                        ));
                        $tmp->render();?>
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
                <div class="row">
                    <div class="col-md-12">
                        <button class="button button-primary"><?php _e('Save Changes', JBP_TEXT_DOMAIN) ?></button>
                    </div>
                </div>
                <?php wp_nonce_field('jbp_admin_save_pro', 'jbp_admin_save_pro') ?>
                <?php $form->endForm() ?>
            </div>
        </div>
    <?php
    }

    private function buildFormElementName($model, $attribute)
    {
        $model_class_name = get_class($model);
        $frm_element_name = $model_class_name . "[$attribute]";

        return $frm_element_name;
    }
}