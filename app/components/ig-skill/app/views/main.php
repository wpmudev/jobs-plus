<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?php _e('Skills', ig_skill()->domain) ?></strong>
        <button type="button"
                class="btn btn-primary btn-xs pull-right add-skill"><?php _e('Add', ig_skill()->domain) ?>
            <i class="glyphicon glyphicon-plus"></i></button>
    </div>
    <div class="panel-body">
        <div class="jbp-skillbars">
            <?php
            foreach ($models as $model) {
                $this->render_partial('_icon', array(
                    'model' => $model
                ));
            }
            ?>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        if ($.fn.tooltip != undefined) {
            function enable_tootip() {
                $('.edit-skill').tooltip({
                    position: {
                        my: "center bottom-15",
                        at: "center top"
                    },
                    tooltipClass: 'ig-container'
                });
            }

            enable_tootip();
        }
        var instance = null;
        $('body').on('mouseenter', '.add-skill', function () {
            if ($(this).data('plugin_webuiPopover') == undefined) {
            $(this).webuiPopover({
                    type: 'async',
                    placement: 'top-left',
                    url: '<?php echo admin_url('admin-ajax.php?action=social_skill_form&_wpnonce='.wp_create_nonce('social_skill_form')) ?>',
                    content: function (data) {
                        return data;
                    }
                }).on('show.webui.popover', function () {
                    var that = $(this);
                    var pop = that.data('plugin_webuiPopover');
                    $('body').on('click', '.hn-cancel-skill', function () {
                        pop.hide();
                    });
                    instance = that;
                    pop.$target.find('form').find('input[name="score"]').trigger('change');
                });
            }
        })

        $('body').on('mouseenter', '.edit-skill', function () {
            if ($(this).data('plugin_webuiPopover') == undefined) {
                var id = $(this).closest('div').parent().data('id');
                $(this).webuiPopover({
                    type: 'async',
                    placement: 'top-left',
                    url: '<?php echo admin_url('admin-ajax.php?action=social_skill_form&_wpnonce='.wp_create_nonce('social_skill_form')) ?>&parent_id=<?php echo $parent->id ?>&id=' + id,
                    content: function (data) {
                        return data;
                    }
                }).on('show.webui.popover', function () {
                    var that = $(this);
                    var pop = that.data('plugin_webuiPopover');
                    $('body').on('click', '.hn-cancel-skill', function () {
                        pop.hide();
                    });
                    instance = that;
                    pop.$target.find('form').find('input[name="score"]').trigger('change');
                    pop.$target.find('form').find('select').trigger('change');
                });
            }
        })
        $('body').on('keyup', 'input[name="name"]', function () {
            var preview = $('.skill-preview');
            preview.find('.skill-name').text($(this).val());
        });

        $('body').on('change mousemove', 'input[name="score"]', function () {
            $('.skill-preview').find('.progress-bar').text($(this).val() + '%').css('width', $(this).val() + '%')
        });

        $('body').on('change', 'select[name="color"]', function () {
            //clear old class
            $(this).find('option').each(function () {
                $('.skill-preview').find('.progress-bar').removeClass('progress-bar-' + $(this).val());
            });
            $('.skill-preview').find('.progress-bar').addClass('progress-bar-' + $(this).val());
        })

        $('body').on('click', 'input[name="animated"]', function () {
            if ($(this).prop('checked') == true) {
                $('.skill-preview').find('.progress-bar').addClass('progress-bar-striped active');
            } else {
                $('.skill-preview').find('.progress-bar').removeClass('progress-bar-striped active');
            }
        });

        $('body').on('submit', '.ig-skill-form', function () {
            var form = $(this);
            var parent = $('.skill-add-form').parent();
            var input = $('#<?php echo $element ?>');
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                data: {
                    action: 'jbp_skill_add',
                    _nonce: '<?php echo wp_create_nonce('jbp_skill_add') ?>',
                    parent_id: '<?php echo $parent->id ?>',
                    name: form.find('input[name="name"]').val(),
                    value: form.find('input[name="score"]').val(),
                    css: $('.skill-preview').find('.progress-bar').attr('class')
                },
                beforeSend: function () {
                    parent.find('.ig-overlay').removeClass('hide');
                    parent.find('.alert').addClass('hide');
                },
                success: function (data) {
                    parent.find('.ig-overlay').addClass('hide');
                    data = $.parseJSON(data);
                    if (data.status == 0) {
                        parent.find('.alert').html(data.errors).removeClass('hide');
                        instance.webuiPopover('reposition');
                    } else {
                        if (instance.hasClass('add-skill')) {
                            var element = $(data.html);
                            $('.jbp-skillbars').prepend(data.html).find('p').remove();
                            instance.webuiPopover('destroy');
                            enable_tootip();
                            input.val(input.val() + ',' + element.data('id'));
                        } else {
                            instance.webuiPopover('destroy');
                            var element = $(data.html);
                            var key = element.data('id');
                            input.val(input.val().replace(instance.parent().data('id'), key));
                            instance.parent().replaceWith(element);
                        }
                    }
                }
            })
            return false;
        })
        $('body').on('click', '.hn-delete-skill', function () {
            instance.webuiPopover('destroy');
            var input = $('#<?php echo $element ?>');
            input.val(input.val().replace(instance.parent().data('id'), ''));
            instance.parent().remove();
        })

    })
</script>