<div class="page-header">
    <h3 class="hndle"><span><?php _e('General Options', je()->domain) ?></span></h3>
</div>
<?php $form = new IG_Active_Form($model);
$form->open(array("attributes" => array("class" => "form-horizontal"))); ?>
<div class="form-group">
    <label class="col-md-2 control-label"><?php _e('Icon Colors', je()->domain); ?></label>

    <div class="col-sm-10">
        <div class="radio">
            <label>
                <?php $form->radio('theme', array(
                    'value' => 'dark'
                )) ?>
                <?php printf('%s, <span class="description">%s</span>', esc_html__('Dark Icons', je()->domain), esc_html__('for light button backgrounds', je()->domain)); ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php $form->radio('theme', array(
                    'value' => 'bright'
                )) ?>
                <?php printf('%s, <span class="description">%s</span>', esc_html__('Bright Icons', je()->domain), esc_html__('for dark button backgrounds', je()->domain)); ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php $form->radio('theme', array(
                    'value' => 'none'
                )) ?>
                <?php printf('%s, <span class="description">%s</span>', esc_html__('No Icons', je()->domain), esc_html__('to remove the icons from buttons', je()->domain)); ?>
            </label>
        </div>
                <span
                    class="help-block"><?php _e('Sets the default color of the button icons. May be overriden for individual buttons in the "class" attribute of the shortcode.', je()->domain); ?></span>
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label"><?php _e('Currency', je()->domain); ?></label>

    <div class="col-sm-10">
        <select id="jbp-currency-select" name="<?php echo $form->build_name('currency') ?>">
            <?php
            foreach ($model->currency_list() as $key => $value) {
                ?>
                <option value="<?php echo $key; ?>"<?php selected($model->currency, $key); ?>><?php echo esc_attr($value[0]) . ' - ' . JobsExperts_Helper::format_currency($key); ?></option><?php
            }
            ?>
        </select>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label"><?php _e('Currency Symbol Position', je()->domain); ?></label>

    <div class="col-sm-10">
        <div class="radio">
            <label>
                <?php $form->radio('curr_symbol_position', array(
                    'value' => '1'
                )) ?>
                <?php echo JobsExperts_Helper::format_currency($model->currency); ?>100
            </label>
        </div>
        <div class="radio">
            <label>
                <?php $form->radio('curr_symbol_position', array(
                    'value' => '2'
                )) ?>
                <?php echo JobsExperts_Helper::format_currency($model->currency); ?> 100
            </label>
        </div>
        <div class="radio">
            <label>
                <?php $form->radio('curr_symbol_position', array(
                    'value' => '3'
                )) ?>
                100<?php echo JobsExperts_Helper::format_currency($model->currency); ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php $form->radio('curr_symbol_position', array(
                    'value' => '4'
                )) ?>
                100 <?php echo JobsExperts_Helper::format_currency($model->currency); ?>
            </label>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label"><?php _e('Show Decimal in Prices', je()->domain); ?></label>

    <div class="col-sm-10">
        <div class="radio">
            <label class="radio-inline">
                <?php $form->radio('curr_decimal', array(
                    'value' => '1'
                )) ?>
                <?php _e('Yes', je()->domain) ?>
            </label>
            <label class="radio-inline">
                <?php $form->radio('curr_decimal', array(
                    'value' => '0'
                )) ?>
                <?php _e('No', je()->domain) ?>
            </label>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="page-header">
    <h3 class="hndle"><span><?php _e('Avatar Upload', je()->domain) ?></span></h3>
</div>
<p><?php _e("Which roles can upload custom avatar, please note that role with <strong>upload_files</strong> capability can upload by default", je()->domain) ?></p>
<table class="table table-condensed table-hover">
    <thead>
    <tr>
        <th><?php _e("Role name", je()->domain) ?></th>
        <th><?php _e("Can upload", je()->domain) ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $roles = get_editable_roles();
    foreach ($roles as $key => $role): ?>
        <?php if (isset($role['capabilities']['upload_files']) && $role['capabilities']['upload_files'] == false || !isset($role['capabilities']['upload_files'])): ?>
            <?php $is = in_array($key, $model->allow_avatar); ?>
            <tr>
                <td><?php echo $role['name'] ?></td>
                <td>
                    <div data-key="<?php echo $key ?>" class="btn-group btn-toggle">
                        <button data-value="1" type="button"
                                class="btn btn-xs <?php echo $is == true ? 'btn-primary active' : 'btn-default'; ?>">
                            <?php _e("Yes", je()->domain) ?></button>
                        <button data-value="0" type="button"
                                class="btn btn-xs <?php echo $is == false ? 'btn-primary active' : 'btn-default'; ?>">
                            <?php _e("No", je()->domain) ?></button>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript">
    jQuery(function ($) {
        $('.btn-toggle button').click(function () {
            //get the index
            var index = $(this).parent().find('button').index(this);
            var parent = $(this).parent();
            if (index == 0) {
                var next = $(this).next();
            } else {
                var next = $(this).prev();
            }
            if ($(this).hasClass('active')) {
                $(this).removeClass('active btn-primary').addClass('btn-default');
                next.addClass('active btn-primary').removeClass('btn-default');
            } else {
                next.removeClass('active btn-primary').addClass('btn-default');
                $(this).addClass('active btn-primary').removeClass('btn-default');
            }
            //update value
            $.ajax({
                type: 'POST',
                data: {
                    action: 'upload_avatar_permission',
                    _nonce: '<?php echo wp_create_nonce('upload_avatar_permission') ?>',
                    role: parent.data('key'),
                    value: $(this).data('value')
                },
                url: ajaxurl,
                beforeSend: function () {
                    parent.find('button').attr('disabled', 'disabled');
                },
                success: function () {
                    parent.find('button').removeAttr('disabled');
                }
            })
        })
    })
</script>
<div class="page-header">
    <h3 class="hndle"><span><?php _e('Add-ons', je()->domain) ?></span></h3>
</div>
<div class="">
    <div class="alert alert-success plugin-status hide">

    </div>
    <?php
    $addon = new JE_AddOn_Table();
    $addon->prepare_items();
    $addon->display();
    ?>

</div>
<div class="form-group">
    <div class="col-sm-10">
        <?php wp_nonce_field('je_settings', '_je_setting_nonce') ?>
        <button type="submit" class="btn btn-primary"><?php _e("Save Changes", je()->domain) ?></button>
    </div>
</div>
<?php $form->close() ?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var addon_has_changed = false;
        $('.plugin').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var that = $(this);
            if ($(this).data('type') == 'deactive') {
                $(this).data('type', 'active');
                /*$('#jbp_components').val($('#jbp_components').val().replace(id, ''));*/
                //ajax update
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                    data: {
                        action: 'addons_action',
                        type: 'deactive',
                        id: id,
                        _nonce: '<?php echo wp_create_nonce('addons_action') ?>'
                    },
                    beforeSend: function () {
                        that.attr('disabled', 'disabled');
                    },
                    success: function (data) {
                        that.removeAttr('disabled');
                        that.text('<?php echo esc_js(__('Activate',je()->domain) )?>');
                        $('.notif').html(data).removeClass('hide');
                        $('#jbp_setting_nav').load("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?> #jbp_setting_nav li");
                    }
                })
            } else {
                $(this).data('type', 'deactive');
                //$('#jbp_components').val($('#jbp_components').val() + ',' + id);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                    data: {
                        action: 'addons_action',
                        type: 'active',
                        id: id,
                        _nonce: '<?php echo wp_create_nonce('addons_action') ?>'
                    },
                    beforeSend: function () {
                        that.attr('disabled', 'disabled');
                    },
                    success: function (data) {
                        that.removeAttr('disabled');
                        that.text('<?php echo esc_js(__('Deactivate',je()->domain) )?>');
                        $('.notif').html(data).removeClass('hide');
                        $('#jbp_setting_nav').load("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?> #jbp_setting_nav li");
                    }
                })
            }

            addon_has_changed = false;
            //console.log(addon_has_changed);
        });

        $('#jobs-setting').on('submit', function () {
            addon_has_changed = false;
        });
        $('.mm-plugin').click(function (e) {
            var that = $(this);
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                data: {
                    action: 'je_plugin_action',
                    id: $(this).data('id')
                },
                beforeSend: function () {
                    that.find('.loader-ani').removeClass('hide');
                },
                success: function (data) {
                    that.find('.loader-ani').addClass('hide');
                    $('.plugin-status').html(data.noty);
                    $('.plugin-status').removeClass('hide');
                    that.text(data.text);
                    $('#jbp_setting_nav').load("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?> #jbp_setting_nav li");
                }
            })
        });

        window.onbeforeunload = function () {
            if (addon_has_changed == true) {
                return '<?php echo __('It looks like you have been editing something -- if you leave before submitting your changes will be lost.',je()->domain) ?>';
            }
        }
    })
</script>