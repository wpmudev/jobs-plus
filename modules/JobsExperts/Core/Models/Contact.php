<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Models_Contact extends JobsExperts_Framework_Model
{
    public $name;
    public $email;
    public $content;

    public function __construct(){
        parent::__construct();
    }

    public function rules()
    {
        return apply_filters('jbp_contact_validate_rules', array(
            array('required', 'name,email,content'),
            array('email', 'email')
        ));
    }
}