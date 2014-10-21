<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_AddOn_Message_Views_Setting extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        global $jbp_message;
        $abs = JobsExperts_AddOn_Message_Models_Message::instance();
        $type = isset($_GET['box']) ? $_GET['box'] : 'inbox';
        $messages = $abs->get_messages();
        $read = $abs->get_read();
        $unread = $abs->get_unread();
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();

        $user_setting = get_user_meta(get_current_user_id(), '_messages_setting', true);

        if (!$user_setting) {
            $user_setting = array(
                'enable_receipt' => false,
                'prevent_receipt' => false
            );
        }

        if ($setting->user_receipt == false) {
            _e("This feature has been disabled by admin", JBP_TEXT_DOMAIN);
            return;
        }
        ?>
        <div class="hn-container">
            <div class="jbp-message">
                <div class="row">
                    <div class="col-md-12 no-padding">
                        <div class="alert alert-success hide">
                            <?php _e("Your changes were successfully saved!", JBP_TEXT_DOMAIN) ?>
                        </div>
                    </div>
                    <div class="col-md-9 col-xs-12 col-sm-12 no-padding">
                        <div class="message-toolbar">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo add_query_arg("box", "inbox", get_permalink(get_the_ID())) ?>"
                                   class="btn btn-default <?php echo $type == 'inbox' ? 'active' : null ?>">
                                    <?php _e("Inbox", JBP_TEXT_DOMAIN) ?>
                                    <span class="badge"><?php echo $messages['total'] ?></span>
                                </a>
                                <a href="<?php echo add_query_arg("box", "unread", get_permalink(get_the_ID())) ?>"
                                   class="btn btn-default <?php echo $type == 'unread' ? 'active' : null ?>">
                                    <?php _e("Unread", JBP_TEXT_DOMAIN) ?>
                                    <span class="badge"><?php echo $unread['total'] ?></span>
                                </a>
                                <a href="<?php echo add_query_arg("box", "read", get_permalink(get_the_ID())) ?>"
                                   class="btn btn-default <?php echo $type == 'read' ? 'active' : null ?>">
                                    <?php _e("Read", JBP_TEXT_DOMAIN) ?>
                                    <span class="badge"><?php echo $read['total'] ?></span>
                                </a>
                                <a href="<?php echo add_query_arg("box", "sent", get_permalink(get_the_ID())) ?>"
                                   class="btn btn-default <?php echo $type == 'sent' ? 'active' : null ?>">
                                    <?php _e("Sent", JBP_TEXT_DOMAIN) ?>
                                </a>

                            </div>
                            <a href="<?php echo add_query_arg('box', 'setting', get_permalink($setting->inbox_page)) ?>"
                               class="btn btn-default btn-sm"><i class="fa fa-gear"></i></a>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-12 no-padding">
                        <form class="form-inline pull-right" method="get"
                              action="<?php echo get_permalink($setting->inbox_page) ?>" role="form">
                            <div class="row">
                                <!-- /.col-lg-6 -->
                                <div class="col-lg-12">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="query" value="<?php echo $this->query ?>"
                                               class="form-control">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit"><i
                class="fa fa-search"></i></button>
      </span>

                                    </div>
                                    <!-- /input-group -->
                                </div>
                                <!-- /.col-lg-6 -->
                            </div>
                            <!-- /.row -->
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 no-padding">
                        <form id="message_setting" class="form-horizontal" role="form">
                            <fieldset>
                                <legend><?php _e("Email Settings", JBP_TEXT_DOMAIN) ?></legend>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <div class="checkbox">
                                            <label>
                                                <input <?php echo checked('true', $user_setting['enable_receipt']) ?>
                                                    type="checkbox"
                                                    class="enable_receipt"> <?php _e("Email me when receiver read my message", JBP_TEXT_DOMAIN) ?>
                                                <span class="help-block"><?php _e("An email will be sent to you when receiver read your message,
                                                but this function will not work if they turn the tracking off", JBP_TEXT_DOMAIN) ?></span>
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php echo checked('true', $user_setting['prevent_receipt']) ?>
                                                    type="checkbox"
                                                    class="prevent_receipt"> <?php _e("Prevent others tracking my message", JBP_TEXT_DOMAIN) ?>
                                                <span
                                                    class="help-block"><?php _e("When you open a message, there won't be an email back to the send to inform them you've read it.", JBP_TEXT_DOMAIN) ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <button class="btn btn-primary"
                                                type="submit"><?php _e("Save Changes", JBP_TEXT_DOMAIN) ?></button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function ($) {
                            $('#message_setting').on('submit', function () {
                                var that = $(this);
                                $.ajax({
                                    type: 'POST',
                                    data: {
                                        action: 'messages_user_setting',
                                        user_id: '<?php echo get_current_user_id() ?>',
                                        receipt: $('.enable_receipt').prop('checked'),
                                        prevent: $('.prevent_receipt').prop('checked'),
                                        _nonce: '<?php echo wp_create_nonce("messages_user_setting") ?>'
                                    },
                                    url: '<?php echo admin_url('admin-ajax') ?>',
                                    beforeSend: function () {
                                        that.find('button').attr('disabled', 'disabled');
                                    },
                                    success: function () {
                                        that.find('button[type="submit"]').removeAttr('disabled');
                                        $.cookie('setting_save', '1');
                                        location.reload();
                                    }
                                });
                                return false;
                            });
                            var saved = $.cookie('setting_save');
                            if (saved == 1) {
                                $('.alert-success').removeClass('hide');
                                $.removeCookie('setting_save');
                            }
                        })
                    </script>
                </div>
            </div>
        </div>
    <?php
    }
}