<?php

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
        //build form
        add_action('je_before_cat_field', array(&$this, 'before_cat_field'), 10, 2);
        add_action('je_after_cat_field', array(&$this, 'after_cat_field'), 10, 2);
        add_action('je_before_job_title_field', array(&$this, 'before_job_title_field'), 10, 2);
        add_action('je_after_job_title_field', array(&$this, 'after_job_title_field'), 10, 2);
        add_action('je_before_description_field', array(&$this, 'before_description_field'), 10, 2);
        add_action('je_after_description_field', array(&$this, 'after_description_field'), 10, 2);
        add_action('je_before_skill_field', array(&$this, 'before_skill_field'), 10, 2);
        add_action('je_after_skill_field', array(&$this, 'after_skill_field'), 10, 2);
        add_action('je_before_price_field', array(&$this, 'before_price_field'), 10, 2);
        add_action('je_after_price_field', array(&$this, 'after_price_field'), 10, 2);
        add_action('je_before_complete_date_field', array(&$this, 'before_complete_date_field'), 10, 2);
        add_action('je_after_complete_date_field', array(&$this, 'after_complete_date_field'), 10, 2);
        add_action('je_before_email_field', array(&$this, 'before_email_field'), 10, 2);
        add_action('je_after_email_field', array(&$this, 'after_email_field'), 10, 2);
        add_action('je_before_open_for_field', array(&$this, 'before_open_for_field'), 10, 2);
        add_action('je_after_open_for_field', array(&$this, 'after_open_for_field'), 10, 2);
        //addition fields
        add_filter('je_job_additions_field', array(&$this, 'addition_jobs_field'));
        add_filter('je_job_relations', array(&$this, 'job_relations'));
    }

    function job_relations($relations)
    {
        $models = JE_Job_Field_Model::model()->all();
        foreach ($models as $model) {
            $relations[] = array(
                'type' => 'meta',
                'key' => $model->cp_id,
                'map' => $model->cp_id
            );
        }
        return $relations;
    }

    function addition_jobs_field($fields)
    {
        $models = JE_Job_Field_Model::model()->all();
        foreach ($models as $model) {
            $fields[] = $model->cp_id;
        }
        return $fields;
    }

    function before_cat_field(JE_Job_Model $model, IG_Active_Form $form)
    {
        $fields = JE_Job_Field_Model::model()->find_by_attributes(array(
            'position' => 'before-cat'
        ));
        foreach ($fields as $field) {
            ?>
            <div class="form-group <?php echo $model->has_error($field->cp_id) ? "has-error" : null ?>">
                <?php $form->label($field->cp_id, array("text" => __($field->title, je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
                <div class="col-lg-9">
                    <?php
                    switch ($field->type) {
                        case 'text':
                            $form->text($field->cp_id, array("attributes" => array("class" => "form-control")));
                            break;
                        case 'radio':
                            foreach ($field->options as $option) {
                                ?>
                                <div class="radio">
                                    <label>
                                        <?php
                                        $form->radio($field->cp_id, array(
                                            'value' => sanitize_title($option)
                                        ));
                                        ?>
                                        <?php echo $option ?>
                                    </label>
                                </div>

                            <?php
                            }
                            break;
                        case 'selectbox':
                            $form->select($field->cp_id, array(
                                'data' => $field->options,
                                'attributes' => array(
                                    'class' => 'form-control'
                                )
                            ));
                            break;
                        case 'multiselectbox':
                            $form->select($field->cp_id, array(
                                'data' => $field->options,
                                'attributes' => array(
                                    'class' => 'form-control',
                                    'multiple' => 'multiple'
                                )
                            ));
                            break;
                        case 'checkbox':
                            foreach ($field->options as $option) {
                                ?>
                                <div class="checkbox">
                                    <label>
                                        <?php
                                        $form->checkbox($field->cp_id, array(
                                            'value' => sanitize_title($option)
                                        ));
                                        ?>
                                        <?php echo $option ?>
                                    </label>
                                </div>

                            <?php
                            }
                            break;
                    }
                    ?>
                    <span class="help-block m-b-none error-job_title"><?php $form->error($field->cp_id) ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }
    }

    function after_cat_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_job_title_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_job_title_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_description_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_description_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_skill_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_skill_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_price_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_price_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_email_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_email_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_complete_date_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_complete_date_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function before_open_for_field(JE_Job_Model $model, IG_Active_Form $form)
    {

    }

    function after_open_for_field(JE_Job_Model $model, IG_Active_Form $form)
    {

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

    public function validation_rules()
    {
        return array(
            'required' => __("Required", je()->domain),
            'valid_email' => __("Valid Email", je()->domain),
            'max_len' => __("Max Length", je()->domain),
            'min_len' => __("Min Length", je()->domain),
            'exact_len' => __("Exactly Length", je()->domain)
        );
    }
}

new JE_Job_Custom_Fields();