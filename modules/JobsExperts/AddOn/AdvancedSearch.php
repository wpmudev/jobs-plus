<?php

/**
 * Name: Advanced Search
 * Description: Add advanced search form for jobs and experts listing page
 * Author: WPMU DEV
 */
class JobsExpert_Compnents_AdvancedSearch extends JobsExperts_AddOn
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

        $this->_add_action('jobs_search_widget_form_after', 'extend_job_search_widget');

        $this->_add_action('wp_enqueue_scripts', 'scripts');

        $skills = JobsExperts_Core_Models_Pro::instance()->get_all_skills(true);
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
        wp_register_style('jbp_ion_slider_style', $plugin->_module_url . 'AddOn/AdvancedSearch/ion-range-slider/css/ion.rangeSlider.css');
        wp_register_style('jbp_ion_slider_flat', $plugin->_module_url . 'AddOn/AdvancedSearch/ion-range-slider/css/ion.rangeSlider.skinFlat.css');
        wp_register_script('jbp_ion_slider', $plugin->_module_url . 'AddOn/AdvancedSearch/ion-range-slider/js/ion.rangeSlider.min.js');

        wp_register_style('jobs-advanced-search', $plugin->_module_url . 'AddOn/AdvancedSearch/style.css');
    }

    function extend_job_search_widget($form_id)
    {
        wp_enqueue_script('jbp_ion_slider');
        wp_enqueue_style('jbp_ion_slider_style');
        wp_enqueue_style('jbp_ion_slider_flat');

        wp_enqueue_style('jobs-advanced-search');

        $plugin = JobsExperts_Plugin::instance();
        global $wpdb;
        $range_max = $wpdb->get_var("select meta_value from wp_postmeta where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; ");;

        $job_min_price = $plugin->settings()->job_min_search_budget;
        $job_max_price = $range_max + 100;
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $job_min_price = $_GET['min_price'];
        }

        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $job_max_price = $_GET['max_price'];
        }
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'latest';
        $cat = (isset($_GET['job_cat']) && $_GET['job_cat'] > 0) ? $_GET['job_cat'] : null;

        ?>
        <label><?php _e('Price Range', JBP_TEXT_DOMAIN) ?></label>
        <input class="job-price-range" type="text"/>
        <input type="hidden" name="min_price">
        <input type="hidden" name="max_price">
        <br/>
        <label><?php _e('Category', JBP_TEXT_DOMAIN) ?></label>
        <?php
        $data = array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name'));
        echo JobsExperts_Framework_Form::dropDownList('job_cat', array(isset($_GET['job_cat']) ? $_GET['job_cat'] : 0), $data, array(
            //'multiple' => 'multiple',
            'style' => 'width:100%',
            'class' => 'input-sm',
            'prompt' => __('--SELECT--', JBP_TEXT_DOMAIN)
        ));
        ?>
        <label><?php _e('Order By', JBP_TEXT_DOMAIN) ?></label>
        <label style="display: inline"><input type="radio" value="name" name="order_by">&nbsp;
            <?php _e('Name', JBP_TEXT_DOMAIN) ?>
        </label>&nbsp;&nbsp;
        <label style="display: inline">
            <input type="radio" value="latest" name="order_by"> <?php _e('Latest', JBP_TEXT_DOMAIN) ?>
        </label> &nbsp;&nbsp;
        <label style="display: inline"><input type="radio" value="ending" name="order_by">&nbsp;
            <?php _e('About to End', JBP_TEXT_DOMAIN) ?>
        </label>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var price_data = {
                    from:<?php echo $job_min_price ?>,
                    to:<?php echo $job_max_price ?>
                };
                var form = $('#<?php echo $form_id ?>');
                var order_by = '<?php echo $order_by ?>';
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
                form.find('input[name="order_by"][value="' + order_by + '"]').prop('checked', true);
            })
        </script>
    <?php
    }

    function jobs_search_form()
    {
        $plugin = JobsExperts_Plugin::instance();
        wp_enqueue_script('jbp_ion_slider');
        wp_enqueue_style('jbp_ion_slider_style');
        wp_enqueue_style('jbp_ion_slider_flat');

        wp_enqueue_style('jobs-advanced-search');

        global $wpdb;
        $range_max = $wpdb->get_var("select meta_value from wp_postmeta where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; ");;

        $job_min_price = $plugin->settings()->job_min_search_budget;
        $job_max_price = $range_max + 100;
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $job_min_price = $_GET['min_price'];
        }

        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $job_max_price = $_GET['max_price'];
        }
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'latest';
        $cat = (isset($_GET['job_cat']) && $_GET['job_cat'] > 0) ? $_GET['job_cat'] : null;

        ?>
        <button type="button"
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
                    title: '<?php echo esc_js(__('Advance Search Form',JBP_TEXT_DOMAIN)) ?>'
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
                            form.append('<input type="hidden" value="' + $('.job-query').val() + '" name="query"> ');
                        })
                    }
                    <?php
                     //build the format
                     $setting = JobsExperts_Plugin::instance()->settings();

                     $pos = $setting->curr_symbol_position;
                     if($pos==1 || $pos==2){
                        $pos='prefix';
                     }else{
                         $pos = 'postfix';
                     }
                    ?>

                    $(".job-price-range").ionRangeSlider({
                        min: <?php echo $plugin->settings()->job_min_search_budget ?>,
                        max: '<?php echo $range_max+100 ?>',
                        type: "double",
                        <?php echo $pos ?>: "<?php echo JobsExperts_Helper::format_currency($setting->currency,'') ?>",
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
        <form class="job_advanced_search_form form-horizontal" method="get"
              action="<?php echo is_singular() ? get_permalink(get_the_ID()) : get_post_type_archive_link('jbp_job') ?>">
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
                        <?php
                        $data = array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name'));
                        echo JobsExperts_Framework_Form::dropDownList('job_cat', array(isset($_GET['job_cat']) ? $_GET['job_cat'] : 0), $data, array(
                            //'multiple' => 'multiple',
                            'style' => 'width:100%',
                            'prompt' => __('--SELECT--', JBP_TEXT_DOMAIN)
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
        global $wpdb;
        //do the manual query
        $sql = 'SELECT ID FROM ' . $wpdb->prefix . 'posts posts {{join}} {{where}} {{group}} {{order}}';
        $join = array();
        $where = array();
        $order = array();
        $group = array();

        $where[] = $wpdb->prepare('post_status=%s AND post_type=%s', 'publish', 'jbp_job');

        $plugin = JobsExperts_Plugin::instance();
        $tax_query = array();
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'latest';
        $cat = (isset($_GET['job_cat']) && $_GET['job_cat'] > 0) ? $_GET['job_cat'] : null;
        if (!empty($cat)) {
            $join[] = '
            INNER JOIN wp_term_relationships tr ON posts.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
';

            $where[] = $wpdb->prepare(" tt.term_id=%d", $cat);
        }
        //process order
        if ($order_by == 'latest') {
            $order[] = 'ORDER BY posts.ID DESC';
        } elseif ($order_by == 'ending') {
            $order[] = 'ORDER BY deadline.meta_value ASC';
            $join[] = $wpdb->prepare('INNER JOIN wp_postmeta deadline ON deadline.post_id = posts.ID AND deadline.meta_key=%s', '_ct_jbp_job_Due');
        } elseif ($order_by == 'name') {
            $order[] = 'ORDER BY posts.post_title ASC';
        }

        //price
        if ($plugin->settings()->job_budget_range == 1) {
            if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                $job_min_price = $_GET['min_price'];
            } else {
                $job_min_price = $plugin->settings()->job_min_search_budget;
            }

            if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                $job_max_price = $_GET['max_price'];
            } else {
                $range_max = $wpdb->get_var("select meta_value from wp_postmeta where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; ");;
                if (empty($range_max)) {
                    $range_max = 5000;
                }
                $job_max_price = $range_max;
            }

            $join[] = $wpdb->prepare('
            INNER JOIN wp_postmeta min_price ON min_price.post_id = posts.ID AND min_price.meta_key=%s
INNER JOIN wp_postmeta max_price ON max_price.post_id = posts.ID AND max_price.meta_key=%s
            ', '_jbp_job_budget_min', '_jbp_job_budget_max');

            $where[] = $wpdb->prepare('
            min_price.meta_value >=%d AND max_price.meta_value <=%d
            ', $job_min_price, $job_max_price);

        } else {
            if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                $job_min_price = $_GET['min_price'];
            } else {
                $job_min_price = $plugin->settings()->job_min_search_budget;
            }
            if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                $job_max_price = $_GET['max_price'];
            } else {
                $range_max = $wpdb->get_var("select meta_value from wp_postmeta where meta_key='_jbp_job_budget' ORDER BY ABS(meta_value) DESC LIMIT 1; ");;
                $job_max_price = $range_max;
            }

            $join[] = $wpdb->prepare('
            INNER JOIN wp_postmeta price ON price.post_id = posts.ID AND price.meta_key=%s
            ', '_ct_jbp_job_Budget');

            $where[] = $wpdb->prepare('
            ABS(price.meta_value) BETWEEN %d AND %d
            ', $job_min_price, $job_max_price);
        }


        //build query
        $group[] = 'GROUP BY ID';

        $sql = str_replace('{{join}}', implode(' ', $join), $sql);
        $where = ' WHERE ' . implode(' AND ', $where);
        $sql = str_replace('{{where}}', $where, $sql);
        $group = implode(' ', $group);
        $sql = str_replace('{{group}}', $group, $sql);
        $sql = str_replace('{{order}}', implode(' ', $order), $sql);

        $data = $wpdb->get_col($sql);
        if (empty($data)) {
            $args['post__in'] = array('-1');
        } else {
            $args['post__in'] = $data;
        }
        $args['orderby'] = 'post__in';
        return $args;

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
                class="btn btn-link job-advance-search pro-advance-search"><?php _e('Advanced Search', JBP_TEXT_DOMAIN) ?></button>
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
                <?php if(isset($_GET['country'])): ?>
                form_data.push({
                    name: 'country',
                    value: '<?php echo $_GET['country'] ?>'
                })
                <?php endif; ?>

                $('.pro-advance-search').popover({
                    placement: 'bottom',
                    html: true,
                    trigger: 'click',
                    content: '<?php echo $this->get_advance_search_form() ?>',
                    title: '<?php echo esc_js(__('Advance Search Form',JBP_TEXT_DOMAIN)) ?>'
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
                            //console.log(selected);
                            //form.find('select').val(selected).trigger('change');
                        }
                        form.find(':input').on('change', function () {
                            form_data = form.serializeArray();

                        })
                        form.find('.cancel_search_form').on('click', function () {
                            that.popover('hide');
                        });

                        form.on('submit', function () {
                            form.append('<input type="hidden" value="' + $('.pro-search').val() + '" name="query"> ');
                        })
                    }

                });
            })
        </script>
    <?php
    }

    function addition_expert_search_params($args)
    {
        global $wpdb;
        //do the manual query
        $sql = 'SELECT ID FROM ' . $wpdb->prefix . 'posts posts {{join}} {{where}} {{group}} {{order}}';
        $join = array();
        $where = array();
        $order = array();
        $group = array();

        $where[] = $wpdb->prepare('post_status=%s AND post_type=%s', 'publish', 'jbp_pro');

        if (isset($_GET['order_by']) && !empty($_GET['order_by'])) {
            switch ($_GET['order_by']) {
                case 'name':
                    $order[] = 'ORDER BY posts.post_title ASC';
                    break;
                case 'popular':
                    $join[] = $wpdb->prepare("INNER JOIN wp_postmeta view_count ON posts.ID = view_count.post_id AND view_count.meta_key=%s", 'jbp_pro_view_count');
                    $order[] = 'ORDER BY ABS(view_count.meta_value) DESC';
                    break;
                case 'like':
                    $join[] = $wpdb->prepare("INNER JOIN wp_postmeta like_count ON posts.ID = like_count.post_id AND like_count.meta_key=%s", 'jbp_pro_like_count');
                    $order[] = 'ORDER BY ABS(like_count.meta_value) DESC';
                    break;
            }

        }

        if (isset($_GET['country']) && !empty($_GET['country'])) {
            $join[] = $wpdb->prepare("INNER JOIN wp_postmeta location ON posts.ID = location.post_id AND location.meta_key=%s", '_ct_jbp_pro_Location');
            $where[] = $wpdb->prepare('location.meta_value = %s', $_GET['country']);
        }

        //build query
        $group[] = 'GROUP BY ID';

        $sql = str_replace('{{join}}', implode(' ', $join), $sql);
        $where = ' WHERE ' . implode(' AND ', $where);
        $sql = str_replace('{{where}}', $where, $sql);
        $group = implode(' ', $group);
        $sql = str_replace('{{group}}', $group, $sql);
        $sql = str_replace('{{order}}', implode(' ', $order), $sql);

        $data = $wpdb->get_col($sql);

        if (empty($data)) {
            $args['post__in'] = array('-1');
        } else {
            if (isset($_GET['skill']) && !empty($_GET['skill'])) {
                $skill = $_GET['skill'];
                foreach ($data as $key => $val) {
                    //get the skill of this expert

                    $expert_skill = get_post_meta($val, '_ct_jbp_pro_Skills',true);
                    $has_skill = false;
                    $expert_skill = explode(',', $expert_skill);
                    $expert_skill = array_filter($expert_skill);
                    foreach ($expert_skill as $s) {
                        $compare = strcmp(trim(strtolower($s)), trim(strtolower($skill)));
                        if ($compare === 0) {
                            $has_skill = true;
                            break;
                        }
                    }
                    if ($has_skill == false) {
                        unset($data[$key]);
                    }
                }
                if (empty($data)) {
                    $args['post__in'] = array(-1);
                } else {
                    $args['post__in'] = $data;
                }
            }

        }
        $args['orderby'] = 'post__in';
        return $args;
    }

    function get_advance_search_form($show_search_form = '')
    {
        wp_enqueue_style('jobs-advanced-search');
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
                    <td style="width: 20%"><?php _e('Skill', JBP_TEXT_DOMAIN) ?></td>
                    <td style="width: 80%">
                        <?php
                        $selected = (isset($_GET['skill']) && !empty($_GET['skill'])) ? $_GET['skill'] : null;
                        $skills = JobsExperts_Core_Models_Pro::instance()->get_all_skills(true);
                        echo JobsExperts_Framework_Form::dropDownList('skill', array($selected), array_combine($skills, $skills), array(
                            'prompt' => '--Select--'
                        ))
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