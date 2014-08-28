<?php

/**
 *
 * @since 1.0
 */
class JobsExperts_Core_Widgets_JobAdd extends WP_Widget
{

    function __construct()
    {
        $plugin = JobsExperts_Plugin::instance();

        $widget_ops = array(
            'classname' => 'widget_add_job',
            'description' => sprintf(__("Post a new %s", JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name)
        );
        parent::__construct('add-job', sprintf(__('Jobs + Post New %s', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name), $widget_ops);
        $this->alt_option_name = 'widget_add_job';
    }

    function widget($args, $instance)
    {
        $plugin = JobsExperts_Plugin::instance();
        //we need the shortcode module for reuse can view function
        $shortcode = $plugin->shortcode_module();
        $view = apply_filters('widget_add_job_can_view', empty($instance['view']) ? 'both' : $instance['view'], $instance, $this->id_base);
        if (!$shortcode->can_view($view)) {
            return '';
        }
        wp_enqueue_style('job-plus-widgets');

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? sprintf(__('Post new %s', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name) : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        $page_module = $plugin->page_module();
        ?>
        <section class="jobsearch-widgetbar postjob">
            <div class="hn-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well well-sm">
                            <form class="search-form" method="GET"
                                  action="<?php echo get_permalink($page_module::instance()->page($page_module::JOB_ADD)); ?>">
                                <div class="jbp-search-box-container">
                                    <input type="text" name="job_title" class="input-sm" value="" autocomplete="off"
                                           placeholder="<?php echo esc_attr(sprintf(__('%s title', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->singular_name)); ?>"/>

                                    <div class="clearfix"></div>
                                    <button type="submit"
                                            class="btn btn-primary btn-sm pull-right"><?php _e('Post it', JBP_TEXT_DOMAIN) ?></button>
                                <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
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

register_widget('JobsExperts_Core_Widgets_JobAdd');