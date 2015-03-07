<?php

/**
 * Name: MarketPress integration
 * Description: Integrate MarketPress with Jobs&Experts, MarketPress required
 * Author: WPMU DEV
 * Required: marketpress/marketpress.php
 */
class JE_MarketPress
{
    public function __construct()
    {
        global $mp;
        if (is_object($mp) && $mp instanceof MarketPress) {
            include_once(je()->plugin_path . 'app/components/ig-wallet.php');
            include_once(je()->plugin_path . 'app/components/je-credit-rules.php');
        }

        //disabled all the product
        add_action('je_addon_activated', array(&$this, 'active'), 10, 2);
        add_action('je_addon_deactivated', array(&$this, 'clean_up'), 10, 2);
    }

    function active($id, $meta)
    {
        if ($id != __FILE__) {
            return;
        }

        $options = get_option('ig_credit_plan');
        foreach ($options as $plan) {
            $post = get_post($plan['product_id']);
            if (is_object($post)) {
                $post->post_status = 'publish';
                wp_update_post($post->to_array());
            }
        }
    }

    function clean_up($id, $meta)
    {
        if ($id != __FILE__) {
            return;
        }

        $options = get_option('ig_credit_plan');
        foreach ($options as $plan) {
            $post = get_post($plan['product_id']);
            if (is_object($post)) {
                $post->post_status = 'draft';
                wp_update_post($post->to_array());
            }
        }
    }
}

new JE_MarketPress();