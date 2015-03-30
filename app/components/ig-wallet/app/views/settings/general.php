<?php $data = array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')); ?>
<?php $form = new IG_Active_Form($model);
$form->open(array("attributes" => array("class" => "form-horizontal"))); ?>
    <div class="form-group <?php echo $model->has_error("my_wallet_page") ? "has-error" : null ?>">
        <?php $form->label("my_wallet_page", array("text" => "My Wallet Page", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
        <div class="col-lg-10">
            <div class="row">
                <div class="col-md-6">
                    <?php $form->select("my_wallet_page", array(
                        'data' => $data,
                        "attributes" => array("class" => "form-control"),
                        'nameless' => __('--Choose--', je()->domain))) ?>
                </div>
                <div class="col-md-6">
                    <button type="button" data-id="wallet_page"
                            class="button button-primary wallet-create-page"><?php _e('Create Page', je()->domain) ?></button>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group <?php echo $model->has_error("plans_page") ? "has-error" : null ?>">
        <?php $form->label("plans_page", array("text" => "Plans Page", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
        <div class="col-lg-10">
            <div class="row">
                <div class="col-md-6">
                    <?php $form->select("plans_page", array(
                        'data' => $data,
                        "attributes" => array("class" => "form-control"),
                        'nameless' => __('--Choose--', je()->domain))) ?>
                </div>
                <div class="col-md-6">
                    <button type="button" data-id="plans_page"
                            class="button button-primary wallet-create-page"><?php _e('Create Page', je()->domain) ?></button>
                </div>
            </div>
                                <span
                                    class="help-block m-b-none error-plans_page"><?php $form->error("plans_page") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <div class="col-lg-10 col-lg-offset-2">
            <button type="submit" name="je_credit_setting_save" value="1" class="btn btn-primary">
                <?php _e("Save Changes", je()->domain) ?>
            </button>
        </div>
    </div>
<?php $form->close(); ?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('body').on('click', '.wallet-create-page', function () {
            var that = $(this);
            $.ajax({
                type: 'POST',
                data: {
                    type: $(this).data('id'),
                    action: 'jbp_create_credits_page'
                },
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                beforeSend: function () {
                    that.attr('disabled', 'disabled').text('<?php echo esc_js(__('Creating...',je()->domain)) ?>');
                },
                success: function (data) {
                    var element = that.parent().parent().find('select').first();
                    $.get('<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>', function (html) {
                        html = $(html);
                        var clone = html.find('select[name="' + element.attr('name') + '"]');
                        element.replaceWith(clone);
                        that.removeAttr('disabled').text('<?php echo esc_js(__('Create Page',je()->domain)) ?>');
                    });
                }
            })
        })
    })
</script>