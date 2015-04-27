<?php

/**
 * @author:Hoang Ngo
 */
class JE_Contact_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_shortcode('jbp-job-contact-page', array(&$this, 'job'));
        add_shortcode('jbp-expert-contact-page', array(&$this, 'expert'));

        if (is_user_logged_in()) {
            add_action('wp_loaded', array(&$this, 'process'));
            add_action('wp_loaded', array(&$this, 'process_expert'));
        }
    }

    function process_expert()
    {
        if (je()->post('contact_expert', null) === null) {
            return;
        }

        if (!wp_verify_nonce(je()->post('_wpnonce'), 'expert_contact')) {
            return;
        }

        $model = new JE_Contact_Model();
        $model->import(je()->post('JE_Contact_Model'));
        if ($model->validate()) {
            $url = $this->send_expert_contact(je()->post('id'), $model->export());
            $this->redirect($url);
        } else {
            je()->global['contact'] = $model;
        }
    }

    function process()
    {
        if (je()->post('contact_job', null) === null) {
            return;
        }

        if (!wp_verify_nonce(je()->post('_wpnonce'), 'jbp_contact')) {
            return;
        }

        $model = new JE_Contact_Model();
        $model->import(je()->post('JE_Contact_Model'));
        if ($model->validate()) {
            $url = $this->send_job_contact(je()->post('id'), $model->export());
            $this->redirect($url);
        } else {
            je()->global['contact'] = $model;
        }
    }

    function send_job_contact($id, $data = array())
    {
        $model = JE_Job_Model::model()->find($id);
        if (empty($data)) {
            $data = $_POST;
        }
        if (is_object($model)) {
            $user = get_userdata($model->owner);
            $content = wpautop($this->email_replace(je()->settings()->job_email_content, get_post($model->id), $user->user_login, $data));
            $subject = $this->email_replace(je()->settings()->job_email_subject, get_post($model->id), $user->user_login, $data);

            $from = sprintf('%s <%s>', sanitize_text_field($data['name']), sanitize_email($data['email']));

            $message_headers = array();
            $message_headers[] = "MIME-Version: 1.0";
            $message_headers[] = "From: $from";
            $message_headers[] = "Reply-To: $from";
            $message_headers[] = sprintf("Content-Type: text/html; charset=\"%s\"", get_option('blog_charset'));

            if (je()->settings()->job_cc_admin) {
                $message_headers[] = "Cc: " . get_option('admin_email');
            }

            if (je()->settings()->job_cc_sender) {
                $message_headers[] = "Cc: $from";
            }

            $contact_id = je()->pages->page(JE_Page_Factory::JOB_CONTACT);
            if (wp_mail($model->contact_email, $subject, $content, $message_headers)) {
                return (esc_url(add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id))));
            } else {
                return (esc_url(add_query_arg(array('status' => 'fail', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id))));
            }

        }
    }

    function send_expert_contact($id, $data = array())
    {
        $model = JE_Expert_Model::model()->find($id);
        if (empty($data)) {
            $data = $_POST;
        }
        if (is_object($model)) {
            $user = get_userdata($model->user_id);
            $content = wpautop($this->email_replace(je()->settings()->expert_email_content, get_post($model->id), $user->user_login, $data));
            $subject = $this->email_replace(je()->settings()->expert_email_subject, get_post($model->id), $user->user_login, $data);

            $from = sprintf('%s <%s>', sanitize_text_field($data['name']), sanitize_email($data['email']));

            $message_headers = array();
            $message_headers[] = "MIME-Version: 1.0";
            $message_headers[] = "From: $from";
            $message_headers[] = "Reply-To: $from";
            $message_headers[] = sprintf("Content-Type: text/html; charset=\"%s\"", get_option('blog_charset'));

            if (je()->settings()->job_cc_admin) {
                $message_headers[] = "Cc: " . get_option('admin_email');
            }

            if (je()->settings()->job_cc_sender) {
                $message_headers[] = "Cc: $from";
            }

            $contact_id = je()->pages->page(JE_Page_Factory::EXPERT_CONTACT);
            if (wp_mail($model->contact_email, $subject, $content, $message_headers)) {
                return (esc_url(add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id))));
            } else {
                return (esc_url(add_query_arg(array('status' => 'fail', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id))));
            }

        }
    }

    function email_replace($content = '', $post, $user_name, $data)
    {
        $result =
            str_replace('SITE_NAME', get_bloginfo('name'),
                str_replace('POST_TITLE', esc_html($post->post_title),
                    str_replace('POST_LINK', make_clickable(get_permalink($post->ID)),
                        str_replace('TO_NAME', $user_name,
                            str_replace('FROM_NAME', sanitize_text_field($data['name']),
                                str_replace('FROM_EMAIL', sanitize_email($data['email']),
                                    str_replace('FROM_MESSAGE', $data['content'],
                                        $content)))))));

        return $result;
    }

    function job($atts)
    {
        if (!is_user_logged_in()) {
            return $this->render('login', array(), false);
        }
        $a = shortcode_atts(array(
            'success_text' => __("Your request has been sent. Thank you!", je()->domain),
            'error_text' => __("Some error happened, please try later. Thank you!", je()->domain),
            'id' => 0
        ), $atts);

        //get plugin instance
        if ($a['id'] != 0) {
            $model = JE_Job_Model::model()->find($a['id']);
        } else {
            $slug = isset($_GET['contact']) ? $_GET['contact'] : null;
            $model = JE_Job_Model::model()->find_by_slug($slug);
        }
        if (isset(je()->global['contact'])) {
            $contact = je()->global['contact'];
        } else {
            $contact = new JE_Contact_Model();
        }
        if (is_object($model)) {
            return $this->render('contact/job', array(
                'model' => $model,
                'contact' => $contact,
                'a' => $a
            ), false);
        }
    }

    function expert($atts)
    {
        if (!is_user_logged_in()) {
            return $this->render('login', array(), false);
        }
        $a = shortcode_atts(array(
            'success_text' => __("Your request has been sent. Thank you!", je()->domain),
            'error_text' => __("Some error happened, please try later. Thank you!", je()->domain),
            'id' => 0
        ), $atts);

        //get plugin instance
        if ($a['id'] != 0) {
            $model = JE_Expert_Model::model()->find($a['id']);
        } else {
            $slug = isset($_GET['contact']) ? $_GET['contact'] : null;
            $model = JE_Expert_Model::model()->find_by_slug($slug);
        }
        if (isset(je()->global['contact'])) {
            $contact = je()->global['contact'];
        } else {
            $contact = new JE_Contact_Model();
        }
        if (is_object($model)) {
            return $this->render('contact/expert', array(
                'model' => $model,
                'contact' => $contact,
                'a' => $a
            ), false);
        }
    }
}