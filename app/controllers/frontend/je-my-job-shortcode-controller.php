<?php

/**
 * @author:Hoang Ngo
 */
class JE_My_Job_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_shortcode('jbp-my-job-page', array(&$this, 'main'));
    }

    function main()
    {
        if (!is_user_logged_in()) {
            return $this->render('login', array(), false);
        }
        $models = JE_Job_Model::model()->find_by_attributes(array(
            'owner' => get_current_user_id(),
            'status' => array('publish', 'draft', 'pending')
        ));
        return $this->render('my-job/main', array(
            'models' => $models
        ), false);
    }
}