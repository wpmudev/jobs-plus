<?php

/**
 * Class JobsExperts_Model_Job
 * Func section
 * CRUD
 * Taxonomy
 * Capability
 */
class JobsExperts_Core_Models_Job extends JobsExperts_Framework_PostModel
{
    //model own property
    public $job_title;
    public $categories;
    public $skills;
    public $description;
    public $budget;
    public $contact_email;
    public $dead_line;
    public $open_for;
    public $portfolios;
    public $status;
    public $min_budget;
    public $max_budget;
    public $owner;


    public function storage_name()
    {
        return 'jbp_job';
    }

    function before_save()
    {
        $this->description = wp_kses($this->description, wp_kses_allowed_html('post'));
    }

    function after_save()
    {
        $categories = $this->categories;
        $skills = explode(',', $this->skills);
        $skills = array_filter($skills);
        if (!empty($categories)) {
            //update the term
            $this->assign_categories($categories, false);
        }

        if (!empty($skills)) {
            $this->assign_skill_tag($skills, false);
        }
    }

    public function rules()
    {
        $rules = array(
            array('required', 'job_title,contact_email,dead_line,open_for,description'),
            array('email', 'contact_email'),
        );
        if (JobsExperts_Plugin::instance()->settings()->job_budget_range == 1) {
            $rules[] = array('required', 'min_budget,max_budget');
            $rules[] = array('numeric', 'min_budget,max_budget');
        } else {
            $rules[] = array('required', 'budget');
        }

        return $rules;
    }

    ///////////////////// CURD///////////////////////////

    /**
     * This function will prepare data from class to wordpress post array
     */
    public function prepare_import_data()
    {
        //core data
        $args = array(
            'post' => array(
                'ID' => !$this->is_new_record() ? $this->id : null,
                'post_title' => $this->job_title,
                'post_content' => $this->description,
                'post_status' => $this->status,
                'post_type' => 'jbp_job',
                'ping_status' => 'closed',
                'comment_status' => 'closed',
            ),
            'categories' => $this->categories,
            'tags' => $this->skills,
            'meta' => array(
                '_ct_jbp_job_Budget' => $this->budget,
                '_ct_jbp_job_Contact_Email' => $this->contact_email,
                '_ct_jbp_job_Due' => date('Y-m-d', strtotime($this->dead_line)),
                '_jbp_job_expires' => $this->open_for,
                '_jbp_job_portfolios' => $this->portfolios,
                '_jbp_job_budget_min' => $this->min_budget,
                '_jbp_job_budget_max' => $this->max_budget,
            )
        );

        return $args;
    }

    /**
     * @param WP_Post $data
     */
    public function prepare_load_data(WP_Post $post)
    {
        $this->id = $post->ID;
        $this->job_title = $post->post_title;
        $this->description = $post->post_content;
        $this->owner = $post->post_author;
        //meta
        $this->budget = get_post_meta($this->id, '_ct_jbp_job_Budget', true);
        $this->contact_email = get_post_meta($this->id, '_ct_jbp_job_Contact_Email', true);
        $this->dead_line = get_post_meta($this->id, '_ct_jbp_job_Due', true);
        $this->open_for = get_post_meta($this->id, '_jbp_job_expires', true);
        $this->min_budget = get_post_meta($this->id, '_jbp_job_budget_min', true);
        $this->max_budget = get_post_meta($this->id, '_jbp_job_budget_max', true);

        $this->portfolios = get_post_meta($this->id, '_jbp_job_portfolios', true);

        $this->categories = $this->find_terms('jbp_category');
    }

    public function addition_validate()
    {
        //check does it max size
        if ($this->is_reach_max()) {
            $this->set_error('id', __('You have reach maximum job amount!'));
        }

        if (JobsExperts_Plugin::instance()->settings()->job_budget_range == 1) {
            if (!empty($this->max_budget) && $this->min_budget >= $this->max_budget) {
                $this->set_error('min_budget', __('Min budget can not be larger than max budget'));
            }
        }
    }


    //////////////////////////// TAXONOMY /////////////////
    public function assign_categories($categories = array(), $append = true)
    {
        if (!is_array($categories)) {
            $categories = array($categories);
        }
        $this->assign_terms($categories, 'jbp_category', $append);
    }

    public function assign_skill_tag($skills = array(), $append = true)
    {
        if (!is_array($skills)) {
            $skills = array($skills);
        }
        $this->assign_terms($skills, 'jbp_skills_tag', $append);
    }

    //////// ULITIES

    public function get_price()
    {
        $plugin = JobsExperts_Plugin::instance();
        if ($plugin->settings()->job_budget_range == 1) {
            //use range
            if (!empty($this->min_budget) && !empty($this->max_budget)) {
                return array($this->min_budget, $this->max_budget);
            } else {
                //fallback to normal budget
                return $this->budget;
            }
        } else {
            return $this->budget;
        }
    }

    public function get_files()
    {
        $files = get_posts(array(
            'post_type' => 'jbp_media',
            'post_parent' => $this->id
        ));
        var_dump($files);
        return $files;
    }

    public function render_prices($return = '')
    {
        $prices = $this->get_price();
        if (is_array($prices)) {
            ?>
            <?php if (empty($return)): ?>
                $<?php echo money_format($this->min_budget, 2) ?> - $<?php echo money_format($this->max_budget, 2) ?>
            <?php else: ?>
                $<?php echo money_format($this->max_budget, 2) ?>
            <?php endif; ?>
        <?php
        } else {

            ?>
            $<?php echo money_format($this->budget, 2) ?>
        <?php
        }
    }

    public function get_due_day()
    {
        $post = get_post($this->id);
        if ($post) {
            $created_date = get_post_meta($post->ID, 'jbp_job_post_day', true);
            if (!$created_date) {
                $created_date = $post->post_date;
            }
            $expire_date = strtotime('+ ' . $this->open_for . ' days', strtotime($created_date));

            return $this->days_hours($expire_date);
        }
        //return $this->days_hours();
    }

    private function days_hours($expires)
    {
        $date = intval($expires);
        $secs = $date - time();
        if ($secs > 0) {
            $days = floor($secs / (60 * 60 * 24));
            $hours = round(($secs - $days * 60 * 60 * 24) / (60 * 60));

            return sprintf(__('%d Days %dhrs', JBP_TEXT_DOMAIN), $days, $hours);
        } else {
            return __('Expired', JBP_TEXT_DOMAIN);
        }
    }

    function get_end_date()
    {
        return date_i18n(get_option('date_format'), strtotime($this->dead_line));
    }

    protected function count_user_posts_by_type($user_id = 0, $post_type = 'post')
    {
        global $wpdb;

        $where = get_posts_by_author_sql($post_type, TRUE, $user_id);

        if (in_array($post_type, array('jbp_pro', 'jbp_job'))) {
            $where = str_replace("post_status = 'publish'", "post_status = 'publish' OR post_status = 'draft'", $where);
        }
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts $where");

        return apply_filters('get_usernumposts', $count, $user_id);
    }

    /////PERMISSIOn

    function is_current_owner()
    {
        if (current_user_can('manage_options')) {
            return true;
        }

        if (get_current_user_id() == $this->owner) {
            return true;
        }

        return false;
    }

    function is_current_can_edit()
    {
        /*if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        return current_user_can( 'edit_jbp_jobs' );*/
        return apply_filters('jbp_can_edit_job', true);
    }

    function is_reach_max()
    {
        if (current_user_can('manage_options')) {
            return false;
        }
        if ($this->count_user_posts_by_type(get_current_user_id(), 'jbp_job') >= JobsExperts_Plugin::instance()->settings()->job_max_records) {
            return true;
        }

        return false;
    }

    function get_status()
    {
        $status = $this->get_raw_post()->post_status;
        if ($status == 'publish') {
            $status = 'published';
        }
        return $status;
    }

    public function add_view_count()
    {
        $view = intval(get_post_meta($this->id, 'jbp_job_view_count', true));
        update_post_meta($this->id, 'jbp_job_view_count', $view + 1);
    }


}