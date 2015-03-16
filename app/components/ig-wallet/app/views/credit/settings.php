<div class="wrap">
    <h2><?php _e("Settings", je()->domain) ?></h2>
    <br/>
    <?php $data = array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')); ?>
    <div class="ig-container">
        <?php if ($this->has_flash('wallet_settings_saved')): ?>
            <div class="alert alert-success">
                <?php echo $this->get_flash('wallet_settings_saved') ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <ul id="jbp_setting_nav"
                    style="margin-top: 0;padding-top: 0;margin-right: -1px;z-index:9;padding-right: 0"
                    class="nav nav-tabs tabs-left col-md-2 no-padding hidden-sm hidden-xs">
                    <li <?php echo je()->get('tab', 'general') == 'general' ? 'class="active"' : null ?>>
                        <a href="<?php echo admin_url('admin.php?page=ig-credits-setting') ?>">
                            <i class="glyphicon glyphicon-cog"></i> <?php _e('General Settings', je()->domain) ?>
                        </a>
                    </li>
                    <li <?php echo je()->get('tab') == 'give_credit' ? 'class="active"' : null ?>>
                        <a href="<?php echo admin_url('admin.php?page=ig-credits-setting&tab=give_credit') ?>">
                            <i class="fa fa-bank"></i> <?php _e('Send Credits', je()->domain) ?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content col-md-10">
                    <div class="jbp-setting-content tab-pane active">
                        <?php do_action('je_credit_settings_content_' . je()->get('tab', 'general')) ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('body').on('click', '.create-page', function () {
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