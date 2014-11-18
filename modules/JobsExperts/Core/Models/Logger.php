<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Models_Logger extends JobsExperts_Framework_OptionModel
{
    public $data = array();

    public function __construct(){
        parent::__construct();
    }

    public function storage_name()
    {
        return 'jbp_log';
    }

    public static function log($text)
    {
        //init model
        $model = new JobsExperts_Core_Models_Logger();
        $model->load();
        //check for the date we want to store the log, default 3 months
        $date_limit = apply_filters('jbp_log_date_limit', 90);
        //todo limit the log

        $log = array(
            'date' => time(),
            'text' => $text
        );
        $model->data[] = $log;
        $model->save();
    }
}