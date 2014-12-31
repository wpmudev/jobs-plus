<?php

/**
 * @author:Hoang Ngo
 */
class JE_Settings_Controller extends IG_Request
{
    protected $flash_key = 'je_flash';

    public function __construct()
    {
        add_action('je_settings_content_general', array(&$this, 'general'));
        add_action('je_settings_content_job', array(&$this, 'job'));
        add_action('je_settings_content_expert', array(&$this, 'expert'));
        add_action('je_settings_content_uploader', array(&$this, 'uploader'));
        add_action('je_settings_content_shortcode',array(&$this,'shortcode'));
        add_action('wp_loaded', array(&$this, 'save_settings'));
    }

    function shortcode(){
        $this->render('backend/settings/shortcode');
    }

    function save_settings()
    {
        if (!wp_verify_nonce(je()->post('_je_setting_nonce'), 'je_settings')) {
            return '';
        }
        $model = new JE_Settings_Model();
        $model->import(je()->post('JE_Settings_Model'));
        $model->allow_attachment = array_filter($model->allow_attachment);
        $model->save();
        $this->set_flash('je_settings', __('Your settings have been successfully updated.', je()->domain));
        do_action('je_saved_setting');
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }

    function general()
    {
        $model = new JE_Settings_Model();
        $this->render('backend/settings/general', array(
            'model' => $model
        ));
    }

    function job()
    {
        $model = new JE_Settings_Model();
        $this->render('backend/settings/job', array(
            'model' => $model,
            'job_labels' => get_post_type_object('jbp_job')->labels,
            'pro_labels' => get_post_type_object('jbp_pro')->labels
        ));
    }

    function expert()
    {
        $model = new JE_Settings_Model();
        $this->render('backend/settings/expert', array(
            'model' => $model,
            'job_labels' => get_post_type_object('jbp_job')->labels,
            'pro_labels' => get_post_type_object('jbp_pro')->labels
        ));
    }

    function uploader()
    {
        $model = new JE_Settings_Model();
        $this->render('backend/settings/uploader', array(
            'model' => $model
        ));
    }
}