<div class="wrap ig-container">
    <h2><?php _e('Settings', je()->domain) ?></h2>
    <?php if($this->has_flash('je_settings')): ?>
        <div class="alert alert-success"><?php echo $this->get_flash('je_settings') ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <ul id="jbp_setting_nav" style="margin-top: 0;padding-top: 0;margin-right: -1px;z-index:9;padding-right: 0"
                class="nav nav-tabs tabs-left col-md-2 no-padding hidden-sm hidden-xs">
                <li <?php echo je()->get('tab', 'general') == 'general' ? 'class="active"' : null ?>>
                    <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=general') ?>">
                        <i class="glyphicon glyphicon-cog"></i> <?php _e('General Settings', je()->domain) ?>
                    </a>
                </li>
                <li <?php echo je()->get('tab') == 'job' ? 'class="active"' : null  ?>>
                    <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job') ?>">
                        <img
                            src="<?php echo je()->plugin_url ?>assets/image/backend/icons/16px/16px_Jobs_Dark.svg"> <?php _e('Jobs Settings', je()->domain) ?>
                    </a>
                </li>
                <li <?php echo je()->get('tab') == 'expert' ? 'class="active"' : null  ?>>
                    <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert') ?>">
                        <img
                            src="<?php echo je()->plugin_url ?>assets/image/backend/icons/16px/16px_Expert_Dark.svg"> <?php _e('Experts Settings', je()->domain) ?>
                    </a></li>
                <li <?php echo je()->get('tab') == 'shortcode' ? 'class="active"' : null  ?>>
                    <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=shortcode') ?>">
                        <i class="fa fa-info"></i>
                        <?php _e('Shortcodes', je()->domain) ?>
                    </a></li>
                <li <?php echo je()->get('tab') == 'uploader' ? 'class="active"' : null  ?>>
                    <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=uploader') ?>">
                        <i class="glyphicon glyphicon-paperclip"></i>
                        <?php _e(' Attachments', je()->domain) ?>
                    </a></li>
                <?php do_action('jbp_setting_menu') ?>
            </ul>
            <div class="tab-content col-md-10">
                <div class="jbp-setting-content tab-pane active">
                    <?php do_action('je_settings_content_'.je()->get('tab','general')) ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>