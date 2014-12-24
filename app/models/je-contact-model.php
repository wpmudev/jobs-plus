<?php

/**
 * @author:Hoang Ngo
 */
class JE_Contact_Model extends IG_Model
{
    public $name;
    public $email;
    public $content;

    public function before_validate()
    {
        $rules = apply_filters('jbp_contact_validate_rules', array(
            'name' => 'required',
            'email' => 'required|valid_email',
            'content' => 'required'
        ));
        $this->rules = $rules;
    }
}