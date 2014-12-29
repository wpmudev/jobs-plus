<?php

/**
 *
 * @since 1.0
 */
class JE_Job_Add_Widget_Controller extends WP_Widget
{
    function __construct()
    {
        $widget_ops = array(
            'classname' => 'widget_add_job',
            'description' => __("Post a new Job", je()->domain)
        );
        parent::__construct('add-job', __('Jobs + Post New Job', je()->domain), $widget_ops);
        $this->alt_option_name = 'widget_add_job';
    }

    function widget($args, $instance)
    {
        //we need the shortcode module for reuse can view function
        $view = apply_filters('widget_add_job_can_view', empty($instance['view']) ? 'both' : $instance['view'], $instance, $this->id_base);
        if (!$this->can_view($view)) {
            return;
        }
        je()->load_script('widget');

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Post new Job', je()->domain) : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        ?>
        <section class="jobsearch-widgetbar postjob">
            <div class="ig-container">
                <div class="hn-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="well well-sm">
                                <form class="search-form" method="GET"
                                      action="<?php echo get_permalink(je()->pages->page(JE_Page_Factory::JOB_ADD)); ?>">
                                    <div class="jbp-search-box-container">
                                        <input type="text" name="job_title" class="input-sm" value="" autocomplete="off"
                                               placeholder="<?php echo esc_attr(__('Job title', je()->domain)); ?>"/>

                                        <div class="clearfix"></div>
                                        <button type="submit"
                                                class="btn btn-primary btn-sm pull-right"><?php _e('Post it', je()->domain) ?></button>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>
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
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title: ', je()->domain); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('view')); ?>"><?php _e('Who can view:', je()->domain); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('view')); ?>">
                <option <?php echo selected('both', $view) ?>
                    value="both"><?php _e('Both', je()->domain) ?></option>
                <option <?php echo selected('loggedin', $view) ?>
                    value="loggedin"><?php _e('Signed in', je()->domain) ?></option>
                <option <?php echo selected('loggedout', $view) ?>
                    value="loggedout"><?php _e('Not sign in', je()->domain) ?></option>
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