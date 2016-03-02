<?php

/**
 * @author:Hoang Ngo
 */
class JE_Expert_Admin_Controller extends IG_Request
{
    protected $flash_key = 'je_flash';

    public function __construct()
    {
        add_filter('get_edit_post_link', array(&$this, 'update_edit_link'), 10, 3);
        add_action('wp_loaded', array(&$this, 'process_request'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_filter('manage_jbp_pro_posts_columns', array(&$this, 'table_columns'));
        add_filter('manage_jbp_pro_posts_custom_column', array(&$this, 'table_columns_content'), 10, 2);
    }

    function table_columns($columns)
    {
        $columns['author'] = __("User", je()->domain);
        $columns['title'] = __("Name", je()->domain);
        unset($columns['comments']);
        $new_cols = array(
            'status' => __("Status", je()->domain)
        );
        $columns = array_merge(array_slice($columns, 0, 1), array_slice($columns, 1, 2), $new_cols, array_slice($columns, 1, count($columns) - 1));
        return $columns;
    }

    function table_columns_content($column_name, $post_id)
    {
        $model = JE_Expert_Model::model()->find($post_id);
        switch ($column_name) {
            case 'status':
                echo ucfirst($model->status);
                break;
        }
    }

    function admin_menu()
    {
        add_submenu_page(null,
            __('Edit Expert', je()->domain),
            __('Edit Expert', je()->domain),
            'manage_options',
            'jobs-plus-edit-pro',
            array(&$this, 'edit_expert')
        );
    }

    function process_request()
    {
        if (!wp_verify_nonce(je()->post('_wpnonce'), 'ig_expert_add')) {
            return;
        }
        $data = je()->post('JE_Expert_Model');
        if (isset($data['id']) && !empty($data['id'])) {
            $model = JE_Expert_Model::model()->find($data['id']);
        } else {
            $model = new JE_Expert_Model();
        }
        $model->import($data);
        $model->biography = je()->post('biography');
        $model->name = $model->first_name . ' ' . $model->last_name;
        if ($model->validate()) {
            $model->save();
            $this->set_flash('expert_saved', __("Expert Profile saved!"));
            $this->redirect(add_query_arg(array('id' => $model->id), admin_url('edit.php?post_type=jbp_pro&page=jobs-plus-edit-pro')));
        } else {
            je()->global['expert_model'] = $model;
        }
    }

    function edit_expert()
    {
        wp_enqueue_style('jbp_admin');
        $id = je()->get('id', 0);
        if (isset(je()->global['expert_model'])) {
            $model = je()->global['expert_model'];
        } else {
            $model = JE_Expert_Model::model()->find($id);
        }
        if (!is_object($model)) {
            echo __("Expert not found!", je()->domain);
        }
        $model->biography = stripslashes($model->biography);
        $model->short_description = stripslashes($model->short_description);
        $this->render('backend/experts/edit', array(
            'model' => $model
        ));
    }

    function add_new_expert()
    {
        wp_enqueue_style('jbp_admin');
        if (isset(je()->global['expert_model'])) {
            $model = je()->global['expert_model'];
        } else {
            $model = JE_Expert_Model::model()->find_one_by_attributes(array(
                'status' => 'je-draft',
                'user_id' => get_current_user_id()
            ));
            if (!is_object($model)) {
                $model = new JE_Expert_Model();
                $model->status = 'je-draft';
                $model->biography = '';
                $model->user_id = get_current_user_id();
                $model->save();
            }
        }

        $this->render('backend/experts/add', array(
            'model' => $model
        ));
    }

    function update_edit_link($url, $post_id, $context)
    {
        $post = get_post($post_id);

        if ($post->post_type == 'jbp_pro') {
            //check does page core
            if (!je()->pages->is_core_page($post->ID)) {
                $url = add_query_arg(array('id' => $post->ID), admin_url('edit.php?post_type=jbp_pro&page=jobs-plus-edit-pro'));
            }
        }
        return $url;
    }
}