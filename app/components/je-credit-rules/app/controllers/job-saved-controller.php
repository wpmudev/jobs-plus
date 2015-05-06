<?php

/**
 * @author:Hoang Ngo
 */
class Job_Saved_Controller extends IG_Request
{
    public function __construct()
    {
        add_action('je_credit_rules', array(&$this, 'settings'));
        add_action('wp_ajax_job_saved_setting', array(&$this, 'save_settings'));
        add_action('je_job_saving_process', array($this, 'check_user_can_post'));
        add_action('je_job_before_form', array(&$this, 'display_alert'));
    }

    function display_alert()
    {
        $settings = new Job_Saved_Model();

        if ($settings->status != 1) {
            return;
        }

        $user = new WP_User(get_current_user_id());
        //check does this user role can post free
        $roles = $settings->free_for;
        foreach ($user->roles as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }
        //check if this user already reach the free limit
        if ($settings->free_from > 0) {
            if ($this->count_paid() > $settings->free_from) {
                return true;
            }
        }

        if (!User_Credit_Model::check_balance($settings->credit_use, get_current_user_id())) {
            ?>
            <div class="alert alert-warning">
                <?php echo sprintf(__('Your balance\'s not enough for posting a new job - require %s credit(s), please visit <a href="%s">here</a> for purchasing', je()->domain), $settings->credit_use, get_permalink(ig_wallet()->settings()->plans_page)) ?>
            </div>
        <?php
        } else {
            ?>
            <div class="alert alert-info">
                <?php echo sprintf(__('It will cost %s credit(s) for posting a new job', je()->domain), $settings->credit_use); ?>
            </div>
        <?php
        }
    }

    function check_user_can_post(JE_Job_Model $model)
    {
        $settings = new Job_Saved_Model();

        if ($settings->status != 1) {
            return;
        }

        if ($model->status != 'je-draft') {
            //we need to check, if this model in pending
            $is_paid = get_post_meta($model->id, 'je_job_paid', true);
            if ($is_paid == 1) {
                return;
            }
        }

        $user = new WP_User(get_current_user_id());
        //check does this user role can post free
        $roles = $settings->free_for;
        foreach ($user->roles as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }
        //check if this user already reach the free limit
        if ($settings->free_from > 0) {
            if ($this->count_paid() > $settings->free_from) {
                return true;
            }
        }

        //will check if this is saving for draft or publish
        if (je()->post('status') == 'draft') {
            //we will need to add a flag to know this pending paid
            update_post_meta($model->id, 'je_job_paid', -1);
            return;
        }

        if (!User_Credit_Model::check_balance($settings->credit_use, get_current_user_id())) {
            //store as draft
            $model->status = 'je-draft';
            $model->save();
            User_Credit_Model::go_to_plans_page();
        } else {
            //remove points
            User_Credit_Model::update_balance(0 - $settings->credit_use, get_current_user_id(), '',
                sprintf(__("You have used %s credit(s) for posting the job %s", je()->domain), $settings->credit_use, $model->job_title), __("Spent Credits", je()->domain));
            update_post_meta($model->id, 'je_job_paid', 1);
        }
    }

    function count_paid()
    {
        $models = JE_Job_Model::model()->all_with_conditions(array(
            'meta_key' => 'je_job_paid',
            'nopaging' => true
        ));
        return count($models);
    }

    function save_settings()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $model = new Job_Saved_Model();
        $data = je()->post('Job_Saved_Model');
        if (!isset($data['free_for'])) {
            $data['free_for'] = array();
        }
        $model->import($data);

        if ($model->validate()) {
            $model->save();
            $this->set_flash('rule_saved', __("Settings has been saved successfully!", je()->domain));
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
        $model = new Job_Saved_Model();
        $this->render('job-saved/settings', array(
            'model' => $model
        ));
    }
}