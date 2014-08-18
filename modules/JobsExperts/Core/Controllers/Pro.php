<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Controllers_Pro
{
    public static function save($data)
    {
        $model = new JobsExperts_Core_Models_Pro();
        $model->import($data);
        $model->name = trim($model->first_name . ' ' . $model->last_name);
        if ($model->validate()) {
            $model->short_description = jbp_filter_text($model->short_description);
            $model->biography = jbp_filter_text($model->biography);
            $model->status = $_POST['status'];
            $model->save();
            return true;
        } else {
            return $model;
        }
    }

    public static function send_contact($id)
    {
        $plugin = JobsExperts_Plugin::instance();
        $model = JobsExperts_Core_Models_Pro::instance()->get_one($id);
        $page_module = $plugin->page_module();
        if (is_object($model)) {
            $user = get_userdata($model->user_id);
            $content = wpautop(self::email_replace($plugin->settings()->expert_email_content, get_post($model->id), $user->user_login));;
            $subject = self::email_replace($plugin->settings()->expert_email_subject, get_post($model->id), $user->user_login);

            $from = sprintf('%s <%s>', sanitize_text_field($_POST['name']), sanitize_email($_POST['email']));

            $message_headers = array();
            $message_headers[] = "MIME-Version: 1.0";
            $message_headers[] = "From: $from";
            $message_headers[] = "Reply-To: $from";
            $message_headers[] = sprintf("Content-Type: text/html; charset=\"%s\"", get_option('blog_charset'));

            if ($plugin->settings()->expert_cc_admin) {
                $message_headers[] = "Cc: " . get_option('admin_email');
            }

            if ($plugin->settings()->expert_cc_sender) {
                $message_headers[] = "Cc: $from";
            }

            $contact_id = $page_module->page($page_module::EXPERT_CONTACT);
            if (wp_mail($model->contact_email, $subject, $content, $message_headers)) {
                wp_redirect(add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            } else {
                wp_redirect(add_query_arg(array('fail' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            }
        }
    }

    public static function delete_expert($id)
    {
        wp_delete_post($id);
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        wp_redirect(get_permalink($page_module->page($page_module::MY_EXPERT)));
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