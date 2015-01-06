<?php

/**
 * Name: Custom layout
 * Description: Dynamic layout for jobs/experts listing page
 * Author: WPMU DEV
 */
class JE_Custom_Layout
{
    public function __construct()
    {
        add_action('jbp_setting_menu', array(&$this, 'menu'));
        add_action('je_settings_content_custom_layout', array(&$this, 'content'));

        add_action('je_saved_setting', array(&$this, 'save_setting'));

        add_filter('jbp_jobs_list_layout', array(&$this, 'layout_modify'));
        add_filter('jbp_expert_list_layout', array(&$this, 'expert_layout_modify'));
    }

    function expert_layout_modify( $layouts ) {
        $custom_layout = get_option( 'jbp_experts_custom_layout' );
        if ( ! empty( $custom_layout ) ) {
            $custom_layout = trim( nl2br( $custom_layout ) );
            $custom_layout = explode( '<br />', $custom_layout );
            //filter empty row
            $custom_layout = array_filter( $custom_layout );

            return $custom_layout;
        }

        return $layouts;
    }

    function layout_modify( $layouts ) {
        $custom_layout = get_option( 'jbp_jobs_custom_layout' );
        if ( ! empty( $custom_layout ) ) {
            $custom_layout = trim( nl2br( $custom_layout ) );
            $custom_layout = explode( '<br />', $custom_layout );
            //filter empty row
            $custom_layout = array_filter( $custom_layout );

            return $custom_layout;
        }

        return $layouts;
    }

    function save_setting()
    {
        if (!wp_verify_nonce(je()->post('_je_setting_nonce'), 'je_settings')) {
            return '';
        }

        if (isset($_POST['jobs_custom_layout'])) {
            update_option('jbp_jobs_custom_layout', $_POST['jobs_custom_layout']);
        }
        if (isset($_POST['experts_custom_layout'])) {
            update_option('jbp_experts_custom_layout', $_POST['experts_custom_layout']);
        }
    }

    function menu()
    {
        ?>
        <li <?php echo je()->get('tab') == 'custom_layout' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=custom_layout') ?>">
                <i class="dashicons dashicons-align-center"></i> <?php _e('Custom Layout', je()->domain) ?>
            </a></li>
    <?php
    }

    function content()
    {
        ?>
        <form method="post">
            <div class="page-header" style="margin-top: 0">
                <h3><?php _e('Jobs listing page custom layout', je()->domain) ?></h3>
            </div>
            <p><?php _e("We have 4 sizes:", je()->domain) ?></p>
            <ul>
                <li><strong>lg</strong> <?php _e('means the job block will have 100% width', je()->domain) ?></li>
                <li><strong>md</strong> <?php _e('means the job block will have 1/2 width', je()->domain) ?></li>
                <li><strong>sm</strong> <?php _e('means the job block will have 1/3 width', je()->domain) ?></li>
                <li><strong>xs</strong> <?php _e('means the job block will have 1/4 width', je()->domain) ?></li>
            </ul>
            <p><?php _e('You can customise the layout using the above, each row should equal 100%. For example sm, sm, sm is 100%.', je()->domain) ?></p>

            <p><?php _e('Example for default layout', je()->domain) ?></p>

            <div class="well well-sm">
                <span>lg</span>

                <div class="clearfix"></div>
                <span>md,md</span>

                <div class="clearfix"></div>
                <span>lg</span>

                <div class="clearfix"></div>
                <span>md,md</span>

                <div class="clearfix"></div>
            </div>
            <?php $result = get_option('jbp_jobs_custom_layout') != false ? get_option('jbp_jobs_custom_layout') : null; ?>
            <textarea name="jobs_custom_layout" style="width: 100%" rows="5"><?php echo $result ?></textarea>

            <div class="page-header" style="margin-top: 0">
                <h3><?php _e('Expert listing page custom layout', je()->domain) ?></h3>
            </div>
            <p>We have 4 size:</p>
            <ul>
                <li><strong>lg</strong> <?php _e('mean the expert block will have 100% width', je()->domain) ?>
                </li>
                <li><strong>md</strong> <?php _e('mean the expert block will have 1/2 width', je()->domain) ?></li>
                <li><strong>sm</strong> <?php _e('mean the expert block will have 1/3 width', je()->domain) ?></li>
                <li><strong>xs</strong> <?php _e('mean the expert block will have 1/4 width', je()->domain) ?></li>
            </ul>
            <p><?php _e('You can customize the layout, each row should have 100% score, and the default is xs', je()->domain) ?></p>

            <p><?php _e('Example for default layout', je()->domain) ?></p>

            <div class="well well-sm">
                <span>sm,sm,sm</span>

                <div class="clearfix"></div>
                <span>sm,sm,sm</span>
            </div>
            <?php $result = get_option('jbp_experts_custom_layout') != false ? get_option('jbp_experts_custom_layout') : null;?>
            <textarea name="experts_custom_layout" style="width: 100%" rows="5"><?php echo $result ?></textarea>

            <div class="clearfix"></div>
            <br/>
            <?php wp_nonce_field('je_settings', '_je_setting_nonce') ?>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><?php _e("Save Changes", je()->domain) ?></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    <?php
    }
}

new JE_Custom_Layout();