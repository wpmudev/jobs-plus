<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Controllers_Job extends JobsExperts_Framework_Module
{
    public static function get_model_backend()
    {
        $plugin = JobsExperts_Plugin::instance();
        if (isset($plugin->global['jbp_job'])) {
            $model = $plugin->global['jbp_job'];
        } else {
            //check does having id
            $model = null;
            if (isset($_GET['id'])) {
                $model = JobsExperts_Core_Models_Job::instance()->get_one($_GET['id'], array(
                    'publish', 'pending', 'draft'
                ));
            }
            if (!is_object($model)) {
                $model = new JobsExperts_Core_Models_Job();
                $model->status = 'auto-draft';
                $model->save();
            }
        }
        return $model;
    }

    public static function process_add_job()
    {
        if (isset($_POST['jbp_admin_save_job']) && wp_verify_nonce($_POST['jbp_admin_save_job'], 'jbp_admin_save_job')) {
            $data = $_POST['JobsExperts_Core_Models_Job'];
            $data['description'] = $_POST['job_description'];
            $model = JobsExperts_Core_Models_Job::instance()->get_one($data['id'], array(
                'status', 'pending', 'draft', 'auto-draft'
            ));
            $model->import($data);
            $categories = $model->categories;
            $skills = explode(',', $model->skills);
            $skills = array_filter($skills);
            $model->assign_categories($categories, false);
            $model->assign_skill_tag($skills, false);
            $model->save();
            if ($model->validate()) {
                wp_redirect(add_query_arg(array('id' => $model->id, 'status' => 'add-success'), admin_url('edit.php?post_type=jbp_job&page=jobs-plus-edit-job')));
                exit;
            } else {
                $model->status = 'auto-draft';
                $model->save();
                JobsExperts_Plugin::instance()->global['jbp_job'] = $model;
            }
        }
    }

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

    public static function send_contact($id, $data = array())
    {
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        $model = JobsExperts_Core_Models_Job::instance()->get_one($id);
        if (empty($data)) {
            $data = $_POST;
        }
        if (is_object($model)) {
            $user = get_userdata($model->owner);
            $content = wpautop(self::email_replace($plugin->settings()->job_email_content, get_post($model->id), $user->user_login, $data));
            $subject = self::email_replace($plugin->settings()->job_email_subject, get_post($model->id), $user->user_login, $data);

            $from = sprintf('%s <%s>', sanitize_text_field($data['name']), sanitize_email($data['email']));

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

            $contact_id = $page_module->page(JobsExperts_Core_PageFactory::JOB_CONTACT);
            if (wp_mail($model->contact_email, $subject, $content, $message_headers)) {
                return (add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            } else {
                return (add_query_arg(array('fail' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            }

        }
    }

    public static function delete_job($id)
    {
        wp_delete_post($id);
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        wp_redirect(get_permalink($page_module->page(JobsExperts_Core_PageFactory::MY_JOB)));
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