<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Components_Skill_Model extends JobsExperts_Framework_Model
{
    public $id;
    public $parent_id;
    public $name;
    public $value;
    public $css;

    public $component;

    public function __construct()
    {
        global $jbp_component_skill;
        $this->component = $jbp_component_skill;
    }

    public function get_one($skill, $parent_id = null)
    {
        if (is_null($parent_id)) {
            $parent_id = $this->parent_id;
        }
        $skills = get_post_meta($parent_id, '_expert_skill');
        foreach ($skills as $record) {
            if ($record['name'] == $skill) {
                $model = new JobsExperts_Components_Skill_Model();
                $model->import($record);
                return $model;
            }
        }
        return null;
    }

    public function get_all($parent_id = null)
    {
        $data = get_post_meta($parent_id, '_expert_skill');
        $models = array();
        foreach ($data as $record) {
            $model = new JobsExperts_Components_Skill_Model();
            $model->import($record);
            $models[] = $model;
        }
        return $models;
    }

    public function delete()
    {
        delete_post_meta($this->parent_id, '_expert_skill', $this->export());
    }

    public function save()
    {
        //get post meta
        $old = $this->get_one($this->name, $this->parent_id);
        if (is_object($old)) {
            update_post_meta($this->parent_id, '_expert_skill', $this->export(), $old->export());
        } else {
            add_post_meta($this->parent_id, '_expert_skill', $this->export());
        }
    }

    public function after_save()
    {

    }

    public function rules()
    {
        $rules = array(
            array('required', 'name')
        );

        return $rules;

    }
}