<?php

/**
 * Name: Custom fields for job
 * Description: Create random expert records, for testing purposes.
 * Author: WPMU DEV
 */
class JE_Job_Custom_Fields extends IG_Request
{
    protected $base_path;
    protected $base_url;

    public function __construct()
    {
        $this->base_path = plugin_dir_path(__FILE__) . 'je-job-custom-fields/app';
        $this->base_url = plugin_dir_url(__FILE__) . 'je-job-custom-fields/';
        require_once $this->base_path . '/models/je-job-field-model.php';
        add_action('jbp_setting_menu', array(&$this, 'menu'));
        add_action('je_settings_content_job_custom_fields', array(&$this, 'content'));
        add_action('admin_enqueue_scripts', array(&$this, 'script'));
        add_action('wp_ajax_job_assign_to_block', array(&$this, 'assign_to_block'));
    }

    function assign_to_block()
    {
        if (!wp_verify_nonce(je()->post('_wpnonce'), 'job_assign_to_block')) {
            return;
        }
        foreach (je()->post('data') as $key => $id) {
            $model = JE_Job_Field_Model::model()->find($id);
            if (is_object($model)) {
                $model->position = je()->post('id');
                $model->priority = $key;
                $model->save();
            }
        }
        die;
    }

    function script()
    {
        wp_enqueue_style('je-job-custom-field', plugin_dir_url(__FILE__) . 'je-job-custom-fields/assets/main.css');
    }

    function menu()
    {
        ?>
        <li <?php echo je()->get('tab') == 'job_custom_fields' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job_custom_fields') ?>">
                <i class="fa fa-puzzle-piece"></i> <?php _e('Customize Job Fields', je()->domain) ?>
            </a>
        </li>
    <?php
    }

    function content()
    {
        wp_enqueue_script('jquery-ui-sortable');
        $this->import_cp_fields();
        $this->render('composer');
    }

    function find_free_fields()
    {
        return JE_Job_Field_Model::model()->find_by_attributes(array(
            'position' => 'free'
        ));
    }

    function find_before_cat_fields()
    {
        return JE_Job_Field_Model::model()->find_by_attributes(array(
            'position' => 'before-cat'
        ));
    }

    function find_after_cat_fields()
    {
        return JE_Job_Field_Model::model()->find_by_attributes(array(
            'position' => 'after-cat'
        ));
    }

    private function import_cp_fields()
    {
        $fields = get_option('ct_custom_fields');
        foreach ($fields as $field) {
            if (!in_array('jbp_job', $field['object_type'])) {
                continue;
            }
            //check does field exist
            $flag = JE_Job_Field_Model::model()->find_one_by_attributes(array(
                'cp_id' => $field['field_id']
            ));
            if (!is_object($flag)) {
                $model = new JE_Job_Field_Model();
                $model->cp_id = $field['field_id'];
                $model->title = $field['field_title'];
                $model->description = $field['field_description'];
                if (isset($field['field_options'])) {
                    $model->options = $field['field_options'];
                }
                $model->type = $field['field_type'];
                $model->position = 'free';
                $model->save();
            } else {
                $model = $flag;
                $model->cp_id = $field['field_id'];
                $model->title = $field['field_title'];
                $model->description = $field['field_description'];
                if (isset($field['field_options'])) {
                    $model->options = $field['field_options'];
                }
                $model->type = $field['field_type'];
                $model->save();
            }
        }
    }
}

new JE_Job_Custom_Fields();