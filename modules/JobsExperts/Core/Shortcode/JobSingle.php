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
class JobsExperts_Core_Shortcode_JobSingle extends JobsExperts_Shortcode
{
    const NAME = __CLASS__;

    public function __construct()
    {
        $this->_add_shortcode('jbp-job-single-page', 'shortcode');
    }

    private function load_assets()
    {
        $this->_add_action('wp_head', 'inject_assets', 999);

    }

    function inject_asset()
    {
        wp_enqueue_style('jobs-single-shortcode');
    }

    public function shortcode($atts)
    {
        $a = shortcode_atts(array(
            'id' => get_the_ID()
        ), $atts);

        ///$this->load_assets();
        global $jbp_component_uploader;
        $jbp_component_uploader->load_scripts();
        //get plugin instance
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();

        $model = JobsExperts_Core_Models_Job::instance()->get_one($a['id']);
        if (is_object($model)) {
            //add view count
            $model->add_view_count();
            ob_start();
            ?>
            <div class="hn-container">
                <?php $template = new JobsExperts_Core_Views_JobSingle(array(
                    'model' => $model
                ));
                $template->render();?>
                <?php do_action('jbp_after_single_job') ?>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}

new JobsExperts_Core_Shortcode_JobSingle();