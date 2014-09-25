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
class JobsExperts_Core_Views_Settings extends JobsExperts_Framework_Render
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

        $model = new JobsExperts_Core_Models_Settings();
        ?>
        <div class="wrap hn-container">
            <h2><?php _e('Settings', JBP_TEXT_DOMAIN) ?></h2>

            <div class="row">
                <div class="col-md-12">
                    <ul id="jbp_setting_nav" style="margin-top: 0;padding-top: 0;margin-right: -1px;z-index:9" class="nav nav-tabs tabs-left col-md-3 no-padding hidden-sm hidden-xs">
                        <li <?php echo $this->active_tab('general', 'general') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=general') ?>">
                                <i class="glyphicon glyphicon-cog"></i> <?php _e('General Settings', JBP_TEXT_DOMAIN) ?>
                            </a>
                        </li>
                        <li <?php echo $this->active_tab('job') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job') ?>">
                                <img
                                    src="<?php echo $plugin->_module_url ?>assets/image/backend/icons/16px/16px_Jobs_Dark.svg"> <?php _e('Jobs Settings', JBP_TEXT_DOMAIN) ?>
                            </a>
                        </li>
                        <li <?php echo $this->active_tab('expert') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert') ?>">
                                <img
                                    src="<?php echo $plugin->_module_url ?>assets/image/backend/icons/16px/16px_Expert_Dark.svg"> <?php _e('Experts Settings', JBP_TEXT_DOMAIN) ?>
                            </a></li>
                        <li <?php echo $this->active_tab('shortcode') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=shortcode') ?>">
                                <i class="fa fa-info"></i>
                                <?php _e('Shortcode Implement', JBP_TEXT_DOMAIN) ?>
                            </a></li>
                        <?php do_action('jbp_setting_menu') ?>
                    </ul>
                    <ul class="nav nav-pills nav-stacked hidden-lg hidden-md">
                        <li <?php echo $this->active_tab('general', 'general') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=general') ?>">
                                <i class="glyphicon glyphicon-cog"></i> <?php _e('General Settings', JBP_TEXT_DOMAIN) ?>
                            </a>
                        </li>
                        <li <?php echo $this->active_tab('job') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job') ?>">
                                <img
                                    src="<?php echo $plugin->_module_url ?>assets/image/backend/icons/16px/16px_Jobs_Dark.svg"> <?php _e('Jobs Settings', JBP_TEXT_DOMAIN) ?>
                            </a>
                        </li>
                        <li <?php echo $this->active_tab('expert') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert') ?>">
                                <img
                                    src="<?php echo $plugin->_module_url ?>assets/image/backend/icons/16px/16px_Expert_Dark.svg"> <?php _e('Experts Settings', JBP_TEXT_DOMAIN) ?>
                            </a></li>
                        <li <?php echo $this->active_tab('shortcode') ?>>
                            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=shortcode') ?>">
                                <i class="fa fa-info"></i>
                                <?php _e('Shortcode Implement', JBP_TEXT_DOMAIN) ?>
                            </a></li>
                        <?php do_action('jbp_setting_menu') ?>
                        <li><br/></li>
                    </ul>
                    <div class="tab-content col-md-9 no-padding">
                        <div class="jbp-setting-content tab-pane active">
                            <?php $form = JobsExperts_Framework_ActiveForm::generateForm($model);
                            $form->openForm('#', 'POST', array(
                                'class' => 'form-horizontal',
                                'id' => 'jobs-setting'
                            ))?>
                            <?php do_action('jbp_setting_content', $form, $model) ?>
                            <div class="row" style="margin-top: 20px">
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-9">
                                    <?php wp_nonce_field('jobs-plus-settings'); ?>
                                    <input type="hidden" name="jobs-plus-settings" value="1"/>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <button type="submit"
                                            class="button button-primary "><?php _e('Save Changes', JBP_TEXT_DOMAIN) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php $form->endForm(); ?>
                        </div>

                    </div>

                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    <?php
    }

    function active_tab($id, $default_tab = '')
    {
        if (isset($_GET['tab'])) {
            if ($id == $_GET['tab']) {
                return 'class="active"';
            }
        } else {
            if ($id == $default_tab) {
                return 'class="active"';
            }
        }

        return null;
    }
}