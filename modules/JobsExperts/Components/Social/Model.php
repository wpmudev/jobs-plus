<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Components_Social_Model extends JobsExperts_Framework_Model
{
    public $id;
    public $parent_id;
    public $name;
    public $value;
    public $type;
    public $status;

    public $component;

    public function __construct()
    {
        global $jbp_component_social;
        $this->component = $jbp_component_social;
    }

    public function get_one($social, $parent_id = null)
    {
        if (is_null($parent_id)) {
            $parent_id = $this->parent_id;
        }
        $socials = get_post_meta($parent_id, '_expert_social');
        foreach ($socials as $record) {
            if ($record['name'] == $social) {
                $model = new JobsExperts_Components_Social_Model();
                $model->import($record);
                return $model;
            }
        }
        return null;
    }

    public function get_all($parent_id = null)
    {
        $data = get_post_meta($parent_id, '_expert_social');
        $models = array();
        foreach ($data as $record) {
            $model = new JobsExperts_Components_Social_Model();
            $model->import($record);
            $models[] = $model;
        }
        return $models;
    }

    public function delete()
    {
        delete_post_meta($this->parent_id, '_expert_social', $this->export());
    }

    public function save()
    {
        add_post_meta($this->parent_id, '_expert_social', $this->export());
    }

    public function after_save()
    {

    }

    public function rules()
    {
        $rules = array(
            array('required', 'name,value')
        );
        if ($this->type == 'url') {
            $rules[] = array(
                'url', 'value'
            );
        } elseif ($this->type == 'email') {
            $rules[] = array(
                'email', 'value'
            );
        }

        return $rules;

    }
}