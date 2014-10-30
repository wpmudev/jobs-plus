<?php

class JobsExperts_AddOn_Message_Views_PopupContact extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        global $jbp_message;
        $model = new MM_Message_Model();
        wp_enqueue_style('jobs-contact');
        wp_enqueue_script('jobs-noty');
        ?>
        <div class="modal fade" id="send-contact-modal" style="top:1%;">
            <div class="modal-dialog">
                <div class="modal-content" id="contact-modal-content">
                    <?php if (is_user_logged_in()): ?>
                    <div class="modal-header">
                        <h4 class="modal-title"><?php _e("Compose Message", JBP_TEXT_DOMAIN) ?></h4>
                    </div>
                    <form method="post" id="send-contact-form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label style="font-weight: normal"><?php _e("Message", JBP_TEXT_DOMAIN) ?></label>
                                <textarea style="box-sizing: border-box;height:150px" class="form-control jbp_wysiwyg"
                                          name="content"
                                          placeholder="<?php esc_attr_e("Write your message", JBP_TEXT_DOMAIN) ?>"></textarea>
                            </div>
                            <?php $form = new IG_Active_Form($model);
                            $form->hidden('attachment')?>

                            <?php
                            /*$form = JobsExperts_Framework_ActiveForm::generateForm($model);
                            $jbp_message->inject_uploader($form);*/
                            ig_uploader()->show_upload_control($model, 'attachment', 'contact-modal-content');
                            ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal"><?php _e("Cancel", JBP_TEXT_DOMAIN) ?></button>
                            <button type="submit"
                                    class="btn btn-primary"><?php _e("Send", JBP_TEXT_DOMAIN) ?></button>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                    <?php $this->load_login_form() ?>
                <?php
                endif;
                ?>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var type;
                $('.jbp_contact_expert, .jbp_contact_job').click(function (e) {
                    e.preventDefault();
                    var that = $(this);
                    $('#send-contact-form').find('#type').remove();
                    if (that.hasClass('jbp_contact_expert')) {
                        type = 'pro';
                    } else {
                        type = 'job';
                    }
                    $('#send-contact-form').find('.modal-body').append('<input id="type" type="hidden" name="type" value="' + type + '">');
                    $('#send-contact-modal').modal({
                        keyboard: false
                    })
                });
                $('#send-contact-form').on('submit', function () {
                    var that = $(this);
                    var form = that.closest('form');
                    //trigger validate
                    var old_text = '';
                    $.ajax({
                        type: 'POST',
                        data: {
                            'class': '<?php echo get_class(new JobsExperts_Core_Models_Contact()) ?>',
                            'action': 'send_email',
                            'data': form.serializeArray(),
                            'status': that.val(),
                            '_nonce': '<?php echo wp_create_nonce('send_email') ?>',
                            'type': type,
                            'id': '<?php echo get_the_ID() ?>'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            form.find('button').attr('disabled', 'disabled');
                            //old_text = that.html();
                            //that.text('Sending...');
                        },
                        success: function (data) {
                            data = $.parseJSON(data);
                            if (data.status == 1) {
                                location.href = data.url;
                            } else {
                                //rebind
                                form.find('button').removeAttr('disabled');
                                //fill error
                                $.each(data.errors, function (i, v) {
                                    //build name
                                    var input = form.find(':input[name="' + i + '"]');
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
                                    text: '<?php echo esc_js(__('Error happen, please check the form data',JBP_TEXT_DOMAIN)) ?>',
                                    layout: 'center',
                                    type: 'error',
                                    timeout: 5000
                                });
                            }
                        }
                    })
                    return false;
                });
            })
        </script>
        <?php
        do_action('jbp_after_popup_contact');
    }

    function load_login_form()
    {
        ?>
        <div class="hn-container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default jbp_login_form">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php _e('Please login', JBP_TEXT_DOMAIN) ?>
                                <?php
                                $can_register = is_multisite() == true ? get_site_option('users_can_register') : get_option('users_can_register');
                                if ($can_register): ?>
                                    or <?php echo sprintf('<a href="%s">%s</a>', wp_registration_url(), __('register here', JBP_TEXT_DOMAIN)) ?>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <?php echo wp_login_form(array('echo' => false)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}