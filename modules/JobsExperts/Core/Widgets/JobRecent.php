<?php

/**
 *
 * @since 1.0
 */
class JobsExperts_Core_Widgets_JobRecent extends WP_Widget
{
    public $id;

    function __construct()
    {
        $this->id = uniqid();
        $plugin = JobsExperts_Plugin::instance();

        $widget_ops = array(
            'classname' => 'widget_recent_job_entries',
            'description' => sprintf(__("The most recent %s posts on your site", JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name)
        );
        parent::__construct('recent-jobs', sprintf(__('Jobs+ The most recent %s posts on your site', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name), $widget_ops);
        $this->alt_option_name = 'widget_recent_job_entries';
    }

    function widget($args, $instance)
    {
        $plugin = JobsExperts_Plugin::instance();
        //we need the shortcode module for reuse can view function
        $shortcode = $plugin->shortcode_module();
        $view = apply_filters('widget_search_job_can_view', empty($instance['view']) ? 'both' : $instance['view'], $instance, $this->id_base);
        if (!$shortcode->can_view($view)) {
            return '';
        }
        wp_enqueue_style('job-plus-widgets');

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? sprintf(__('Recent %s Posts', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name) : $instance['title'], $instance, $this->id_base);

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

        $page_module = $plugin->page_module();

        $data = JobsExperts_Core_Models_Job::instance()->get_all($post_args);
        if (count($data['data'])) {
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
                for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of posts to show:', JBP_TEXT_DOMAIN); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text"
                   value="<?php echo esc_attr($number); ?>" size="3"/>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_cat); ?>
                   id="<?php echo esc_attr($this->get_field_id('show_cat')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('show_cat')); ?>"/>
            <label
                for="<?php echo esc_attr($this->get_field_id('show_cat')); ?>"><?php _e('Display job categories?', JBP_TEXT_DOMAIN); ?></label>
        </p>
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('order_by')); ?>"><?php _e('Order by', JBP_TEXT_DOMAIN); ?>
                :</label>
            <select id="<?php echo esc_attr($this->get_field_id('order_by')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('order_by')); ?>">
                <option <?php selected('randomize', $order_by) ?>
                    value="randomize"><?php _e('Randomize', JBP_TEXT_DOMAIN); ?></option>
                <option <?php selected('latest', $order_by) ?>
                    value="latest"><?php _e('Latest', JBP_TEXT_DOMAIN); ?></option>
            </select>
        </p>
        <p>
            <label><?php _e('Categories', JBP_TEXT_DOMAIN) ?>:</label>
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
}

register_widget('JobsExperts_Core_Widgets_JobRecent');