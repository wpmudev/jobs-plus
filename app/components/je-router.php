<?php

/**
 * @author:Hoang Ngo
 */
class JE_Router
{
    public function __construct()
    {
        if (apply_filters('jbp_use_core_front_request', true)) {
            add_action('template_include', array(&$this, 'determine_page'));
            add_filter('the_content', array(&$this, 'je_single_content'));
            add_filter('the_title', array(&$this, 'je_single_title'));
            add_filter('get_edit_post_link', array(&$this, 'hide_edit_post_link'));
        }
    }

    function je_single_content($content)
    {
        if (in_the_loop() && is_main_query()) {
            if (is_singular('jbp_job') && !JE_Page_Factory::is_core_page(get_the_ID()) && !is_404()) {
                return do_shortcode('[jbp-job-single-page]');
            } elseif (is_singular('jbp_pro') && !JE_Page_Factory::is_core_page(get_the_ID()) && !is_404()) {
                return do_shortcode('[jbp-job-pro-page]');
            }
        }
        return $content;
    }

    function je_single_title($title)
    {
        if (in_the_loop()) {
            $shortcodes = apply_filters('je_buttons_on_single_page', '[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]');
            if (is_tax('jbp_category')) {
                $term = get_term_by('slug', get_query_var('jbp_category'), 'jbp_category');

                return __('Job Category: ', je()->domain) . ' ' . $term->name;
            } elseif (is_singular('jbp_job') && in_the_loop() && !JE_Page_Factory::is_core_page(get_the_ID())) {
                global $wp_query;
                if ($wp_query->is_main_query()) {
                    $title = do_shortcode('<p style="text-align: center">' . $shortcodes . '</p>') . esc_html($title);
                    remove_filter('the_title', array(&$this, 'je_single_title'));
                }
            } elseif (is_singular('jbp_pro') && in_the_loop() && !JE_Page_Factory::is_core_page(get_the_ID())) {
                global $wp_query;
                if ($wp_query->is_main_query()) {
                    $title = do_shortcode('<p style="text-align: center">' . $shortcodes . '</p>');
                    remove_filter('the_title', array(&$this, 'je_single_title'));
                }
            }
        }
        return $title;
    }

    function hide_edit_post_link($link)
    {
        global $post;
        if ($post->post_type == 'jbp_job' || $post->post_type == 'jbp_pro') {
            return null;
        }
        return $link;
    }

    function determine_page($template)
    {
        global $wp_query;
        //this is for jobs section
        if (get_query_var('post_type') == 'jbp_job' && !is_404()) {
            global $wp_query;
            $template = array('single-jbp_job.php', 'page.php', 'index.php');
            if (is_archive('jbp_job')) {
                $vpost = get_post(je()->pages->page(JE_Page_Factory::JOB_LISTING));
                $wp_query->posts = array($vpost);
                $wp_query->post_count = 1;
                $template = array_merge(array($vpost->post_name . '-page.php'), $template);
            }
            $template = locate_template($template);
        }
        //yah, experts time
        if (get_query_var('post_type') == 'jbp_pro') {
            $template = locate_template(array('page.php', 'index.php'));
            $template = array('single-jbp_pro.php', 'page.php', 'index.php');
            if (is_archive('jbp_pro')) {
                $vpost = get_post(je()->pages->page(JE_Page_Factory::JOB_LISTING));
                global $wp_query;
                $wp_query->posts = array(get_post(je()->pages->page(JE_Page_Factory::EXPERT_LISTING)));
                $wp_query->post_count = 1;
                $template = array_merge(array($vpost->post_name . '-page.php'), $template);
            }
            $template = locate_template($template);
        }

        if (is_tax(array('jbp_category', 'jbp_skills_tag'))) {
            global $wp_query;
            $template = array('page.php', 'index.php');
            if (is_archive('jbp_job')) {
                $vpost = get_post(je()->pages->page(JE_Page_Factory::JOB_LISTING));
                $wp_query->posts = array($vpost);
                $wp_query->post_count = 1;
                $template = array_merge(array($vpost->post_name . '-page.php'), $template);

            }
            $template = locate_template($template);
        }

        return $template;
    }
}