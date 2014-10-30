<?php

/**
 * Name: Message
 * Description: This add-on extends the contact form functionality of Jobs & Experts to make it into a fully featured on-site private message system.
 * Author: WPMU DEV
 */
class JobsExperts_AddOn_Message extends JobsExperts_AddOn
{
    public function __construct()
    {
        //$this->_add_action('init', 'on_init');
        $this->_add_action('plugins_loaded', 'load_files');
    }

    function load_files()
    {
        if (!function_exists('mmg')) {
            include_once __DIR__ . '/Message/lib/messaging.php';
        }

        $this->_add_action('jbp_setting_menu', 'menu');

        //send contact
        $this->_add_filter('jbp_contact_send_email', 'save_message', 10, 5);

        //shortcode
        $this->_add_filter('the_content', 'append_inbox_button');
        $this->_add_shortcode('jbp-message-inbox-btn', 'inbox_btn');
        $this->_add_shortcode('jbp-message-inbox', 'message_inbox');

        //scripts
        $this->_add_action('wp_enqueue_scripts', 'scripts');

        //contact popup
        $this->_add_action('jbp_after_single_expert', 'contact_in_popup');
        $this->_add_action('jbp_after_single_job', 'contact_in_popup');

        $this->_add_filter('mm_create_inbox_page', 'create_page');
        $this->_add_filter('jbp_contact_validate_rules', 'contact_validate_rules');
    }

    function contact_in_popup()
    {
        global $wp_scripts;
        $wp_scripts->dequeue('hn_uploaderiframe_transport');
        $render = new JobsExperts_AddOn_Message_Views_PopupContact(array());
        $render->render();
    }


    function contact_validate_rules()
    {
        return array(
            array('required', 'content'),
        );
    }

    function jbp_job_contact($content, $a)
    {
        $render = new JobsExperts_AddOn_Message_Views_JobContact(array(
            'a' => $a
        ));
        return $render->_to_html();
    }

    function jbp_expert_contact($content, $a)
    {
        $render = new JobsExperts_AddOn_Message_Views_ExpertContact(array(
            'a' => $a
        ));
        return $render->_to_html();
    }

    function reorder_menu($menu_order)
    {
        global $submenu, $menu;
        $pro_menu = empty($submenu['edit.php?post_type=jbp_pro']) ? false : $submenu['edit.php?post_type=jbp_pro'];

        if ($pro_menu) {
            //getting the first
            $message = array_shift($pro_menu);
            array_splice($pro_menu, count($pro_menu) - 1, 0, array($message));
            $submenu['edit.php?post_type=jbp_pro'] = $pro_menu;
        }

        //var_dump($submenu);

        return $menu_order;
    }

    function custom_admin_menu()
    {
        add_submenu_page('edit.php?post_type=jbp_job',
            __('Messages', JBP_TEXT_DOMAIN),
            __('Messages', JBP_TEXT_DOMAIN),
            'manage_options',
            'message-main',
            array($this, 'admin_messages')
        );
        add_submenu_page('edit.php?post_type=jbp_pro',
            __('Messages', JBP_TEXT_DOMAIN),
            __('Messages', JBP_TEXT_DOMAIN),
            'manage_options',
            'message-main',
            array($this, 'admin_messages')
        );
        add_submenu_page('',
            __('View Message', JBP_TEXT_DOMAIN),
            __('View Message', JBP_TEXT_DOMAIN),
            'manage_options',
            'message-main-view',
            array($this, 'admin_messages_view')
        );
    }

    function message_inbox($atts)
    {
        wp_enqueue_script('ig-bootstrap');
        wp_enqueue_style('ig-bootstrap');
        wp_enqueue_style('ig-fontawesome');
        ob_start();
        echo do_shortcode('[message_inbox]');
        $content = ob_get_clean();
        return $content;
    }

    function scripts()
    {
        wp_register_style('jbp_message', JobsExperts_Plugin::instance()->_module_url . 'AddOn/Message/style.css');
    }

    function append_inbox_button($content)
    {
        $new_content = str_replace('[jbp-expert-profile-btn]', '[jbp-expert-profile-btn][jbp-message-inbox-btn]', $content);
        return $new_content;
    }

    function inbox_btn($atts)
    {
        wp_enqueue_style('jbp_message');
        $setting = new MM_Setting_Model();
        $setting->load();
        $link = !empty($setting->inbox_page) ? get_permalink($setting->inbox_page) : null;
        extract(shortcode_atts(array(
            'text' => __('Inbox', JBP_TEXT_DOMAIN),
            'view' => 'both', //loggedin, loggedout, both
            'class' => JobsExperts_Plugin::instance()->settings()->theme,
            'template' => '',
            'url' => $link
        ), $atts));
        $ob = sprintf('<a class="jbp-shortcode-button jbp-message-inbox %s" href="%s">
			<i style="display: block" class="fa fa-inbox fa-2x"></i>%s
		</a>', esc_attr($class), $url, esc_html($text));

        return $ob;
    }

    function save_message($return, $type, $id, JobsExperts_Core_Models_Contact $contact, $user_id)
    {
        $plugin = JobsExperts_Plugin::instance();
        $model = null;
        $to = null;
        $subject = "";
        $user = null;
        $page_module = $plugin->page_module();
        if ($type == 'job') {
            $contact_id = $plugin->page_module()->page($page_module::JOB_CONTACT);
            $model = JobsExperts_Core_Models_Job::instance()->get_one($id);
            $to = $model->owner;
            $user = get_userdata($model->owner);
            $subject = $subject = JobsExperts_Core_Controllers_Job::email_replace($plugin->settings()->job_email_subject, get_post($model->id), $user->user_login, $contact->export());
        } else {
            $contact_id = $plugin->page_module()->page($page_module::EXPERT_CONTACT);
            $model = JobsExperts_Core_Models_Pro::instance()->get_one($id);
            $to = $model->user_id;
            $user = get_userdata($to);
            $subject = JobsExperts_Core_Controllers_Pro::email_replace($plugin->settings()->expert_email_subject, get_post($model->id), $user->user_login, $contact->export());
        }
        //save message
        if (is_object($model)) {
            $conv = new MM_Conversation_Model();
            $conv->save();

            $message = new MM_Message_Model();
            $message->status = 'unread';
            $message->send_from = $user_id;
            $message->send_to = $to;
            $message->content = jbp_filter_text($contact->content);
            $message->subject = $subject;
            $message->conversation_id = $conv->id;

            foreach ($_POST['data'] as $key => $val) {
                if ($val['name'] == (get_class($message) . '[attachment]')) {
                    $message->attachment = $val['value'];
                }
            }
            $message->save();
            $conv->update_index($message->id);
            /*//update all the attachment link to this
            foreach (explode(',', $message->attachment) as $id) {
                if (filter_var($id, FILTER_VALIDATE_INT)) {
                    $u = JobsExperts_Components_Uploader_Model::instance()->get_one($id);
                    if (is_object($u)) {
                        $u->parent_id = $message->id;
                        $u->save();
                    }
                }
            }*/

            $link = (add_query_arg(array('status' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            echo json_encode(array(
                'status' => 1,
                'id' => $model->id,
                'url' => $link
            ));
        } else {
            $link = (add_query_arg(array('fail' => 'success', 'contact' => get_post($model->id)->post_name), get_permalink($contact_id)));
            echo json_encode(array(
                'status' => 0,
                'id' => $model->id,
                'url' => $link
            ));
        }

        //always false to prevent core function
        return false;
    }

    function create_page($args)
    {
        $page_module = JobsExperts_Plugin::instance()->page_module();
        $vid = $page_module->page($page_module::JOB_ADD);
        $vpage = get_post($vid);
        $args['post_content'] = str_replace('[jbp-job-update-page]', '[jbp-message-inbox]', $vpage->post_content);
        return $args;

    }

    function menu()
    {
        $plugin = JobsExperts_Plugin::instance();
        ?>
        <li <?php echo $this->active_tab('job_message') ?>>
            <a href="<?php echo admin_url('admin.php?page=mm_setting') ?>">
                <i class="glyphicon glyphicon-envelope"></i> <?php _e('Message', JBP_TEXT_DOMAIN) ?>
            </a></li>
    <?php
    }
}

global $jbp_message;
$jbp_message = new JobsExperts_AddOn_Message();
