<?php

/**
 * @author:Hoang Ngo
 */
class JE_Page_Factory
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

    protected $jobs_obj;
    protected $expert_obj;
    protected $warning;
    protected $buttons;

    public function __construct()
    {
        $this->jobs_obj = get_post_type_object('jbp_job');
        $this->expert_obj = get_post_type_object('jbp_pro');
        //prepared content
        $this->warning = __("<!-- You may edit this page, the title and the slug, but it requires a minimum of the correct page shortcode to function. You can recreate the original default page by deleting this one. -->\n", je()->domain) . PHP_EOL;
        $this->buttons = '<p style="text-align: center">[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]</p>' . PHP_EOL;
    }

    public function init()
    {
        $class = new ReflectionClass ('JE_Page_Factory');
        foreach ($class->getConstants() as $key => $val) {
            //$this->$val();
        }
        $this->default_category();
    }

    /**
     * We need to make sure there's will be always a category
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
        $id = je()->settings()->landing_page;
        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => __('Jobs & Experts', je()->domain),
                'post_name' => __('jobs-experts', je()->domain),
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page',
                'post_content' => $this->warning . $this->buttons . '[jbp-landing-page]',
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
    public function page($page, $no_filter = false)
    {
        //$page = self::$page();
        $page_id = $this->$page();
        if ($no_filter == true) {
            return $page_id;
        }
        return apply_filters('jbp_page_factory_get_page', $page_id, $page);
    }

    protected function job_add()
    {
        $id = je()->settings()->job_add;
        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Add a %s', je()->domain), $this->jobs_obj->labels->singular_name),
                'post_name' => sprintf(__('add-a-%s', je()->domain), $this->jobs_obj->rewrite['slug']),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function job_edit()
    {
        $id = je()->settings()->job_edit;
        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Edit %s', je()->domain), $this->jobs_obj->labels->singular_name),
                'post_name' => sprintf(__('edit-%s', je()->domain), $this->jobs_obj->rewrite['slug']),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function job_listing()
    {
        $id = je()->settings()->job_listing;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s', je()->domain), $this->jobs_obj->labels->name),
                'post_name' => sprintf(__('%s', je()->domain), $this->jobs_obj->rewrite['slug']),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }


        return $id;
    }

    protected function job_contact()
    {
        $id = je()->settings()->job_contact;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s Contact', je()->domain), $this->jobs_obj->labels->singular_name),
                'post_name' => sprintf(__('%s-contact', je()->domain), $this->jobs_obj->rewrite['slug']),
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
           /* wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function my_job()
    {
        $id = je()->settings()->my_job;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('My %s', je()->domain), $this->jobs_obj->labels->name),
                'post_name' => sprintf(__('my-%s', je()->domain), $this->jobs_obj->rewrite['slug'] . 's'),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function expert_add()
    {
        $id = je()->settings()->expert_add;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Add a %s', je()->domain), $this->expert_obj->labels->singular_name),
                'post_name' => sprintf(__('add-a-%s', je()->domain), $this->expert_obj->rewrite['slug']),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function expert_edit()
    {
        $id = je()->settings()->expert_edit;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('Edit %s', je()->domain), $this->expert_obj->labels->singular_name),
                'post_name' => sprintf(__('edit-%s', je()->domain), $this->expert_obj->rewrite['slug']),
                'post_status' => 'virtual',
                'post_author' => 1,
                'post_type' => 'jbp_pro',
                'post_content' => $this->warning . $this->buttons . '[jbp-expert-update-page]',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );
            $id = wp_insert_post($args);
            //update setting
            $this->save_setting(self::EXPERT_EDIT, $id);
        } else {
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function expert_listing()
    {
        $id = je()->settings()->expert_listing;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s', je()->domain), $this->expert_obj->labels->name),
                'post_name' => sprintf(__('%s', je()->domain), $this->expert_obj->rewrite['slug']),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function expert_contact()
    {
        $id = je()->settings()->expert_contact;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('%s Contact', je()->domain), $this->expert_obj->labels->singular_name),
                'post_name' => sprintf(__('%s-contact', je()->domain), $this->expert_obj->rewrite['slug']),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    protected function my_expert()
    {
        $id = je()->settings()->my_expert;

        if (empty($id) || !$this->page_exist($id)) {
            $args = array(
                'post_title' => sprintf(__('My %s Profile', je()->domain), $this->expert_obj->labels->name),
                'post_name' => sprintf(__('my-%s-profile', je()->domain), $this->expert_obj->rewrite['slug'] . 's'),
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
            /*wp_update_post(array(
                'post_status' => 'virtual',
                'ID' => $id
            ));*/
        }

        return $id;
    }

    private function page_exist($id)
    {
        global $wpdb;
        $exist = $wpdb->get_var(
            $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE id=%d AND post_status IN ('virtual','publish')", $id)
        );
        if ($exist) {
            return true;
        }
        return false;

    }

    public static function is_core_page($id)
    {
        $class = new ReflectionClass ('JE_Page_Factory');
        foreach ($class->getConstants() as $key => $val) {
            $core_id = je()->settings()->$val;
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
        $setting = je()->settings();
        $setting->$key = $id;
        $setting->save();
    }
}