<?php

/**
 * @author:Hoang Ngo
 */
class JE_Shared_Controller extends IG_Request
{
    public function __construct()
    {
        add_action('query_vars', array(&$this, 'add_my_var'));
    }

    function add_my_var($public_query_vars)
    {
        if (!is_admin()) {
            $public_query_vars[] = 'je-paged';
        }
        return $public_query_vars;
    }
}