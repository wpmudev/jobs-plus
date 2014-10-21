<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_AddOn_Message_Views_Inbox extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        global $jbp_message;

        $models = $this->models['data'];
        $type = $this->type;
        $abs = JobsExperts_AddOn_Message_Models_Message::instance();
        if (!empty($this->query)) {
            $messages = $this->models;
        } else {
            $messages = $abs->get_messages();
        }
        $read = $abs->get_read();
        $unread = $abs->get_unread();
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        ?>
        <div class="hn-container">
            <div class="jbp-message">
                <div class="row">
                    <div class="col-md-12 no-padding">
                        <div class="alert alert-success hide">
                            <?php _e("Your message has sent!", JBP_TEXT_DOMAIN) ?>
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
                <?php if ($this->models['total_pages'] > 1): ?>
                    <?php $paged = get_query_var('paged') != 0 ? get_query_var('paged') : 1; ?>
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-md-6 col-xs-6 col-sm-6 no-padding">
                            <a <?php echo $paged <= 1 ? 'disabled' : null ?> class="btn btn-info btn-sm"
                                                                             href="<?php echo add_query_arg("paged", $paged - 1, get_permalink($setting->inbox_page)) ?>">
                                <i class="fa fa-angle-left"></i> <?php _e("Newer Messages", JBP_TEXT_DOMAIN) ?> </a>
                        </div>
                        <div class="col-md-6 col-xs-6 col-sm-6 no-padding">
                            <a <?php echo $paged >= $this->models['total_pages'] ? 'disabled' : null ?>
                                class="btn btn-info pull-right btn-sm"
                                href="<?php echo add_query_arg("paged", $paged + 1, get_permalink($setting->inbox_page)) ?>">
                                <?php _e("Older Messages", JBP_TEXT_DOMAIN) ?>  <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <?php endif; ?>

                <div class="row">

                    <div class="col-md-5 no-padding">
                        <div class="ps-container ps-active-x ps-active-y" id="message-list">
                            <div class="list-group ">
                                <?php foreach ($models as $key => $model): ?>
                                    <?php
                                    $is_read = '';
                                    if ($model->status == 'unread') {
                                        $is_read = 'unread';
                                    }
                                    if ($type == 'sent') {
                                        $is_read = 'read';
                                    }
                                    $active = $key == 0 ? 'active' : null;
                                    ?>
                                    <div id="message-preview-<?php echo $model->id ?>"
                                         data-id="<?php echo $model->id ?>"
                                         class="in-btn list-group-item <?php echo $is_read . ' ' . $active ?>">
                                        <div class="row">
                                    <span class="col-md-3 col-sm-2 col-xs-2 no-padding">
                                        <a href="#">
                                            <?php echo $jbp_message->get_avatar($model->send_from) ?>
                                        </a>
                                    </span>
                                    <span class="col-md-5 col-sm-8 col-xs-8 no-padding">

                                    <h4><?php echo $jbp_message->getFullName($model->send_from) ?>

                                    </h4>
                                    </span>
                                    <span class="col-md-4 col-sm-2 col-xs-2">
                                        <span class="label label-default label-white">
                                                 <?php echo date('j M', strtotime($model->date)) ?>
                                             </span>
                                        </span>
                                        </div>
                                        <p>
                                            <?php echo jbp_shorten_text(strip_tags($model->content), 58) ?>
                                        </p>

                                        <div class="clearfix"></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 no-padding" style="padding-left: 10px">
                        <div id="message-content-list">
                            <?php $model = array_shift($models); ?>
                            <?php if (is_object($model)) {
                                if ($type == 'sent') {
                                    echo $jbp_message->render_message($model, false);
                                } else {
                                    echo $jbp_message->render_message($model);
                                }
                            } else {
                                echo __("No message", JBP_TEXT_DOMAIN);
                            } ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="reply-form" style="top:5%;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?php _e("Compose Message", JBP_TEXT_DOMAIN) ?></h4>
                        </div>
                        <form method="post" id="reply-form">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label style="font-weight: normal"><?php _e("Message", JBP_TEXT_DOMAIN) ?></label>
                                    <textarea style="box-sizing: border-box;height:150px"
                                              class="form-control jbp_wysiwyg"
                                              name="message"
                                              placeholder="<?php esc_attr_e("Write your message", JBP_TEXT_DOMAIN) ?>"></textarea>
                                </div>
                                <?php
                                $form = JobsExperts_Framework_ActiveForm::generateForm($model);
                                $jbp_message->inject_uploader($form); ?>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default"
                                            data-dismiss="modal"><?php _e("Cancel", JBP_TEXT_DOMAIN) ?></button>
                                    <button type="submit"
                                            class="btn btn-primary"><?php _e("Send", JBP_TEXT_DOMAIN) ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>
        <!-- /.modal -->
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#message-list').perfectScrollbar({
                    suppressScrollX: true
                });
                $('#message_history').perfectScrollbar({
                    suppressScrollX: true
                });
                $('.in-btn').on('click', function (e) {
                    e.preventDefault();
                    var that = $(this);
                    $.ajax({
                        type: 'POST',
                        data: {
                            action: 'jbp_load_message',
                            id: that.data('id'),
                            _nonce: '<?php echo wp_create_nonce("jbp_message_ajax") ?>',
                            context: '<?php echo $type ?>'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            $('#message-display').css('opacity', '0.5')
                            $('.in-btn').css('cursor', 'wait');
                        },
                        success: function (html) {
                            $('#message-content-list').html(html).css('opacity', 1);
                            $('.in-btn').removeClass('active');
                            that.removeClass('unread').addClass('read active');
                            $('.in-btn').css('cursor', 'pointer');
                            $('#message_history').perfectScrollbar({
                                suppressScrollX: true
                            });
                        }
                    })
                });
                $('body').on('click', '.remove-message', function (e) {
                    e.preventDefault();
                    var that = $(this);
                    if (confirm("<?php echo esc_js(__("Are you sure?",JBP_TEXT_DOMAIN)) ?>")) {
                        $.ajax({
                            type: 'POST',
                            data: {
                                action: 'jbp_remove_message',
                                id: that.data('id'),
                                _nonce: '<?php echo wp_create_nonce("jbp_remove_message") ?>',
                                context: '<?php echo $type ?>'
                            },
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            beforeSend: function () {
                                that.attr('disabled', 'disabled');
                            },
                            success: function (html) {
                                id = that.data('id');
                                $('#message-list').find('.list-group-item').first().trigger('click');
                                $('#message-preview-' + id).remove();
                            }
                        })
                    }
                });
                $('body').on('click', '.reply-message', function (e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var that = this;
                    $('#reply-form').find('#msg_id').remove();
                    $('#reply-form').find('.modal-body').append('<input id="msg_id" type="hidden" name="id" value="' + id + '">');
                    $('#reply-form').modal({
                        keyboard: false
                    })
                });
                $('#reply-form').on('submit', function () {
                    var that = $(this);
                    $.ajax({
                        type: 'POST',
                        data: {
                            id: that.find('#msg_id').val(),
                            message: that.find('textarea').val(),
                            _wpnonce: '<?php echo wp_create_nonce('jbp_reply_message') ?>',
                            attachments: that.find('.sample_file_fields').val(),
                            action: 'jbp_reply_message'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            that.find('button').attr('disabled', 'disabled');
                        },
                        success: function (data) {
                            data = $.parseJSON(data);
                            that.find('button[type="submit"]').removeAttr('disabled');
                            if (data.status == 'success') {
                                that.find('textarea').val('');
                                $.cookie('message_sent', '1');
                                location.reload();
                            } else {
                                $.each(data.data, function (i, v) {
                                    var element = $(':input[name="message"]');
                                    var parent = element.parent();
                                    parent.append('<p class="help-block">' + v + '</p>');
                                })
                            }


                            /*$('#message-display').html(data);
                             that.find('button[type="submit"]').removeAttr('disabled');
                             $('#reply-form').modal('hide');*/
                        }
                    });
                    return false;
                });
                var sent = $.cookie('message_sent');
                if (sent == 1) {
                    $('.alert-success').removeClass('hide');
                    $.removeCookie('message_sent');
                }
            })
        </script>
        <?php
        do_action('jbp_after_inbox_message');
    }
}