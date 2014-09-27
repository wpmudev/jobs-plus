<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Shortcode_LandingPage extends JobsExperts_Shortcode
{
    public function __construct()
    {
        $this->_add_shortcode('jbp-landing-page', 'shortcode');
    }

    function shortcode($atts)
    {
        $a = shortcode_atts(array(
            'job_show_count' => 3,
            'expert_show_count' => 6
        ), $atts);

        wp_enqueue_style('jobs-list-shortcode');
        wp_enqueue_style('expert-list-shortcode');
        wp_enqueue_style('jobs-landing-shortcode');
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();

        $jobs_query = JobsExperts_Core_Models_Job::instance()->get_all(array(
            'posts_per_page' => $a['job_show_count'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        $jobs = $jobs_query['data'];
        /////epxert
        $expert_query = JobsExperts_Core_Models_Pro::instance()->get_all(array(
            'posts_per_page' => $a['expert_show_count'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        $pros = $expert_query['data'];
        $css_class = array(
            'lg' => 'col-md-12 col-xs-12 col-sm-12',
            'md' => 'col-md-6 col-xs-12 col-sm-12',
            'sx' => 'col-md-3 col-xs-12 col-sm-12',
            'sm' => 'col-md-4 col-xs-12 col-sm-12'
        );

        ob_start();
        ?>
        <div class="hn-container">
            <div class="jbp-landing-page">
                <div class="row">
                    <div class="col-md-6 col-xs-12 col-sm-12">
                        <div class="page-header">
                            <h3><?php echo sprintf(__('Recently posted %s', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->name) ?></h3>
                        </div>
                        <?php if (empty($jobs)): ?>
                            <div class="empty-records">
                                <p><?php printf(__('No %s Found', JBP_TEXT_DOMAIN), $plugin->get_job_type()->labels->name); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="jbp-job-list">
                                <?php
                                $colors = array(
                                    'jbp-yellow',
                                    'jbp-mint',
                                    'jbp-rose',
                                    'jbp-blue',
                                    'jbp-amber',
                                    'jbp-grey'
                                );
                                //prepare for layout, we will create the jobs data at chunk
                                //the idea is, we will set fix of the grid on layout, seperate the array into chunk, each chunk is a row
                                //so it will supported by css and responsive

                                $chunks = array();
                                foreach (array_chunk($jobs, 3) as $row) {
                                    //ok now, we have large chunk each is 3 items
                                    $chunk = array();
                                    foreach ($row as $r) {
                                        $chunk[] = array(
                                            'class' => $css_class['lg'],
                                            'item' => $r,
                                            'text_length' => 3
                                        );
                                    }
                                    $chunks[] = $chunk;
                                }
                                $template = new JobsExperts_Core_Views_JobList(array(
                                    'chunks' => $chunks,
                                    'colors' => $colors,
                                    'lite' => true
                                ));
                                $template->render();
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="add-record">
                            <a class="btn btn-primary"
                               href="<?php echo apply_filters('jbp_add_new_job_url',get_permalink($page_module->page($page_module::JOB_ADD))) ?>"><?php _e('Add a Job', JBP_TEXT_DOMAIN) ?></a>
                        </div>

                    </div>
                    <div class="col-md-6 col-xs-12 col-sm-12">
                        <div class="page-header">
                            <h3><?php echo sprintf(__('Recent %s', JBP_TEXT_DOMAIN), $plugin->get_expert_type()->labels->name) ?></h3>
                        </div>
                        <div class="jbp-pro-list">
                            <?php
                            $chunks = array();
                            foreach (array_chunk($pros, 2) as $row) {
                                //ok now, we have large chunk each is 3 items
                                $chunk = array();
                                foreach ($row as $r) {
                                    $chunk[] = array(
                                        'class' => $css_class['md'],
                                        'item' => $r,
                                        'text_length' => 1.6
                                    );
                                }
                                $chunks[] = $chunk;
                            }
                            if (!empty($chunks)) {

                                foreach ($chunks as $chunk): ?>
                                    <div class="row no-margin">
                                        <?php foreach ($chunk as $key => $col): ?>
                                            <?php
                                            $pro = $col['item'];
                                            $size = $col['class'];
                                            global $post;
                                            setup_postdata($pro->get_raw_post());
                                            $avatar = $pro->get_avatar();;
                                            $name = $pro->name;
                                            $charlength = 30 / ($col['text_length'] == 1 ? 1 : 1.3);

                                            $name = jbp_shorten_text($name, $charlength);

                                            ?>
                                            <div style="<?php echo($key == 0 ? 'margin-left:0' : null) ?>"
                                                 class="jbp_expert_item <?php echo $size; ?> no-padding">
                                                <div class="jbp_pro_except">
                                                    <div class="jbp_inside">
                                                        <div class="meta_holder">
                                                            <div class="expert-avatar on-landing">
                                                                <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo $avatar ?></a>
                                                            </div>
                                                            <?php $text = !empty($pro->short_description) ? $pro->short_description : $pro->biography; ?>
                                                            <div class="jbp_pro_meta hide hidden-sx hidden-sm">

                                                                <div class="row jbp-pro-stat no-margin">
                                                                    <div class="col-md-6 no-padding">
                                                                        <span><?php echo $pro->get_view_count() ?></span>&nbsp;<i
                                                                            class="glyphicon glyphicon-eye-open"></i>
                                                                        <small><?php _e('Views', JBP_TEXT_DOMAIN) ?></small>
                                                                    </div>
                                                                    <div class="col-md-6 no-padding">
                                                                        <span><?php echo $pro->get_like_count() ?></span><i
                                                                            class="glyphicon glyphicon-heart"></i>
                                                                        <small><?php _e('Likes', JBP_TEXT_DOMAIN) ?></small>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p>
                                                            <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo $name ?></a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php
                            } else {
                                ?>
                                <div class="empty-records">
                                    <p><?php echo sprintf(__('No %s found'), $plugin->get_expert_type()->labels->name) ?></p>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="add-record">
                                <a class="btn btn-primary"
                                   href="<?php echo apply_filters('jbp_add_new_expert_url', get_permalink($page_module->page($page_module::EXPERT_ADD))) ?>"><?php _e('Become Expert', JBP_TEXT_DOMAIN) ?></a>
                            </div>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.meta_holder').mouseenter(function () {
                    $(this).find('.jbp_pro_meta').removeClass('hide');
                }).mouseleave(function () {
                    $(this).find('.jbp_pro_meta').addClass('hide');
                });
            })
        </script>
        <?php
        return ob_get_clean();
    }
}

new JobsExperts_Core_Shortcode_LandingPage();