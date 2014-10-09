<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Controllers_Pro
{
    public static function get_model_backend()
    {
        $plugin = JobsExperts_Plugin::instance();
        if (isset($plugin->global['jbp_pro'])) {
            $model = $plugin->global['jbp_pro'];
        } else {
            //check does having id
            $model = null;
            if (isset($_GET['id'])) {
                $model = JobsExperts_Core_Models_Pro::instance()->get_one($_GET['id'], array(
                    'publish', 'pending', 'draft'
                ));
            }
            if (!is_object($model)) {
                $model = new JobsExperts_Core_Models_Pro();
                $model->status = 'auto-draft';
                $model->save();
            }
        }
        return $model;
    }

    public static function get_model_frontend()
    {
        $plugin = JobsExperts_Plugin::instance();
        if (isset($plugin->global['jbp_pro'])) {
            $model = $plugin->global['jbp_pro'];
        } else {
            //check does having id
            $model = null;
            if (isset($_GET['id'])) {
                $model = JobsExperts_Core_Models_Pro::instance()->get_one($_GET['id'], array(
                    'publish', 'pending', 'draft'
                ));
            }
            if (!is_object($model)) {
                $model = new JobsExperts_Core_Models_Pro();
                $model->status = 'auto-draft';
                $model->save();
            }
        }
        return $model;
    }

    public static function process_add_pro()
    {
        if (isset($_POST['jbp_admin_save_pro']) && wp_verify_nonce($_POST['jbp_admin_save_pro'], 'jbp_admin_save_pro')) {
            $data = $_POST['JobsExperts_Core_Models_Pro'];
            $data['biography'] = $_POST['biography'];
            $data['short_description'] = $_POST['short_description'];
            $data['social'] = implode(',', array_unique(explode(',', $data['social'])));

            $model = JobsExperts_Core_Models_Pro::instance()->get_one($data['id'], array(
                'status', 'pending', 'draft', 'auto-draft'
            ));
            $model->import($data);
            $model->save();
            if ($model->validate()) {
                wp_redirect(add_query_arg(array('id' => $model->id, 'status' => 'add-success'), admin_url('edit.php?post_type=jbp_job&page=jobs-plus-edit-pro')));
                exit;
            } else {
                $model->status = 'auto-draft';
                $model->save();

                JobsExperts_Plugin::instance()->global['jbp_pro'] = $model;
            }
        }
    }

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

    public static function send_contact($id, $data = array())
    {
        $plugin = JobsExperts_Plugin::instance();
        $model = JobsExperts_Core_Models_Pro::instance()->get_one($id);
        $page_module = $plugin->page_module();
        if (empty($data)) {
            $data = $_POST;
        }
        if (is_object($model)) {
            $user = get_userdata($model->user_id);
            $content = wpautop(self::email_replace($plugin->settings()->expert_email_content, get_post($model->id), $user->user_login, $data));;
            $subject = self::email_replace($plugin->settings()->expert_email_subject, get_post($model->id), $user->user_login, $data);

            $from = sprintf('%s <%s>', sanitize_text_field($data['name']), sanitize_email($data['email']));

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
                return (add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            } else {
                return (add_query_arg(array('fail' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
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

    static function email_replace($content = '', $post, $user_name, $data)
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
}