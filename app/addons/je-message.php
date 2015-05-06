<?php

/**
 * Name: Message
 * Description: This add-on extends the contact form functionality of Jobs & Experts to make it into a fully featured on-site private message system.
 * Author: WPMU DEV
 */
class JE_Message
{
    public function __construct()
    {
        $this->load_files();
    }

    function load_files()
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        if (!is_plugin_active('messaging/messaging.php')) {
            $plugin = dirname(__FILE__) . '/je-message/lib/messaging.php';
            include_once dirname(__FILE__) . '/je-message/lib/messaging.php';
            //active bundler
            /*var_dump(file_exists($plugin));
            var_dump(validate_plugin($plugin));
            var_dump(activate_plugin(dirname(__FILE__) . '/je-message/lib/messaging.php'));*/
        }

        add_filter('jbp_job_contact_btn', array(&$this, 'contact_job_poster_btn'), 10, 2);
        add_filter('jbp_expert_contact_btn', array(&$this, 'contact_expert_poster_btn'), 10, 2);
        //send contact
        //$this->_add_filter('jbp_contact_send_email', 'save_message', 10, 5);

        //shortcode
        add_filter('the_content', array(&$this, 'append_inbox_button'));
        add_shortcode('jbp-message-inbox-btn', array(&$this, 'inbox_btn'));
        add_filter('je_buttons_on_single_page', array(&$this, 'append_inbox_button'));
        add_action('mm_before_layout', array(&$this, 'je_buttons_for_mm'));
        //$this->_add_shortcode('jbp-message-inbox', 'message_inbox');

        //scripts
        //$this->_add_action('wp_enqueue_scripts', 'scripts');

        //contact popup
        //$this->_add_action('jbp_after_single_expert', 'contact_in_popup');
        //$this->_add_action('jbp_after_single_job', 'contact_in_popup');

        //$this->_add_filter('mm_create_inbox_page', 'create_page');
        //$this->_add_filter('jbp_contact_validate_rules', 'contact_validate_rules');
    }

    function je_buttons_for_mm()
    {
        $shortcodes = apply_filters('je_buttons_on_single_page', '[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]');
        echo '<p style="text-align: center">' . do_shortcode($shortcodes) . '</p>';
    }

    function inbox_btn($atts)
    {
        wp_enqueue_style('jbp_message');
        $setting = new MM_Setting_Model();
        $setting->load();
        $link = !empty($setting->inbox_page) ? get_permalink($setting->inbox_page) : null;
        extract(shortcode_atts(array(
            'text' => __('Inbox', je()->domain),
            'view' => 'both', //loggedin, loggedout, both
            'class' => je()->settings()->theme,
            'template' => '',
            'url' => $link
        ), $atts));

        if (!$this->can_view($view)) {
            return '';
        }

        $ob = sprintf('<a class="ig-container jbp-shortcode-button jbp-message-inbox %s" href="%s">
			<i style="display: block" class="fa fa-inbox fa-2x"></i>%s
		</a>', esc_attr($class), $url, esc_html($text));

        return $ob;
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

    function append_inbox_button($content)
    {
        $pattern = get_shortcode_regex();
        if (preg_match_all('/' . $pattern . '/s', $content, $matches)
            && array_key_exists(2, $matches)
            && in_array('jbp-expert-profile-btn', $matches[2])
        ) {
            //getting the raw shortcode
            $key = array_search('jbp-expert-profile-btn', $matches[2]);
            $sc = $matches[0][$key];
            $new_content = str_replace($sc, $sc . '[jbp-message-inbox-btn]', $content);
            return $new_content;
        }

        return $content;
    }

    function contact_job_poster_btn($content, JE_Job_Model $model)
    {
        $user_id = $model->owner;
        $content = do_shortcode('[pm_user user_id="' . $user_id . '" text="' . __('Contact', je()->domain) . '"]');
        return $content;
    }

    function contact_expert_poster_btn($content, JE_Expert_Model $model)
    {
        $user_id = $model->user_id;
        $content = do_shortcode('[pm_user user_id="' . $user_id . '" text="' . __('Contact Me', je()->domain) . '"]');
        return $content;
    }
}

new JE_Message();