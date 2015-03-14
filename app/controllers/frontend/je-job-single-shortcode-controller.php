<?php

/**
 * @author:Hoang Ngo
 */
class JE_Job_Single_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_shortcode('jbp-job-single-page', array(&$this, 'main'));
        add_action('wp_loaded', array(&$this, 'process'));
        if (je()->settings()->job_allow_discussion == 1) {
            add_post_type_support('jbp_job', 'comments');
            add_filter('comments_open', array(&$this, 'enable_comment_jobs'));
        }
    }

    function enable_comment_jobs($open)
    {
        global $post;
        if ($post->post_type == 'jbp_job') {
            $open = 'open';
        }
        return $open;
    }

    function process()
    {
        if (!isset($_POST['delete_job'])) {
            return;
        }

        if (!wp_verify_nonce(je()->post('_wpnonce'), 'delete_job_' . je()->post('job_id'))) {
            return;
        }

        $model = JE_Job_Model::model()->find(je()->post('job_id', -1));
        if (is_object($model) && $model->is_current_owner()) {
            $model->delete();
            $this->set_flash('job_deleted', __("Job delete successful!", je()->domain));
            $this->redirect(get_permalink(je()->pages->page(JE_Page_Factory::MY_JOB)));
        }
    }

    function main($atts)
    {
        je()->load_script('job');
        $a = shortcode_atts(array(
            'id' => get_the_ID()
        ), $atts);

        $model = JE_Job_Model::model()->find($a['id']);
        if (is_object($model)) {
            //add view count
            //$model->add_view_count();
            return $this->render('job-single/main', array(
                'model' => $model
            ), false);
        }
    }
}