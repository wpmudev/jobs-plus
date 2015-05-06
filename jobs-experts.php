<?php
/**
 * Plugin Name: Jobs and Experts
 * Plugin URI: http://premium.wpmudev.org/jobs-plus/
 * Description: Match people with projects to industry professionals – it’s more than your average WordPress jobs board.
 * Version: 1.0.1.9
 * Author:WPMU DEV
 * Author URI: http://premium.wpmudev.org
 * Text Domain: jbp
 * Domain Path: languages
 * Network: false
 * WDP ID: 912971
 * License: GPLv2 or later
 */

//some shorthand function needed
function get_max_file_upload()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $upload_mb = min($max_upload, $max_post, $memory_limit);

    return $upload_mb;
}

function jbp_format_bytes($bytes, $precision = 2)
{

    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function jbp_filter_text($text)
{
    $allowed_tags = wp_kses_allowed_html('post');

    return wp_kses($text, $allowed_tags);
}

//add action to load language
add_action('plugins_loaded', 'jbp_load_languages');
function jbp_load_languages()
{
    load_plugin_textdomain(je()->domain, false, plugin_basename(je()->plugin_path . 'languages/'));
}

///
require_once(dirname(__FILE__) . '/framework/loader.php');
require_once(dirname(__FILE__) . '/Helper.php');
if (!class_exists('SmartDOMDocument')) {
    include_once(dirname(__FILE__) . '/vendors/SmartDOMDocument.class.php');
}

class Jobs_Experts
{
    public $plugin_url;
    public $plugin_path;
    public $domain;
    public $prefix;

    public $version = "1.0.1.8";
    public $db_version = "1.0";

    public $global = array();

    private static $_instance;

    private $dev = false;

    /**
     * @vars
     * Short hand for pages factory
     */
    public $pages;

    private function __construct()
    {
        //variables init
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->domain = 'jbp';
        $this->prefix = 'je_';
        //load the framework
        //autoload
        spl_autoload_register(array(&$this, 'autoload'));

        //enqueue scripts, use it here so both frontend and backend can use
        add_action('wp_enqueue_scripts', array(&$this, 'scripts'));
        add_action('admin_enqueue_scripts', array(&$this, 'scripts'));

        add_action('init', array(&$this, 'dispatch'));
        add_action('widgets_init', array(&$this, 'init_widget'));
        $this->upgrade();
        //
        $this->load_addons();
    }

    function upgrade()
    {
        $vs = get_option($this->prefix . 'db_version');
        if ($vs == false || $vs != $this->version) {
            global $wpdb;
            $sql = "UPDATE " . $wpdb->posts . " SET post_type='iup_media' WHERE post_type='jbp_media';";
            $wpdb->query($sql);
            update_option($this->prefix . 'db_version', $this->db_version);
        }
    }

    function load_script($scenario = '')
    {
        switch ($scenario) {
            case 'buttons':
                wp_enqueue_style('jobs-buttons-shortcode');
                break;
            case 'jobs':
                wp_enqueue_script('jobs-main');
                wp_enqueue_style('jobs-list-shortcode');
                break;
            case 'job':
                wp_enqueue_style('jobs-single-shortcode');
                break;
            case 'job-form':
                wp_enqueue_style('jobs-form-shortcode');
                wp_enqueue_script('jobs-select2');
                wp_enqueue_style('jobs-select2');
                wp_enqueue_script('jquery-ui-datepicker');
                break;
            case 'contact':
                wp_enqueue_style('jobs-contact');
                break;
            case 'experts':
                wp_enqueue_script('jobs-main');
                wp_enqueue_style('expert-list-shortcode');
                wp_enqueue_script('jobs-main');
                break;
            case 'expert':
                wp_enqueue_style('expert-single-shortcode');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jobs-main');
                break;
            case 'expert-form':
                wp_enqueue_style('expert-form-shortcode');
                wp_enqueue_script('jobs-main');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-frame-transport');
                wp_enqueue_style('jobs-validation');
                wp_enqueue_script('jobs-validation');
                wp_enqueue_script('jobs-validation-en');
                break;
            case 'landing':
                wp_enqueue_style('jobs-list-shortcode');
                wp_enqueue_style('expert-list-shortcode');
                wp_enqueue_style('jobs-landing-shortcode');
                wp_enqueue_script('jobs-main');
                break;
            case 'widget':
                wp_enqueue_style('job-plus-widgets');
                break;
        }
    }


    function scripts()
    {
        wp_enqueue_script('jquery');
        if (is_admin()) {
            wp_enqueue_style('jbp_admin', $this->plugin_url . 'assets/css/admin.css', array('ig-packed'), $this->version);
            wp_register_style('jbp_select2', $this->plugin_url . 'assets/select2/select2.css', array('ig-packed'), $this->version);
            wp_register_script('jbp_select2', $this->plugin_url . 'assets/select2/select2.min.js', array('jquery'), $this->version);
        } else {
            $min = $this->dev == true ? null : '.min';
            //style
            wp_register_style('jobs-main', $this->plugin_url . 'assets/main' . $min . '.css', array('ig-packed'), $this->version);
            wp_register_style('jobs-buttons-shortcode', $this->plugin_url . 'assets/buttons' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('jobs-single-shortcode', $this->plugin_url . 'assets/jobs-single' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('jobs-form-shortcode', $this->plugin_url . 'assets/jobs-form' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('expert-form-shortcode', $this->plugin_url . 'assets/expert-form' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('expert-single-shortcode', $this->plugin_url . 'assets/expert-single' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('jobs-list-shortcode', $this->plugin_url . 'assets/jobs-list' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('expert-list-shortcode', $this->plugin_url . 'assets/expert-list' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('jobs-contact', $this->plugin_url . 'assets/contact' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('jobs-landing-shortcode', $this->plugin_url . 'assets/landing' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_style('job-plus-widgets', $this->plugin_url . 'assets/widget' . $min . '.css', array('jobs-main'), $this->version);
            wp_register_script('webuipopover', $this->plugin_url . 'assets/popover/webuipopover.js', array('jquery'));
            wp_register_style('webuipopover', $this->plugin_url . 'assets/popover/webuipopover.css');

            //js
            wp_register_script('jobs-main', $this->plugin_url . 'assets/main.js', array('jquery', 'ig-packed'), $this->version);
            wp_register_script('jobs-validation', $this->plugin_url . 'assets/jquery-validation-engine/js/jquery.validationEngine.js', array('jquery'), $this->version, true);
            wp_register_script('jobs-validation-en', $this->plugin_url . 'assets/jquery-validation-engine/js/languages/jquery.validationEngine-en.js', array('jquery'), $this->version, true);
            wp_register_style('jobs-validation', $this->plugin_url . 'assets/jquery-validation-engine/css/validationEngine.jquery.css', array(), $this->version);
            wp_register_script('jobs-select2', $this->plugin_url . 'assets/select2/select2.min.js');
            wp_register_style('jobs-select2', $this->plugin_url . 'assets/select2/select2.css');

            wp_register_script('jobs-noty', $this->plugin_url . 'assets/vendors/noty/packaged/jquery.noty.packaged.min.js', array(), $this->version, true);
        }
    }


    function compress_assets($css = array(), $js = array(), $write_path)
    {
        if (defined('DOING_AJAX') && DOING_AJAX)
            return;

        $css_write_path = $write_path . '/' . implode('-', $css) . '.css';
        $css_cache = get_option($this->prefix . 'style_last_cache');
        if ($css_cache && file_exists($css_write_path) && strtotime('+1 hour', $css_cache) < time()) {
            //remove cache
            unlink($css_write_path);
        }
        $js_write_path = $write_path . '/' . implode('-', $js) . '.js';
        if (!file_exists($css_write_path)) {
            global $wp_styles;
            $css_paths = array();
            //loop twice, position is important
            foreach ($css as $c) {
                foreach ($wp_styles->registered as $style) {
                    if ($style->handle == $c) {
                        $css_paths[] = $style->src;
                    }
                }
            }
            //started
            $css_strings = '';
            foreach ($css_paths as $path) {
                //path is an url, we need to changeed it to local
                $path = str_replace($this->plugin_url, $this->plugin_path, $path);
                $css_strings = $css_strings . PHP_EOL . file_get_contents($path);
            }

            file_put_contents($css_write_path, trim($css_strings));
            update_option($this->prefix . 'style_last_cache', time());
        }
        $css_write_path = str_replace($this->plugin_path, $this->plugin_url, $css_write_path);
        wp_enqueue_style(implode('-', $css), $css_write_path);

        $js_cache = get_option($this->prefix . 'script_last_cache');
        if ($js_cache && file_exists($js_write_path) && strtotime('+1 hour', $js_cache) < time()) {
            //remove cache
            unlink($js_write_path);
        }
        if (!file_exists($js_write_path)) {
            global $wp_scripts;
            $js_paths = array();
            //js
            foreach ($js as $j) {
                foreach ($wp_scripts->registered as $script) {
                    if ($script->handle == $j) {
                        $js_paths[] = $script->src;
                    }
                }
            }
            $js_strings = '';
            foreach ($js_paths as $path) {
                //path is an url, we need to changeed it to local
                $path = str_replace($this->plugin_url, $this->plugin_path, $path);
                if (file_exists($path)) {
                    $js_strings = $js_strings . PHP_EOL . file_get_contents($path);
                }
            }

            file_put_contents($js_write_path, trim($js_strings));
            update_option($this->prefix . 'script_last_cache', time());
        }
        $js_write_path = str_replace($this->plugin_path, $this->plugin_url, $js_write_path);
        wp_enqueue_script(implode('-', $js), $js_write_path);
    }

    function can_compress()
    {
        $runtime_path = $this->plugin_path . 'framework/runtime';
        if (!is_dir($runtime_path)) {
            //try to create
            mkdir($runtime_path);
        }
        if (!is_dir($runtime_path))
            return false;
        $use_compress = false;
        if (!is_writeable($runtime_path)) {
            chmod($runtime_path, 775);
        }
        if (is_writeable($runtime_path)) {
            $use_compress = $runtime_path;;
        }
        return $use_compress;
    }

    function load_addons()
    {
        $addons = $this->settings()->plugins;
        if (!is_array($addons)) {
            $addons = array();
        }
        if (array_search($this->plugin_path . 'app/addons/je-message.php', $addons) !== false) {
            include $this->plugin_path . 'app/addons/je-message.php';
        }
    }

    function dispatch()
    {
        //load post type
        new JE_Custom_Content();
        add_action('wp_loaded', array(&$this, 'init_pages'));
        //uploader
        include_once($this->plugin_path . 'app/components/ig-uploader.php');
        ig_uploader()->init_uploader($this->can_upload(), $this->domain);
        //social-walll
        include_once($this->plugin_path . 'app/components/ig-social-wall.php');
        include_once($this->plugin_path . 'app/components/ig-skill.php');
        if (is_admin()) {
            $this->global['admin'] = new JE_Admin_Controller();
            new JE_Settings_Controller();
        } else {
            //load router
            $router = new JE_Router();
        }
        //load shortcode
        $buttons = new JE_Buttons_Shortcode_Controller();
        $job_archive = new JE_Job_Archive_Shortcode_Controller;
        $job_single = new JE_Job_Single_Shortcode_Controller();
        $job_form = new JE_Job_Form_Shortcode_Controller();
        $my_job = new JE_My_Job_Shortcode_Controller();

        $expert_archive = new JE_Expert_Archive_Shortcode_Controller();
        $expert_single = new JE_Expert_Single_Shortcode_Controller();
        $my_expert = new JE_My_Expert_Shortcode_Controller();
        $expert_form = new JE_Expert_Form_Shortcode_Controller();

        $contact = new JE_Contact_Shortcode_Controller();
        $landing = new JE_Landing_Shortcode_Controller();
        $shared = new JE_Shared_Controller();

        //load addon
        //load add on
        $addons = $this->settings()->plugins;
        if (!is_array($addons)) {
            $addons = array();
        }

        foreach ($addons as $addon) {
            if (file_exists($addon) && $addon != $this->plugin_path . 'app/addons/je-message.php') {
                include_once $addon;
            }
        }
    }

    function init_pages()
    {
        $this->pages = new JE_Page_Factory();
        $this->pages->init();
    }

    function init_widget()
    {
        //widget
        register_widget('JE_Job_Add_Widget_Controller');
        register_widget('JE_Job_Recent_Widget_Controller');
        register_widget('JE_Job_Search_Widget_Controller');
        register_widget('JE_Expert_Add_Widget_Controller');
    }

    function can_upload()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        if (current_user_can('upload_files'))
            return true;

        $allowed = $this->settings()->allow_attachment;
        if (!is_array($allowed)) {
            $allowed = array();
        }
        $allowed = array_filter($allowed);
        $user = new WP_User(get_current_user_id());
        foreach ($user->roles as $role) {
            if (in_array($role, $allowed)) {
                return true;
            }
        }
        return false;
    }

    function can_upload_avatar()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        if (current_user_can('upload_files'))
            return true;

        $allowed = $this->settings()->allow_avatar;
        if (!is_array($allowed)) {
            $allowed = array();
        }
        $allowed = array_filter($allowed);
        $user = new WP_User(get_current_user_id());
        foreach ($user->roles as $role) {
            if (in_array($role, $allowed)) {
                return true;
            }
        }
        return false;
    }

    function autoload($class)
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
        } elseif (file_exists($this->plugin_path . 'app/' . $filename)) {
            include_once $this->plugin_path . 'app/' . $filename;
        } elseif (file_exists($this->plugin_path . 'app/components/' . $filename)) {
            include_once $this->plugin_path . 'app/components/' . $filename;
        }
    }

    public static function get_instance()
    {
        if (!self::$_instance instanceof Jobs_Experts) {
            self::$_instance = new Jobs_Experts();
        }

        return self::$_instance;
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
                if (is_dir($dir . '/' . $ff)) {
                    $list = array_merge($list, $this->listFolderFiles($dir . '/' . $ff));
                }
            }
        }

        return $list;
    }

    function get_avatar_url($get_avatar)
    {
        if (preg_match("/src='(.*?)'/i", $get_avatar, $matches)) {
            preg_match("/src='(.*?)'/i", $get_avatar, $matches);

            return $matches[1];
        } else {
            preg_match("/src=\"(.*?)\"/i", $get_avatar, $matches);

            return $matches[1];
        }
    }

    function mb_word_wrap($string, $max_length = 100, $end_substitute = null, $html_linebreaks = false)
    {

        if ($html_linebreaks) {
            $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
        }
        $string = strip_tags($string); //gets rid of the HTML

        if (empty($string) || mb_strlen($string) <= $max_length) {
            if ($html_linebreaks) {
                $string = nl2br($string);
            }

            return $string;
        }

        if ($end_substitute) {
            $max_length -= mb_strlen($end_substitute, 'UTF-8');
        }

        $stack_count = 0;
        while ($max_length > 0) {
            $char = mb_substr($string, --$max_length, 1, 'UTF-8');
            if (preg_match('#[^\p{L}\p{N}]#iu', $char)) {
                $stack_count++;
            } //only alnum characters
            elseif ($stack_count > 0) {
                $max_length++;
                break;
            }
        }
        $string = mb_substr($string, 0, $max_length, 'UTF-8') . $end_substitute;
        if ($html_linebreaks) {
            $string = nl2br($string);
        }

        return $string;
    }

    function encrypt($text)
    {
        if (function_exists('mcrypt_encrypt')) {
            $key = SECURE_AUTH_KEY;
            $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $text, MCRYPT_MODE_CBC, md5(md5($key))));
            return $encrypted;
        } else {
            return $text;
        }
    }

    function decrypt($text)
    {
        if (function_exists('mcrypt_decrypt')) {
            $key = SECURE_AUTH_KEY;
            $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($text), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
            return $decrypted;
        } else {
            return $text;
        }
    }

    function trim_text($input, $length, $ellipses = true, $strip_html = true)
    {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    function get_available_addon()
    {
        //load all shortcode
        $coms = glob($this->plugin_path . 'app/addons/*.php');
        $data = array();
        foreach ($coms as $com) {
            if (file_exists($com)) {
                $meta = get_file_data($com, array(
                    'Name' => 'Name',
                    'Author' => 'Author',
                    'Description' => 'Description',
                    'AuthorURI' => 'Author URI',
                    'Network' => 'Network',
                    'Required' => 'Required'
                ), 'component');

                if (strlen(trim($meta['Name'])) > 0) {
                    $data[$com] = $meta;
                }
            }
        }

        return $data;
    }

    function settings()
    {
        return new JE_Settings_Model();
    }

    function get_logger($type = 'file', $location = '')
    {
        if (empty($location)) {
            $location = $this->domain;
        }
        $logger = new IG_Logger($type, $location);

        return $logger;
    }

    function get($key, $default = NULL)
    {
        $value = isset($_GET[$key]) ? $_GET[$key] : $default;
        return apply_filters('je_query_get_' . $key, $value);
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
        return apply_filters('je_query_post_' . $key, $value);
    }

    function login_form($args = array())
    {
        $defaults = array(
            'echo' => true,
            'redirect' => (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            // Default redirect is back to the current page
            'form_id' => 'loginform',
            'label_username' => __('Username'),
            'label_password' => __('Password'),
            'label_remember' => __('Remember Me'),
            'label_log_in' => __('Sign In'),
            'id_username' => 'user_login',
            'id_password' => 'user_pass',
            'id_remember' => 'rememberme',
            'id_submit' => 'wp-submit',
            'remember' => true,
            'value_username' => '',
            'value_remember' => false,
            // Set this to true to default the "Remember me" checkbox to checked
        );

        /**
         * Filter the default login form output arguments.
         *
         * @since 3.0.0
         *
         * @see wp_login_form()
         *
         * @param array $defaults An array of default login form arguments.
         */
        $args = wp_parse_args($args, apply_filters('login_form_defaults', $defaults));

        /**
         * Filter content to display at the top of the login form.
         *
         * The filter evaluates just following the opening form tag element.
         *
         * @since 3.0.0
         *
         * @param string $content Content to display. Default empty.
         * @param array $args Array of login form arguments.
         */
        $login_form_top = apply_filters('login_form_top', '', $args);

        /**
         * Filter content to display in the middle of the login form.
         *
         * The filter evaluates just following the location where the 'login-password'
         * field is displayed.
         *
         * @since 3.0.0
         *
         * @param string $content Content to display. Default empty.
         * @param array $args Array of login form arguments.
         */
        $login_form_middle = apply_filters('login_form_middle', '', $args);

        /**
         * Filter content to display at the bottom of the login form.
         *
         * The filter evaluates just preceding the closing form tag element.
         *
         * @since 3.0.0
         *
         * @param string $content Content to display. Default empty.
         * @param array $args Array of login form arguments.
         */
        $login_form_bottom = apply_filters('login_form_bottom', '', $args);

        $form = '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url(site_url('wp-login.php', 'login_post')) . '" method="post">
			' . $login_form_top . '
			 <div class="form-group">
				<label for="' . esc_attr($args['id_username']) . '">' . esc_html($args['label_username']) . '</label>
				<input type="text" name="log" id="' . esc_attr($args['id_username']) . '" class="form-control" value="' . esc_attr($args['value_username']) . '" size="20" />
			</div>
			<div class="form-group">
				<label for="' . esc_attr($args['id_password']) . '">' . esc_html($args['label_password']) . '</label>
				<input type="password" name="pwd" id="' . esc_attr($args['id_password']) . '" class="form-control" value="" size="20" />
			</div>
			' . $login_form_middle . '
			' . ($args['remember'] ? '<p class="login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr($args['id_remember']) . '" value="forever"' . ($args['value_remember'] ? ' checked="checked"' : '') . ' /> ' . esc_html($args['label_remember']) . '</label>
			<a class="pull-right" href="' . wp_lostpassword_url() . '">' . __("Forgot password?", je()->domain) . '</a></p>' : '') . '
			<p class="login-submit">
				<button type="submit" name="wp-submit" id="' . esc_attr($args['id_submit']) . '" class="btn btn-primary">' . esc_attr($args['label_log_in']) . '</button>
				<input type="hidden" name="redirect_to" value="' . esc_url($args['redirect']) . '" />
			</p>
			' . $login_form_bottom . '
		</form>';

        if ($args['echo']) {
            echo $form;
        } else {
            return $form;
        }
    }
}

function je()
{
    return Jobs_Experts::get_instance();
}

je();

/* -------------------- WPMU DEV Dashboard Notice -------------------- */
global $wpmudev_notices;
$wpmudev_notices[] = array('id' => '912971',
    'name' => 'Jobs +',
    'screens' => array(
        'plugins',
        'jbp_job_page_jobs-plus-menu',
        'edit-jbp_job',
        'edit-jbp_category',
        'edit-jbp_tag',
        'edit-jbp_skills_tag',
        'jbp_job',
        'edit-jbp_pro',
        'jbp_pro',
    ));

include_once(je()->plugin_path . 'ext/wpmudev-dash-notification.php');
register_deactivation_hook(__FILE__, 'je_remove_rewrite');
function je_remove_rewrite()
{
    delete_option('je_rewrite');
}