<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Components extends JobsExperts_Framework_Module
{
    const NAME = __CLASS__;

    /**
     * The shortcode
     * @var array
     */
    public $shortcodes = array();

    public function __construct()
    {
        $coms = glob(JBP_PLUGIN_DIR . '/modules/JobsExperts/Components/*.php');
        $data = array();
        foreach ($coms as $com) {
            if (file_exists($com)) {
                include $com;
            }
        }
        //load widget
        $this->_add_action( 'widgets_init', 'widget_init' );
    }

    function widget_init()
    {
        $widgets = glob( JobsExperts_Plugin::instance()->_module_path . 'Core/Widgets/*.php' );
        foreach ( $widgets as $widget ) {
            if ( file_exists( $widget ) ) {
                include $widget;
            }
        }
    }
}