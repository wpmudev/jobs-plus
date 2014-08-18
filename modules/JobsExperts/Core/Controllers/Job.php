<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Controllers_Job
{
    public static function save($data)
    {
        $model = new JobsExperts_Core_Models_Job();
        $model->import($data);
        if ($model->validate()) {
            //get the category and skill and process it later
            $categories = $model->categories;
            $skills = explode(',', $model->skills);
            $skills = array_filter($skills);
            $model->status = $data['status'];
            $model->description = jbp_filter_text($model->description);
            $model->save();
            //update the term
            $model->assign_categories($categories, false);
            $model->assign_skill_tag($skills, false);

            return true;
        } else {
            return $model;
        }
    }

    public static function send_contact($id)
    {
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        $model = JobsExperts_Core_Models_Job::instance()->get_one($id);
        if (is_object($model)) {
            $user = get_userdata($model->owner);
            $content = wpautop(self::email_replace($plugin->settings()->job_email_content, get_post($model->id), $user->user_login));
            $subject = self::email_replace($plugin->settings()->job_email_subject, get_post($model->id), $user->user_login);

            $from = sprintf('%s <%s>', sanitize_text_field($_POST['name']), sanitize_email($_POST['email']));

            $message_headers = array();
            $message_headers[] = "MIME-Version: 1.0";
            $message_headers[] = "From: $from";
            $message_headers[] = "Reply-To: $from";
            $message_headers[] = sprintf("Content-Type: text/html; charset=\"%s\"", get_option('blog_charset'));

            if ($plugin->settings()->job_cc_admin) {
                $message_headers[] = "Cc: " . get_option('admin_email');
            }

            if ($plugin->settings()->job_cc_sender) {
                $message_headers[] = "Cc: $from";
            }

            $contact_id = $page_module->page($page_module::JOB_CONTACT);
            if (wp_mail($model->contact_email, $subject, $content, $message_headers)) {
                wp_redirect(add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            } else {
                wp_redirect(add_query_arg(array('fail' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            }

        }
    }

    public static function delete_job($id)
    {
        wp_delete_post($id);
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        wp_redirect(get_permalink($page_module->page($page_module::MY_JOB)));
    }

    static function email_replace($content = '', $post, $user_name)
    {
        $result =
            str_replace('SITE_NAME', get_bloginfo('name'),
                str_replace('POST_TITLE', esc_html($post->post_title),
                    str_replace('POST_LINK', make_clickable(get_permalink($post->ID)),
                        str_replace('TO_NAME', $user_name,
                            str_replace('FROM_NAME', sanitize_text_field($_POST['name']),
                                str_replace('FROM_EMAIL', sanitize_email($_POST['email']),
                                    str_replace('FROM_MESSAGE', $_POST['content'],
                                        $content)))))));

        return $result;
    }
}