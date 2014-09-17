<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * Setting page class.
 *
 * @category   JobsExperts
 * @package    Render
 * @subpackage Feeds
 *
 * @since      1.0.0
 */
class JobsExperts_Core_Views_GettingStart extends JobsExperts_Framework_Render
{

    /**
     * Constructor.
     *
     * @sicne  1.0.0
     *
     * @access public
     *
     * @param array $data The array of data associated with current template.
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    function _to_html()
    {
        $plugin = JobsExperts_Plugin::instance();
        $job_labels = JobsExperts_Plugin::instance()->get_job_type()->labels;
        $pro_labels = JobsExperts_Plugin::instance()->get_expert_type()->labels;
        $page_module = $plugin->page_module();
        ?>
        <div class="hn-container">
            <div class="row">
                <div class="col-md-12">
                    <h2><?php esc_html_e('Welcome to Jobs & Experts - Getting Started & Overview Page', JBP_TEXT_DOMAIN); ?></h2>
                    <p class="text-muted"><?php esc_html_e('Thank you! for using our Jobs & Expert plugin', JBP_TEXT_DOMAIN) ?></p>
                </div>
            </div>
        </div>
    <?php
    }

    function _to_html1()
    {
        $plugin = JobsExperts_Plugin::instance();
        $job_labels = JobsExperts_Plugin::instance()->get_job_type()->labels;
        $pro_labels = JobsExperts_Plugin::instance()->get_expert_type()->labels;
        $page_module = $plugin->page_module();
        ?>
        <div class="hn-container">
            <div class="row  hidden-lg hidden-md">
                <div class="col-xs-12 text-center">
                    <h4><?php esc_html_e('Welcome to Jobs & Experts - Getting Started & Overview Page', JBP_TEXT_DOMAIN); ?></h4>

                    <p class="text-muted text-center"><?php esc_html_e('Thank you! for using our Jobs & Expert plugin', JBP_TEXT_DOMAIN) ?></p>
                </div>
            </div>
            <div class="row hidden-lg hidden-md">
                <div class="col-xs-12 col-sm-12">
                    <p class="first text-center">
                        <a href="<?php echo get_post_type_archive_link('jbp_job') ?>" target="jobs"
                           class="jbp_button jbp_job_pages"><?php echo esc_html(sprintf(__('%s Listings', JBP_TEXT_DOMAIN), $job_labels->name)); ?></a>
                    </p>

                    <p class="text-center">
                        <a href="<?php echo get_edit_post_link($page_module->page($page_module::JOB_LISTING)) ?>"><?php _e('Edit', JBP_TEXT_DOMAIN) ?></a><?php esc_html_e(' this virtual page', JBP_TEXT_DOMAIN) ?>
                    </p>

                    <p class="text-center">
                        <a href="<?php echo get_permalink($page_module->page($page_module::JOB_ADD)) ?>"><?php _e('Add', JBP_TEXT_DOMAIN) ?></a> <?php echo esc_html(sprintf(__('a new %s', JBP_TEXT_DOMAIN), $job_labels->singular_name)); ?>
                    </p>
                </div>
                <hr/>
                <div class="col-xs-12 col-sm-12">
                    <p class="first text-center">
                        <a href="<?php echo get_permalink($page_module->page($page_module::LANDING_PAGE)) ?>"
                           target="landing"
                           class="jbp_button jbp_landing_page"><?php esc_html_e('Jobs & Experts Overview', JBP_TEXT_DOMAIN); ?></a>
                    </p>

                    <p class="text-center">
                        <a href="<?php echo get_edit_post_link($page_module->page($page_module::LANDING_PAGE)) ?>"><?php esc_html_e('Edit', JBP_TEXT_DOMAIN); ?></a> <?php esc_html_e(' this virtual page', JBP_TEXT_DOMAIN) ?>
                    </p>
                </div>
                <hr/>
                <div class="col-xs-12 col-sm-12">
                    <p class="first text-center">
                        <a href="<?php echo get_post_type_archive_link('jbp_pro') ?>" target="pros"
                           class="jbp_button jbp_experts_pages"><?php echo esc_html(sprintf(__('%s Listings', JBP_TEXT_DOMAIN), $pro_labels->name)); ?></a>
                    </p>

                    <p class="text-center">
                        <a href="<?php echo get_edit_post_link($page_module->page($page_module::EXPERT_LISTING)) ?>"><?php _e('Edit', JBP_TEXT_DOMAIN) ?></a> <?php _e(' this virtual page', JBP_TEXT_DOMAIN) ?>
                    </p>

                    <p class="text-center">
                        <a href="<?php echo get_permalink($page_module->page($page_module::EXPERT_ADD)) ?>"><?php esc_html_e('Add', JBP_TEXT_DOMAIN) ?></a> <?php echo esc_html(sprintf(__('a new %s', JBP_TEXT_DOMAIN), $pro_labels->singular_name)); ?>
                    </p>
                </div>
            </div>


            <div class="wrap jbp_started_page hidden-xs hidden-sm">
                <h2><?php esc_html_e('Welcome to Jobs & Experts - Getting Started & Overview Page', JBP_TEXT_DOMAIN); ?></h2>

                <div style="display: inline-table; width: 20%">

                    <p class="jbp_light"><?php esc_html_e('Thank you! for using our Jobs & Expert plugin', JBP_TEXT_DOMAIN) ?></p>

                    <p class="jbp_default">
                        <?php /*esc_html_e( 'To get started just create some demo content or browse, edit and add content using the buttons below. You can return to this page at anytime.', JBP_TEXT_DOMAIN ) */ ?><!--</p>
-->
                        <!--<div class="jbp-demo">
					<p class="first">
						<a href="<?php /*echo esc_attr( 'edit.php?post_type=jbp_job&page=jobs-plus-about&create-demo=true' ); */ ?>" class="jbp_button"><?php /*esc_html_e( 'Create demo Jobs & Experts content', JBP_TEXT_DOMAIN ); */ ?></a>
					</p>
				</div>-->

                        <?php echo do_action('jbp_notice'); ?>

                    <p><img src="<?php echo $plugin->_module_url . 'assets/image/backend/getting-started.png'; ?>"/></p>

                    <div class="jbp_plans">
                        <div class="jbp_plan">
                            <p class="first">
                                <a href="<?php echo get_post_type_archive_link('jbp_job') ?>" target="jobs"
                                   class="jbp_button jbp_job_pages"><?php echo esc_html(sprintf(__('%s Listings', JBP_TEXT_DOMAIN), $job_labels->name)); ?></a>
                            </p>

                            <p>
                                <a href="<?php echo get_edit_post_link($page_module->page($page_module::JOB_LISTING)) ?>"><?php _e('Edit', JBP_TEXT_DOMAIN) ?></a><?php esc_html_e(' this virtual page', JBP_TEXT_DOMAIN) ?>
                            </p>

                            <p>
                                <a href="<?php echo get_permalink($page_module->page($page_module::JOB_ADD)) ?>"><?php _e('Add', JBP_TEXT_DOMAIN) ?></a> <?php echo esc_html(sprintf(__('a new %s', JBP_TEXT_DOMAIN), $job_labels->singular_name)); ?>
                            </p>
                        </div>
                        <div class="jbp_plan">
                            <p class="first">
                                <a href="<?php echo get_permalink($page_module->page($page_module::LANDING_PAGE)) ?>"
                                   target="landing"
                                   class="jbp_button jbp_landing_page"><?php esc_html_e('Jobs & Experts Overview', JBP_TEXT_DOMAIN); ?></a>
                            </p>

                            <p>
                                <a href="<?php echo get_edit_post_link($page_module->page($page_module::LANDING_PAGE)) ?>"><?php esc_html_e('Edit', JBP_TEXT_DOMAIN); ?></a> <?php esc_html_e(' this virtual page', JBP_TEXT_DOMAIN) ?>
                            </p>
                        </div>
                        <div class="jbp_plan">
                            <p class="first">
                                <a href="<?php echo get_post_type_archive_link('jbp_pro') ?>" target="pros"
                                   class="jbp_button jbp_experts_pages"><?php echo esc_html(sprintf(__('%s Listings', JBP_TEXT_DOMAIN), $pro_labels->name)); ?></a>
                            </p>

                            <p>
                                <a href="<?php echo get_edit_post_link($page_module->page($page_module::EXPERT_LISTING)) ?>"><?php _e('Edit', JBP_TEXT_DOMAIN) ?></a> <?php _e(' this virtual page', JBP_TEXT_DOMAIN) ?>
                            </p>

                            <p>
                                <a href="<?php echo get_permalink($page_module->page($page_module::EXPERT_ADD)) ?>"><?php esc_html_e('Add', JBP_TEXT_DOMAIN) ?></a> <?php echo esc_html(sprintf(__('a new %s', JBP_TEXT_DOMAIN), $pro_labels->singular_name)); ?>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php
    }
}