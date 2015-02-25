<?php

/**
 * @author:Hoang Ngo
 */
class Expert_Saved_Controller extends IG_Request
{
    public function __construct()
    {
        add_action('je_credit_rules', array(&$this, 'settings'));
        add_action('wp_ajax_expert_saved_setting', array(&$this, 'save_settings'));
        add_action('je_expert_saving_process', array($this, 'check_user_can_post'));
    }

    function check_user_can_post(JE_Expert_Model $model)
    {
        if (!$model->status == 'je-draft') {
            return;
        }

        $settings = new Expert_Saved_Model();

        if (!User_Credit_Model::check_balance($settings->credit_use, get_current_user_id())) {
            User_Credit_Model::go_to_plans_page();
        } else {
            //remove points
            User_Credit_Model::update_balance(0 - $settings->credit_use, get_current_user_id());
        }
    }

    function save_settings()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $model = new Expert_Saved_Model();
        $model->import(je()->post('Expert_Saved_Model'));

        if ($model->validate()) {
            $model->save();
            wp_send_json(array(
                'status' => 'success'
            ));
        } else {
            wp_send_json(array(
                'status' => 'fail',
                'errors' => $model->get_errors()
            ));
        }
        die;
    }

    function settings()
    {
        $model = new Expert_Saved_Model();
        $this->render('expert-saved/settings', array(
            'model' => $model
        ));
    }
}