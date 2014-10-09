<?php

/**
 * Uploader render class
 * Author: Hoang Ngo
 */
class JobsExperts_Components_Uploader_View extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        if (is_admin()) {
            $this->_to_html_backend();
        } else {
            $this->_to_html_front();
        }
    }

    public function _to_html_backend()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $form = $this->form;
        ?>
        <?php $form->hiddenField($model, 'portfolios', array('class' => 'sample_file_fields')) ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?php _e('Attach specs examples or extra information', JBP_TEXT_DOMAIN) ?></strong>
                <button type="button"
                        class="btn btn-primary btn-xs pull-right add-file"><?php _e('Add') ?> <i
                        class="glyphicon glyphicon-plus"></i></button>
            </div>
            <div class="panel-body file-view-port">
                <?php
                $files = array_unique(array_filter(explode(',', $model->$attribute)));
                if (empty($files)) {
                    ?>
                    <p><?php _e('No sample file.', JBP_TEXT_DOMAIN) ?></p>
                <?php
                } else {
                    global $jbp_component_uploader;
                    foreach ($files as $file) {
                        echo $jbp_component_uploader->file_template($file);
                    }
                }
                ?>
                <div class="clearfix"></div>
            </div>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var main_input = $('.sample_file_fields');


            $('body').on('click', '.hn-file-delete', function () {
                var parent = $(this).closest('div').parent();
                if (confirm('<?php echo esc_js(__('Are you sure?',JBP_TEXT_DOMAIN)) ?>')) {
                    var id = parent.data('id');
                    $.ajax({
                        type: 'POST',
                        data: {
                            id: id,
                            parent_id: '<?php echo $model->id ?>',
                            attribute: '<?php echo $attribute ?>',
                            'class': '<?php echo get_class($model) ?>',
                            action: 'hn_delete_file',
                            _nonce: '<?php echo wp_create_nonce('hn_delete_file') ?>'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            //create overlay
                            var overlay = $('<?php echo $this->load_overlay() ?>');
                            parent.append(overlay);
                        },
                        success: function () {
                            parent.remove();
                            main_input.val(main_input.val().replace(id, ''));
                        }
                    })
                }
            });
            bind_edit_popover();

            function bind_edit_popover() {
                $('.hn-file-update').popover({
                    content: '<?php echo $this->tempate_upload_file($model) ?>',
                    html: true,
                    trigger: 'click',
                    container: false,
                    placement: 'auto'
                }).on('shown.bs.popover', function () {
                    var overlay = $('<?php echo $this->load_overlay() ?>');
                    var next = $(this).next();
                    var that = $(this);

                    if (next.hasClass('popover')) {
                        //remove current overlay
                        next.find('.hn-overlay').remove();
                        var form = next.find('form').first();
                        var parent = form.closest('div');
                        //load form
                        next.append(overlay);
                        $.ajax({
                            type: 'POST',
                            data: {
                                id: that.data('id'),
                                _nonce: '<?php echo wp_create_nonce('hn_load_file_data') ?>',
                                action: 'hn_load_file_data'
                            },
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            success: function (data) {
                                overlay.remove();
                                if (data.length > 0) {
                                    data = $.parseJSON(data);
                                    form.find('input[name="link"]').val(data.url);
                                    form.find('textarea').val(data.description);
                                    if (data.file.length > 0) {
                                        form.find('.text-info').removeClass('hide');
                                        next.css('top', 0 - next.height());
                                    }
                                    form.append('<input type="hidden" name="id" value="' + data.id + '">');
                                    console.log(data);
                                }
                            }
                        });
                        var file_frame;
                        form.find('.upload_image_button').on('click', function () {
                            if (file_frame) {
                                // Open frame
                                file_frame.open();
                                return;
                            }

                            // Create the media frame.
                            file_frame = wp.media.frames.file_frame = wp.media({
                                title: '<?php echo esc_js(__('Please select a file',JBP_TEXT_DOMAIN)) ?>',
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
                        form.find('.hn-cancel-file').click(function () {
                            that.popover('hide');
                        });
                        form.submit(function () {
                            var parent = $(this).closest('div');
                            var form = $(this);
                            var args = {
                                data: form.serialize(),
                                processData: false,
                                iframe: true,
                                method: 'POST',
                                url: '<?php echo add_query_arg(array('upload_file_nonce'=>wp_create_nonce('hn_upload_file')),"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>'
                            }
                            var file = $(":file", form);

                            if (file.val()) {
                                args.files = file;
                            }
                            //upload progess
                            args.xhr = function () {
                                var xhr = new window.XMLHttpRequest();
                                //Upload progress
                                xhr.upload.addEventListener("progress", function (evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = evt.loaded / evt.total;
                                        //Do something with upload progress
                                        //console.log(percentComplete);
                                    }
                                }, false);
                                return xhr;
                            }
                            args.beforeSend = function () {
                                next.find('.alert').remove();
                                form.find('button').attr('disabled', 'disabled');
                                next.append(overlay);
                            };
                            args.success = function (data) {
                                next.find('.alert').remove();
                                overlay.remove();
                                form.find(':input, button').removeAttr('disabled');

                                if (is_json(data)) {
                                    //console.log(parent);
                                    var result = $.parseJSON(data);
                                    var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                    form.closest('div').addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        parent.removeClass('animated shake');
                                    });
                                    next.css('top', 0 - next.height());
                                } else {
                                    //still case error when upload
                                    var tmp = $(data);
                                    if (is_json(tmp.text())) {
                                        next.css('top', 0 - next.height());
                                        var result = $.parseJSON(tmp.text());
                                        var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                        parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                            parent.removeClass('animated shake');
                                        });
                                    } else {
                                        var parent = that.closest('div').parent();
                                        parent.replaceWith(data);
                                        shorten_text();
                                        that.popover('hide');
                                        bind_edit_popover();
                                    }
                                }
                            }
                            $.ajax(args);

                            return false;
                        })
                    }
                })
            }

            shorten_text();
            function shorten_text() {
                // $('.hn-file-meta h5').ellipsis();
            }


            $('.hn-media-file .btn-group').hide();
            $('body').on('mouseenter', '.hn-media-file', function () {
                $(this).find('.btn-group').show();
            }).on('mouseleave', '.hn-media-file', function () {
                $(this).find('.btn-group').hide();
            })
            $('.add-file').popover({
                content: '<?php echo $this->tempate_upload_file($model) ?>',
                html: true,
                trigger: 'click',
                container: false,
                placement: 'auto'
            }).on('shown.bs.popover', function () {
                var next = $(this).next();
                var that = $(this);
                if (next.hasClass('popover')) {
                    var form = next.find('form').first();
                    //replace this with the uploader
                    form.find('.upload_image_button').on('click', function () {
                        var upload_btn = $(this);
                        // Create the media frame.
                        var file_frame = wp.media.frames.file_frame = wp.media({
                            title: '<?php echo esc_js(__('Please select a file',JBP_TEXT_DOMAIN)) ?>',
                            multiple: false  // Set to true to allow multiple files to be selected
                        });

                        // When an image is selected, run a callback.
                        file_frame.on('select', function () {
                            // We set multiple to false so only get one image from the uploader
                            attachment = file_frame.state().get('selection').first().toJSON();

                            // Do something with attachment.id and/or attachment.url here
                            $('#attachment').val(attachment.id).change();
                            console.log(attachment);
                            $('#attachment_name').text(attachment.filename);
                        });

                        // Finally, open the modal
                        file_frame.open();
                    });


                    form.find('.hn-cancel-file').click(function () {
                        that.popover('hide');
                    });
                    form.submit(function () {
                        var parent = $(this).closest('div');
                        var form = $(this);
                        var args = {
                            data: form.serialize(),
                            processData: false,
                            iframe: true,
                            method: 'POST',
                            url: '<?php echo add_query_arg(array('upload_file_nonce'=>wp_create_nonce('hn_upload_file')),"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>'
                        }
                        var file = $(":file", form);

                        if (file.val()) {
                            args.files = file;
                        }
                        var overlay = $('<?php echo $this->load_overlay() ?>');
                        //upload progess
                        args.xhr = function () {
                            var xhr = new window.XMLHttpRequest();
                            //Upload progress
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    //Do something with upload progress
                                    //console.log(percentComplete);
                                }
                            }, false);
                            return xhr;
                        }
                        args.beforeSend = function () {
                            parent.find('.alert').remove();
                            form.find('button').attr('disabled', 'disabled');
                            parent.append(overlay);
                        };
                        args.success = function (data) {
                            parent.find('.alert').remove();
                            overlay.remove();
                            form.find(':input, button').removeAttr('disabled');

                            if (is_json(data)) {
                                var result = $.parseJSON(data);
                                var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                    parent.removeClass('animated shake');
                                });
                            } else {
                                //still case error when upload
                                var tmp = $(data);
                                if (is_json(tmp.text())) {
                                    var result = $.parseJSON(tmp.text());
                                    var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                    parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        parent.removeClass('animated shake');
                                    });
                                } else {
                                    if ($('.file-view-port').find('.hn-media-file').size() == 0) {
                                        $('.file-view-port').find('p').remove();
                                    }
                                    $('.file-view-port').prepend(data)
                                    data = $(data);
                                    $('.sample_file_fields').val($('.sample_file_fields').val() + ',' + data.data('id'));
                                    shorten_text();
                                    that.popover('hide');
                                    bind_edit_popover();
                                }
                            }
                        }
                        $.ajax(args);

                        return false;
                    })
                }
            });
            function is_json(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
        })
        </script>
    <?php
    }

    public function _to_html_front()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $form = $this->form;
        ?>
        <?php $form->hiddenField($model, 'portfolios', array('class' => 'sample_file_fields')) ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?php _e('Attach specs examples or extra information', JBP_TEXT_DOMAIN) ?></strong>
                <button type="button"
                        class="btn btn-primary btn-xs pull-right add-file"><?php _e('Add') ?> <i
                        class="glyphicon glyphicon-plus"></i></button>
            </div>
            <div class="panel-body file-view-port">
                <?php
                $files = array_unique(array_filter(explode(',', $model->$attribute)));
                if (empty($files)) {
                    ?>
                    <p><?php _e('No sample file.', JBP_TEXT_DOMAIN) ?></p>
                <?php
                } else {
                    global $jbp_component_uploader;
                    foreach ($files as $file) {
                        echo $jbp_component_uploader->file_template($file);
                    }
                }
                ?>
                <div class="clearfix"></div>
            </div>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var main_input = $('.sample_file_fields');

            $('body').on('click', '.hn-file-delete', function () {
                var parent = $(this).closest('div').parent();
                if (confirm('<?php echo esc_js(__('Are you sure?',JBP_TEXT_DOMAIN)) ?>')) {
                    var id = parent.data('id');
                    $.ajax({
                        type: 'POST',
                        data: {
                            id: id,
                            parent_id: '<?php echo $model->id ?>',
                            attribute: '<?php echo $attribute ?>',
                            'class': '<?php echo get_class($model) ?>',
                            action: 'hn_delete_file',
                            _nonce: '<?php echo wp_create_nonce('hn_delete_file') ?>'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            //create overlay
                            var overlay = $('<?php echo $this->load_overlay() ?>');
                            parent.append(overlay);
                        },
                        success: function () {
                            parent.remove();
                            main_input.val(main_input.val().replace(id, ''));
                        }
                    })
                }
            });
            bind_edit_popover();

            function bind_edit_popover() {
                $('.hn-file-update').popover({
                    content: '<?php echo $this->tempate_upload_file($model) ?>',
                    html: true,
                    trigger: 'click',
                    container: false,
                    placement: 'auto'
                }).on('shown.bs.popover', function () {
                    var overlay = $('<?php echo $this->load_overlay() ?>');
                    var next = $(this).next();
                    var that = $(this);

                    if (next.hasClass('popover')) {
                        //remove current overlay
                        next.find('.hn-overlay').remove();
                        var form = next.find('form').first();
                        var parent = form.closest('div');
                        //load form
                        next.append(overlay);
                        $.ajax({
                            type: 'POST',
                            data: {
                                id: that.data('id'),
                                _nonce: '<?php echo wp_create_nonce('hn_load_file_data') ?>',
                                action: 'hn_load_file_data'
                            },
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            success: function (data) {
                                overlay.remove();
                                if (data.length > 0) {
                                    data = $.parseJSON(data);
                                    form.find('input[name="link"]').val(data.url);
                                    form.find('textarea').val(data.description);
                                    if (data.file.length > 0) {
                                        form.find('.text-info').removeClass('hide');
                                        next.css('top', 0 - next.height());
                                    }
                                    form.append('<input type="hidden" name="id" value="' + data.id + '">');
                                }
                            }
                        })
                        form.find('.hn_uploader_element').change(function (e) {
                            var file = e.target.files[0];
                            var allowed = <?php echo json_encode(array_values(get_allowed_mime_types())) ?>;
                            var size_allowed = '<?php echo (get_max_file_upload() * 1000000) ?>';
                            if ($.inArray(file.type, allowed) == -1) {
                                alert('<?php echo esc_js(__('File type not allow.',JBP_TEXT_DOMAIN) )?>');
                                $(this).val("");
                            } else if (file.size > size_allowed) {
                                alert('<?php echo esc_js(__('File too large.',JBP_TEXT_DOMAIN)) ?>');
                                $(this).val("");
                            }
                        });
                        form.find('.hn-cancel-file').click(function () {
                            that.popover('hide');
                        });
                        form.submit(function () {
                            var parent = $(this).closest('div');
                            var form = $(this);
                            var args = {
                                data: form.serialize(),
                                processData: false,
                                iframe: true,
                                method: 'POST',
                                url: '<?php echo add_query_arg(array('upload_file_nonce'=>wp_create_nonce('hn_upload_file')),"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>'
                            }
                            var file = $(":file", form);

                            if (file.val()) {
                                args.files = file;
                            }
                            //upload progess
                            args.xhr = function () {
                                var xhr = new window.XMLHttpRequest();
                                //Upload progress
                                xhr.upload.addEventListener("progress", function (evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = evt.loaded / evt.total;
                                        //Do something with upload progress
                                        //console.log(percentComplete);
                                    }
                                }, false);
                                return xhr;
                            }
                            args.beforeSend = function () {
                                next.find('.alert').remove();
                                form.find('button').attr('disabled', 'disabled');
                                next.append(overlay);
                            };
                            args.success = function (data) {
                                next.find('.alert').remove();
                                overlay.remove();
                                form.find(':input, button').removeAttr('disabled');

                                if (is_json(data)) {
                                    //console.log(parent);
                                    var result = $.parseJSON(data);
                                    var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                    form.closest('div').addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        parent.removeClass('animated shake');
                                    });
                                    next.css('top', 0 - next.height());
                                } else {
                                    //still case error when upload
                                    var tmp = $(data);
                                    if (is_json(tmp.text())) {
                                        next.css('top', 0 - next.height());
                                        var result = $.parseJSON(tmp.text());
                                        var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                        parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                            parent.removeClass('animated shake');
                                        });
                                    } else {
                                        var parent = that.closest('div').parent();
                                        parent.replaceWith(data);
                                        shorten_text();
                                        that.popover('hide');
                                        bind_edit_popover();
                                    }
                                }
                            }
                            $.ajax(args);

                            return false;
                        })
                    }
                })
            }

            shorten_text();
            function shorten_text() {
                // $('.hn-file-meta h5').ellipsis();
            }


            $('.hn-media-file .btn-group').hide();
            $('body').on('mouseenter', '.hn-media-file', function () {
                $(this).find('.btn-group').show();
            }).on('mouseleave', '.hn-media-file', function () {
                $(this).find('.btn-group').hide();
            })
            $('.add-file').popover({
                content: '<?php echo $this->tempate_upload_file($model) ?>',
                html: true,
                trigger: 'click',
                container: false,
                placement: 'auto'
            }).on('shown.bs.popover', function () {
                var next = $(this).next();
                var that = $(this);
                if (next.hasClass('popover')) {
                    var form = next.find('form').first();
                    form.find('.hn_uploader_element').change(function (e) {
                        var file = e.target.files[0];
                        var allowed = <?php echo json_encode(array_values(get_allowed_mime_types())) ?>;
                        var size_allowed = '<?php echo (get_max_file_upload() * 1000000) ?>';
                        if ($.inArray(file.type, allowed) == -1) {
                            alert('<?php echo esc_js(__('File type not allow.',JBP_TEXT_DOMAIN) )?>');
                            $(this).val("");
                        } else if (file.size > size_allowed) {
                            alert('<?php echo esc_js(__('File too large.',JBP_TEXT_DOMAIN)) ?>');
                            $(this).val("");
                        }
                    });
                    form.find('.hn-cancel-file').click(function () {
                        that.popover('hide');
                    });
                    form.submit(function () {
                        var parent = $(this).closest('div');
                        var form = $(this);
                        var args = {
                            data: form.serialize(),
                            processData: false,
                            iframe: true,
                            method: 'POST',
                            url: '<?php echo add_query_arg(array('upload_file_nonce'=>wp_create_nonce('hn_upload_file')),"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>'
                        }
                        var file = $(":file", form);

                        if (file.val()) {
                            args.files = file;
                        }
                        var overlay = $('<?php echo $this->load_overlay() ?>');
                        //upload progess
                        args.xhr = function () {
                            var xhr = new window.XMLHttpRequest();
                            //Upload progress
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    //Do something with upload progress
                                    //console.log(percentComplete);
                                }
                            }, false);
                            return xhr;
                        }
                        args.beforeSend = function () {
                            parent.find('.alert').remove();
                            form.find('button').attr('disabled', 'disabled');
                            parent.append(overlay);
                        };
                        args.success = function (data) {
                            parent.find('.alert').remove();
                            overlay.remove();
                            form.find(':input, button').removeAttr('disabled');

                            if (is_json(data)) {
                                var result = $.parseJSON(data);
                                var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                    parent.removeClass('animated shake');
                                });
                            } else {
                                //still case error when upload
                                var tmp = $(data);
                                if (is_json(tmp.text())) {
                                    var result = $.parseJSON(tmp.text());
                                    var error = $('<div class="alert alert-danger"/>').html(result.errors);
                                    parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        parent.removeClass('animated shake');
                                    });
                                } else {
                                    if ($('.file-view-port').find('.hn-media-file').size() == 0) {
                                        $('.file-view-port').find('p').remove();
                                    }
                                    $('.file-view-port').prepend(data)
                                    data = $(data);
                                    $('.sample_file_fields').val($('.sample_file_fields').val() + ',' + data.data('id'));
                                    shorten_text();
                                    that.popover('hide');
                                    bind_edit_popover();
                                }
                            }
                        }
                        $.ajax(args);

                        return false;
                    })
                }
            });
            function is_json(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
        })
        </script>
    <?php
    }

    private function load_overlay()
    {
        ob_start();
        ?>
        <div class="hn-overlay" style="width: 100%;height:100%;position: absolute;z-index: 999;background-color: white;
    filter:alpha(opacity=60);
    -moz-opacity:0.6;
    -khtml-opacity: 0.6;
    opacity: 0.6;top:0;left:0">
            <img style="position: absolute;top: 50%;left: 50%;margin-top: -15px;margin-left: -15px;"
                 src="<?php echo JobsExperts_Plugin::instance()->_module_url ?>assets/image/ajax-loader.gif"/>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }

    private function tempate_upload_file($model)
    {
        ob_start();
        ?>
        <div class="file-uploader-form">
            <form>
                <label>
                    <?php _e('Select image or file', JBP_TEXT_DOMAIN) ?>
                </label>
                <?php if (is_admin()): ?>
                    <div class="clearfix"></div>
                    <button type="button"
                            class="btn btn-info btn-xs upload_image_button"><?php _e('Chose File', JBP_TEXT_DOMAIN) ?></button>
                    <span id="attachment_name"></span>
                    <div class="clearfix"></div>
                    <input type="hidden" name="attachment" id="attachment">
                <?php else: ?>
                    <input type="file" class="hn_uploader_element" name="hn_uploader">
                <?php endif; ?>
                <p class="text-info hide"><?php _e('File attached, upload new file will replace the current file.', JBP_TEXT_DOMAIN) ?></p>
                <label>
                    <?php _e('Add link for more information', JBP_TEXT_DOMAIN) ?>
                </label>
                <input type="text" value="" style="max-width: 300px" name="link">
                <label><?php _e('Description', JBP_TEXT_DOMAIN) ?></label>
                <textarea name="description" style="max-width: 300px" rows="4"></textarea>

                <div class="clearfix" style="margin-top: 5px"></div>
                <input type="hidden" name="parent_id" value="<?php echo $model->id ?>">
                <button class="btn btn-primary btn-sm hn-save-file"
                        type="submit"><?php _e('Submit', JBP_TEXT_DOMAIN) ?></button>
                <button class="btn btn-default btn-sm hn-cancel-file"
                        type="button"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
            </form>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }

}