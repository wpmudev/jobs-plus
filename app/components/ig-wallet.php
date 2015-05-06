<?php
/**
 * @author:Hoang Ngo
 */

if (!class_exists('IG_Wallet')) {
    class IG_Wallet
    {
        private static $_instance;

        public $plugin_url;
        public $plugin_path;
        public $domain;
        public $prefix;

        public $global;

        public $controller;

        private function __construct()
        {
            //variables init
            $this->plugin_url = plugin_dir_url(__FILE__) . 'ig-wallet/';
            $this->plugin_path = plugin_dir_path(__FILE__) . 'ig-wallet/';
            $this->domain = je()->domain;
            $this->prefix = 'iwl_';

            spl_autoload_register(array(&$this, 'loader'));

            add_action('wp_enqueue_scripts', array(&$this, 'scripts'));
            add_action('admin_enqueue_scripts', array(&$this, 'scripts'));
            add_action('admin_menu', array(&$this, 'admin_menu'));
            //add_filter('menu_order', array(&$this, 'menu_order'));
            //$this->custom_post_type();
            $this->controller = new Credit_Plan_Controller();
        }

        function admin_menu()
        {
            add_menu_page(__('Credit Plans', $this->domain), __('Credit Plans', $this->domain), 'manage_options', 'ig-credit-plans', array($this->controller, 'main'), 'dashicons-products', 35);
            add_submenu_page('ig-credit-plans', __("Rules", $this->domain), __("Rules", $this->domain), 'manage_options', 'ig-credit-rules', array($this->controller, 'rules'));
            add_submenu_page('ig-credit-plans', __("Settings", $this->domain), __("Settings", $this->domain), 'manage_options', 'ig-credits-setting', array($this->controller, 'settings'));
            //add_submenu_page('ig-credit-plans', __("Getting Start", $this->domain), __("Getting Start", $this->domain), 'manage_options', 'ig-credit-getting-start', array($this->controller, 'getting_start'));
        }

        function menu_order($menu_order)
        {
            global $submenu;

            if (isset($submenu['ig-credit-plans'])) {
                $nav = $submenu['ig-credit-plans'];
                //get the last
                $start = array_pop($nav);
                $nav = array_merge(array($start), $nav);
                $submenu['ig-credit-plans'] = $nav;
            }

            return $menu_order;
        }

        function custom_post_type()
        {
            $labels = array(
                'name' => __('Credit Plans', $this->domain),
                'singular_name' => __('Credit Plan', $this->domain),
                'menu_name' => __('Credit Plans', $this->domain),
                'parent_item_colon' => __('Parent Item:', $this->domain),
                'all_items' => __('All Items', $this->domain),
                'view_item' => __('View Item', $this->domain),
                'add_new_item' => __('Add New Item', $this->domain),
                'add_new' => __('Add New', $this->domain),
                'edit_item' => __('Edit Item', $this->domain),
                'update_item' => __('Update Item', $this->domain),
                'search_items' => __('Search Item', $this->domain),
                'not_found' => __('Not found', $this->domain),
                'not_found_in_trash' => __('Not found in Trash', $this->domain),
            );
            $args = array(
                'label' => __('ig_credit_plan', $this->domain),
                'labels' => $labels,
                'supports' => array(
                    'title', //'custom-fields'
                ),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_in_admin_bar' => true,
                //'menu_position' => 5,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'capability_type' => 'page',
                'menu_icon' => 'dashicons-products',
                'register_meta_box_cb' => array(new Credit_Plan_Controller(), 'metabox')
            );
            register_post_type('ig_credit_plan', $args);
        }

        function scripts()
        {

        }

        function loader($class)
        {
            $filename = str_replace('_', '-', strtolower($class)) . '.php';
            if (strstr($filename, '-controller.php')) {
                //looking in the controllers folder and sub folders to get this class
                $files = $this->listFolderFiles($this->plugin_path . 'app/controllers');
                foreach ($files as $file) {
                    if (strcmp($filename, pathinfo($file, PATHINFO_BASENAME)) === 0) {
                        include_once $file;
                        break;
                    }
                }
            } elseif (strstr($filename, '-model.php')) {
                $files = $this->listFolderFiles($this->plugin_path . 'app/models');

                foreach ($files as $file) {
                    if (strcmp($filename, pathinfo($file, PATHINFO_BASENAME)) === 0) {
                        include_once $file;
                        break;
                    }
                }
            } else {
                //include normal file inside app folder
                if (file_exists($this->plugin_path . 'app/' . $filename)) {
                    include_once $this->plugin_path . 'app/' . $filename;
                }
            }
        }

        function listFolderFiles($dir)
        {
            $ffs = scandir($dir);
            $i = 0;
            $list = array();
            foreach ($ffs as $ff) {
                if ($ff != '.' && $ff != '..') {
                    if (strlen($ff) >= 5) {
                        if (substr($ff, -4) == '.php') {
                            $list[] = $dir . '/' . $ff;
                        }
                    }
                    if (is_dir($dir . '/' . $ff))
                        $list = array_merge($list, $this->listFolderFiles($dir . '/' . $ff));
                }
            }
            return $list;
        }

        static function get_instance()
        {
            if (!is_object(self::$_instance)) {
                self::$_instance = new IG_Wallet();
            }
            return self::$_instance;
        }

        function get($key, $default = NULL)
        {
            $value = isset($_GET[$key]) ? $_GET[$key] : $default;
            return apply_filters('igu_wallet_get_' . $key, $value);
        }

        function post($key, $default = NULL)
        {
            $array_dereference = NULL;
            if (strpos($key, '[')) {
                $bracket_pos = strpos($key, '[');
                $array_dereference = substr($key, $bracket_pos);
                $key = substr($key, 0, $bracket_pos);
            }
            $value = isset($_POST[$key]) ? $_POST[$key] : $default;
            if ($array_dereference) {
                preg_match_all('#(?<=\[)[^\[\]]+(?=\])#', $array_dereference, $array_keys, PREG_SET_ORDER);
                $array_keys = array_map('current', $array_keys);
                foreach ($array_keys as $array_key) {
                    if (!is_array($value) || !isset($value[$array_key])) {
                        $value = $default;
                        break;
                    }
                    $value = $value[$array_key];
                }
            }
            return apply_filters('igu_wallet_get_' . $key, $value);
        }

        function settings()
        {
            return new Credit_Plan_Settings_Model();
        }
    }

    function ig_wallet()
    {
        return IG_Wallet::get_instance();
    }

    ig_wallet();
}