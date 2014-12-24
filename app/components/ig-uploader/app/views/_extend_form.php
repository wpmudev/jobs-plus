<?php
$upload_new_id = uniqid();
$c_id = uniqid();
$f_id = uniqid();
?>
<div id="<?php echo $c_id ?>">
    <div class="panel panel-default" style="margin-bottom: 5px;border-width: 1px;position:relative;">
        <div class="panel-heading">
            <strong
                class="hidden-xs hidden-sm"><?php echo $attributes['title'] ?></strong>
            <small
                class="hidden-md hidden-lg"><?php echo $attributes['title'] ?></small>
            <button type="button"
                    class="btn btn-primary btn-xs pull-right add-file"><?php _e('Add', ig_uploader()->domain) ?> <i
                    class="glyphicon glyphicon-plus"></i>
            </button>
        </div>
        <div class="panel-body file-view-port">
            <?php if (is_array($models) && count($models)): ?>
                <?php foreach ($models as $model): ?>
                    <?php $this->render_partial(apply_filters('igu_single_file_template', '_single_file'), array(
                        'model' => $model
                    )) ?>
                <?php endforeach; ?>
                <div class="clearfix"></div>
            <?php else: ?>
                <p class="no-file"><?php _e("No sample file.", ig_uploader()->domain) ?></p>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var instance;
            $('body').on('mouseenter', '.add-file', function () {
                $(this).webuiPopover({
                    type: 'async',
                    width: 'auto',
                    height: 'auto',
                    title: '<?php echo esc_js(__("Upload Attachment",ig_uploader()->domain)) ?>',
                    url: '<?php echo admin_url('admin-ajax.php?action=iup_load_upload_form&is_admin='.$is_admin.'&_wpnonce='.wp_create_nonce('iup_load_upload_form')) ?>',
                    content: function (data) {
                        return data;
                    }
                }).on('show.webui.popover', function () {
                    var that = $(this);
                    var pop = that.data('plugin_webuiPopover');
                    var container = pop.$target;
                    var form = container.find('form').first();
                    if (form.size() > 0) {
                        $('body').on('click', '.igu-close-uploader', function () {
                            pop.hide();
                        });
                    }
                    instance = that;
                });
            });

            $('body').on('mouseenter', '.igu-file-update', function () {
                $(this).webuiPopover({
                    type: 'async',
                    width: 'auto',
                    height: 'auto',
                    title: '<?php echo esc_js(__("Upload Attachment",ig_uploader()->domain)) ?>',
                    url: '<?php echo admin_url('admin-ajax.php?action=iup_load_upload_form&is_admin='.$is_admin.'&_wpnonce='.wp_create_nonce('iup_load_upload_form')) ?>&id=' + $(this).data('id'),
                    content: function (data) {
                        return data;
                    }
                }).on('show.webui.popover', function () {
                    var that = $(this);
                    var pop = that.data('plugin_webuiPopover');
                    var container = pop.$target;
                    var form = container.find('form').first();
                    if (form.size() > 0) {
                        $('body').on('click', '.igu-close-uploader', function () {
                            pop.hide();
                        });
                    }
                    instance = that;
                });
            })
            <?php if(is_admin()): ?>
            $('body').on('submit', '.igu-upload-form', function () {
                var that = $(this);
                $.ajax('<?php echo add_query_arg('igu_uploading','1') ?>', {
                    type: 'POST',
                    data: $(this).find(':input').serialize(),
                    iframe: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        that.find('button').attr('disabled', 'disabled');
                    },
                    success: function (data) {
                        //data = $.parseJSON(data);
                        that.find('button').removeAttr('disabled');
                        if (data.status == 'success') {
                            //check case update or case insert
                            if (instance.hasClass('add-file') == false) {
                                var html = $(data.html);
                                instance.webuiPopover('destroy');
                                $('#igu-media-file-' + data.id).html(html.html());
                            } else {
                                var container = $('#<?php echo $c_id ?>');
                                var file_view_port = container.find('.file-view-port');

                                file_view_port.find('.no-file').remove();
                                file_view_port.prepend(data.html);

                                var input = $('#<?php echo $target_id ?>');

                                input.val(input.val() + ',' + data.id);
                                that.find(':input:not([type=hidden])').val('');
                                instance.webuiPopover('destroy');
                            }
                        } else {
                            that.find('.form-group').removeClass('has-error has-success');
                            $.each(data.errors, function (i, v) {
                                var element = that.find('.error-' + i);
                                element.parent().addClass('has-error');
                                element.html(v);
                            });
                            that.find('.form-group').each(function () {
                                if (!$(this).hasClass('has-error')) {
                                    $(this).find('.m-b-none').text('');
                                    $(this).addClass('has-success');
                                }
                            });
                        }
                    }
                })
                return false;
            });
            <?php else: ?>
            $('body').on('submit', '.igu-upload-form', function () {
                var that = $(this);
                $.ajax('<?php echo add_query_arg('igu_uploading','1') ?>', {
                    type: 'POST',
                    data: $(this).find(':input').serializeArray(),
                    files: $(this).find(':file').first(),
                    iframe: true,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        that.find('button').attr('disabled', 'disabled');
                    },
                    success: function (data) {
                        data = $(data).text();
                        data = $.parseJSON(data);
                        that.find('button').removeAttr('disabled');
                        if (data.status == 'success') {
                            //check case update or case insert
                            if (instance.hasClass('add-file') == false) {
                                var html = $(data.html);
                                instance.webuiPopover('destroy');
                                $('#igu-media-file-' + data.id).html(html.html());
                            } else {
                                var container = $('#<?php echo $c_id ?>');
                                var file_view_port = container.find('.file-view-port');

                                file_view_port.find('.no-file').remove();
                                file_view_port.prepend(data.html);

                                var input = $('#<?php echo $target_id ?>');

                                input.val(input.val() + ',' + data.id);
                                that.find(':input:not([type=hidden])').val('');
                                instance.webuiPopover('destroy');
                            }
                        } else {
                            that.find('.form-group').removeClass('has-error has-success');
                            $.each(data.errors, function (i, v) {
                                var element = that.find('.error-' + i);
                                element.parent().addClass('has-error');
                                element.html(v);
                            });
                            that.find('.form-group').each(function () {
                                if (!$(this).hasClass('has-error')) {
                                    $(this).find('.m-b-none').text('');
                                    $(this).addClass('has-success');
                                }
                            });
                        }
                    }
                })
                return false;
            });
            <?php endif; ?>
            $('body').on('click', '.igu-file-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var that = $(this);
                var parent = $('#igu-media-file-' + id);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                    data: {
                        action: 'igu_file_delete',
                        id: id,
                        _wpnonce: '<?php echo wp_create_nonce('igu_file_delete') ?>'
                    },
                    beforeSend: function () {
                        /* that.parent().parent().find('button').attr('disabled', 'disabled');
                         that.parent().parent().css('opacity', 0.5);*/
                        parent.find('button').attr('disabled', 'disabled');
                        parent.css('opacity', 0.5);
                    },
                    success: function () {
                        $('#<?php echo $target_id ?>').val($('#<?php echo $target_id ?>').val().replace(id, ''));
                        parent.remove();
                    }
                })
            });
            <?php if(is_admin()): ?>
            var file_frame;
            $('body').on('click', '.upload_image_button', function () {
                if (file_frame) {
                    // Open frame
                    file_frame.open();
                    return;
                }

                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: '<?php echo esc_js(__('Please select a file',je()->domain)) ?>',
                    multiple: false  // Set to true to allow multiple files to be selected
                });

                // When an image is selected, run a callback.
                file_frame.on('select', function () {
                    // We set multiple to false so only get one image from the uploader
                    attachment = file_frame.state().get('selection').first().toJSON();

                    // Do something with attachment.id and/or attachment.url here
                    $('#attachment').val(attachment.id);
                });

                // Finally, open the modal
                file_frame.open();
            });
            <?php endif; ?>
        })
    </script>
</div>