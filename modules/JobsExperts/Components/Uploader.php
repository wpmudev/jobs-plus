<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Components_Uploader extends JobsExperts_Components
{
    public $id;

    public function __construct()
    {
        $this->id = 'hn_uploader';
        $this->_add_action('init', 'init');
    }

    function init()
    {
        $this->_add_action('admin_enqueue_scripts', 'scripts');
        $this->_add_action('wp_enqueue_scripts', 'scripts');
        $this->_add_action('wp_loaded', 'process_upload');
        $this->_add_ajax_action('hn_delete_file', 'delete_file');
        $this->_add_ajax_action('hn_load_file_data', 'load_file_data');
        $this->_add_ajax_action('expert_delete_avatar', 'delete_avatar');
    }

    function delete_avatar()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'expert_delete_avatar')) {
            $parent_id = $_POST['parent_id'];

            $model = JobsExperts_Core_Models_Pro::instance()->get_one($parent_id);
            if (is_object($model)) {
                delete_post_meta($parent_id, '_expert_avatar');
                echo $model->get_avatar();
            }
        }
        exit;
    }

    function load_file_data()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'hn_load_file_data')) {
            $id = $_POST['id'];
            $model = JobsExperts_Components_Uploader_Model::instance()->get_one($id);
            if (is_object($model)) {
                echo json_encode($model->export());
            }
            exit;
        }
    }

    function delete_file()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'hn_delete_file')) {
            $id = $_POST['id'];
            $parent_id = $_POST['parent_id'];
            $attribute = $_POST['attribute'];
            //find parent
            $class = $_POST['class'];
            $model = $class::instance()->get_one($parent_id);
            if (is_object($model)) {
                //remove the file
                wp_delete_post($id);
                //remove index
                $model->$attribute = str_replace($id, '', $model->$attribute);
                $model->$attribute = explode(',', $model->$attribute);
                $model->$attribute = array_filter($model->$attribute);
                $model->$attribute = implode(',', $model->$attribute);
                $model->save();
            }
            exit;
        }
    }

    function avatar_upload_render($model)
    {
        ?>
        <div class="expert-avatar">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php echo $model->get_avatar(420) ?>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-xs btn-primary change-avatar">
                        <?php _e('Change Avatar', JBP_TEXT_DOMAIN) ?>
                    </button>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.change-avatar').popover({
                    content: '<?php echo $this->avatar_form($model) ?>',
                    html: true,
                    trigger: 'click',
                    container: false,
                    placement: 'right'
                }).on('shown.bs.popover', function (e) {
                    var popover_id = $(this).attr('aria-describedby');
                    //var next = $('#' + popover_id);
                    var next = $(this).next();
                    var that = $(this);
                    //console.log(next);
                    //console.log(next.hasClass('popover'));
                    if (next.hasClass('popover')) {

                        var form = next.find('form').first();
                        //console.log(form);
                        form.find('.hn_uploader_element').change(function (e) {
                            var file = e.target.files[0];
                            var type = file.type.split('/');
                            var size_allowed = '<?php echo (get_max_file_upload() * 1000000) ?>';
                            if (type[0] != 'image') {
                                alert('<?php echo esc_js(__('Avatar must be an image',JBP_TEXT_DOMAIN)) ?>');
                                $(this).val("");
                            } else if (file.size > size_allowed) {
                                alert('<?php echo esc_js(__('File too large.',JBP_TEXT_DOMAIN)) ?>');
                                $(this).val("");
                            }
                        });
                        form.find('.hn-cancel-avatar').click(function () {
                            that.popover('hide');
                        });
                        form.find('.hn-delete-avatar').click(function () {
                            var parent = form.closest('div');
                            var overlay = $('<?php echo $this->load_overlay() ?>');
                            $.ajax({
                                type: 'POST',
                                data: {
                                    parent_id: '<?php echo $model->id ?>',
                                    action: 'expert_delete_avatar',
                                    _nonce: '<?php echo wp_create_nonce('expert_delete_avatar') ?>'
                                },
                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                beforeSend: function () {
                                    next.append(overlay);
                                },
                                success: function (data) {
                                    overlay.remove();
                                    $('.expert-avatar .panel-body').html(data);
                                    $(this).addClass('hide');
                                    that.popover('hide');
                                }
                            })
                        })
                        form.submit(function () {
                            var parent = form.closest('div');
                            var args = {
                                data: form.serialize(),
                                processData: false,
                                iframe: true,
                                method: 'POST',
                                url: '<?php echo add_query_arg(array('upload_file_nonce'=>wp_create_nonce('hn_upload_avatar')),home_url()) ?>'
                            }
                            var file = $(":file", form);

                            if (!file.val()) {
                                alert('<?php echo esc_js(__('Please select a file',JBP_TEXT_DOMAIN)) ?>');
                            } else {
                                args.files = file;
                                var overlay = $('<?php echo $this->load_overlay() ?>');
                                args.beforeSend = function () {
                                    parent.find('.alert').remove();
                                    form.find('button').attr('disabled', 'disabled');
                                    parent.append(overlay);
                                };
                                args.success = function (data) {
                                    //parent.find('.alert').remove();
                                    overlay.remove();
                                    form.find(':input, button').removeAttr('disabled');
                                    var tmp = $(data);
                                    var url = tmp.text();
                                    $('.expert-avatar .panel-body').html('<img src="' + url + '"/>');
                                    that.popover('hide');
                                    form.find('.hn-delete-avatar').removeClass('hide');
                                }
                                $.ajax(args);
                            }
                            return false;
                        })
                    }
                })
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
            <img
                style="box-shadow:none;position: absolute;width:auto;height:auto;top: 50%;left: 50%;margin-top: -15px;margin-left: -15px;"
                src="<?php echo JobsExperts_Plugin::instance()->_module_url ?>assets/image/ajax-loader.gif"/>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }

    function avatar_form($model)
    {
        ob_start();
        ?>
        <div class="file-uploader-form" style="position: relative">
            <form>
                <label>
                    <?php _e('Select image or file', JBP_TEXT_DOMAIN) ?>
                </label>
                <input type="file" class="hn_uploader_element" name="hn_uploader">

                <div class="clearfix" style="margin-top: 5px"></div>
                <input type="hidden" name="parent_id" value="<?php echo $model->id ?>">
                <button class="btn btn-primary btn-sm hn-upload-avatar"
                        type="submit"><?php _e('Submit', JBP_TEXT_DOMAIN) ?></button>
                <?php if ($model->has_avatar()): ?>
                    <button class="btn btn-danger btn-sm hn-delete-avatar"
                            type="button"><?php _e('Delete Uploaded Avatar', JBP_TEXT_DOMAIN) ?></button>
                <?php else: ?>
                    <button class="btn btn-danger btn-sm hn-delete-avatar hide"
                            type="button"><?php _e('Delete Uploaded Avatar', JBP_TEXT_DOMAIN) ?></button>
                <?php endif; ?>
                <button class="btn btn-default btn-sm hn-cancel-avatar"
                        type="button"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
            </form>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }

    function process_upload()
    {
        if (isset($_GET['upload_file_nonce']) && wp_verify_nonce($_GET['upload_file_nonce'], 'hn_upload_file')) {
            $model = '';
            if (isset($_POST['id'])) {
                $model = JobsExperts_Components_Uploader_Model::instance()->get_one($_POST['id']);
            }
            if (!is_object($model)) {
                $model = new JobsExperts_Components_Uploader_Model();
            }
            $model->url = $_POST['link'];
            $model->description = jbp_filter_text($_POST['description']);

            //case frontend, the upload as normal FILE
            if (!is_admin()) {
                if (isset($_FILES['hn_uploader'])) {
                    $model->file_upload = $_FILES['hn_uploader'];
                }
            } else {
                //this is for the admin
                //the admin always ID of the attachment.
                if (!empty($_POST['attachment'])) {
                    $model->file = $_POST['attachment'];
                }
            }

            $model->parent_id = $_POST['parent_id'];

            if ($model->validate()) {
                $result = $model->save();
                if (!is_wp_error($result)) {
                    //return html
                    echo $this->file_template($result);
                } else {
                    echo json_encode(array(
                        'status' => 0,
                        'errors' => $result->get_error_messages()
                    ));
                }
            } else {
                echo json_encode(array(
                    'status' => 0,
                    'errors' => implode('<br/>', $model->get_errors())
                ));
            }

            exit;
        }
        if (isset($_GET['upload_file_nonce']) && wp_verify_nonce($_GET['upload_file_nonce'], 'hn_upload_avatar')) {

            if (!function_exists('wp_handle_upload')) require_once(ABSPATH . 'wp-admin/includes/file.php');
            $uploadedfile = $_FILES['hn_uploader'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            update_post_meta($_POST['parent_id'], '_expert_avatar', $movefile['url']);
            echo $movefile['url'];
            exit;
        }

    }

    function scripts()
    {
        $plugin = JobsExperts_Plugin::instance();
        //wp_register_script($this->id . 'file_uploader', $plugin->_module_url . 'Components/Uploader/assets/jquery.fileupload.js');
        wp_register_script($this->id . 'iframe_transport', $plugin->_module_url . 'Components/Uploader/assets/jquery-iframe-transport.js');
        if (is_admin()) {
            wp_register_script($this->id . 'bootstrap_js', $plugin->_module_url . 'Components/Uploader/assets/bootstrap.min.js');
        }
        wp_register_style('jbp_uploader', $plugin->_module_url . 'Components/Uploader/assets/file-uploader.css');
    }

    public function file_template($model_id)
    {
        ob_start();
        $model = JobsExperts_Components_Uploader_Model::instance()->get_one($model_id);
        if (!is_object($model))
            return;
        ?>
        <div class="hn-media-file border-fade" data-id="<?php echo $model_id ?>">

            <div class="hn-actions">
                <button data-id="<?php echo $model_id ?>" type="button" class="btn btn-primary btn-xs hn-file-update">
                    <i class="glyphicon glyphicon-pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-xs hn-file-delete">
                    <i class="glyphicon glyphicon-trash"></i>
                </button>
            </div>
            <?php
            $colors = array(
                'hn-blue', 'hn-pink', 'hn-dark-blue', 'hn-green', 'hn-black',
                'hn-yellow', 'hn-purple', 'hn-grey', 'hn-green-alt', 'hn-red',
                'hn-marine',
            );
            $color = $colors[array_rand($colors)];
            ?>
            <div <?php echo !is_admin() ? 'style="font-size:5em;padding:19px 0 0 0"' : null ?>
                class="hn-file-icon <?php echo $color ?>">
                <?php echo $model->mime_to_icon() ?>
            </div>
            <div class="hn-file-meta">
                <h5><?php echo $model->name() ?></h5>

                <p class="text-muted"><?php echo get_the_date(null, $model->id) ?></p>
            </div>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }

    function show_on_front($id, $ids)
    {
        $models = JobsExperts_Components_Uploader_Model::instance()->get_all(array(
            'post_type' => 'jbp_media',
            'post_parent' => $id,
            'nopaging' => 1,
            'post__in' => $ids
        ));
        ?>
        <div class="hn-sample-files">
            <div class="row">
                <?php foreach ($models['data'] as $model): ?>
                    <?php
                    $id = uniqid();
                    ?>
                    <div>
                        <?php $colors = array(
                            'hn-blue', 'hn-pink', 'hn-dark-blue', 'hn-green', 'hn-black',
                            'hn-yellow', 'hn-purple', 'hn-grey', 'hn-green-alt', 'hn-red',
                            'hn-marine',
                        ); ?>
                        <div class="hn-widget <?php echo $colors[array_rand($colors)] ?>">
                            <a href="#" data-toggle="modal" data-target="#<?php echo $id ?>"> <i
                                    class="glyphicon glyphicon-info-sign"></i></a>

                            <div class="hn-widget-body">
                                <?php echo $model->mime_to_icon() ?>
                            </div>
                            <div class="hn-widget-footer">
                                <div class="hn-widget-cell">
                                    <p><?php echo jbp_shorten_text($model->name(), 30) ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="<?php echo $id ?>" labelledby="myLargeModalLabel" tabindex="-1"
                             role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title"><?php echo $model->name() ?></h4>
                                    </div>
                                    <div class="modal-body sample-pop" style="max-height:500px;overflow-y:scroll">
                                        <?php
                                        $file = $model->file;
                                        $file_url = '';
                                        $show_image = false;


                                        if ($file) {
                                            $file_url = wp_get_attachment_url($file);
                                            $mime = explode('/', get_post_mime_type($file));
                                            if (array_shift($mime) == 'image') {
                                                $show_image = true;
                                            }
                                        }
                                        if ($show_image) {
                                            echo '<img src="' . $file_url . '"/>';
                                        } elseif ($file) {
                                            //show meta
                                            ?>
                                            <ul class="list-group">
                                                <li class="list-group-item upload-item">
                                                    <i class="glyphicon glyphicon-floppy-disk"></i>
                                                    <?php _e('Size', JBP_TEXT_DOMAIN) ?>:
                                                    <strong><?php echo jbp_format_bytes(filesize(get_attached_file($file))) ?></strong>
                                                </li>
                                                <li class="list-group-item upload-item">
                                                    <i class="glyphicon glyphicon-file"></i>
                                                    <?php _e('Type', JBP_TEXT_DOMAIN) ?>:
                                                    <strong><?php echo ucwords(get_post_mime_type($file)) ?></strong>
                                                </li>
                                            </ul>
                                        <?php
                                        } else {
                                            ?>
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <i class="glyphicon glyphicon-link"></i>
                                                    <strong><?php _e('Link', JBP_TEXT_DOMAIN) ?></strong>:
                                                    <?php echo $model->url ?>
                                                </li>
                                            </ul>
                                        <?php
                                        }
                                        echo $model->description
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <?php if ($model->url): ?>
                                            <a class="btn btn-info" rel="nofollow" href="<?php echo $model->url ?>" target="_blank">Visit
                                                Link</a>
                                        <?php endif; ?>
                                        <?php if ($file): ?>
                                            <a href="<?php echo $file_url ?>" download
                                               class="btn btn-info"><?php _e('Download File', JBP_TEXT_DOMAIN) ?></a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal"><?php _e('Close', JBP_TEXT_DOMAIN) ?>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                //determine width
                var width = $('.hn-sample-files').outerWidth();
                var css = 'col-md-3';
                var data = [
                    {
                        width: 200,
                        css: 'col-md-12'
                    },
                    {
                        width: 400,
                        css: 'col-md-6'
                    },
                    {
                        width: 600,
                        css: 'col-md-4'
                    }
                ];
                $.each(data, function (i, v) {
                    //console.log(width <= v.width);
                    if (width <= v.width) {
                        css = v.css;
                        return false;
                    }
                })
                //console.log(css);
                $('.hn-sample-files .hn-widget').parent().addClass(css);

            })
        </script>
    <?php
    }

    function load_scripts()
    {
        //wp_enqueue_script($this->id . 'file_uploader');
        wp_enqueue_script($this->id . 'iframe_transport');
        wp_enqueue_script($this->id . 'bootstrap_js');
        wp_enqueue_style('jbp_uploader');
    }
}

global $jbp_component_uploader;
$jbp_component_uploader = new JobsExperts_Components_Uploader();