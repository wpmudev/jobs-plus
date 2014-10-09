<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_AddOn extends JobsExperts_Framework_Module
{
    const NAME = __CLASS__;

    /**
     * The shortcode
     * @var array
     */
    public $shortcodes = array();

    public function __construct()
    {
        //todo load saved components
        $plugin = JobsExperts_Plugin::instance();
        $components = $plugin->settings()->plugins;
        $components = explode(',', $components);
        foreach ($components as $com) {
            if (file_exists($com)) {
                include $com;
            }
        }
    }

    public static function get_available_components()
    {
        //load all shortcode
        $coms = glob(JBP_PLUGIN_DIR . '/modules/JobsExperts/AddOn/*.php');
        $data = array();
        foreach ($coms as $com) {
            if (file_exists($com)) {
                $meta = get_file_data($com, array(
                    'Name' => 'Name',
                    'Author' => 'Author',
                    'Description' => 'Description',
                    'AuthorURI' => 'Author URI',
                    'Network' => 'Network'
                ), 'component');

                if (strlen(trim($meta['Name'])) > 0) {
                    $data[$com] = $meta;
                }
            }
        }
        return $data;
    }

    function active_tab($id)
    {
        if (isset($_GET['tab'])) {
            if ($id == $_GET['tab']) {
                return 'class="active"';
            }
        }

        return null;
    }

    function is_current_tab($id)
    {
        $tab = isset($_GET['tab']) ? $_GET['tab'] : null;
        if ($tab == $id) {
            return true;
        }

        return false;
    }
}