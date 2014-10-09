<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Components_Skill_View extends JobsExperts_Framework_Render
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
        global $jbp_component_skill;
        ?>
        <?php echo $form->hiddenField($model, $attribute, array(
        'class' => 'skill-input'
    )) ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?php _e('Skills', JBP_TEXT_DOMAIN) ?></strong>
                <button type="button"
                        class="btn btn-primary btn-xs pull-right add-skill"><?php _e('Add', JBP_TEXT_DOMAIN) ?>
                    <i class="glyphicon glyphicon-plus"></i></button>
            </div>
            <div class="panel-body">
                <div class="jbp-skillbars">
                    <?php
                    $skills = array_filter(array_unique(explode(',', $model->$attribute)));
                    if (empty($skills)) {
                        echo '<p>' . __('No skill added.', JBP_TEXT_DOMAIN) . '</p>';
                    } else {
                        foreach ($skills as $skill) {
                            $smodel = JobsExperts_Components_Skill_Model::instance()->get_one($skill, $model->id);
                            if (is_object($smodel)) {
                                echo $jbp_component_skill->skill_bar_template($smodel);
                            }
                        }
                    }
                    ?>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var state = [];
            $('.add-skill').popover({
                content: '<?php echo $this->template_add_skill($model) ?>',
                html: true,
                trigger: 'click',
                container: false,
                placement: 'left'
            }).on('shown.bs.popover', function () {
                //load slider
                var that = $(this);
                var next = that.next();
                var overlay = $('<?php echo $this->load_overlay() ?>');
                if (next.hasClass('popover')) {
                    var main_input = $('.skill-input');
                    var form = next.find('form').first();
                    var preview = next.find('.skill-preview');
                    var parent = form.closest('div');

                    if (state.length > 0) {
                        $.each(state, function (i, v) {
                            form.find('input[name="' + v.name + '"]').val(v.value).trigger('change');
                            if (v.name == 'animated' && v.value == 'on') {
                                form.find('input[name="animated"]').prop('checked', true).trigger('click');
                            }
                        })
                    }

                    form.find('input[name="name"]').keyup(function () {
                        preview.find('.skill-name').text($(this).val());
                    });

                    form.find('input[name="score"]').on('change mousemove',function () {
                        preview.find('.progress-bar').text($(this).val() + '%').css('width', $(this).val() + '%')
                    }).trigger('change');

                    form.find('select').change(function () {
                        //clear old class
                        form.find('select').find('option').each(function () {
                            preview.find('.progress-bar').removeClass('progress-bar-' + $(this).val());
                        })
                        preview.find('.progress-bar').addClass('progress-bar-' + $(this).val());
                    })

                    form.find('input[name="animated"]').click(function () {
                        var cclss = 'progress-bar progress-bar-' + form.find('select').val();
                        if ($(this).prop('checked') == true) {
                            preview.find('.progress-bar').addClass('progress-bar-striped active');
                        } else {
                            preview.find('.progress-bar').removeClass('progress-bar-striped active');
                        }
                    });

                    form.find('.hn-cancel-skill').click(function () {
                        that.popover('hide');
                    })

                    form.find(':input').change(function () {
                        state = form.serializeArray();
                    });

                    form.submit(function () {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            data: {
                                action: 'jbp_skill_add',
                                _nonce: '<?php echo wp_create_nonce('jbp_skill_add') ?>',
                                parent_id:<?php echo $model->id ?>,
                                attribute: '<?php echo $attribute ?>',
                                'class': '<?php echo get_class($model) ?>',
                                name: form.find('input[name="name"]').val(),
                                value: form.find('input[name="score"]').val(),
                                'css': preview.find('.progress-bar').attr('class')
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
                                    next.css('top', parseInt(next.css('top')) - 50);
                                } else {
                                    var element = $(data.html);
                                    $('.jbp-skillbars').prepend(data.html).find('p').remove();
                                    $('.skill-bar .progress').tooltip();
                                    that.popover('hide');
                                    state = [];
                                    main_input.val(main_input.val() + ',' + element.data('id'));
                                    bind_skill_edit();
                                }
                            }
                        })

                        return false;
                    })
                }
            });
            bind_skill_edit();
            $('.skill-bar .progress').tooltip();

            function bind_skill_edit() {

                $('.edit-skill').popover({
                    content: '<?php echo $this->template_add_skill($model,true) ?>',
                    html: true,
                    trigger: 'click',
                    container: false,
                    placement: 'auto',
                    'title': '<?php _e('Update Skill',JBP_TEXT_DOMAIN) ?>'
                }).on('shown.bs.popover', function () {
                    //load slider
                    var that = $(this);
                    var next = that.next();
                    var overlay = $('<?php echo $this->load_overlay() ?>');
                    var top_parent = that.closest('div').parent();
                    if (next.hasClass('popover')) {
                        var main_input = $('.skill-input');
                        var form = next.find('form').first();
                        var preview = next.find('.skill-preview');
                        var parent = form.closest('div');

                        form.find('input[name="name"]').keyup(function () {
                            preview.find('.skill-name').text($(this).val());
                        }).val(top_parent.data('id')).trigger('change');

                        form.find('input[name="score"]').on('change mousemove',function () {
                            preview.find('.progress-bar').text($(this).val() + '%').css('width', $(this).val() + '%')
                        }).val(top_parent.data('value')).trigger('change');
                        var selected = top_parent.find('.progress-bar').first();
                        var current = form.find('select').val();
                        form.find('select').find('option').each(function () {
                            if (selected.hasClass('progress-bar-' + $(this).val())) {
                                current = $(this).val();
                            }
                        });
                        form.find('select').change(function () {
                            //clear old class
                            form.find('select').find('option').each(function () {
                                preview.find('.progress-bar').removeClass('progress-bar-' + $(this).val());
                            })
                            preview.find('.progress-bar').addClass('progress-bar-' + $(this).val());
                        }).val(current).trigger('change');

                        form.find('input[name="animated"]').click(function () {
                            var cclss = 'progress-bar progress-bar-' + form.find('select').val();
                            if ($(this).prop('checked') == true) {
                                preview.find('.progress-bar').addClass('progress-bar-striped active');
                            } else {
                                preview.find('.progress-bar').removeClass('progress-bar-striped active');
                            }
                        });

                        if (selected.hasClass('active')) {
                            form.find('input[name="animated"]').trigger('click');
                        }
                        form.find('.hn-cancel-skill').click(function () {
                            that.popover('hide');
                        });
                        form.find('.hn-delete-skill').click(function () {
                            if (confirm('<?php  _e('Are you sure?',JBP_TEXT_DOMAIN)?>')) {
                                main_input.val(main_input.val().replace(top_parent.data('id'), ''));
                                top_parent.remove();
                                that.popover().hide();
                            }
                        })

                        form.submit(function () {
                            $.ajax({
                                type: 'POST',
                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                data: {
                                    action: 'jbp_skill_add',
                                    _nonce: '<?php echo wp_create_nonce('jbp_skill_add') ?>',
                                    parent_id: '<?php echo $model->id ?>',
                                    attribute: '<?php echo $attribute ?>',
                                    'class': '<?php echo get_class($model) ?>',
                                    name: form.find('input[name="name"]').val(),
                                    value: form.find('input[name="score"]').val(),
                                    'css': preview.find('.progress-bar').attr('class')
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
                                        next.css('top', parseInt(next.css('top')) - 50);
                                    } else {
                                        var element = $(data.html);
                                        main_input.val(main_input.val().replace(top_parent.data('id'), element.data('id')));
                                        that.popover('hide');
                                        top_parent.replaceWith(element);
                                        element.addClass('animated flash').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                            element.removeClass('animated flash');
                                        });
                                        $('.skill-bar .progress').tooltip();
                                        bind_skill_edit();
                                    }
                                }
                            })

                            return false;
                        })
                    }
                });
            }
        })
        </script>
    <?php
    }

    function template_add_skill($model, $edit = false)
    {
        global $jbp_component_skill;
        ob_start();
        ?>
        <div class="skill-add-form">
            <div class="row">
                <div class="col-md-6" style="box-sizing: border-box">
                    <div class="skill-preview">
                        <div class="page-header">
                            <h5>Preview</h5>
                        </div>
                        <h5 class="skill-name"><?php _e('Skill name') ?></h5>

                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                 aria-valuemax="100" style="width: 0%;">
                                0%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"  style="box-sizing: border-box">
                    <form method="post">
                        <label>Name</label>
                        <input type="text" name="name">
                        <label>Score</label>
                        <input type="range" min="0" max="100" name="score" value="50"/>
                        <label>Color</label>
                        <select name="color">
                            <option value="primary"><?php _e('Blue', JBP_TEXT_DOMAIN) ?></option>
                            <option value="danger"><?php _e('Red', JBP_TEXT_DOMAIN) ?></option>
                            <option value="warning"><?php _e('Orange', JBP_TEXT_DOMAIN) ?></option>
                            <option value="info"><?php _e('Light Blue', JBP_TEXT_DOMAIN) ?></option>
                            <option value="success"><?php _e('Green', JBP_TEXT_DOMAIN) ?></option>
                        </select>
                        <label><?php _e('Enable Animated', JBP_PLUGIN) ?> &nbsp;&nbsp;&nbsp;
                            <input style="position: relative;top:-2px" type="checkbox" name="animated"></label>
                        <button class="btn btn-primary btn-sm hn-save-skill"
                                type="submit"><?php _e('Submit', JBP_TEXT_DOMAIN) ?></button>
                        &nbsp;
                        <button class="btn btn-default btn-sm hn-cancel-skill"
                                type="button"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
                        <?php if ($edit): ?>
                            &nbsp;
                            <button class="btn btn-danger btn-sm hn-delete-skill"
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