<?php

/**
 * Author: WPMUDEV
 */
class JobsExpert_Components_Skill extends JobsExperts_Components
{
    public function __construct()
    {
        $this->_add_action('admin_enqueue_scripts', 'scripts');
        $this->_add_action('wp_enqueue_scripts', 'scripts');
        $this->_add_ajax_action('jbp_skill_add', 'add_skill');

    }

    function add_skill()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'jbp_skill_add')) {
            $model = JobsExperts_Components_Skill_Model::instance()->get_one($_POST['name'], $_POST['parent_id']);
            if (!is_object($model)) {
                $model = new JobsExperts_Components_Skill_Model();
            }
            $model->import($_POST);
            if ($model->validate()) {
                $model->save();
                echo json_encode(array(
                    'status' => 1,
                    'html' => $this->skill_bar_template($model)
                ));

            } else {
                echo json_encode(array(
                    'status' => 0,
                    'errors' => implode('<br/>', $model->get_errors())
                ));
            }
        }
        exit;
    }

    function scripts()
    {
        $plugin = JobsExperts_Plugin::instance();
        wp_register_style('jbp_ion_slider_style', $plugin->_module_url . 'assets/ion-range-slider/css/ion.rangeSlider.css');
        wp_register_style('jbp_ion_slider_flat', $plugin->_module_url . 'assets/ion-range-slider/css/ion.rangeSlider.skinFlat.css');
        wp_register_script('jbp_ion_slider', $plugin->_module_url . 'assets/ion-range-slider/js/ion.rangeSlider.min.js');

        wp_register_style('jbp-skill', $plugin->_module_url . 'Components/Skill/style.css');
    }

    public function load_scripts()
    {
        wp_enqueue_script('jbp_ion_slider');
        wp_enqueue_style('jbp_ion_slider_style');
        wp_enqueue_style('jbp_ion_slider_flat');

        wp_enqueue_style('jbp-skill');
    }

    function skill_bar_template($model, $tool_tip = true)
    {
        ob_start();
        ?>
        <div class="skill-bar" data-value="<?php echo $model->value ?>" data-id="<?php echo $model->name ?>">
            <h5><?php echo $model->name ?></h5>

            <div class="progress edit-skill"
                <?php
                if ($tool_tip == true) {
                    echo 'data-toggle="tooltip" data-placement="auto"
                 title="' . __('Please click here for update data . ', JBP_TEXT_DOMAIN) . '"';
                }
                ?>>
                <div class="<?php echo $model->css ?>" role="progressbar" aria-valuenow="<?php echo $model->value ?>"
                     aria-valuemin="0"
                     aria-valuemax="100" style="width: <?php echo $model->value ?>%;">
                    <?php echo $model->value ?>%
                </div>
            </div>
        </div>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }
}

global $jbp_component_skill;
$jbp_component_skill = new JobsExpert_Components_Skill();