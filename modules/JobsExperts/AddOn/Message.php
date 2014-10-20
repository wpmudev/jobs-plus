<?php

/**
 * Name: Message
 * Description: This add-on extends the contact form of Jobs&Experts, make it an full feature in-site private message.
 * Author: WPMU DEV
 */
class JobsExperts_AddOn_Message extends JobsExperts_AddOn
{
    public function __construct()
    {
        $this->_add_action('init', 'on_init');
        $this->_add_action('jbp_setting_menu', 'menu');
        $this->_add_action('jbp_setting_content', 'content', 10, 2);
        $this->_add_action('jbp_after_save_settings', 'save_setting');

        //send contact
        $this->_add_filter('jbp_contact_send_email', 'save_message', 10, 5);

        $this->_add_action('jbp_middle_expert_contact_form', 'inject_uploader');
        $this->_add_action('jbp_middle_job_contact_form', 'inject_uploader');

        //shortcode
        $this->_add_filter('the_content', 'append_inbox_button');
        $this->_add_shortcode('jbp-message-inbox-btn', 'inbox_btn');
        $this->_add_shortcode('jbp-message-inbox', 'message_inbox');

        //scripts
        $this->_add_action('wp_enqueue_scripts', 'scripts');

        $this->_add_ajax_action('jbp_create_message_page', 'create_page');
        $this->_add_ajax_action('jbp_load_message', 'load_message');
        $this->_add_ajax_action('jbp_reply_message', 'reply_message');
        $this->_add_ajax_action('jbp_remove_message', 'remove_message');
        $this->_add_ajax_action('messages_user_setting', 'messages_user_setting');

        //noti
        $this->_add_action('message_after_message_save', 'new_message_notification');
        $this->_add_action('message_message_read', 'read_message_notification');

        //admin page
        $this->_add_action('admin_menu', 'custom_admin_menu');
        $this->_add_filter('menu_order', 'reorder_menu', 20);


        $this->_add_filter('jbp_contact_validate_rules', 'contact_validate_rules');
        $this->_add_filter('jbp_expert_contact', 'jbp_expert_contact', 10, 2);
        $this->_add_filter('jbp_job_contact', 'jbp_job_contact', 10, 2);
    }

    function contact_validate_rules()
    {
        return array(
            array('required', 'name,content'),
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

    function admin_messages_view()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $model = JobsExperts_AddOn_Message_Models_Message::instance()->get_one($id);
        if (is_object($model)) {
            ?>
            <div class="wrap">
                <div class="hn-container" style="width: 100%">
                    <div class="row">
                        <div class="col-md-12">
                            <h2><?php echo sprintf(__("Message From [%s] To [%s]", JBP_TEXT_DOMAIN), $this->getFullName($model->send_from), $this->getFullName($model->send_to)) ?></h2>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <?php echo $this->render_message($model, false, false) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
    }

    function admin_messages()
    {
        $view = new JobsExperts_AddOn_Message_Views_MessageAdmin();
        $view->render();
    }

    function read_message_notification(JobsExperts_AddOn_Message_Models_Message $model)
    {
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        $is_send = $setting->global_receipt;
        if (!$is_send)
            return;
        if ($setting->user_receipt == true) {
            //check does user enable
            $sender_setting = get_user_meta($model->send_from, 'messages_user_setting', true);
            if (!$sender_setting) {
                $sender_setting = array(
                    'enable_receipt' => $setting->global_receipt,
                    'prevent_receipt' => false
                );
            }
            if ($sender_setting['enable_receipt'] != true) {
                //user don't enable it,
                return;
            }
            //user enable it, checking does the receiver block it
            $reciver_setting = get_user_meta($model->send_to, 'messages_user_setting', true);
            if (!$reciver_setting) {
                $reciver_setting = array(
                    'enable_receipt' => $setting->global_receipt,
                    'prevent_receipt' => false
                );
            }
            if ($reciver_setting['prevent_receipt'] == true) {
                //this user has block it, return
                return;
            }
        }

        $model->read_message_notification();
    }

    function new_message_notification(JobsExperts_AddOn_Message_Models_Message $model)
    {
        $model->new_message_notification();
    }

    function on_init()
    {
        $args = array(
            'supports' => array(),
            'taxonomies' => array(),
            'hierarchical' => true,
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => false,
            'capability_type' => 'page',
        );
        register_post_type('jbp_message', $args);
    }

    function inject_uploader($form)
    {
        global $jbp_component_uploader;
        $jbp_component_uploader->load_scripts();
        $model = new JobsExperts_AddOn_Message_Models_Message();
        $uploader = new JobsExperts_Components_Uploader_View(array(
            'model' => $model,
            'attribute' => 'attachments',
            'form' => $form
        ));
        $uploader->render();
    }

    function message_inbox($atts)
    {
        if (!is_user_logged_in()) {
            return sprintf(__("Please <a href=\"%s\">Login</a>", JBP_TEXT_DOMAIN), wp_login_url());
        }
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
        $setting->load();
        wp_enqueue_style('jbp_message-scrollbar');
        wp_enqueue_script('jbp_message-scrollbar');
        wp_enqueue_script('jbp_message-cookie');
        $query = isset($_GET['query']) ? $_GET['query'] : null;
        $id = isset($_GET['message_id']) ? $_GET['message_id'] : null;
        $type = isset($_GET['box']) ? $_GET['box'] : 'inbox';
        if ($type == 'setting') {
            $view = new JobsExperts_AddOn_Message_Views_Setting();
            return $view;
        }
        if (!empty($query)) {
            $type = 'inbox';
            $models = JobsExperts_AddOn_Message_Models_Message::instance()->get_all(array(
                's' => $query,
                'status' => 'publish',
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
        } elseif (!empty($id)) {
            $type = 'inbox';
            $models = JobsExperts_AddOn_Message_Models_Message::instance()->get_all(array(
                'status' => 'publish',
                'posts_per_page' => $setting->message_per_page,
                'paged' => get_query_var('paged') != 0 ? get_query_var('paged') : 1,
                'post__in' => array($id),
                'meta_query' => array(
                    array(
                        'key' => '_send_to',
                        'value' => get_current_user_id(),
                        'compare' => '=',
                    ),
                ),
            ));
        } else {
            $abs = JobsExperts_AddOn_Message_Models_Message::instance();
            switch ($type) {
                case 'inbox':
                    $models = $abs->get_messages();
                    break;
                case 'read':
                    $models = $abs->get_read();
                    break;
                case 'unread':
                    $models = $abs->get_unread();
                    break;
                case 'sent':
                    $models = $abs->get_sent();
                    break;
            }
        }
        $view = new JobsExperts_AddOn_Message_Views_Inbox(array(
            'models' => $models,
            'type' => $type,
            'query' => $query
        ));
        return $view;
    }

    function scripts()
    {
        wp_register_style('jbp_message', JobsExperts_Plugin::instance()->_module_url . 'AddOn/Message/style.css');
        wp_register_style('jbp_message-scrollbar', JobsExperts_Plugin::instance()->_module_url . 'AddOn/Message/perfect-scrollbar.min.css');
        wp_register_script('jbp_message-scrollbar', JobsExperts_Plugin::instance()->_module_url . 'AddOn/Message/perfect-scrollbar.min.js');
        wp_register_script('jbp_message-cookie', JobsExperts_Plugin::instance()->_module_url . 'AddOn/Message/jquery.cookie.js');
    }

    function append_inbox_button($content)
    {
        $new_content = str_replace('[jbp-expert-profile-btn]', '[jbp-expert-profile-btn][jbp-message-inbox-btn]', $content);
        return $new_content;
    }

    function inbox_btn($atts)
    {
        wp_enqueue_style('jbp_message');
        $setting = new JobsExperts_AddOn_Message_Models_Setting();
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
		</a>', esc_attr($class), apply_filters('jbp_button_url', $url, 'my_jobs'), esc_html($text));
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
            $message = new JobsExperts_AddOn_Message_Models_Message();
            $message->status = 'unread';
            $message->send_from = $user_id;
            $message->send_to = $to;
            $message->content = $contact->content;
            $message->subject = $subject;
            $message->contact_type = $type;
            $message->ref_id = $id;
            foreach ($_POST['data'] as $key => $val) {
                if ($val['name'] == (get_class($message) . '[attachments]')) {
                    $message->attachments = $val['value'];
                }
            }
            $message->save();
            //update all the attachment link to this
            foreach (explode(',', $message->attachments) as $id) {
                if (filter_var($id, FILTER_VALIDATE_INT)) {
                    $u = JobsExperts_Components_Uploader_Model::instance()->get_one($id);
                    if (is_object($u)) {
                        $u->parent_id = $message->id;
                        $u->save();
                    }
                }
            }

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

    function create_page()
    {
        if (isset($_POST['m_type'])) {
            $page_module = JobsExperts_Plugin::instance()->page_module();
            $model = new JobsExperts_AddOn_Message_Models_Setting();
            $model->load();
            switch ($_POST['m_type']) {
                case 'inbox':
                    $vid = $page_module->page($page_module::JOB_ADD);
                    $vpage = get_post($vid);
                    $vpage->post_title = "Inbox";
                    $vpage->post_content = str_replace('[jbp-job-update-page]', '[jbp-message-inbox]', $vpage->post_content);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->inbox_page = $new_id;
                    $model->save();
                    //update

                    echo $new_id;
                    break;
            }
        }
        exit;
    }

    function reset_page($vpage)
    {
        $vpage->post_status = 'publish';
        $vpage->post_name = null;
        $vpage->post_type = 'page';
        $vpage->post_date = null;
        $vpage->post_date_gmt = null;
        $vpage->ID = null;

        return $vpage;
    }


    public function save_setting()
    {
        if (isset($_POST['JobsExperts_AddOn_Message_Models_Setting'])) {
            $model = new JobsExperts_AddOn_Message_Models_Setting();
            $model->load();
            $model->import($_POST['JobsExperts_AddOn_Message_Models_Setting']);

            $model->save();
        }
    }

    function menu()
    {
        $plugin = JobsExperts_Plugin::instance();
        ?>
        <li <?php echo $this->active_tab('job_message') ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job_message') ?>">
                <i class="glyphicon glyphicon-envelope"></i> <?php _e('Message', JBP_TEXT_DOMAIN) ?>
            </a></li>
    <?php
    }

    function content(JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model)
    {
        $model = new JobsExperts_AddOn_Message_Models_Setting();
        $model->load();
        if ($this->is_current_tab('job_message')) {
            ?>
            <fieldset style="min-height: 300px">
                <div class="page-header" style="margin-top: 0">
                    <h4><?php _e('Message', JBP_TEXT_DOMAIN) ?></h4>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Enable Receipt', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <?php $form->hiddenField($model, 'global_receipt', array('value' => 0)) ?>
                        <label><?php $form->checkBox($model, "global_receipt", array("value" => 1)) ?></label>
                        <span
                            class="help-inline"><?php _e('This will enable the receipt globally', JBP_TEXT_DOMAIN) ?></span>


                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label
                        class="col-md-3 label-control"><?php _e('User can on/off receipt', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <?php $form->hiddenField($model, 'user_receipt', array('value' => 0)) ?>
                        <label><?php $form->checkBox($model, "user_receipt", array("value" => 1)) ?></label>
                        <span
                            class="help-inline"><?php _e('This will let the user turn off or on', JBP_TEXT_DOMAIN) ?></span>

                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="page-header" style="margin-top: 0">
                    <h4><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></h4>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Inbox Page', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($model, 'inbox_page',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="inbox"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </fieldset>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('body').on('click', '.create-page', function () {
                        var that = $(this);
                        $.ajax({
                            type: 'POST',
                            data: {
                                m_type: $(this).data('id'),
                                action: 'jbp_create_message_page'
                            },
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            beforeSend: function () {
                                that.attr('disabled', 'disabled').text('<?php echo esc_js(__('Creating...',JBP_TEXT_DOMAIN)) ?>');
                            },
                            success: function (data) {
                                var element = that.parent().parent().find('select').first();
                                console.log(element);
                                $.get("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>", function (html) {
                                    html = $(html);
                                    var clone = html.find('select[name="' + element.attr('name') + '"]');
                                    element.replaceWith(clone);
                                    that.removeAttr('disabled').text('<?php echo esc_js(__('Create Page',JBP_TEXT_DOMAIN)) ?>');
                                })
                            }
                        })
                    })
                })
            </script>
        <?php
        }
    }

    public function get_avatar($user_id, $size = 60, $use_ratio = false)
    {
        $models = JobsExperts_Core_Models_Pro::instance()->get_all(array(
            'author' => $user_id
        ));
        $avatar = null;
        if (count($models['data'])) {
            $model = array_shift($models['data']);
            $avatar = $model->get_avatar($size, $use_ratio);
        } else {
            $avatar = get_avatar($user_id, $size);
        }
        return $avatar;
    }

    public function getFullName($user_id)
    {
        //check if tis user have expert profile
        $models = JobsExperts_Core_Models_Pro::instance()->get_all(array(
            'author' => $user_id
        ));
        if (count($models['data'])) {
            //having expert, getting this name
            $model = array_shift($models['data']);
            return $model->first_name . ' ' . $model->last_name;
        }
        $name = '';
        $userdata = get_userdata($user_id);
        $name = $userdata->user_info . ' ' . $userdata->last_name;
        $name = trim($name);
        if (!empty($name)) {
            return $name;
        }
        return $userdata->user_login;
    }

    function render_message($model, $display_reply = true, $display_trash = true)
    {
        ob_start();
        echo $this->_render_message($model, $display_reply, $display_trash);
        $ids = get_post_ancestors($model->id);
        if (!empty($ids)) {
            ?>
            <div class="well well-sm ps-container ps-active-x ps-active-y" id="message_history">
                <h4><?php _e("History Messages", JBP_TEXT_DOMAIN) ?></h4>
                <?php
                foreach ($ids as $id) {
                    $h = JobsExperts_AddOn_Message_Models_Message::instance()->get_one($id);
                    echo $this->_render_message($h, false, false);
                }
                ?>
            </div>
        <?php
        }
        return ob_get_clean();
    }

    function _render_message($model, $display_reply = true, $display_trash = true)
    {
        ob_start();
        ?>
        <div class="inbox-message-display">
            <div class="inbox-message-display-header">
                <div class="row">
                    <div class="col-md-7">
                        <p><?php _e("From", JBP_TEXT_DOMAIN) ?>:
                            <strong><?php echo $this->getFullName($model->send_from) ?></strong>
                        </p>
                    </div>
                    <div class="col-md-5">
                        <div class="pull-right btn-group btn-group-xs">
                            <?php if ($display_reply == true): ?>
                                <a data-id="<?php echo $model->id ?>" href="#"
                                   class="btn btn-default bnt-xs reply-message">
                                    <i class="fa fa-reply"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($display_trash == true): ?>
                                <a data-id="<?php echo $model->id ?>" href="#"
                                   class="btn btn-danger bnt-xs remove-message">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <p><?php _e("Sent", JBP_TEXT_DOMAIN) ?>
                            :
                            <strong><?php echo date('F j, Y, g:i a', strtotime($model->date)) ?></strong>
                        </p>

                        <p><?php _e("Subject", JBP_TEXT_DOMAIN) ?>
                            : <strong class="subject"><?php echo $model->subject ?></strong></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <hr/>
            <div class="inbox-message-display-body">
                <?php echo wpautop($model->content) ?>
            </div>
            <?php
            if (!empty($model->attachments)) {
                $ids = explode(',', $model->attachments);
                $ids = array_filter($ids);
                $files = JobsExperts_Components_Uploader_Model::instance()->get_all(array(
                    'post__in' => $ids
                ));
                if (!empty($files['data'])) {
                    ?>
                    <div class="inbox-message-display-footer">
                        <div class="row">
                            <?php foreach ($files['data'] as $upload): ?>
                                <div class="col-md-6 message-attachment">
                                    <a data-toggle="modal" data-target="#<?php echo $upload->id ?>" href="#">
                                        <i class="fa <?php echo !empty($upload->file) ? 'fa-paperclip' : 'fa-globe' ?> fa-2x pull-left"></i>
                                        <?php echo $upload->name() ?>
                                    </a>

                                    <div class="clearfix"></div>
                                    <!-- Modal -->
                                    <div style="top:10%" class="modal fade" id="<?php echo $upload->id ?>"
                                         labelledby="myLargeModalLabel" tabindex="-1"
                                         role="dialog" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><?php echo $upload->name() ?></h4>
                                                </div>
                                                <div class="modal-body sample-pop"
                                                     style="max-height:500px;overflow-y:scroll">
                                                    <?php
                                                    $file = $upload->file;
                                                    $file_url = '';
                                                    $show_image = false;

                                                    if ($file) {
                                                        $file_url = wp_get_attachment_url($file);
                                                        $mime = explode('/', get_post_mime_type($file));
                                                        if (array_shift($mime) == 'image') {
                                                            $show_image = true;
                                                        }
                                                    }
                                                    if ($show_image) {
                                                        echo '<img src="' . $file_url . '"/>';
                                                    } elseif ($file) {
                                                        //show meta
                                                        ?>
                                                        <ul class="list-group">
                                                            <li class="list-group-item upload-item">
                                                                <i class="glyphicon glyphicon-floppy-disk"></i>
                                                                <?php _e('Size', JBP_TEXT_DOMAIN) ?>:
                                                                <strong><?php echo jbp_format_bytes(filesize(get_attached_file($file))) ?></strong>
                                                            </li>
                                                            <li class="list-group-item upload-item">
                                                                <i class="glyphicon glyphicon-file"></i>
                                                                <?php _e('Type', JBP_TEXT_DOMAIN) ?>:
                                                                <strong><?php echo ucwords(get_post_mime_type($file)) ?></strong>
                                                            </li>
                                                        </ul>
                                                    <?php
                                                    } else {
                                                        ?>
                                                        <ul class="list-group">
                                                            <li class="list-group-item">
                                                                <i class="glyphicon glyphicon-link"></i>
                                                                <strong><?php _e('Link', JBP_TEXT_DOMAIN) ?></strong>:
                                                                <?php echo $upload->url ?>
                                                            </li>
                                                            <div class="clearfix"></div>
                                                        </ul>
                                                    <?php
                                                    }
                                                    echo $upload->description
                                                    ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <?php if ($upload->url): ?>
                                                        <a class="btn btn-info" rel="nofollow"
                                                           href="<?php echo esc_attr($upload->url) ?>" target="_blank">
                                                            <?php _e("Visit Link", JBP_TEXT_DOMAIN) ?>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($file): ?>
                                                        <a href="<?php echo $file_url ?>" download
                                                           class="btn btn-info"><?php _e('Download File', JBP_TEXT_DOMAIN) ?></a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal"><?php _e('Close', JBP_TEXT_DOMAIN) ?>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php
                }
            }
            ?>
        </div>

        <?php
        return ob_get_clean();
    }

    function load_message()
    {
        $nonce = isset($_POST['_nonce']) ? $_POST['_nonce'] : 0;
        if (!wp_verify_nonce($nonce, "jbp_message_ajax"))
            return;
        $id = $_POST['id'];
        $context = $_POST['context'];
        $model = JobsExperts_AddOn_Message_Models_Message::instance()->get_one($id);
        if (is_object($model)) {
            //update status
            if ($context != 'sent' && $model->status == 'unread') {
                $model->status = 'read';
                $model->save();
                do_action('message_message_read', $model);
                echo $this->render_message($model);
            } else {
                echo $this->render_message($model, false);
            }

        } else {
            echo __("Something wrong happen, please refresh the page and try again", JBP_TEXT_DOMAIN);
        }
        exit;
    }

    function reply_message()
    {
        $nonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : 0;
        if (!wp_verify_nonce($nonce, "jbp_reply_message"))
            return;
        $parent_id = $_POST['id'];
        $parent = JobsExperts_AddOn_Message_Models_Message::instance()->get_one($parent_id);
        $content = $_POST['message'];
        $attachments = $_POST['attachments'];
        $model = new JobsExperts_AddOn_Message_Models_Message();
        $model->status = 'unread';
        $model->send_from = get_current_user_id();
        $model->send_to = $parent->send_from;
        $model->content = $content;
        $model->attachments = trim($attachments);
        $model->subject = __('Re:', JBP_TEXT_DOMAIN) . ' ' . $parent->subject;
        $model->reply_to = $parent->id;
        $model->save();
        exit;
    }

    function messages_user_setting()
    {
        $nonce = isset($_POST['_nonce']) ? $_POST['_nonce'] : 0;
        if (!wp_verify_nonce($nonce, "messages_user_setting"))
            return;
        $user_id = $_POST['user_id'];
        $enable_receipt = $_POST['receipt'];
        $prevent_receipt = $_POST['prevent'];
        $setting = get_user_meta($user_id, '_messages_setting', true);
        if (!$setting) {
            $setting = array();
        }
        $setting['enable_receipt'] = $enable_receipt;
        $setting['prevent_receipt'] = $prevent_receipt;
        update_user_meta($user_id, '_messages_setting', $setting);
    }

    function remove_message()
    {
        $nonce = isset($_POST['_nonce']) ? $_POST['_nonce'] : 0;
        if (!wp_verify_nonce($nonce, "jbp_remove_message"))
            return;
        $id = $_POST['id'];
        $post = get_post($id);
        if (is_object($post)) {
            clean_post_cache($post->ID);
        } else {
            echo __("Something wrong happen, please refresh the page and try again", JBP_TEXT_DOMAIN);
        }
        exit;
    }
}

global $jbp_message;
$jbp_message = new JobsExperts_AddOn_Message();
