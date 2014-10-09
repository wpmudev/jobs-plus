<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Components_Social_View extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $form = $this->form;
        global $jbp_component_social;
        ?>
        <?php echo $form->hiddenField($model, $attribute, array(
        'class' => 'social-input'
    )) ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?php _e('Social Profile', JBP_TEXT_DOMAIN) ?></strong>
                <button type="button"
                        class="btn btn-primary btn-xs pull-right add-social"><?php _e('Add', JBP_TEXT_DOMAIN) ?>
                    <i class="glyphicon glyphicon-plus"></i></button>
            </div>
            <div class="panel-body">
                <ul class="jbp-socials">
                    <?php
                    $social = $model->$attribute;
                    $social = array_filter(array_unique(explode(',', $social)));
                    if (count($social) == 0) {
                        ?>
                        <p><?php _e('No social profile.', JBP_TEXT_DOMAIN); ?></p>
                    <?php
                    } else {
                        foreach ($social as $key) {
                            $s_model = JobsExperts_Components_Social_Model::instance()->get_one($key, $model->id);
                            echo '<li>';
                            if (is_object($s_model)) {
                                echo $jbp_component_social->template_social_display($s_model->export());
                            }
                            echo '</li>';
                        }
                    }
                    ?>
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var add_state = [];
            var main_input = $('.social-input');
            $('.jbp-social').tooltip();
            $('.add-social').popover({
                content: '<?php echo $this->template_add_social($model) ?>',
                html: true,
                trigger: 'click',
                container: false,
                placement: 'auto'
            }).on('shown.bs.popover', function () {
                var that = $(this);
                var next = that.next();

                var parent = next.find('.social-add-form').first();

                var form = next.find('form').first();
                var preview = next.find('.social-preview').first();

                var pre_select = form.find('select').val();
                var url = '';
                if (add_state.length > 0) {
                    pre_select = add_state[0].value;
                    url = add_state[1].value;
                }
                var overlay = $('<?php echo $this->load_overlay() ?>');
                form.find('select').on('change',function () {
                    //load preview
                    var socials = <?php echo json_encode($jbp_component_social->get_social_list()) ?>;
                    var data = socials[form.find('select').first().val()];
                    preview.find('h4').text(data.name);
                    preview.find('img').attr('src', data.url);
                    next.css('top', 0 - next.height());
                    form.find('.note').text(capitaliseFirstLetter(data.type));
                }).val(pre_select).change();

                form.find(':input').change(function () {
                    add_state = form.serializeArray();
                })

                form.find('.hn-cancel-social').click(function () {
                    that.popover('hide');
                });

                form.submit(function () {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        data: {
                            action: 'jbp_social_add',
                            _nonce: '<?php echo wp_create_nonce('jbp_social_add') ?>',
                            parent_id:<?php echo $model->id ?>,
                            attribute: '<?php echo $attribute ?>',
                            'class': '<?php echo get_class($model) ?>',
                            name: form.find('select[name="social"]').val(),
                            'value': form.find('input[name="value"]').val()
                        },
                        beforeSend: function () {
                            next.append(overlay);
                        },
                        success: function (data) {
                            overlay.remove();
                            data = $.parseJSON(data);
                            parent.find('.alert').remove();
                            if (data.status == 0) {
                                var error = $('<div class="alert alert-danger"/>').html(data.errors);
                                parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                    parent.removeClass('animated shake');
                                });
                                next.css('top', 0 - next.height());
                            } else {
                                var element = $(data.html);
                                //check does this appear in the pool
                                var exist = $('.jbp-socials').find("[data-id='" + element.data('id') + "']");
                                if (exist.size() > 0) {
                                    exist.replaceWith(element);
                                    element.addClass('animated flash').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        element.removeClass('animated flash');
                                    });
                                } else {
                                    $('.jbp-socials').append($('<li/>').html(element)).find('p').remove();
                                }
                                main_input.val(main_input.val() + ',' + element.data('id'));

                                that.popover('hide');
                                $('.jbp-social').tooltip();
                                edit_social();
                            }
                        }
                    })
                    return false;
                })
            })
            edit_social();
            function edit_social() {
                $('.jbp-social').popover({
                    content: '<?php echo $this->template_add_social($model,true) ?>',
                    html: true,
                    trigger: 'click',
                    container: false,
                    placement: 'auto'
                }).on('shown.bs.popover',function () {
                    var that = $(this);
                    var next = that.next();

                    var parent = next.find('.social-add-form').first();
                    var form = next.find('form').first();
                    var preview = next.find('.social-preview').first();

                    //getting data
                    var pre_select = that.data('id');
                    var url = that.data('value');
                    var overlay = $('<?php echo $this->load_overlay() ?>');
                    form.find('select').on('change',function () {
                        //load preview
                        var socials = <?php echo json_encode($jbp_component_social->get_social_list()) ?>;
                        var data = socials[form.find('select').first().val()];
                        preview.find('h4').text(data.name);
                        preview.find('img').attr('src', data.url);
                        next.css('top', 0 - next.height());
                        form.find('.note').text(capitaliseFirstLetter(data.type));
                    }).val(pre_select).change();
                    form.find('input[type="text"]').val(url);

                    form.find('.hn-cancel-social').click(function () {
                        that.popover('hide');
                    });

                    form.find('.hn-delete-social').click(function () {
                        main_input.val(main_input.val().replace(that.data('id'), ''));
                        that.popover('hide');
                        that.remove();
                    })

                    form.submit(function () {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            data: {
                                action: 'jbp_social_add',
                                _nonce: '<?php echo wp_create_nonce('jbp_social_add') ?>',
                                parent_id:<?php echo $model->id ?>,
                                attribute: '<?php echo $attribute ?>',
                                'class': '<?php echo get_class($model) ?>',
                                name: form.find('select[name="social"]').val(),
                                'value': form.find('input[name="value"]').val()
                            },
                            beforeSend: function () {
                                next.append(overlay);
                            },
                            success: function (data) {
                                overlay.remove();
                                data = $.parseJSON(data);
                                parent.find('.alert').remove();
                                if (data.status == 0) {
                                    var error = $('<div class="alert alert-danger"/>').html(data.errors);
                                    parent.addClass('animated shake').prepend(error).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        parent.removeClass('animated shake');
                                    });
                                    next.css('top', 0 - next.height());
                                } else {
                                    var element = $(data.html);
                                    //update main input
                                    main_input.val(main_input.val().replace(that.data('id'),element.data('id')));
                                    that.popover('hide');
                                    that.replaceWith(element);
                                    element.addClass('animated flash').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                        element.removeClass('animated flash');
                                    });

                                    $('.jbp-social').tooltip();
                                    edit_social();
                                }
                            }
                        })
                        return false;
                    })
                }).on('click', function (e) {
                    e.preventDefault();
                });
            }

            function capitaliseFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
        })
        </script>
    <?php
    }

    function template_add_social($model, $edit = false)
    {
        global $jbp_component_social;
        $socials = $jbp_component_social;
        ob_start();
        ?>
        <div class="social-add-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="social-preview">
                        <h4>Name</h4>
                        <img>
                    </div>
                </div>
                <div class="col-md-8">
                    <form method="post">
                        <label><?php _e('Select Social') ?></label>
                        <select name="social">
                            <?php foreach ($jbp_component_social->get_social_list() as $val): ?>
                                <option value="<?php echo $val['key'] ?>">
                                    <?php echo $val['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="note"><?php _e('Social information (Url or Username)') ?></label>
                        <input type="text" name="value">
                        <button class="btn btn-primary btn-sm hn-save-social"
                                type="submit"><?php _e('Submit', JBP_TEXT_DOMAIN) ?></button>
                        &nbsp;
                        <button class="btn btn-default btn-sm hn-cancel-social"
                                type="button"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
                        <?php if ($edit): ?>
                            &nbsp;
                            <button class="btn btn-danger btn-sm hn-delete-social"
                                    type="button"><?php _e('Delete', JBP_TEXT_DOMAIN) ?></button>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
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
}