<?php

/**
 *
 * @since 1.0
 */
class JobsExperts_Core_Widgets_JobSearch extends WP_Widget
{
    public $id;

    function __construct()
    {
        $this->id = uniqid();
        $plugin = JobsExperts_Plugin::instance();

        $widget_ops = array(
            'classname' => 'widget_search_job',
            'description' => sprintf(__("Search %s Widget", JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->name)
        );
        parent::__construct('search-job', sprintf(__('Jobs+ Search %s', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->name), $widget_ops);
        $this->alt_option_name = 'widget_add_job';
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

        $title = apply_filters('widget_title', empty($instance['title']) ? sprintf(__('Search %s', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name) : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        $form_id = uniqid();
        ?>
        <?php
        $from = isset($_GET['min_price']) ? $_GET['min_price'] : $plugin->settings()->job_min_search_budget;
        $to = isset($_GET['max_price']) ? $_GET['max_price'] : $plugin->settings()->job_max_search_budget;
        ?>
        <section class="jobsearch-widgetbar">
            <div class="hn-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well well-sm ">
                            <form class="search-form" id="<?php echo $form_id ?>" method="GET"
                                  action="<?php echo get_post_type_archive_link('jbp_job'); ?>">
                                <label><?php _e('Keywords', JBP_TEXT_DOMAIN) ?></label>
                                <input class="input-sm" type="text" value="<?php echo isset($_GET['s']) ? $_GET['s'] : null ?>" name="s">
                                <?php do_action('jobs_search_widget_form_after', $form_id) ?>
                                <div class="clearfix"></div>
                                <button type="submit"
                                        class="btn btn-primary btn-sm pull-right"><?php _e('Search', JBP_TEXT_DOMAIN) ?></button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>
        <?php
        echo $after_widget;
        ob_get_flush();
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['view'] = $new_instance['view'];

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_add_job', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $view = isset($instance['view']) ? esc_attr($instance['view']) : '';
        ?>

        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title: ', JBP_TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('view')); ?>"><?php esc_html_e('Who can view:', JBP_TEXT_DOMAIN); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('view')); ?>">
                <option <?php echo selected('both', $view) ?>
                    value="both"><?php esc_html_e('Both', JBP_TEXT_DOMAIN) ?></option>
                <option <?php echo selected('loggedin', $view) ?>
                    value="loggedin"><?php esc_html_e('Signed in', JBP_TEXT_DOMAIN) ?></option>
                <option <?php echo selected('loggedout', $view) ?>
                    value="loggedout"><?php esc_html_e('Not sign in', JBP_TEXT_DOMAIN) ?></option>
            </select>
        </p>
    <?php
    }
}

register_widget('JobsExperts_Core_Widgets_JobSearch');