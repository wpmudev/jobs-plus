<?php

/**
 * Author: WPMUDEV
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
            <h3 class="hndle"><span><?php _e('General Options', JBP_TEXT_DOMAIN) ?></span></h3>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label"><?php _e('Icon Colors', JBP_TEXT_DOMAIN); ?></label>

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
                <div class="radio">
                    <label>
                        <?php $form->radioButton($model, 'theme', 'none') ?>
                        <?php printf('%s, <span class="description">%s</span>', esc_html__('No Icons', JBP_TEXT_DOMAIN), esc_html__('to remove the icons from buttons', JBP_TEXT_DOMAIN)); ?>
                    </label>
                </div>
                <span
                    class="help-block"><?php _e('Sets the default color of the button icons. May be overriden for individual buttons in the "class" attribute of the shortcode.', JBP_TEXT_DOMAIN); ?></span>
            </div>
        </div>

        <div class="page-header">
            <h3 class="hndle"><span><?php _e('Addons', JBP_TEXT_DOMAIN) ?></span></h3>
        </div>
        <div class="">
            <div class="alert alert-success alert-sm hide notif">

            </div>
            <?php
            $components->prepare_items();
            $components->display();
            //$form->hiddenField($model, 'plugins', array('id' => 'jbp_components'));
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    var addon_has_changed = false;
                    $('.plugin').click(function (e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var that = $(this);
                        if ($(this).data('type') == 'deactive') {
                            $(this).data('type', 'active');
                            /*$('#jbp_components').val($('#jbp_components').val().replace(id, ''));*/
                            //ajax update
                            $.ajax({
                                type: 'POST',
                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                data: {
                                    action: 'addons_action',
                                    type: 'deactive',
                                    id: id,
                                    _nonce: '<?php echo wp_create_nonce('addons_action') ?>'
                                },
                                beforeSend: function () {
                                    that.attr('disabled','disabled');
                                },
                                success: function (data) {
                                    that.removeAttr('disabled');
                                    that.text('<?php _e('Activate',JBP_TEXT_DOMAIN) ?>');
                                    $('.notif').html(data).removeClass('hide');
                                    $('#jbp_setting_nav').load("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?> #jbp_setting_nav li");
                                }
                            })
                        } else {
                            $(this).data('type', 'deactive');
                            //$('#jbp_components').val($('#jbp_components').val() + ',' + id);
                            $.ajax({
                                type: 'POST',
                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                data: {
                                    action: 'addons_action',
                                    type: 'active',
                                    id: id,
                                    _nonce: '<?php echo wp_create_nonce('addons_action') ?>'
                                },
                                beforeSend: function () {
                                    that.attr('disabled','disabled');
                                },
                                success: function (data) {
                                    that.removeAttr('disabled');
                                    that.text('<?php _e('Deactivate',JBP_TEXT_DOMAIN) ?>');
                                    $('.notif').html(data).removeClass('hide');
                                    $('#jbp_setting_nav').load("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?> #jbp_setting_nav li");
                                }
                            })
                        }

                        addon_has_changed = false;
                        //console.log(addon_has_changed);
                    });

                    $('#jobs-setting').on('submit', function () {
                        addon_has_changed = false;
                    })

                    window.onbeforeunload = function () {
                        if (addon_has_changed == true) {
                            return '<?php echo __('It looks like you have been editing something -- if you leave before submitting your changes will be lost.',JBP_TEXT_DOMAIN) ?>';
                        }
                    }
                })
            </script>
        </div>
    <?php
    }
}