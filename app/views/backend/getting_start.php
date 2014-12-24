<div class="wrap">
    <div class="ig-container">
        <div class="row hidden-lg hidden-md">
            <div class="col-xs-12 text-center">
                <h4><?php _e('Get started with Jobs & Experts', je()->domain); ?></h4>

            </div>
        </div>
        <div class="row hidden-lg hidden-md">
            <div class="col-xs-12 col-sm-12">
                <p class="first text-center">
                    <a href="<?php echo get_post_type_archive_link('jbp_job') ?>" target="jobs"
                       class="jbp_button jbp_job_pages"><?php echo esc_html(sprintf(__('View %s Listings', je()->domain), $job_labels->name)); ?></a>
                </p>

                <p class="text-center">
                    <a href="<?php echo get_edit_post_link(je()->pages->page(JE_Page_Factory::JOB_LISTING)) ?>"><?php _e('Edit', je()->domain) ?></a><?php _e(' this virtual page', je()->domain) ?>
                </p>

                <p class="text-center">
                    <a href="<?php echo get_permalink(je()->pages->page(JE_Page_Factory::JOB_ADD)) ?>"><?php _e('Add', je()->domain) ?></a> <?php echo esc_html(sprintf(__('a new %s', je()->domain), $job_labels->singular_name)); ?>
                </p>
            </div>
            <hr/>
            <div class="col-xs-12 col-sm-12">
                <p class="first text-center">
                    <a href="<?php echo get_permalink(je()->pages->page(JE_Page_Factory::LANDING_PAGE)) ?>"
                       target="landing"
                       class="jbp_button jbp_landing_page"><?php _e('Jobs & Experts Overview', je()->domain); ?></a>
                </p>

                <p class="text-center">
                    <a href="<?php echo get_edit_post_link(je()->pages->page(JE_Page_Factory::LANDING_PAGE)) ?>"><?php _e('Edit', je()->domain); ?></a> <?php _e(' this virtual page', je()->domain) ?>
                </p>
            </div>
            <hr/>
            <div class="col-xs-12 col-sm-12">
                <p class="first text-center">
                    <a href="<?php echo get_post_type_archive_link('jbp_pro') ?>" target="pros"
                       class="jbp_button jbp_experts_pages"><?php echo esc_html(sprintf(__('View %s Listings', je()->domain), $pro_labels->name)); ?></a>
                </p>

                <p class="text-center">
                    <a href="<?php echo get_edit_post_link(je()->pages->page(JE_Page_Factory::EXPERT_LISTING)) ?>"><?php _e('Edit', je()->domain) ?></a> <?php _e(' this virtual page', je()->domain) ?>
                </p>

                <p class="text-center">
                    <a href="<?php echo get_permalink(je()->pages->page(JE_Page_Factory::EXPERT_ADD)) ?>"><?php _e('Add', je()->domain) ?></a> <?php echo esc_html(sprintf(__('a new %s', je()->domain), $pro_labels->singular_name)); ?>
                </p>
            </div>
        </div>


        <div class="jbp_started_page hidden-xs hidden-sm">
            <h2><?php _e('Get started with Jobs & Experts', je()->domain); ?></h2>

            <div style="display: inline-table; width: 20%">

                <!--<p class="jbp_light"><?php /*_e('Thank you! for using our Jobs & Expert plugin', je()->domain) */?></p>-->

                <p class="jbp_default">
                    <?php /*_e( 'To get started just create some demo content or browse, edit and add content using the buttons below. You can return to this page at anytime.', je()->domain ) */ ?><!--</p>
-->
                    <!--<div class="jbp-demo">
					<p class="first">
						<a href="<?php /*echo esc_attr( 'edit.php?post_type=jbp_job&page=jobs-plus-about&create-demo=true' ); */ ?>" class="jbp_button"><?php /*_e( 'Create demo Jobs & Experts content', je()->domain ); */ ?></a>
					</p>
				</div>-->

                    <?php echo do_action('jbp_notice'); ?>

                <p><img src="<?php echo je()->plugin_url . 'assets/image/backend/getting-started.png'; ?>"/></p>

                <div class="jbp_plans">
                    <div class="jbp_plan">
                        <p class="first">
                            <a href="<?php echo get_post_type_archive_link('jbp_job') ?>" target="jobs"
                               class="jbp_button jbp_job_pages"><?php echo esc_html(sprintf(__('View %s Listings', je()->domain), $job_labels->name)); ?></a>
                        </p>

                        <p>
                            <a href="<?php echo get_edit_post_link(je()->pages->page(JE_Page_Factory::JOB_LISTING)) ?>"><?php _e('Edit', je()->domain) ?></a><?php _e(' this virtual page', je()->domain) ?>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job') ?>"><?php _e('Manage', je()->domain) ?></a> <?php echo esc_html(sprintf(__('%s', je()->domain), $job_labels->name)); ?>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-add-job') ?>"><?php _e('Add', je()->domain) ?></a> <?php echo esc_html(sprintf(__('a new %s', je()->domain), $job_labels->singular_name)); ?>
                        </p>

                        <p>
                            <?php echo esc_html(sprintf(__('%s', je()->domain), $job_labels->name)); ?>  <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job') ?>"><?php _e('Settings', je()->domain) ?></a>
                        </p>
                    </div>
                    <div class="jbp_plan">
                        <p class="first">
                            <a href="<?php echo get_permalink(je()->pages->page(JE_Page_Factory::LANDING_PAGE)) ?>"
                               target="landing"
                               class="jbp_button jbp_landing_page"><?php _e('Jobs & Experts Overview', je()->domain); ?></a>
                        </p>

                        <p>
                            <a href="<?php echo get_edit_post_link(je()->pages->page(JE_Page_Factory::LANDING_PAGE)) ?>"><?php _e('Edit', je()->domain); ?></a> <?php _e(' this virtual page', je()->domain) ?>
                        </p>
                    </div>
                    <div class="jbp_plan">
                        <p class="first">
                            <a href="<?php echo get_post_type_archive_link('jbp_pro') ?>" target="pros"
                               class="jbp_button jbp_experts_pages"><?php echo esc_html(sprintf(__('View %s Listings', je()->domain), $pro_labels->name)); ?></a>
                        </p>

                        <p>
                            <a href="<?php echo get_edit_post_link(je()->pages->page(JE_Page_Factory::EXPERT_LISTING)) ?>"><?php _e('Edit', je()->domain) ?></a> <?php _e(' this virtual page', je()->domain) ?>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_pro') ?>"><?php _e('Manage', je()->domain) ?></a> <?php echo esc_html(sprintf(__('%s', je()->domain), $pro_labels->name)); ?>
                        </p>

                        <p>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_pro&page=jobs-plus-add-pro') ?>"><?php _e('Add', je()->domain) ?></a> <?php echo esc_html(sprintf(__('a new %s', je()->domain), $pro_labels->singular_name)); ?>
                        </p>

                        <p>
                            <?php echo esc_html(sprintf(__('%s', je()->domain), $pro_labels->name)); ?>  <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert') ?>"><?php _e('Settings', je()->domain) ?></a>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>