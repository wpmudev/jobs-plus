<?php

/**
 * @author:Hoang Ngo
 */
class JE_Landing_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_shortcode('jbp-landing-page', array(&$this, 'main'));
    }

    function main($atts)
    {
        je()->load_script('landing');
        $a = shortcode_atts(array(
            'job_show_count' => 3,
            'expert_show_count' => 6
        ), $atts);

        $jobs = JE_Job_Model::model()->all_with_condition(array(
            'posts_per_page' => $a['job_show_count'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        /////epxert
        $pros = JE_Expert_Model::model()->all_with_condition(array(
            'posts_per_page' => $a['expert_show_count'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        $colors = array(
            'jbp-yellow',
            'jbp-mint',
            'jbp-rose',
            'jbp-blue',
            'jbp-amber',
            'jbp-grey'
        );

        return $this->render('landing/main', array(
            'jobs' => $jobs,
            'pros' => $pros,
            'colors' => $colors
        ),false);
    }
}