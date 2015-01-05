<div class="panel panel-default">
    <div class="panel-heading">
        <?php _e("Social Profile", ig_social_wall()->domain) ?>
        <button type="button"
                class="btn btn-primary btn-xs pull-right add-social"><?php _e('Add', ig_social_wall()->domain) ?>
            <i class="glyphicon glyphicon-plus"></i></button>
    </div>
    <div class="panel-body">
        <ul class="jbp-socials">
            <?php foreach ($models as $model) {
                echo '<li>';
                $this->render_partial('_icon', array(
                    'data' => $model->export(),
                    'social' => ig_social_wall()->social($model->name)
                ));
                echo '</li>';
            } ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        var width = 'auto';
        var height = 'auto';
        var instance = null;

        function is_mobile() {
            var check = false;
            (function (a, b) {
                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))check = true
            })(navigator.userAgent || navigator.vendor || window.opera);
            return check;
        }

        if (is_mobile()) {
            width = $(window).width() * 80 / 100;
            //height = 162;
        }
        $('body').on('mouseenter', '.add-social', function () {
            if ($(this).data('plugin_webuiPopover') == undefined) {
                $(this).webuiPopover({
                    type: 'async',
                    width: width,
                    height: height,
                    placement: 'top-left',
                    url: '<?php echo admin_url('admin-ajax.php?action=social_wall_form&_wpnonce='.wp_create_nonce('social_wall_form')) ?>',
                    content: function (data) {
                        return data;
                    }
                }).on('show.webui.popover', function () {
                    $('select[name="social"]').trigger('change');
                    var that = $(this);
                    var pop = that.data('plugin_webuiPopover');
                    var container = pop.$target;
                    $('body').on('click', '.hn-cancel-social', function () {
                        pop.hide();
                    });
                    instance = that;
                });
            }
        });

        $('body').on('mouseenter', '.jbp-social', function () {
            if ($(this).data('plugin_webuiPopover') == undefined) {
                $(this).webuiPopover({
                    type: 'async',
                    width: width,
                    height: height,
                    placement: 'top-left',
                    url: '<?php echo admin_url('admin-ajax.php?action=social_wall_form&_wpnonce='.wp_create_nonce('social_wall_form')) ?>&parent_id=<?php echo $parent->id ?>&id=' + $(this).data('id'),
                    content: function (data) {
                        return data;
                    }
                }).on('show.webui.popover', function () {
                    //$('select[name="social"]').trigger('change');
                    var that = $(this);
                    var pop = that.data('plugin_webuiPopover');
                    $('body').on('click', '.hn-cancel-social', function () {
                        pop.hide();
                    });
                    instance = that;
                    var form = pop.$target.find('form');

                });
            }
        })

        $('body').on('change', 'select[name="social"]', function () {
            var socials = <?php echo json_encode(ig_social_wall()->get_social_list()) ?>;
            var form = $(this).closest('form');
            var data = socials[form.find('select').first().val()];
            var preview = form.find('.social-preview');
            preview.find('h4').text(data.name);
            preview.find('img').attr('src', data.url);
            form.find('.note').text(capitaliseFirstLetter(data.type));
        });

        $('body').on('submit', '.social-form', function () {
            var form = $(this);
            var parent = $('.social-add-form').parent();
            var target = $('#<?php echo $element ?>');
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                beforeSend: function () {
                    parent.find('.ig-overlay').removeClass('hide');
                    parent.find('.alert').addClass('hide');
                },
                data: {
                    action: 'social_add',
                    name: form.find('select[name="social"]').val(),
                    value: form.find('input[name="value"]').val(),
                    parent_id: '<?php echo $parent->id ?>',
                    _wpnonce: '<?php echo wp_create_nonce('social_add') ?>'
                },
                success: function (data) {
                    parent.find('.ig-overlay').addClass('hide');
                    data = $.parseJSON(data);

                    if (data.status == 0) {
                        parent.find('.alert').html(data.errors).removeClass('hide');
                        instance.webuiPopover('reposition');
                    } else {
                        var element = $(data.html);
                        if (instance.hasClass('add-social')) {
                            //check does this appear in the pool
                            var exist = $('.jbp-socials').find("[data-id='" + element.data('id') + "']");
                            if (exist.size() > 0) {
                                exist.replaceWith(element);
                                element.addClass('animated flash').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                    element.removeClass('animated flash');
                                });
                            } else {
                                $('.jbp-socials').append($('<li/>').html(data.html));
                            }
                            instance.webuiPopover('destroy');
                            target.val(target.val() + ',' + element.data('id'));
                        } else {
                            instance.webuiPopover('destroy');
                            instance.replaceWith(element);
                            target.val(target.val().replace(instance.data('id'), element.data('id')));
                        }
                    }
                }
            })
            return false;
        })
        $('body').on('click', '.hn-delete-social', function () {
            var target = $('#<?php echo $element ?>');
            target.val(target.val().replace(instance.data('id'), ''));
            instance.webuiPopover('destroy');
            instance.remove();
        })
        function capitaliseFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    })
</script> 