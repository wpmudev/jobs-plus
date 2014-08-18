<?php

/**
 * Name: Advanced Search
 * Description: Add advacned search form for jobs and experts listing page
 * Author: Hoang Ngo
 */
class JobsExpert_Compnents_AdvancedSearch extends JobsExperts_Components
{
    public $id;

    public function __construct()
    {
        $this->id = 'advanced_search';
        /*$this->_add_action('jbp_setting_menu', 'menu');
        $this->_add_action('jbp_setting_content', 'content', 10, 2);*/

        $this->_add_action('jbp_job_listing_after_search_form', 'jobs_search_form');
        $this->_add_filter('jbp_job_search_params', 'addition_search_params');

        $this->_add_action('jbp_expert_listing_after_search_form', 'experts_search_form');
        $this->_add_filter('jbp_expert_search_params', 'addition_expert_search_params');

        $this->_add_action('wp_enqueue_scripts', 'scripts');
    }

    function menu()
    {
        $plugin = JobsExperts_Plugin::instance();
        ?>
        <li <?php echo $this->active_tab('advanced_search') ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=advanced_search') ?>">
                <i class="dashicons dashicons-feedback"></i> <?php _e('Advanced Search', JBP_TEXT_DOMAIN) ?>
            </a></li>
    <?php
    }

    function content(JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model)
    {
        if ($this->is_current_tab('advanced_search')) {
            _e('This will add an advanced search form to job listing page and expert listing page', JBP_TEXT_DOMAIN);
        }
    }

    function scripts()
    {
        $plugin = JobsExperts_Plugin::instance();
        wp_register_style('jbp_ion_slider_style', $plugin->_module_url . 'assets/ion-range-slider/css/ion.rangeSlider.css');
        wp_register_style('jbp_ion_slider_flat', $plugin->_module_url . 'assets/ion-range-slider/css/ion.rangeSlider.skinFlat.css');
        wp_register_script('jbp_ion_slider', $plugin->_module_url . 'assets/ion-range-slider/js/ion.rangeSlider.min.js');
    }

    function jobs_search_form()
    {
        $plugin = JobsExperts_Plugin::instance();
        wp_enqueue_script('jbp_ion_slider');
        wp_enqueue_style('jbp_ion_slider_style');
        wp_enqueue_style('jbp_ion_slider_flat');

        $job_min_price = $plugin->settings()->job_min_search_budget;
        $job_max_price = $plugin->settings()->job_max_search_budget;
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $job_min_price = $_GET['min_price'];
        }

        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $job_max_price = $_GET['max_price'];
        }
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'latest';
        $cat = (isset($_GET['job_cat']) && $_GET['job_cat'] > 0) ? $_GET['job_cat'] : null;

        global $wpdb;
        $range_max = $wpdb->get_var("select meta_value from wp_postmeta where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; ");;
        ?>
        <button style="display: block" type="button"
                class="btn btn-link job-advance-search"><?php _e('Advanced Search', JBP_TEXT_DOMAIN) ?></button>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var order_by = '<?php echo $order_by ?>';
                var category = '<?php echo $cat ?>';
                var price_data = {
                    from:<?php echo $job_min_price ?>,
                    to:<?php echo $job_max_price ?>
                };
                $('.job-advance-search').popover({
                    placement: 'bottom',
                    html: true,
                    trigger: 'click',
                    content: '<?php echo $this->get_job_advance_search_form() ?>',
                    title: '<?php _e('Advance Search Form',JBP_TEXT_DOMAIN) ?>'
                }).on('shown.bs.popover', function () {
                    var that = $(this);
                    var next = $(this).next();
                    if (next.hasClass('popover')) {
                        var form = next.find('form').first();
                        form.find(':input[name="cat"]').on('change', function () {
                            category = $(this).val();
                        });
                        if (category != '') {
                            form.find(':input[name="cat"]').val(category).change();
                        }
                        form.find(':input[name="order_by"]').on('click', function () {
                            if ($(this).prop('checked') == true) {
                                order_by = $(this).val();
                            }
                        });
                        form.find('input[name="min_price"]').val(price_data.from);
                        form.find('input[name="max_price"]').val(price_data.to);
                        if (order_by != '') {
                            form.find(':input[name="order_by"][value="' + order_by + '"]').prop('checked', true);
                        }
                        form.find('.cancel_search_form').on('click', function () {
                            that.popover('hide');
                        });

                        form.on('submit', function () {
                            form.append('<input type="hidden" value="' + $('.job-query').val() + '" name="s"> ');
                        })
                    }

                    $(".job-price-range").ionRangeSlider({
                        min: <?php echo $plugin->settings()->job_min_search_budget ?>,
                        max: '<?php echo $range_max+100 ?>',
                        type: "double",
                        prefix: "$",
                        maxPostfix: "+",
                        prettify: false,
                        hasGrid: true,
                        from: price_data.from,
                        to: price_data.to,
                        gridMargin: 7,
                        onChange: function (obj) {      // callback is called on every slider change
                            price_data = {
                                from: obj.fromNumber,
                                to: obj.toNumber
                            };
                            form.find('input[name="min_price"]').val(price_data.from);
                            form.find('input[name="max_price"]').val(price_data.to);
                        }
                    });
                });
            })
        </script>
    <?php
    }

    function get_job_advance_search_form($show_search_form = '')
    {
        ob_start();
        ?>
        <form class="job_advanced_search_form" method="get"
              action="<?php echo get_post_type_archive_link('jbp_job') ?>">
            <input type="hidden" name="advance_search" value="1">
            <table class="table">
                <tr>
                    <td style="width: 20%"><?php _e('Price Range', JBP_TEXT_DOMAIN) ?></td>
                    <td style="width: 80%">
                        <div class="job-price-range">
                            <input class="job-price-range" type="text"/>
                            <input type="hidden" name="min_price">
                            <input type="hidden" name="max_price">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%"><?php _e('Category', JBP_TEXT_DOMAIN) ?></td>
                    <td style="width: 80%">
                        <?php echo str_replace('\'', '"', wp_dropdown_categories(array(
                            'taxonomy' => 'jbp_category',
                            'echo' => 0,
                            'show_option_none' => __('--SELECT--', JBP_TEXT_DOMAIN),
                            'name' => 'job_cat',
                            'selected' => isset($_GET['job_cat']) ? $_GET['job_cat'] : 0
                        ))) ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%"><?php _e('Order By', JBP_TEXT_DOMAIN) ?></td>
                    <td style="width: 80%">
                        <label><input type="radio" value="name" name="order_by">&nbsp;
                            <?php _e('Name', JBP_TEXT_DOMAIN) ?>
                        </label>&nbsp;&nbsp;
                        <label>
                            <input type="radio" value="latest" name="order_by"> <?php _e('Latest', JBP_TEXT_DOMAIN) ?>
                        </label> &nbsp;&nbsp;
                        <label><input type="radio" value="ending" name="order_by">&nbsp;
                            <?php _e('About to End', JBP_TEXT_DOMAIN) ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit"
                                class="btn btn-xs btn-primary"><?php _e('Search', JBP_TEXT_DOMAIN) ?></button>
                        &nbsp;
                        <button type="button"
                                class="btn btn-xs cancel_search_form btn-default"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
                    </td>
                </tr>
            </table>
        </form>
        <?php
        $content = ob_get_clean();

        return preg_replace('/^\s+|\n|\r|\s+$/m', '', $content);
    }

    function addition_search_params($args)
    {
        $plugin = JobsExperts_Plugin::instance();
        $tax_query = array();
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'latest';
        $cat = (isset($_GET['job_cat']) && $_GET['job_cat'] > 0) ? $_GET['job_cat'] : null;

        $meta_query = array();
        if ($order_by == 'latest') {
            $args['orderby'] = 'ID';
            $args['order'] = 'DESC';
        } elseif ($order_by == 'ending') {
            $args['orderby'] = 'meta_value';
            $args['meta_key'] = '_ct_jbp_job_Due ';
            $args['order'] = 'ASC';
        } elseif ($order_by == 'name') {
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
        }

        //category
        if (!empty($cat)) {
            $tax_query[] = array(
                'taxonomy' => 'jbp_category',
                'field' => 'term_id',
                'terms' => $cat
            );
        }

        $args['tax_query'] = $tax_query;

        $job_min_price = $plugin->settings()->job_min_search_budget;
        //get the max price
        global $wpdb;
        $job_max_price = $plugin->settings()->job_max_search_budget;
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $job_min_price = $_GET['min_price'];
        }

        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $job_max_price = $_GET['max_price'];
        }

        if ($plugin->settings()->job_budget_range) {
            //todo find another way to inject wp query to have better search, the logic should be min price > min price search and less than job max price
            /*$meta_query[]           = array(
                'key'     => '_jbp_job_budget_min',
                'value'   => $job_max_price,
                'type'    => 'numeric',
                'compare' => '<=',
            );*/
            $meta_query[] = array(
                'key' => '_jbp_job_budget_max',
                'value' => $job_min_price,
                'type' => 'numeric',
                'compare' => '>=',
            );
        } else {
            $meta_query[] = array(
                'key' => '_ct_jbp_job_Budget',
                'value' => array($job_min_price, $job_max_price),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            );
        }

        $args['meta_query'] = $meta_query;

        return $args;
    }

    function experts_search_form()
    {

        ?>
        <button type="button"
                class="btn btn-link pro-advance-search"><?php _e('Advanced Search', JBP_TEXT_DOMAIN) ?></button>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var form_data = [];
                <?php if(isset($_GET['pro_skill'])): ?>
                form_data.push({
                    name: 'pro_skill',
                    value: '<?php echo $_GET['pro_skill'] ?>'
                });
                <?php endif; ?>
                <?php if(isset($_GET['order_by'])): ?>
                form_data.push({
                    name: 'order_by',
                    value: '<?php echo $_GET['order_by'] ?>'
                });
                <?php endif; ?>

                $('.pro-advance-search').popover({
                    placement: 'bottom',
                    html: true,
                    trigger: 'click',
                    content: '<?php echo $this->get_advance_search_form() ?>',
                    title: '<?php _e('Advance Search Form',JBP_TEXT_DOMAIN) ?>'
                }).on('shown.bs.popover', function () {
                    var that = $(this);
                    var next = $(this).next();
                    if (next.hasClass('popover')) {
                        var form = next.find('form').first();
                        //bind
                        if (form_data.length > 0) {
                            var selected = [];
                            $.each(form_data, function (i, v) {
                                if (v.name == 'pro_skill') {
                                    selected.push(v.value);
                                } else {
                                    form.find('input[name="' + v.name + '"][value="' + v.value + '"]').prop('checked', true);
                                }
                            })
                            form.find('select').val(selected);
                        }
                        form.find(':input').on('change', function () {
                            form_data = form.serializeArray();

                        })
                        form.find('.cancel_search_form').on('click', function () {
                            that.popover('hide');
                        });

                        form.on('submit', function () {
                            form.append('<input type="hidden" value="' + $('.pro-search').val() + '" name="s"> ');
                        })
                    }

                });
            })
        </script>
    <?php
    }

    function addition_expert_search_params($args)
    {

        if (isset($_GET['order_by']) && !empty($_GET['order_by'])) {
            switch ($_GET['order_by']) {
                case 'name':
                    $args['orderby'] = 'title';
                    $args['order'] = 'ASC';
                    break;
                case 'popular':
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = 'jbp_pro_view_count';
                    $args['order'] = 'DESC';
                    break;
                case 'like':
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = 'jbp_pro_like_count';
                    $args['order'] = 'DESC';
                    break;
            }

        }

        if (isset($_GET['country']) && !empty($_GET['country'])) {
            $args['meta_query'] = array(
                array(
                    'key' => '_ct_jbp_pro_Location',
                    'value' => $_GET['country']
                )
            );
        }

        return $args;
    }

    function get_advance_search_form($show_search_form = '')
    {
        ob_start();

        ?>
        <form class="job_advanced_search_form" method="get">
            <input type="hidden" name="advance_search" value="1">
            <table class="table">
                <tr>
                    <td style="width: 20%"><?php _e('Location', JBP_TEXT_DOMAIN) ?></td>
                    <td style="width: 80%">
                        <?php
                        $selected = (isset($_GET['country']) && !empty($_GET['country'])) ? $_GET['country'] : null;
                        echo JobsExperts_Framework_Form::countryDropdown('country', array($selected), array(
                            'prompt' => '--Select--'
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%"><?php _e('Order By', JBP_TEXT_DOMAIN) ?></td>
                    <td style="width: 80%">
                        <label><input type="radio" value="name" name="order_by">&nbsp;
                            <?php _e('Name', JBP_TEXT_DOMAIN) ?>
                        </label>&nbsp;&nbsp;
                        <label>
                            <input type="radio" value="popular"
                                   name="order_by"> <?php _e('Most Popular', JBP_TEXT_DOMAIN) ?>
                        </label> &nbsp;&nbsp;
                        <label><input type="radio" value="like" name="order_by">&nbsp;
                            <?php _e('Most Likes', JBP_TEXT_DOMAIN) ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit"
                                class="btn btn-xs btn-primary"><?php _e('Search', JBP_TEXT_DOMAIN) ?></button>
                        &nbsp;
                        <button type="button"
                                class="btn btn-xs cancel_search_form btn-default"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
                    </td>
                </tr>
            </table>
        </form>
        <?php
        $content = ob_get_clean();

        return preg_replace('/^\s+|\n|\r|\s+$/m', '', $content);
    }
}

new JobsExpert_Compnents_AdvancedSearch();