<?php

/**
 * @author:Hoang Ngo
 */
class JE_Job_Form_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_shortcode('jbp-job-update-page', array(&$this, 'main'));
        if (is_user_logged_in()) {
            add_action('wp_loaded', array(&$this, 'process'));
        }
    }

    function process()
    {
        if (!wp_verify_nonce(je()->post('_wpnonce'), 'je_job_form')) {
            return;
        }

        $data = je()->post('JE_Job_Model');
        if (isset($data['id']) && !empty($data['id'])) {
            $model = JE_Job_Model::model()->find($data['id']);
        } else {
            $model = new JE_Job_Model();
        }
        $model->import($data);
        //
        if ($model->validate()) {
            do_action('je_job_saving_process', $model);
            $model->status = je()->post('status');
            $model->save();
            if ($model->status == 'publish') {
                $this->redirect(get_permalink($model->id));
            } else {
                $this->redirect(get_permalink(je()->pages->page(JE_Page_Factory::MY_JOB)));
            }
        } else {
            je()->global['job_model'] = $model;
        }
    }

    function main($atts)
    {
        if (is_user_logged_in()) {
            je()->load_script('job-form');
            $slug = je()->get('job', null);
            if (isset(je()->global['job_model']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
                $model = je()->global['job_model'];
            } else {
                $model = null;
                if (!is_null($slug)) {
                    if (filter_var($slug, FILTER_VALIDATE_INT)) {
                        $model = JE_Job_Model::model()->find($slug);
                    } else {
                        $model = JE_Job_Model::model()->find_by_slug($slug);
                    }
                } else {
                    //check does this man can post new
                    if (JE_Job_Model::model()->count() >= je()->settings()->job_max_records && !current_user_can('mange_options')) {
                        return $this->render('job-form/limit', array(), false);
                    } else {
                        $model = JE_Job_Model::model()->find_one_by_attributes(array(
                            'status' => 'je-draft',
                            'owner' => get_current_user_id()
                        ));

                        if (!is_object($model)) {
                            $model = new JE_Job_Model();
                            $model->status = 'je-draft';
                            $model->description = '';
                            $model->owner = get_current_user_id();
                            $model->save();
                        }
                    }
                }
            }

            if ($model->is_current_owner()) {
                return $this->render('job-form/main', array(
                    'model' => $model
                ), false);
            } else {
                //var_dump($model->is_current_owner());
            }
        } else {
            return $this->render('login', array(), false);
        }
    }
}