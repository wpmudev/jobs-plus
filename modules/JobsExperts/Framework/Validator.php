<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_Validator
{

    public static function validate($rules, $data)
    {
        $errors = array();

        $built_in = array(
            'required', 'numeric', 'email', 'compare', 'length', 'url'
        );
        foreach ($rules as $rule) {
            $rule_name = $rule[0];
            $fields = explode(',', $rule[1]);
            if (in_array($rule_name, $built_in)) {
                foreach ($fields as $field) {
                    $field_value = isset($data[$field]) ? $data[$field] : null;
                    switch ($rule_name) {
                        case 'required':
                            if (empty($field_value)) {
                                if (!isset($errors[$field])) {
                                    $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> is required!';
                                }
                            }
                            break;
                        case 'numeric':
                            if (!isset($errors[$field])) {
                                if (!filter_var($field_value, FILTER_VALIDATE_FLOAT)) {
                                    $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> must be a number!';
                                }
                            }
                            break;
                        case 'email':
                            if (!isset($errors[$field])) {
                                if (!filter_var($field_value, FILTER_VALIDATE_EMAIL)) {
                                    $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> not a valid email!';
                                }
                            }
                            break;
                        case'url':
                            if (!empty($field_value) && !isset($errors[$field])) {
                                if (!filter_var($field_value, FILTER_VALIDATE_URL)) {
                                    $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> not a valid url!';
                                }
                            }
                            break;
                        case 'compare':
                            if (!isset($errors[$field])) {
                                if (isset($rule['to'])) {
                                    $compare = $rule['to'];
                                    if ($field_value != $compare) {
                                        $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> not match!';
                                    }
                                } elseif (isset($rule['not_in'])) {
                                    $not_in = $rule['not_in'];
                                    if (!is_array($not_in)) {
                                        $not_in = array();
                                    }
                                    if (in_array($field_value, $not_in)) {
                                        $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> should not be ' . implode(',', $not_in) . '!';
                                    }
                                }
                            }
                            break;
                        case 'length':
                            if (!isset($errors[$field])) {
                                if (isset($rule['min'])) {
                                    $min = $rule['min'];
                                    if (strlen($field_value) < $min) {
                                        $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> minimum length is ' . $min . '!';
                                    }
                                } elseif (isset($rule['max'])) {
                                    $max = $rule['max'];
                                    if (strlen($field_value) > $max) {
                                        $errors[$field] = 'Field <strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong> maximum length is ' . $max . '!';
                                    }
                                }
                            }
                            break;
                    }
                }
            }
        }

        return $errors;
    }
}