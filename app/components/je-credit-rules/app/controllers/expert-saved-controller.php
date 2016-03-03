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
        add_action('je_begin_expert_form', array(&$this, 'display_alert'));
        //add_action('post_updated', array(&$this, 'charge_in_pending_review_case'), 10, 3);
    }

    function charge_in_pending_review_case($post_ID, $post_after, $post_before)
    {
        //this case is when admin review & publish
        if ($post_after->post_type == 'jbp_pro' && $post_after->post_status == 'publish') {
            $is_paid = get_post_meta($post_ID, 'je_expert_paid', true);
            if ($is_paid == -1) {
                //this profile still not paid,check the balance
                $settings = new Expert_Saved_Model();
                if (!User_Credit_Model::check_balance($settings->credit_use, $post_after->post_author)) {
                    //switch to draft && sending email

                } else {
                    User_Credit_Model::update_balance(0 - $settings->credit_use, get_current_user_id(), '',
                        sprintf(__("You have used %s credit(s) for posting the profile %s", je()->domain), $settings->credit_use, $model->name));
                    update_post_meta($model->id, 'je_expert_paid', 1);
                }
            }
        }
    }

    function display_alert()
    {
        $settings = new Expert_Saved_Model();
        if ($settings->status == 0) {
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
                <?php echo sprintf(__('Your balance is not enough for creating a new profile (requires %s credit(s)), please click the following  <a href="%s">link</a> to top up your credit balance.', je()->domain), $settings->credit_use, get_permalink(ig_wallet()->settings()->plans_page)) ?>
            </div>
        <?php
        }
    }

    function check_user_can_post(JE_Expert_Model $model)
    {
        $settings = new Expert_Saved_Model();
        if ($settings->status == 0) {
            return;
        }

        if (!$model->status == 'je-draft') {
            $is_paid = get_post_meta($model->id, 'je_expert_paid', true);
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
            update_post_meta($model->id, 'je_expert_paid', -1);
            return;
        }

        //finally check the points
        if (!User_Credit_Model::check_balance($settings->credit_use, get_current_user_id())) {
            //store as draft
            $model->status = 'je-draft';
            $model->save();
            User_Credit_Model::go_to_plans_page();
        } else {
            //remove points
            User_Credit_Model::update_balance(0 - $settings->credit_use, get_current_user_id(), '',
                sprintf(__("You have used %s credit(s) for posting the profile %s", je()->domain), $settings->credit_use, $model->name), __('Spent Credits', je()->domain));
            update_post_meta($model->id, 'je_expert_paid', 1);
        }
    }

    function count_paid()
    {
        $models = JE_Expert_Model::model()->all_with_conditions(array(
            'meta_key' => 'je_expert_paid',
            'nopaging' => true
        ));
        return count($models);
    }

    function save_settings()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $model = new Expert_Saved_Model();
        $data = je()->post('Expert_Saved_Model');
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
        $model = new Expert_Saved_Model();
        $this->render('expert-saved/settings', array(
            'model' => $model
        ));
    }
}