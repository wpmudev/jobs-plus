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
        } else {
            /**
             * this is the case if the add-on activated, but admin deactivated MP plugin
             * @since 1.0.1.9
             */
            $settings = je()->settings()->plugins;
            if (in_array(__FILE__, $settings)) {
                //add an admin notice
                add_action('admin_notices', array(&$this, 'admin_notices'));
            }
        }


        //disabled all the product
        add_action('je_addon_activated', array(&$this, 'active'), 10, 2);
        add_action('je_addon_deactivated', array(&$this, 'clean_up'), 10, 2);
    }

    /**
     * Show admin notice if the addon enabled without marketpress
     * @since 1.0.1.9
     */
    function admin_notices()
    {
        $class = "error";
        $message = __("The add-on <strong>MarketPress integration</strong> of <strong>Jobs&Experts</strong> required <strong>MarketPress</strong>", je()->domain);
        echo "<div class=\"$class\"> <p>$message</p></div>";
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