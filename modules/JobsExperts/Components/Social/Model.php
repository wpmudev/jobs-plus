<?php

/**
 * Author: WPMUDEV
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

    public function addition_validate()
    {
        //todo check the service
        if ($this->type == 'url' && !empty($this->value)) {
            global $jbp_component_social;
            $social = $jbp_component_social->social($this->name);
            if (!empty($social['domain'])) {
                //start to validate
                $domain = parse_url($this->value, PHP_URL_HOST);
                if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
                    $domain = $regs['domain'];
                }
                if (strcmp($domain, $social['domain']) != 0) {
                    $this->set_error('url', sprintf(__('This url is not from <strong>%s</strong>'), $social['name']));
                }
            }
        }
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