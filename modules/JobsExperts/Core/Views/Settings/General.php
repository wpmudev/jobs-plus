<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_Settings_General extends JobsExperts_Framework_Render
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
        $components = new JobsExperts_Core_Table_AddOn();
        ?>
        <div class="page-header">
            <h3 class="hndle"><span><?php esc_html_e('General Options', JBP_TEXT_DOMAIN) ?></span></h3>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label"><?php esc_html_e('Icon Colors', JBP_TEXT_DOMAIN); ?></label>

            <div class="col-sm-10">
                <div class="radio">
                    <label>
                        <?php $form->radioButton($model, 'theme', 'dark') ?>
                        <?php printf('%s, <span class="description">%s</span>', esc_html__('Dark Icons', JBP_TEXT_DOMAIN), esc_html__('for light button backgrounds', JBP_TEXT_DOMAIN)); ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <?php $form->radioButton($model, 'theme', 'bright') ?>
                        <?php printf('%s, <span class="description">%s</span>', esc_html__('Bright Icons', JBP_TEXT_DOMAIN), esc_html__('for dark button backgrounds', JBP_TEXT_DOMAIN)); ?>
                    </label>
                </div>
                <div class="radio disabled">
                    <label>
                        <?php $form->radioButton($model, 'theme', 'none') ?>
                        <?php printf('%s, <span class="description">%s</span>', esc_html__('No Icons', JBP_TEXT_DOMAIN), esc_html__('to remove the icons from buttons', JBP_TEXT_DOMAIN)); ?>
                    </label>
                </div>
                <span
                    class="help-block"><?php esc_html_e('Sets the default color of the button icons. May be overriden for individual buttons in the "class" attribute of the shortcode.', JBP_TEXT_DOMAIN); ?></span>
            </div>
        </div>

        <div class="page-header">
            <h3 class="hndle"><span><?php esc_html_e('Addons', JBP_TEXT_DOMAIN) ?></span></h3>
        </div>
        <div class="">
            <div class="alert alert-info alert-sm hide notif">
                <?php _e('Settings have changed, you should save them!'); ?>
            </div>
            <?php
            $components->prepare_items();
            $components->display();
            $form->hiddenField($model, 'plugins', array('id' => 'jbp_components'));
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('.plugin').click(function (e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        if ($(this).data('type') == 'deactive') {
                            $(this).data('type', 'active');
                            $('#jbp_components').val($('#jbp_components').val().replace(id, ''));
                            $(this).text('Active');
                        } else {
                            $(this).data('type', 'deactive');
                            $('#jbp_components').val($('#jbp_components').val() + ',' + id);
                            $(this).text('Deactive');
                        }
                        $('.notif').removeClass('hide');
                    })
                })
            </script>
        </div>
    <?php
    }
}