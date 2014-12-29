<?php

/**
 * @author:Hoang Ngo
 */
class JE_Buttons_Shortcode_Controller extends IG_Request
{
    public function  __construct()
    {
        $buttons = array(
            'jbp-expert-post-btn' => 'expert_add',
            'jbp-job-post-btn' => 'job_add',
            'jbp-job-browse-btn' => 'job_list',
            'jbp-expert-profile-btn' => 'my_profile',
            'jbp-my-job-btn' => 'my_jobs',
            'jbp-expert-browse-btn' => 'expert_list',
        );

        foreach ($buttons as $key => $val) {
            add_shortcode($key, array(&$this, $val));
        }
    }

    /**
     *
     * This shortcode will render Add new Job button
     *
     * Shortcode attributes list
     * text - The text below button
     * view - both|loggedin|loggedout
     * template - template to override this shortcode ability, not recommend to changes, the template location will be inside
     *              current theme folder
     * class - dark|bright|none
     *
     *
     * @category JobsExperts
     * @package  Shorcode
     *
     * @since    1.0.0
     */
    function expert_add($atts)
    {
        je()->load_script('buttons');
        $page_module = je()->pages;
        extract(shortcode_atts(array(
            'text' => __('Add an Expert', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => get_permalink($page_module->page(JE_Page_Factory::EXPERT_ADD))
        ), $atts));
        //check does this can view
        if (!$this->can_view($view)) {
            return '';
        }

        if (!empty($template) && locate_template($template)) {
            return $this->custom_template($template);
        }

        $ob = sprintf('<a class="jbp-shortcode-button jbp-add-pro %s" href="%s">
			%s
		</a>', esc_attr($class), apply_filters('jbp_button_url', $url, 'add_new_expert'), esc_html($text));

        return apply_filters('jbp_pro_post_btn_output', $ob);
    }

    /**
     * This shortcode will render Add new Job button
     *
     * Shortcode attributes list
     * text - The text below button
     * view - both|loggedin|loggedout
     * template - template to override this shortcode ability, not recommend to changes, the template location will be inside
     *              current theme folder
     * class - dark|bright|none
     *
     * @category JobsExperts
     * @package  Shorcode
     *
     * @since    1.0.0
     */
    function job_add($atts)
    {
        $page_module = je()->pages;
        je()->load_script('buttons');
        extract(shortcode_atts(array(
            'text' => __('Post a Job', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => get_permalink($page_module->page(JE_Page_Factory::JOB_ADD))
        ), $atts));
        //check does this can view
        if (!$this->can_view($view)) {
            return '';
        }
        if (!empty($template) && locate_template($template)) {
            return $this->custom_template($template);
        }

        $ob = sprintf('<a class="jbp-shortcode-button jbp-add-job %s" href="%s">
			%s
		</a>', esc_attr($class), apply_filters('jbp_button_url', $url, 'add_new_job'), esc_html($text));

        return apply_filters('jbp_job_post_btn_output', $ob);
    }

    /**
     * This shortcode will render Listing Job Button
     *
     * Shortcode attributes list
     * text - The text below button
     * view - both|loggedin|loggedout
     * template - template to override this shortcode ability, not recommend to changes, the template location will be inside
     *              current theme folder
     * class - dark|bright|none
     *
     * @category JobsExperts
     * @package  Shorcode
     *
     * @since    1.0.0
     */

    function expert_list($atts)
    {
        je()->load_script('buttons');
        extract(shortcode_atts(array(
            'text' => __('Browse Experts', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => apply_filters('je_experts_archive_url', get_post_type_archive_link('jbp_pro'))
        ), $atts));
        //check does this can view
        if (!$this->can_view($view)) {
            return '';
        }

        if (!empty($template) && locate_template($template)) {
            return $this->custom_template($template);
        }
        //todo update url
        $ob = sprintf('<a class="jbp-shortcode-button jbp-browse-pro %s" href="%s">
			%s
		</a>', esc_attr($class), $url, esc_html($text));

        return $ob;
    }

    /**
     * This shortcode will render My Jobs Button
     *
     * Shortcode attributes list
     * text - The text below button
     * view - both|loggedin|loggedout
     * template - template to override this shortcode ability, not recommend to changes, the template location will be inside
     *              current theme folder
     * class - dark|bright|none
     *
     *
     * @category JobsExperts
     * @package  Shorcode
     *
     * @since    1.0.0
     */
    function job_list($atts)
    {
        je()->load_script('buttons');
        
        extract(shortcode_atts(array(
            'text' => __('Browse Jobs', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => apply_filters('je_jobs_archive_url', get_post_type_archive_link('jbp_job'))
        ), $atts));
        //check does this can view
        if (!$this->can_view($view)) {
            return '';
        }

        if (!empty($template) && locate_template($template)) {
            return $this->custom_template($template);
        }

        $ob = sprintf('<a class="jbp-shortcode-button jbp-browse-job %s" href="%s">
			%s
		</a>', esc_attr($class), $url, esc_html($text));

        return apply_filters('jbp_job_list_btn_output', $ob);
    }

    function my_profile($atts)
    {
        je()->load_script('buttons');
        $page_module = je()->pages;
        extract(shortcode_atts(array(
            'text' => __('My Profile', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => get_permalink($page_module->page(JE_Page_Factory::MY_EXPERT))
        ), $atts));
        //check does this can view
        if (!$this->can_view($view)) {
            return '';
        }
        //Don't display unless they have a profile.
        if ($this->count_user_posts_by_type(get_current_user_id(), 'jbp_pro') < 1) {
            return '';
        }

        if (!empty($template) && locate_template($template)) {
            return $this->custom_template($template);
        }

        $ob = sprintf('<a class="jbp-shortcode-button jbp-profile-pro %s" href="%s">
			%s
		</a>', esc_attr($class), apply_filters('jbp_button_url', $url, 'my_profiles'), esc_html($text));

        return apply_filters('jbp_expert_profile_btn_output', $ob);
    }

    function my_jobs($atts)
    {
        je()->load_script('buttons');
        $page_module = je()->pages;
        extract(shortcode_atts(array(
            'text' => __('My Jobs', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => get_permalink($page_module->page(JE_Page_Factory::MY_JOB))
        ), $atts));
        //check does this can view
        if (!$this->can_view($view)) {
            return '';
        }
        //Don't display unless they have a posted a job.
        if ($this->count_user_posts_by_type(get_current_user_id(), 'jbp_job') < 1) {
            return '';
        }

        if (!empty($template) && locate_template($template)) {
            return $this->custom_template($template);
        }

        $ob = sprintf('<a class="jbp-shortcode-button jbp-list-job %s" href="%s">
			%s
		</a>', esc_attr($class), apply_filters('jbp_button_url', $url, 'my_jobs'), esc_html($text));

        return apply_filters('jbp_job_list_btn_output', $ob);
    }

    public function can_view($view = 'both')
    {
        $view = strtolower($view);
        if (is_user_logged_in()) {
            if ($view == 'loggedout') {
                return false;
            }
        } else {
            if ($view == 'loggedin') {
                return false;
            }
        }

        return true;
    }

    protected function custom_template($template)
    {
        ob_start();
        include locate_template($template);

        return ob_end_clean();
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
}