<?php

/**
 * @author:Hoang Ngo
 */
class Sending_Credit_Model extends IG_Model
{
    public $amount;
    public $user_id;
    public $reason;

    protected $rules = array(
        'amount' => 'required|min_numeric,0',
        'user_id' => 'required',
        'reason' => 'required'
    );
}