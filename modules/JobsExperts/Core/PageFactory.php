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
 * PageFactory module.
 *
 * This class is use to handler the virtual page, the idea is add a constant name,
 * when init, the class will looking for a function with the const name and run that function to create the page
 *
 * @category JobsExperts
 * @package  Core
 *
 * @since    1.0.0
 */
class JobsExperts_Core_PageFactory
{

    const JOB_ADD = 'job_add',
        JOB_EDIT = 'job_edit',
        JOB_LISTING = 'job_listing',
        JOB_CONTACT = 'job_contact',
        MY_JOB = 'my_job';

    const EXPERT_ADD = 'expert_add',
        EXPERT_EDIT = 'expert_edit',
        EXPERT_LISTING = 'expert_listing',
        EXPERT_CONTACT = 'expert_contact',
        MY_EXPERT = 'my_expert';

    const LANDING_PAGE = 'landing_page';

    private static $_instance = null;

    /**
     * Shorthand to get name here
     */
    private $plugin;
    private $jobs_obj;
    private $expert_obj;

    private $warning;
    private $buttons;

    private function __construct()
    {
        $this->plugin = JobsExperts_Plugin::instance();
        $this->jobs_obj = $this->plugin->get_job_type();
        $this->expert_obj = $this->plugin->get_expert_type();
        //prepared content
        $this->waring = __("<!-- You may edit this page, the title and the slug, but it requires a minimum of the correct page shortcode to function. You can recreate the original default page by deleting this one. -->\n", JBP_TEXT_DOMAIN) . PHP_EOL;
        $this->buttons = '<p style="text-align: center;">[jbp-expert-post-btn][jbp-job-post-btn][jbp-expert-browse-btn][jbp-job-browse-btn][jbp-expert-profile-btn][jbp-my-job-btn]</p>' . PHP_EOL;
    }

    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new JobsExperts_Core_PageFactory();
        }

        return self::$_instance;
    }

    public function init()
    {
        $class = new ReflectionClass ('JobsExperts_Core_PageFactory');
        foreach ($class->getConstants() as $key => $val) {
            $this->$val();
        }
        $this->default_category();
    }

    /**
     *
     */
    public function default_category()
    {
        $count = get_terms('jbp_category');
        if (count($count) == 0) {
            //we will create default terms
            wp_insert_term('General', 'jbp_category');
        }
    }

    public function landing_page()
    {
        $id = $this->plugin->settings()->landing_page;
        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => __('Jobs & Experts', JBP_TEXT_DOMAIN),
                'post_name' => __('jobs-experts', JBP_TEXT_DOMAIN),
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page',
                'post_content' => $this->waring . $this->buttons . '[jbp-landing-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            $this->save_setting(self::LANDING_PAGE, $id);
        }

        return $id;
    }

    /**
     * @param $page
     *
     * @return mixed
     */
    public function page($page)
    {
        //$page = self::$page();
        $page = $this->$page();

        return $page;
    }

    protected function job_add()
    {
        $id = $this->plugin->settings()->job_add;
        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Add a %s', JBP_TEXT_DOMAIN), $this->jobs_obj->labels->singular_name),
                'post_name' => sprintf(__('add-a-%s', JBP_TEXT_DOMAIN), $this->jobs_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_job',
                'post_content' => $this->warning . $this->buttons . '[jbp-job-update-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::JOB_ADD, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function job_edit()
    {
        $id = $this->plugin->settings()->job_edit;
        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Edit %s', JBP_TEXT_DOMAIN), $this->jobs_obj->labels->singular_name),
                'post_name' => sprintf(__('edit-%s', JBP_TEXT_DOMAIN), $this->jobs_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_job',
                'post_content' => $this->warning . $this->buttons . '[jbp-job-update-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::JOB_EDIT, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function job_listing()
    {
        $id = $this->plugin->settings()->job_listing;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s', JBP_TEXT_DOMAIN), $this->jobs_obj->labels->name),
                'post_name' => sprintf(__('%s', JBP_TEXT_DOMAIN), $this->jobs_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_job',
                'post_content' => $this->warning . $this->buttons . '[jbp-job-archive-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::JOB_LISTING, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }


        return $id;
    }

    protected function job_contact()
    {
        $id = $this->plugin->settings()->job_contact;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s Contact', JBP_TEXT_DOMAIN), $this->jobs_obj->labels->singular_name),
                'post_name' => sprintf(__('%s-contact', JBP_TEXT_DOMAIN), $this->jobs_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_job',
                'post_content' => $this->warning . $this->buttons . '[jbp-job-contact-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);

            //update setting
            $this->save_setting(self::JOB_CONTACT, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function my_job()
    {
        $id = $this->plugin->settings()->my_job;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('My %s', JBP_TEXT_DOMAIN), $this->jobs_obj->labels->name),
                'post_name' => sprintf(__('my-%s', JBP_TEXT_DOMAIN), $this->jobs_obj->rewrite['slug'] . 's'),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_job',
                'post_content' => $this->warning . $this->buttons . '[jbp-my-job-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::MY_JOB, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function expert_add()
    {
        $id = $this->plugin->settings()->expert_add;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Add a %s', JBP_TEXT_DOMAIN), $this->expert_obj->labels->singular_name),
                'post_name' => sprintf(__('add-a-%s', JBP_TEXT_DOMAIN), $this->expert_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_pro',
                'post_content' => $this->warning . $this->buttons . '[jbp-expert-update-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::EXPERT_ADD, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function expert_edit()
    {
        $id = $this->plugin->settings()->expert_add;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Edit %s', JBP_TEXT_DOMAIN), $this->expert_obj->labels->singular_name),
                'post_name' => sprintf(__('edit-%s', JBP_TEXT_DOMAIN), $this->expert_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_pro',
                'post_content' => $this->warning . $this->buttons . '[jbp-expert-update-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::EXPERT_ADD, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function expert_listing()
    {
        $id = $this->plugin->settings()->expert_listing;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s', JBP_TEXT_DOMAIN), $this->expert_obj->labels->name),
                'post_name' => sprintf(__('%s', JBP_TEXT_DOMAIN), $this->expert_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_pro',
                'post_content' => $this->warning . $this->buttons . '[jbp-expert-archive-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::EXPERT_LISTING, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function expert_contact()
    {
        $id = $this->plugin->settings()->expert_contact;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s Contact', JBP_TEXT_DOMAIN), $this->expert_obj->labels->singular_name),
                'post_name' => sprintf(__('%s-contact', JBP_TEXT_DOMAIN), $this->expert_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_pro',
                'post_content' => $this->warning . $this->buttons . '[jbp-expert-contact-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);

            //update setting
            $this->save_setting(self::EXPERT_CONTACT, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    protected function my_expert()
    {
        $id = $this->plugin->settings()->my_expert;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('My %s Profile', JBP_TEXT_DOMAIN), $this->expert_obj->labels->name),
                'post_name' => sprintf(__('my-%s-profile', JBP_TEXT_DOMAIN), $this->expert_obj->rewrite['slug'] . 's'),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_pro',
                'post_content' => $this->warning . $this->buttons . '[jbp-my-expert-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::MY_EXPERT, $id);
        } else {
            wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));
        }

        return $id;
    }

    private function page_exist($id)
    {
        $page = get_post($id);
        if ($page instanceof WP_Post && ($page->post_status == 'virtual' || $page->post_status == 'publish')) {
            return true;
        }

        return false;
    }

    public static function is_core_page($id)
    {
        $class = new ReflectionClass ('JobsExperts_Core_PageFactory');
        $plugin = JobsExperts_Plugin::instance();
        foreach ($class->getConstants() as $key => $val) {
            $core_id = $plugin->settings()->$val;
            if ($core_id == $id) {
                return true;
            }
        }

        return false;
    }

    public static function find_core_page_by_name($name = '')
    {
        //find page name
        global $wpdb;
        $sql = 'SELECT * FROM ' . $wpdb->posts . ' WHERE (post_type = %s OR post_type=%s) AND post_status=%s AND post_name=%s';
        $row = $wpdb->get_row($wpdb->prepare($sql, 'jbp_job', 'jbp_pro', 'virtual', $name));
        if (!empty($row) && self::is_core_page($row->ID)) {
            return $row->ID;
        }

        return false;
    }

    private function save_setting($key, $id)
    {
        $setting = $this->plugin->settings();
        $setting->$key = $id;
        $setting->save();
    }

}