<div class="expert-avatar">
    <div class="panel panel-default">
        <div class="panel-body no-padding">
            <?php echo $model->get_avatar(420) ?>
        </div>
        <div class="panel-footer">
            <?php if (je()->can_upload_avatar()): ?>
                <button type="button" class="btn btn-primary btn-sm change-avatar">
                    <?php _e("Change Avatar", je()->domain) ?>
                </button>
            <?php else: ?>
                <?php _e("You don't have permission to upload an avatar", je()->domain) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if (je()->can_upload_avatar()): ?>
    <script type="text/javascript">
        jQuery(function ($) {
            $('.change-avatar').webuiPopover({
                content: function () {
                    var content = $('<div class="ig-container"></div>');
                    var html = $('#je_avatar_uploader').html();
                    content.html(html);
                    return content;
                }
            });
            $('body').on('submit', '.file-uploader-form form', function () {
                var form = $(this);
                var parent = form.closest('div');
                var args = {
                    data: {
                        parent_id: form.find('input[name="parent_id"]').first().val()
                    },
                    //processData: false,
                    iframe: true,
                    cache: false,
                    type: 'POST',
                    url: '<?php echo (add_query_arg(array('upload_file_nonce'=>wp_create_nonce('hn_upload_avatar')))) ?>'
                };

                var file = $(":file", form);

                if (!file.val()) {
                    alert(expert_form.avatar_empty);
                } else {
                    args.files = file;
                    args.beforeSend = function () {
                        parent.find('.alert').remove();
                        form.find('button').attr('disabled', 'disabled');
                    };
                    args.success = function (data) {
                        form.find(':input, button').removeAttr('disabled');
                        var tmp = $(data);
                        var url = tmp.text();
                        $('.expert-avatar .panel-body').html('<img src="' + url + '"/>');
                        $('.change-avatar').webuiPopover('hide');
                        form.find('.hn-delete-avatar').removeClass('hide');
                    }
                    $.ajax(args);
                }
                return false;
            })
            $('body').on('change', '.hn_uploader_element', function (e) {
                var file = e.target.files[0];
                var type = file.type.split('/');
                var size_allowed = '<?php echo (get_max_file_upload() * 1000000) ?>';
                if (type[0] != 'image') {
                    alert(expert_form.avatar_error_file);
                    $(this).val("");
                } else if (file.size > size_allowed) {
                    alert(expert_form.avatar_error_size);
                    $(this).val("");
                }
            });
            $('body').on('click', '.hn-cancel-avatar', function () {
                $('.change-avatar').webuiPopover('hide');
            })

            $('body').on('click', '.hn-delete-avatar', function () {
                $.ajax({
                    type: 'POST',
                    data: {
                        parent_id: '<?php echo $model->id ?>',
                        action: 'expert_delete_avatar',
                        _nonce: '<?php echo wp_create_nonce('expert_delete_avatar') ?>'
                    },
                    success: function (data) {
                        $('.expert-avatar .panel-body').html(data);
                        $(this).addClass('hide');
                        $('.change-avatar').webuiPopover('hide');
                    }
                })
            })
        })
    </script>
<?php endif; ?>