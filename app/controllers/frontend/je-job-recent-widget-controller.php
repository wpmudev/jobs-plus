<?php

/**
 *
 * @since 1.0
 */
class JE_Job_Recent_Widget_Controller extends WP_Widget
{
    public $id;

    function __construct()
    {
        $this->id = uniqid();

        $widget_ops = array(
            'classname' => 'widget_recent_job_entries',
            'description' => __("The most recent jobs posts on your site", je()->domain)
        );
        parent::__construct('recent-jobs', __('Jobs+ The most recent jobs posts on your site', je()->domain), $widget_ops);
        $this->alt_option_name = 'widget_recent_job_entries';
    }

    function widget($args, $instance)
    {
        //we need the shortcode module for reuse can view function
        $view = apply_filters('widget_search_job_can_view', empty($instance['view']) ? 'both' : $instance['view'], $instance, $this->id_base);
        if (!$this->can_view($view)) {
            return '';
        }
        je()->load_script('widget');

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent jobs Posts', je()->domain) : $instance['title'], $instance, $this->id_base);

        if (empty($instance['number']) || !$number = absint($instance['number'])) {
            $number = 10;
        }

        $show_cat = isset($instance['show_cat']) ? $instance['show_cat'] : false;

        $post_args = array(
            'post_type' => 'jbp_job',
            'posts_per_page' => $number,
            //'no_found_rows' => true,
            'post_status' => 'publish',
            //'ignore_sticky_posts' => true,
            'order' => 'DESC'
        );
        $order_by = isset($instance['order_by']) ? $instance['order_by'] : '';

        switch ($order_by) {
            case 'latest':
                $post_args['orderby'] = 'date';
                break;
            case 'randomize':
                $post_args['orderby'] = 'rand';
                break;
        }

        $category_val = isset($instance['category_val']) ? $instance['category_val'] : null;
        if (is_array($category_val) && count($category_val)) {
            $post_args['tax_query'] = array(
                array(
                    'taxonomy' => 'jbp_category',
                    'field' => 'term_id',
                    'terms' => $category_val
                )
            );
        }


        $data = JE_Job_Model::model()->all_with_condition($post_args);
        if (count($data)) {
            ?>
            <div class="hn-container">

            </div>
        <?php
        }
        echo $after_widget;
        ob_get_flush();
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int)$new_instance['number'];
        $instance['show_cat'] = (bool)$new_instance['show_cat'];
        $instance['order_by'] = $new_instance['order_by'];
        $instance['category_val'] = $new_instance['category_val'];
        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_recent_job_entries'])) {
            delete_option('widget_recent_job_entries');
        }

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_recent_jobs', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_cat = isset($instance['show_cat']) ? (bool)$instance['show_cat'] : false;
        $order_by = isset($instance['order_by']) ? $instance['order_by'] : 'latest';
        $category_val = isset($instance['category_val']) ? $instance['category_val'] : array();

        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>

        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of posts to show:', je()->domain); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text"
                   value="<?php echo esc_attr($number); ?>" size="3"/>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_cat); ?>
                   id="<?php echo esc_attr($this->get_field_id('show_cat')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('show_cat')); ?>"/>
            <label
                for="<?php echo esc_attr($this->get_field_id('show_cat')); ?>"><?php _e('Display job categories?', je()->domain); ?></label>
        </p>
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('order_by')); ?>"><?php _e('Order by', je()->domain); ?>
                :</label>
            <select id="<?php echo esc_attr($this->get_field_id('order_by')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('order_by')); ?>">
                <option <?php selected('randomize', $order_by) ?>
                    value="randomize"><?php _e('Randomize', je()->domain); ?></option>
                <option <?php selected('latest', $order_by) ?>
                    value="latest"><?php _e('Latest', je()->domain); ?></option>
            </select>
        </p>
        <p>
            <label><?php _e('Categories', je()->domain) ?>:</label>
            <?php
            $job_cats = get_terms('jbp_category', array(
                'hide_empty' => false
            ));
            ?>
            <select style="display: block;width: 100%" multiple="multiple"
                    name="<?php echo esc_attr($this->get_field_name('category_val')); ?>[]"
                    id="<?php echo esc_attr($this->get_field_id('category_val')); ?>">
                <?php foreach ($job_cats as $cat): ?>
                    <option <?php echo in_array($cat->term_id, $category_val) ? 'selected="selected"' : null ?>
                        value="<?php echo $cat->term_id ?>"><?php echo esc_html($cat->name) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    <?php
    }

    public function can_view($view = 'both')
    {
        $view = strtolower($view);
        if (is_user_logged_in()) {
            if ($view == 'loggedout') {
                return false;
            }
        } else {
            if ($view == 'loggedin') {
                return false;
            }
        }

        return true;
    }

}

