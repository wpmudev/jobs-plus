<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_AddOn_Message_Models_Message extends JobsExperts_Framework_PostModel
{
    public $subject;
    public $content;
    public $send_to;
    public $send_from;
    public $reply_to;
    public $status;
    public $date;

    public $contact_type;
    public $ref_id;

    public $attachments;

    public function storage_name()
    {
        return 'jbp_message';
    }

    public function rules()
    {
        return array(
            array('required', 'content'),
        );
    }

    public function prepare_import_data()
    {
        //core data
        $args = array(
            'post' => array(
                'ID' => !$this->is_new_record() ? $this->id : null,
                'post_title' => $this->subject,
                'post_content' => $this->content,
                'post_status' => 'publish',
                'post_type' => 'jbp_message',
                'ping_status' => 'closed',
                'comment_status' => 'closed',
                'post_author' => $this->send_from,
                'post_parent' => $this->reply_to
            ),
            'meta' => array(
                '_send_to' => $this->send_to,
                '_date' => date('Y-m-d H:i:s', time()),
                '_status' => $this->status,
                '_contact_type' => $this->contact_type,
                '_ref_id' => $this->ref_id,
                '_attachments' => $this->attachments
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
        $this->subject = $post->post_title;
        $this->content = $post->post_content;
        $this->send_from = $post->post_author;
        $this->reply_to = $post->post_parent;
        //meta
        $this->send_to = get_post_meta($this->id, '_send_to', true);
        $this->date = get_post_meta($this->id, '_date', true);
        $this->status = get_post_meta($this->id, '_status', true);
        $this->contact_type = get_post_meta($this->id, '_contact_type', true);
        $this->ref_id = get_post_meta($this->id, '_ref_id', true);
        $this->attachments = get_post_meta($this->id, '_attachments', true);
    }

    public function get_messages()
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        $models = JobsExperts_AddOn_Message_Models_Message::instance()->get_all(array(
            'status' => 'publish',
            //'nopaging' => true,
            'posts_per_page' => $setting->message_per_page,
            'paged' => get_query_var('paged') != 0 ? get_query_var('paged') : 1,
            'meta_query' => array(
                array(
                    'key' => '_send_to',
                    'value' => get_current_user_id(),
                    'compare' => '=',
                ),
            ),
        ));
        return $models;
    }

    public function get_read()
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        $models = JobsExperts_AddOn_Message_Models_Message::instance()->get_all(array(
            'status' => 'publish',
            'posts_per_page' => $setting->message_per_page,
            'paged' => get_query_var('paged') != 0 ? get_query_var('paged') : 1,
            'meta_query' => array(
                array(
                    'key' => '_status',
                    'value' => 'read',
                    'compare' => '=',
                ),
                array(
                    'key' => '_send_to',
                    'value' => get_current_user_id(),
                    'compare' => '=',
                ),
            ),
        ));
        return $models;
    }

    public function get_unread()
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        $models = JobsExperts_AddOn_Message_Models_Message::instance()->get_all(array(
            'status' => 'publish',
            'author' => get_current_user_id(),
            'posts_per_page' => $setting->message_per_page,
            'paged' => get_query_var('paged') != 0 ? get_query_var('paged') : 1,
            'meta_query' => array(
                array(
                    'key' => '_status',
                    'value' => 'unread',
                    'compare' => '=',
                ),
                array(
                    'key' => '_send_to',
                    'value' => get_current_user_id(),
                    'compare' => '=',
                ),
            ),
        ));
        return $models;
    }

    public function get_sent()
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        $models = JobsExperts_AddOn_Message_Models_Message::instance()->get_all(array(
            'status' => 'publish',
            'author' => get_current_user_id(),
            'posts_per_page' => $setting->message_per_page,
            'paged' => get_query_var('paged') != 0 ? get_query_var('paged') : 1,
        ));
        return $models;
    }

    public function after_save()
    {
        do_action('message_after_message_save', $this);
    }

    public function new_message_notification()
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        global $jbp_message;
        $data = array(
            'SITE_NAME' => get_bloginfo('name'),
            'FROM_NAME' => $jbp_message->getFullName($this->send_from),
            'POST_LINK' => add_query_arg('message_id', $this->id, get_permalink($setting->inbox_page)),
            'FROM_MESSAGE' => $this->content
        );
        $data = apply_filters('message_notification_params', $data, $this);

        $subject = $setting->email_new_message_subject;
        $content = $setting->email_new_message_content;
        foreach ($data as $key => $val) {
            $subject = str_replace($key, $val, $subject);
            $content = str_replace($key, $val, $content);
        }
        $sendto = get_userdata($this->send_to);
        $from = get_userdata($this->send_from);
        //prepare atachments
        $attachments = array();
        if ($this->attachments) {
            $ids = explode(',', $this->attachments);
            $ids = array_filter($ids);
            foreach ($ids as $id) {
                if (filter_var($id, FILTER_VALIDATE_INT)) {
                    $upload = JobsExperts_Components_Uploader_Model::instance()->get_one($id);

                    if (is_object($upload) && $upload->file) {
                        $attachments[] = get_attached_file($upload->file);
                    }
                }
            }
        }
        $headers = array(
            'From: ' . $jbp_message->getFullName($this->send_from) . ' <' . $from->user_email . '>',
            'Content-Type: text/html; charset=UTF-8'
        );
        wp_mail($sendto->user_email, $subject, $content, $headers, $attachments);
    }

    function read_message_notification()
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        global $jbp_message;
        $data = array(
            'SITE_NAME' => get_bloginfo('name'),
            'FROM_NAME' => $jbp_message->getFullName($this->send_from),
            'TO_NAME' => $jbp_message->getFullName($this->send_to),
        );
        $subject = $setting->email_read_message_subject;
        $content = $setting->email_read_message_content;
        foreach ($data as $key => $val) {
            $subject = str_replace($key, $val, $subject);
            $content = str_replace($key, $val, $content);
        }
        $sendto = get_userdata($this->send_from);
        $headers = array(
            'Content-Type: text/html; charset=UTF-8'
        );
        wp_mail($sendto->user_email, $subject, $content, $headers);
    }

    function set_html_content_type()
    {
        return 'text/html';
    }
}