<?php

/**
 * @author:Hoang Ngo
 */
class JE_Job_Admin_Controller extends IG_Request
{
    protected $flash_key = 'je_flash';

    public function __construct()
    {
        if (current_user_can('manage_options')) {
            add_filter('get_edit_post_link', array(&$this, 'update_edit_link'), 10, 3);
            add_action('wp_loaded', array(&$this, 'process_request'));
            add_action('admin_menu', array(&$this, 'admin_menu'));
            add_filter('manage_jbp_job_posts_columns', array(&$this, 'table_columns'));
            add_filter('manage_jbp_job_posts_custom_column', array(&$this, 'table_columns_content'), 10, 2);
        }
    }

    function admin_menu()
    {
        add_submenu_page(null,
            __('Edit Job', je()->domain),
            __('Edit Job', je()->domain),
            'manage_options',
            'jobs-plus-edit-job',
            array(&$this, 'edit_job')
        );
    }

    function table_columns_content($column_name, $post_id)
    {
        $model = JE_Job_Model::model()->find($post_id);
        switch ($column_name) {
            case 'price':
                echo $model->render_prices();
                break;
            case 'expire_date':
                echo $model->get_due_day();
                break;
            case 'status':
                echo ucfirst($model->status);
                break;
        }
    }

    function table_columns($columns)
    {
        $columns['author'] = __("Job Owner", je()->domain);
        unset($columns['comments']);
        $new_cols = array(
            'price' => __("Price", je()->domain),
            'expire_date' => __("Open For", je()->domain),
            'status' => __("Status", je()->domain)
        );

        $columns = array_merge(array_slice($columns, 0, 1), array_slice($columns, 1, 1), $new_cols, array_slice($columns, 1, count($columns) - 1));
        return $columns;
    }

    function process_request()
    {
        if (!wp_verify_nonce(je()->post('_wpnonce'), 'ig_job_add')) {
            return;
        }
        $data = je()->post('JE_Job_Model');
        if (isset($data['id']) && !empty($data['id'])) {
            $model = JE_Job_Model::model()->find($data['id']);
        } else {
            $model = new JE_Job_Model();
        }
        $model->import($data);
        $model->description = (je()->post('description'));
        if ($model->validate()) {
            $model->save();
            $this->set_flash('job_saved', __("Job saved!"));
            $this->redirect(add_query_arg(array('id' => $model->id), admin_url('edit.php?post_type=jbp_job&page=jobs-plus-edit-job')));
            exit;
        } else {
            je()->global['job_model'] = $model;
        }
    }

    function edit_job()
    {
        wp_enqueue_style('jbp_admin');
        wp_enqueue_script('jbp_select2');
        wp_enqueue_style('jbp_select2');
        wp_enqueue_script('jquery-ui-datepicker');
        $id = je()->get('id', 0);
        if (isset(je()->global['job_model'])) {
            $model = je()->global['job_model'];
        } else {
            $model = JE_Job_Model::model()->find($id);
        }
        if (!is_object($model)) {
            echo __("Job not found!", je()->domain);
        }
        $this->render('backend/jobs/edit', array(
            'model' => $model
        ));
    }

    function add_new_job()
    {
        wp_enqueue_style('jbp_admin');
        wp_enqueue_script('jbp_select2');
        wp_enqueue_style('jbp_select2');
        wp_enqueue_script('jquery-ui-datepicker');
        if (isset(je()->global['job_model'])) {
            $model = je()->global['job_model'];
        } else {
            $model = new JE_Job_Model();
        }

        $this->render('backend/jobs/add', array(
            'model' => $model
        ));
    }

    function update_edit_link($url, $post_id, $context)
    {
        $post = get_post($post_id);

        if ($post->post_type == 'jbp_job') {
            //check does page core
            if (!je()->pages->is_core_page($post->ID)) {
                $url = add_query_arg(array('id' => $post->ID), admin_url('edit.php?post_type=jbp_job&page=jobs-plus-edit-job'));
            }
        }
        return $url;
    }
}