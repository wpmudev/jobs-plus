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
 *
 * @category JobsExperts
 * @package  Shorcode
 *
 * @since    1.0.0
 */
class JobsExperts_Core_Shortcode_JobForm extends JobsExperts_Shortcode
{
    const NAME = __CLASS__;

    public function __construct()
    {
        $this->_add_shortcode('jbp-job-update-page', 'shortcode');
        //shortcode style
    }

    function scripts()
    {
        $plugin = JobsExperts_Plugin::instance();

        //validate
        wp_register_script('jbp_validate_script', $plugin->_module_url . 'assets/js/jquery.validationEngine.js');
        wp_register_script('jbp_validate_script_en', $plugin->_module_url . 'assets/js/jquery.validationEngine-en.js');
        wp_register_style('jbp_validate_style', $plugin->_module_url . 'assets/css/validationEngine.jquery.css');

        wp_register_script('jbp_iframe_transport', $plugin->_module_url . 'assets/js/jquery-iframe-transport.js');
        wp_register_script('jbp_select2', $plugin->_module_url . 'assets/js/select2.min.js');
        wp_register_style('jbp_select2', $plugin->_module_url . 'assets/css/select2.css');

        wp_register_script('jbp_datepicker', $plugin->_module_url . 'assets/datepicker/js/bootstrap-datepicker.js');
        wp_register_style('jbp_datepicker', $plugin->_module_url . 'assets/datepicker/css/datepicker.css');
    }

    function load_scripts()
    {
        //wp_enqueue_style('jobs-form-shortcode');
        global $jbp_component_uploader;
        $jbp_component_uploader->load_scripts();

        //validate
        wp_enqueue_script('jobs-validation');
        wp_enqueue_script('jobs-validation-en');
        wp_enqueue_style('jobs-validation');
        //calendar
        wp_enqueue_script('jobs-datepicker');
        wp_enqueue_style('jobs-datepicker');
        //select2
        wp_enqueue_script('jobs-select2');
        wp_enqueue_style('jobs-select2');
        //noty
        wp_enqueue_script('jobs-noty');
    }

    public function shortcode($atts)
    {
        $this->load_scripts();
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        ob_start();
        echo '<div class="hn-container">';
        if (!is_user_logged_in()) {
            //user still not login, we need to load login form
            $this->load_login_form();
        } else {
            ///load model
            $model = '';
            $is_edit = false;
            if (isset($plugin->global['jbp_job'])) {
                $model = $plugin->global['jbp_job'];
            } else {
                $is_edit = apply_filters('jbp_is_job_edit', $page_module->page(JobsExperts_Core_PageFactory::JOB_EDIT) == get_the_ID());
                if ($is_edit && isset($_GET['job']) && !empty($_GET['job'])) {
                    $model = JobsExperts_Core_Models_Job::instance()->get_one($_GET['job'], array('publish', 'draft', 'pending'));
                }

                if (!is_object($model)) {
                    $model = new JobsExperts_Core_Models_Job();
                    if (isset($_GET['job_title'])) {
                        $model->job_title = $_GET['job_title'];
                    }
                    $model->status = 'auto-draft';
                    $model->save();
                }
            }
            //bind
            //now we need to check does this user can add new job
            if ($model->is_current_can_edit() == false) {
                //oh no, he can not
                echo '<h4 style="text-align: center">' . _e('Sorry you do not have enough permission to add new job', JBP_TEXT_DOMAIN) . '</h4>';
            } elseif ($model->is_reach_max() && !$is_edit) {
                //this user can not add more
                echo '<h4 style="text-align: center">' . __('Sorry, you reach max amount of jobs', JBP_TEXT_DOMAIN) . '</h4>';
            } else {
                echo '<div class="jobs-form">';
                //ok, load the form

                $template = new JobsExperts_Core_Views_JobForm(array(
                    'model' => $model,
                    'is_edit' => $is_edit
                ));
                $template->render();
                echo '</div>';
            }

        }
        echo '</div>';

        return apply_filters('jbp_job_form_output', ob_get_clean());
    }

    function load_login_form()
    {
        ?>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default jbp_login_form">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?php _e('Please login', JBP_TEXT_DOMAIN) ?>
                            <?php
                            $can_register = is_multisite() == true ? get_site_option('users_can_register') : get_option('users_can_register');
                            if ($can_register): ?>
                                or <?php echo sprintf('<a href="%s">%s</a>', wp_registration_url(), __('register here', JBP_TEXT_DOMAIN)) ?>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?php echo wp_login_form(array('echo' => false)) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}

new JobsExperts_Core_Shortcode_JobForm;