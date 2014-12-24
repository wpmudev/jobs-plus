<?php

/**
 * @author:Hoang Ngo
 */
class IG_Skill_Controller extends IG_Request
{
    public function __construct()
    {
        if (is_user_logged_in()) {
            add_action('wp_ajax_social_skill_form', array(&$this, 'form'));
            add_action('wp_ajax_jbp_skill_add', array(&$this, 'add_skill'));
        }
    }

    function add_skill()
    {
        if (!wp_verify_nonce(ig_skill()->post('_nonce'), 'jbp_skill_add')) {
            return;
        }
        $model = IG_Skill_Model::model()->get_one($_POST['name'], $_POST['parent_id']);
        if (!is_object($model)) {
            $model = new IG_Skill_Model();
        }
        $model->import($_POST);
        if ($model->validate()) {
            $model->save();
            echo json_encode(array(
                'status' => 1,
                'html' => $this->render_partial('_icon', array(
                    'model' => $model
                ), false)
            ));
        } else {
            echo json_encode(array(
                'status' => 0,
                'errors' => implode('<br/>', $model->get_errors())
            ));
        }
        exit;
    }

    function form()
    {
        if (!wp_verify_nonce(ig_skill()->get('_wpnonce'), 'social_skill_form')) {
            return;
        }
        $id = ig_social_wall()->get('id', 0);
        $model = IG_Skill_Model::model()->get_one($id, ig_skill()->get('parent_id', 0));
        if (!is_object($model)) {
            $model = null;
        }
        $this->render('_form', array(
            'model' => $model,
        ));
        die;
    }

    public function display($parent, $attributes, $element)
    {
        wp_enqueue_style('ig-skill');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-tooltip');
        $skills = array_filter(array_unique(explode(',', $parent->$attributes)));
        $models = array();
        foreach ($skills as $skill) {
            $models[] = IG_Skill_Model::model()->get_one($skill, $parent->id);
        }
        $this->render('main', array(
            'models' => $models,
            'parent' => $parent,
            'element' => $element
        ));
    }

    public function front_display($parent, $attributes)
    {
        wp_enqueue_style('ig-skill');
        $skills = array_filter(array_unique(explode(',', $parent->$attributes)));
        $models = array();
        foreach ($skills as $skill) {
            $models[] = IG_Skill_Model::model()->get_one($skill, $parent->id);
        }
        $this->render('front', array(
            'models' => $models,
            'parent' => $parent,
        ));
    }
}