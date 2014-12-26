<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Helper
{
    //display currency symbol
    static function format_currency($currency = '', $amount = false)
    {
        $setting = je()->settings();
        $currencies = $setting->currency_list();
        if (!$currency)
            $currency = $setting->currency;
        if (!$currency) {
            $currency = 'USD';
        }

        // get the currency symbol
        $symbol = $currencies[$currency][1];
        // if many symbols are found, rebuild the full symbol
        $symbols = explode(', ', $symbol);
        if (is_array($symbols)) {
            $symbol = "";
            foreach ($symbols as $temp) {
                $symbol .= '&#x' . $temp . ';';
            }
        } else {
            $symbol = '&#x' . $symbol . ';';
        }

        //check decimal option
        if ($setting->curr_decimal === '0') {
            $decimal_place = 0;
            $zero = '0';
        } else {
            $decimal_place = 2;
            $zero = '0.00';
        }

        //format currency amount according to preference
        if ($amount) {
            if ($setting->curr_symbol_position == 1 || !$setting->curr_symbol_position)
                return $symbol . number_format_i18n($amount, $decimal_place);
            else if ($setting->curr_symbol_position == 2)
                return $symbol . ' ' . number_format_i18n($amount, $decimal_place);
            else if ($setting->curr_symbol_position == 3)
                return number_format_i18n($amount, $decimal_place) . $symbol;
            else if ($setting->curr_symbol_position == 4)
                return number_format_i18n($amount, $decimal_place) . ' ' . $symbol;

        } else if ($amount === false) {
            return $symbol;
        } else {
            if ($setting->curr_symbol_position == 1 || !$setting->curr_symbol_position)
                return $symbol . $zero;
            else if ($setting->curr_symbol_position == 2)
                return $symbol . ' ' . $zero;
            else if ($setting->curr_symbol_position == 3)
                return $zero . $symbol;
            else if ($setting->curr_symbol_position == 4)
                return $zero . ' ' . $symbol;
        }
    }

    static function get_currency_symbol($currency = null)
    {
        if ($currency == null) {
            $currency = je()->settings()->currency;
        }
        $list = je()->settings()->currency_list();

        if (isset($list[$currency][1])) {
            $symbol = $list[$currency][1];
            // if many symbols are found, rebuild the full symbol
            $symbols = explode(', ', $symbol);
            if (is_array($symbols)) {
                $symbol = "";
                foreach ($symbols as $temp) {
                    $symbol .= '&#x' . $temp . ';';
                }
            } else {
                $symbol = '&#x' . $symbol . ';';
            }
            return $symbol;
        }
        return null;
    }

    static function jbp_html_beautifier($html)
    {
        require_once je()->plugin_path . 'vendors/SmartDOMDocument.class.php';
        $x = new SmartDOMDocument();
        $x->loadHTML($html);
        $clean = $x->saveHTMLExact();
        return $clean;
    }

    static function is_user_pro($user_id)
    {
        if (current_user_can('manage_options')) {
            return true;
        }

        $model = JE_Expert_Model::model()->find_one_by_attributes(array(
            'user_id' => $user_id
        ));
        return apply_filters('je_pro_can_contact', is_object($model));
    }
}