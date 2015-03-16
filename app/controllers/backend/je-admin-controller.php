<?php

/**
 * @author:Hoang Ngo
 */
class JE_Admin_Controller extends IG_Request
{
    protected $flash_key = 'je_flash';
    protected $job_admin;
    protected $expert_admin;

    public function __construct()
    {
        add_filter('custom_menu_order', '__return_true');
        add_action('admin_menu', array(&$this, 'menu'));
        add_filter('menu_order', array(&$this, 'menu_order'));
        //load job admin controller
        $admin_job_class_name = apply_filters('je_admin_job_class_name', 'JE_Job_Admin_Controller');
        $this->job_admin = new $admin_job_class_name();
        $admin_expert_class_name = apply_filters('je_admin_expert_class_name', 'JE_Expert_Admin_Controller');
        $this->expert_admin = new $admin_expert_class_name();

        add_action('wp_ajax_je_plugin_action', array(&$this, 'plugins_action'));
        add_action('admin_init', array(&$this, 'redirect_pro_setting'));
        add_filter('admin_url', array(&$this, 'fix_add_new_url'), 10, 3);
    }

    function fix_add_new_url($url, $path, $blog_id)
    {
        if ($path == 'post-new.php?post_type=jbp_job') {
            $url = admin_url('edit.php?post_type=jbp_job&page=jobs-plus-job-form');
        } elseif ($path == 'post-new.php?post_type=jbp_pro') {
            $url = admin_url('edit.php?post_type=jbp_pro&page=jobs-plus-add-pro');
        }
        return $url;
    }

    function redirect_pro_setting()
    {
        if (je()->get('post_type', null) == 'jbp_pro' && je()->get('page', null) == 'jobs-plus-menu') {
            $this->redirect(admin_url("edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert"));
        }
    }

    function menu()
    {
        $job = get_post_type_object('jbp_job');
        add_submenu_page('edit.php?post_type=jbp_job',
            __('Getting Started', je()->domain),
            __('Getting Started', je()->domain),
            'manage_options',
            'jobs-plus-about',
            array($this, 'getting_start')
        );
        add_submenu_page('edit.php?post_type=jbp_job',
            __('New Job', je()->domain),
            __('New Job', je()->domain),
            'manage_options',
            'jobs-plus-job-form',
            array($this->job_admin, 'add_new_job')
        );
        add_submenu_page('edit.php?post_type=jbp_job',
            __('Settings', je()->domain),
            __('Settings', je()->domain),
            'manage_options',
            'jobs-plus-menu',
            array($this, 'backend_setting')
        );
        add_submenu_page('edit.php?post_type=jbp_pro',
            __('New Expert', je()->domain),
            __('New Expert', je()->domain),
            'manage_options',
            'jobs-plus-add-pro',
            array($this->expert_admin, 'add_new_expert')
        );
        add_submenu_page('edit.php?post_type=jbp_pro',
            __('Settings', je()->domain),
            __('Settings', je()->domain),
            'manage_options',
            'jobs-plus-menu',
            array($this, 'backend_setting')
        );

    }

    function menu_order($menu_order)
    {
        global $submenu;

        if (isset($submenu['edit.php?post_type=jbp_job'])) {
            $job = get_post_type_object('jbp_job');
            $nav = $submenu['edit.php?post_type=jbp_job'];
            //move the getting start to top
            $getting_start = array();
            foreach ($nav as $key => &$item) {
                if (is_array($item)) {
                    if ($item[0] == __('Getting Started', je()->domain)) {
                        $getting_start = array(
                            $key => $item
                        );
                        unset($nav[$key]);
                    } elseif ($item[0] == $job->labels->name) {
                        $item[0] = sprintf(__('Manage %s', je()->domain), $job->labels->name);
                    } elseif ($item[0] == __('Add New', je()->domain)) {
                        //unset($nav[$key]);
                        //finding the New Job and fix it to this posiston
                        foreach ($nav as $k => $v) {
                            if (is_array($v) && $v[0] == sprintf(__('New %s', je()->domain), $job->labels->singular_name)) {
                                $nav[$key] = $v;
                                unset($nav[$k]);
                                break;
                            }
                        }
                    }
                }
            }
            if (!empty($getting_start)) {
                $nav = array_merge($getting_start, $nav);
            }

            $submenu['edit.php?post_type=jbp_job'] = $nav;
        }

        if (isset($submenu['edit.php?post_type=jbp_pro'])) {
            $pro = get_post_type_object('jbp_pro');
            $nav = $submenu['edit.php?post_type=jbp_pro'];
            foreach ($nav as $key => &$item) {
                if (is_array($item)) {
                    if ($item[0] == $pro->labels->name) {
                        $item[0] = sprintf(__('Manage %s', je()->domain), $pro->labels->name);
                    } elseif ($item[0] == __('Add New', je()->domain)) {
                        unset($nav[$key]);
                    }
                }
            }
            $submenu['edit.php?post_type=jbp_pro'] = $nav;
        }

        return $menu_order;
    }

    function getting_start()
    {
        wp_enqueue_style('jbp_admin');
        $this->render('backend/getting_start', array(
            'job_labels' => get_post_type_object('jbp_job')->labels,
            'pro_labels' => get_post_type_object('jbp_pro')->labels
        ));
    }

    function plugins_action()
    {
        $setting = je()->settings();
        $addons = $setting->plugins;
        if (!is_array($addons))
            $addons = array();
        $id = je()->post('id');
        $meta = get_file_data($id, array(
            'Name' => 'Name',
            'Author' => 'Author',
            'Description' => 'Description',
            'AuthorURI' => 'Author URI',
            'Network' => 'Network'
        ), 'component');

        if (!in_array($id, $addons)) {
            //activate it
            $addons[] = $id;
            $setting->plugins = $addons;
            $setting->save();
            do_action('je_addon_activated', $id, $meta);
            wp_send_json(array(
                'noty' => __("The Add-on <strong>{$meta['Name']}</strong> has been activated.", je()->domain),
                'text' => __("Deactivate", je()->domain)
            ));
            exit;
        } else {
            unset($addons[array_search($id, $addons)]);
            $setting->plugins = $addons;
            $setting->save();
            do_action('je_addon_deactivated', $id, $meta);
            wp_send_json(array(
                'noty' => __("The Add-on <strong>{$meta['Name']}</strong> has been deactivated.", je()->domain),
                'text' => __("Activate", je()->domain)
            ));

            exit;
        }
    }


    function backend_setting()
    {
        wp_enqueue_style('jbp_admin');
        $this->render('backend/settings');
    }
}