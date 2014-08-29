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
class JobsExperts_Core_Shortcode_ExpertSingle extends JobsExperts_Shortcode
{
    const NAME = __CLASS__;

    public function __construct()
    {
        $this->_add_shortcode('jbp-pro-single-page', 'shortcode');
        //shortcode style
    }

    function load_scripts()
    {
        //wp_enqueue_style('expert-single-shortcode');

        global $jbp_component_uploader;
        $jbp_component_uploader->load_scripts();
        global $jbp_component_social;
        $jbp_component_social->load_scripts();
        global $jbp_component_skill;
        $jbp_component_skill->load_scripts();
    }


    public function shortcode($atts)
    {
        $this->load_scripts();

        //get plugin instance
        $plugin = JobsExperts_Plugin::instance();

        $model = JobsExperts_Core_Models_Pro::instance()->get_one(get_the_ID());
        $model->add_view_count();
        ob_start();
        ?>
        <div class="hn-container">
            <?php echo do_shortcode('<p style="text-align: center">[jbp-expert-post-btn][jbp-job-post-btn][jbp-expert-browse-btn][jbp-job-browse-btn][jbp-expert-profile-btn][jbp-my-job-btn]</p>'); ?>
            <?php $template = new JobsExperts_Core_Views_ExpertSingle(array(
                'model' => $model
            ));
            $template->render();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

new JobsExperts_Core_Shortcode_ExpertSingle;