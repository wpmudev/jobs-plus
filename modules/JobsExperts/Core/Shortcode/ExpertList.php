<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * @author   Hoang Ngo
 * @category JobsExperts
 * @package  Shorcode
 *
 * @since    1.0.0
 */
class JobsExperts_Core_Shortcode_ExpertList extends JobsExperts_Shortcode
{
    const NAME = __CLASS__;

    public function __construct()
    {
        $this->_add_shortcode('jbp-expert-archive-page', 'shortcode');
        //shortcode style
        $this->_add_action('wp_enqueue_scripts', 'scripts', 999);
    }

    function scripts()
    {
        //wp_register_style( 'jbp_shortcode', JBP_PLUGIN_URL . 'assets/css/job-plus-shortcode.css' );
        //wp_register_style( 'jbp_shortcode_grid', JBP_PLUGIN_URL . 'assets/css/grids.css' );
    }

    public function shortcode($atts)
    {
        wp_enqueue_style('jobs-plus');
        wp_enqueue_style('jbp_shortcode');
        wp_enqueue_script('jbp_bootstrap');

        //get plugin instance
        $plugin = JobsExperts_Plugin::instance();
        //get jobs
        $post_per_page = $plugin->settings()->expert_per_page;
        $paged = get_query_var('paged');
        $args = array(
            'post_status' => 'publish',
            'posts_per_page' => $post_per_page,
            'paged' => $paged
        );

        $search = '';
        if (isset($_GET['s'])) {
            $search = $args['s'] = $_GET['s'];
        }

        $args = apply_filters('jbp_expert_search_params', $args);

        $data = JobsExperts_Core_Models_Pro::instance()->get_all($args);
        $pros = $data['data'];

        //now filter the skills
        //todo better skills manager to sync the pro and job
        if (isset($_GET['pro_skill']) && !empty($_GET['pro_skill'])) {
            $data = JobsExperts_Core_Models_Pro::instance()->get_all($args);
            $skill = $_GET['pro_skill'];
            foreach ($pros as $key => $pro) {
                $skills = json_decode($pro->skills, true);
                $find_skill = false;
                foreach ($skills as $value) {
                    if (isset($value['name']) && strtolower($value['name']) == strtolower($skill)) {
                        $find_skill = true;
                        break;
                    }
                }
                if ($find_skill == false) {
                    unset($pros[$key]);
                }
            }
            //recal the total pages
        }

        $total_pages = $data['total_pages'];
        $css_class = array(
            'lg' => 'col-md-12 col-sx-12 col-sm-12',
            'md' => 'col-md-6 col-sx-6 col-sm-6',
            'xs' => 'col-md-3 col-sx-12 col-sm-12',
            'sm' => 'col-md-4 col-sx-12 col-sm-4'
        );
        ob_start();
        ?>
        <div class="hn-container">
            <!--Search section-->
            <div class="job-search">
                <form method="get" action="<?php echo get_post_type_archive_link('jbp_pro'); ?>">
                    <!--Search section-->
                    <div class="jbp_sort_search row">
                        <div class="jbp_search_form">
                            <input type="text" class="pro-search" name="s" value="<?php echo esc_attr($search) ?>"
                                   autocomplete="off"
                                   placeholder="<?php echo __(sprintf('Search For %s', $plugin->get_expert_type()->labels->name), JBP_TEXT_DOMAIN) ?>"/>
                            <button type="submit" class="job-submit-search" value="">
                                <?php echo __('Search', JBP_TEXT_DOMAIN) ?>
                            </button>
                        </div>
                        <div style="clear: both"></div>

                    </div>
                </form>
                <div class="clearfix"></div>
                <?php do_action('jbp_expert_listing_after_search_form') ?>

            </div>
            <!--End search section-->

            <?php if (empty($pros)): ?>
                <h2><?php printf(__('No %s Found', JBP_TEXT_DOMAIN), $plugin->get_expert_type()->labels->name); ?></h2>
            <?php else: ?>
                <div class="jbp-pro-list">
                    <?php
                    //prepare for layout, we will create the pros data at chunk
                    //the idea is, we will set fix of the grid on layout, seperate the array into chunk, each chunk is a row
                    //so it will supported by css and responsive
                    $grid_rules = array(
                        0 => 'sm,sm,sm',
                        1 => 'sm,sm,sm',
                    );
                    $grid_rules = apply_filters('jbp_expert_list_layout', $grid_rules);
                    $chunks = array();
                    foreach ($grid_rules as $rule) {
                        $rule = explode(',', $rule);
                        $rule = array_filter($rule);
                        $chunk = array();
                        foreach ($rule as $val) {
                            $val = trim($val);
                            $post = array_shift($pros);
                            if (is_object($post)) {
                                $chunk[] = array(
                                    'class' => $css_class[$val],
                                    'item' => $post,
                                    'text_length' => 1
                                );
                            } else {
                                break;
                            }
                        }
                        $chunks[] = $chunk;
                    }
                    //if still have items, use default chunk
                    if (count($pros)) {
                        foreach (array_chunk($pros, 4) as $row) {
                            //ok now, we have large chunk each is 3 items
                            $chunk = array();
                            foreach ($row as $r) {
                                $chunk[] = array(
                                    'class' => $css_class['xs'],
                                    'item' => $r,
                                    'text_length' => 1.6
                                );
                            }
                            $chunks[] = $chunk;
                        }
                    }

                    foreach ($chunks as $chunk): ?>
                        <div class="row">
                            <?php foreach ($chunk as $key => $col): ?>
                                <?php
                                $pro = $col['item'];
                                $size = $col['class'];
                                global $post;
                                setup_postdata($pro->get_raw_post());

                                $avatar = get_avatar($pro->contact_email, 240);
                                $name = $pro->name;
                                $charlength = 30 / ($col['text_length'] == 1 ? 1 : 1.3);

                                $name = jbp_shorten_text($name, $charlength);

                                ?>
                                <div style="<?php echo($key == 0 ? 'margin-left:0' : null) ?>"
                                     class="jbp_expert_item <?php echo $size; ?>">
                                    <div class="jbp_pro_except">
                                        <div class="jbp_inside">
                                            <div class="meta_holder">
                                                <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo $avatar ?></a>
                                                <?php $text = !empty($pro->short_description) ? $pro->short_description : $pro->biography; ?>
                                                <div class="jbp_pro_meta hide hidden-sx hidden-sm">
                                                    <p><?php echo apply_filters('jbp_pro_listing_biography', esc_html(jbp_shorten_text($text, 100 / $col['text_length'])), $text, 100 / $col['text_length']) ?></p>

                                                    <div class="row jbp-pro-stat">
                                                        <div class="col-md-6">
                                                            <span><?php echo $pro->get_view_count() ?></span>&nbsp;<i
                                                                class="glyphicon glyphicon-eye-open"></i>
                                                            <small><?php _e('Views', JBP_TEXT_DOMAIN) ?></small>
                                                        </div>
                                                        <div class="col-md-6">
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
                    $paging = new JobsExperts_Core_Views_Pagination(array(
                        'total_pages' => $total_pages
                    ));
                    $paging->render();
                    ?>
                </div>
            <?php endif; ?>
            <div style="clear: both"></div>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('.meta_holder').mouseenter(function () {
                        $(this).find('.jbp_pro_meta').removeClass('hide');
                    }).mouseleave(function () {
                        $(this).find('.jbp_pro_meta').addClass('hide');
                    });
                })
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
}

new JobsExperts_Core_Shortcode_ExpertList();