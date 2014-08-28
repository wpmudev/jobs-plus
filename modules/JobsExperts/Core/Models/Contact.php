<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Models_Contact extends JobsExperts_Framework_Model
{
    public $name;
    public $email;
    public $content;

    public function rules()
    {
        return array(
            array('required', 'name,email,content'),
            array('email', 'email')
        );
    }
}