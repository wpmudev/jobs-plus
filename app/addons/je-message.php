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
            include_once dirname(__FILE__) . '/je-message/lib/messaging.php';
        }

        add_filter('jbp_job_contact_btn', array(&$this, 'contact_job_poster_btn'), 10, 2);
        add_filter('jbp_expert_contact_btn', array(&$this, 'contact_expert_poster_btn'), 10, 2);
        //send contact
        //$this->_add_filter('jbp_contact_send_email', 'save_message', 10, 5);

        //shortcode
        ///$this->_add_filter('the_content', 'append_inbox_button');
        //$this->_add_shortcode('jbp-message-inbox-btn', 'inbox_btn');
        //$this->_add_shortcode('jbp-message-inbox', 'message_inbox');

        //scripts
        //$this->_add_action('wp_enqueue_scripts', 'scripts');

        //contact popup
        //$this->_add_action('jbp_after_single_expert', 'contact_in_popup');
        //$this->_add_action('jbp_after_single_job', 'contact_in_popup');

        //$this->_add_filter('mm_create_inbox_page', 'create_page');
        //$this->_add_filter('jbp_contact_validate_rules', 'contact_validate_rules');
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