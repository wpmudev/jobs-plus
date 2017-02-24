<?php

/**
 * Name: Advanced textarea
 * Description: Use WYSIWYG for Jobs/Experts detail field
 * Author: WPMU DEV
 */
class JE_WYSIWYG
{
    public function __construct()
    {
        if (is_user_logged_in()) {
            add_action('wp_enqueue_scripts', array(&$this, 'scripts'));
            add_action('admin_enqueue_scripts', array(&$this, 'scripts'));
            add_action('wp_footer', array(&$this, 'footer_scripts'));
        }
    }

    function footer_scripts()
    {
        wp_enqueue_script('je_sceditor');
        wp_enqueue_style('je_sceditor');
        wp_enqueue_script('je_sceditor_xhtml');
        if (!class_exists('Mobile_Detect')) {
            include_once dirname(__FILE__) . '/je-wysiwyg/Mobile_Detect.php';
        }
        $detect = new Mobile_Detect();
        //because the viewport of mobile, so we minimize the toolbars on mobile
        if ($detect->isMobile()) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    function load_editor() {
                        if ($('.je_wysiwyg').size() > 0) {
                            $('.je_wysiwyg').sceditor({
                                plugins: "bbcode",
                                autoUpdate: true,
                                autoExpand: true,
                                width: '98%',
                                height: '80%',
                                resizeMinWidth: '50%',
                                resizeMaxWidth: '100%',
                                resizeMaxHeight: '100%',
                                resizeMinHeight: '50%',
                                emoticonsEnabled: false,
                                toolbar: "bold,italic,underline,strike|left,center,right,justify|source",
                                style: '<?php echo je()->plugin_url . 'app/addons/je-wysiwyg/sceditor/minified/jquery.sceditor.default.min.css'?>'
                            });
                        }
                    }

                    load_editor();
                    $('body').on('abc', function () {
                        load_editor();
                    });
                })
            </script>
        <?php
        } else {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    function load_editor() {
                        if ($('.je_wysiwyg').size() > 0) {
                            var editors = $('.je_wysiwyg').sceditor({
                                plugins: "xhtml",
                                autoUpdate: true,
                                width: '98%',
                                resizeMinWidth: '-1',
                                resizeMaxWidth: '100%',
                                resizeMaxHeight: '100%',
                                resizeMinHeight: '-1',
                                readOnly: false,
                                emoticonsEnabled: false,
                                toolbar: "bold,italic,underline,strike|left,center,right,justify|font,size,color,removeformat|cut,copy,paste,pastetext|bulletlist,orderedlist,indent,outdent|link,unlink|date,time|source",
                                style: '<?php echo je()->plugin_url . 'app/addons/je-wysiwyg/sceditor/minified/jquery.sceditor.default.min.css'?>'
                            });
                        }
                    }

                    load_editor();

                    $('body').on('abc', function () {
                        load_editor();
                    });
                })
            </script>
        <?php
        }
    }

    function scripts()
    {
        wp_register_script('je_sceditor', je()->plugin_url . 'app/addons/je-wysiwyg/sceditor/minified/jquery.sceditor.min.js', array('jquery'));
        wp_register_script('je_sceditor_xhtml', je()->plugin_url . 'app/addons/je-wysiwyg/sceditor/minified/plugins/bbcode.js', array('jquery', 'mm_sceditor'));
        //cause the adminbar needed from anywhere,so we bind it
        wp_register_style('je_sceditor', je()->plugin_url . 'app/addons/je-wysiwyg/sceditor/minified/themes/default.min.css');

    }
}

new JE_WYSIWYG();