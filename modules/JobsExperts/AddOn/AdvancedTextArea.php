<?php

/**
 * Name: Advanced textarea
 * Description: Use WYSIWYG for jobs/experts detail field, the value will be filtered by kses for security
 * Author: WPMUDEV
 */
class JobsExpert_Compnents_AdvancedTextArea extends JobsExperts_AddOn
{
    public function __construct()
    {
        $this->_add_action('wp_enqueue_scripts', 'scripts');
        $this->_add_action('wp_footer', 'footer_script');
        $this->_add_filter('jbp_job_list_content', 'job_content', 10, 3);
        $this->_add_filter('jbp_expert_list_content', 'job_content', 10, 3);
        /*
        $this->_add_filter('jbp_expert_form_element', 'expert_text', 10, 3);
        $this->_add_filter('jbp_job_form_element', 'job_text', 10, 4);
        $this->_add_action('wp_footer', 'footer_script');
        $this->_add_filter('jbp_job_list_content', 'ensure_job_html', 10, 3);
    }*/
    }

    function job_content($shroten, $content, $length)
    {
        /*if (class_exists('tidy')) {
            $tidy = new tidy();
            $clean = $tidy->repairString($content);
        } else {
            $dom = new DOMDocument();
            $dom->loadHTML($content);
            $clean = $dom->saveHTML();
        }*/
        $clean = strip_tags($content);
        $content = $this->truncate(wpautop($clean), $length);
        return $content;
    }

    function footer_script()
    {
        //only include when add jobs/expert
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        if (!is_home() && in_array(get_the_ID(), array(
            $page_module->page($page_module::EXPERT_ADD),
            $page_module->page($page_module::EXPERT_EDIT),
            $page_module->page($page_module::JOB_ADD),
            $page_module->page($page_module::JOB_EDIT)
        ))
        ) {
            wp_enqueue_style('jobs-advanced-textarea');

            wp_enqueue_script('jobs-advanced-textarea');
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    if ($('#job_description').size() > 0) {
                        $('#job_description').wysiwyg({
                            css: '<?php echo $plugin->_module_url.'assets/main.css' ?>'
                        });
                    }

                    setInterval(function () {
                        if ($('#biography').size() > 0) {
                            if ($('#biography-wysiwyg-iframe').size() == 0) {
                                $('#biography').wysiwyg({
                                    css: '<?php echo $plugin->_module_url.'assets/main.css' ?>'
                                });
                            }
                        }
                    }, 1000);

                })
            </script>
        <?php
        }
    }

    function scripts()
    {
        $plugin = JobsExperts_Plugin::instance();
        wp_register_style('jobs-advanced-textarea', $plugin->_module_url . 'AddOn/AdvancedTextArea/jquery.wysiwyg.css');
        wp_register_script('jobs-advanced-textarea', $plugin->_module_url . 'AddOn/AdvancedTextArea/jquery.wysiwyg.js');

        //wp_register_style('jobs-advanced-textarea', $plugin->_module_url . 'AddOn/AdvancedTextArea/bootstrap3-wysihtml5.min.css');
        //wp_register_script('jobs-advanced-textarea', $plugin->_module_url . 'AddOn/AdvancedTextArea/bootstrap3-wysihtml5.all.min.js');
    }

    function job_text($html, $element_type, $model, $key)
    {
        if ($element_type == 'textarea') {
            $toolbar = $this->get_toolbar();
            return $toolbar . $html;
        }
        return $html;
    }

    function get_toolbar()
    {
        ob_start();
        ?>
        <div id="wysihtml5-toolbar" style="display: none;margin-bottom: 5px">
            <div class="btn-group">
                <a class="btn btn-xs btn-default" data-wysihtml5-command="bold" title="CTRL+B">
                    <i class="dashicons dashicons-editor-bold"></i> </a>
                <a class="btn btn-xs btn-default" data-wysihtml5-command="italic" title="CTRL+I">
                    <i class="dashicons dashicons-editor-italic"></i>
                </a>
            </div>
            &nbsp;
            <div class="btn-group">
                <a class="btn btn-xs btn-default" data-wysihtml5-command="justifyLeft" title="CTRL+I">
                    <i class="dashicons dashicons-editor-alignleft"></i></a>
                <a class="btn btn-xs btn-default" data-wysihtml5-command="justifyCenter" title="CTRL+I">
                    <i class="dashicons dashicons-editor-aligncenter"></i></a>
                <a class="btn btn-xs btn-default" data-wysihtml5-command="justifyRight" title="CTRL+I">
                    <i class="dashicons dashicons-editor-alignright"></i></a>
            </div>
            &nbsp;
            <div class="btn-group">
                <a class="btn btn-xs btn-default" data-wysihtml5-command="insertUnorderedList"
                   title="Insert an unordered list">
                    <i class="dashicons dashicons-editor-ul"></i>
                </a>
                <a class="btn btn-xs btn-default" data-wysihtml5-command="insertOrderedList"
                   title="Insert an ordered list">
                    <i class="dashicons dashicons-editor-ol"></i>
                </a>
            </div>
            &nbsp;
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <?php _e('Colors', JBP_TEXT_DOMAIN) ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li data-wysihtml5-command="foreColor"
                        data-wysihtml5-command-value="black">
                        <a href="#"><?php _e('Black', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray">
                        <a href="#"><?php _e('Gray', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon">
                        <a href="#">
                            <?php _e('Maroon', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red">
                        <a href="#"><?php _e('Red', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple">
                        <a href="#"><?php _e('Purple', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green">
                        <a href="#"><?php _e('Green', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive">
                        <a href="#"><?php _e('Olive', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy">
                        <a href="#"><?php _e('Navy', JBP_TEXT_DOMAIN) ?></a></li>
                    <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue">
                        <a href="#"><?php _e('Blue', JBP_TEXT_DOMAIN) ?></a></li>
                </ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    function expert_text($html, $val, $model)
    {
        $type = $val['type'];
        $key = $val['id'];
        if ($type == 'textArea') {
            //append the toolbar
            $class = isset($val['class']) ? $val['class'] : "";
            $content = $this->get_toolbar();
            return $content . JobsExperts_Framework_Form::textArea($val['id'], esc_attr(wpautop($model->$key)), array(
                'rows' => 10, 'cols' => 80,
                'class' => $class,
                'data-element' => $this->_buildFormElementName($model, $key),
                'id' => $val['id']
            ));
        } else {
            return $html;
        }
    }

    /**
     * @param $model
     * @param $attribute
     *
     * @return string
     */
    private
    function _buildFormElementName($model, $attribute)
    {
        $model_class_name = get_class($model);
        $frm_element_name = $model_class_name . "[$attribute]";

        return $frm_element_name;
    }

    function truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
    {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }

}

new JobsExpert_Compnents_AdvancedTextArea();