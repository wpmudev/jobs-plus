<?php

/**
 * Name: Advanced Search
 * Description: Add advanced search form for jobs and experts listing page
 * Author: WPMU DEV
 */
class JE_Advanced_Search {
	public function __construct() {
		add_action( 'jbp_job_listing_after_search_form', array( &$this, 'job_search_form' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );

		add_action( 'jbp_expert_listing_after_search_form', array( &$this, 'expert_search_form' ) );
		add_action( 'jbp_expert_search_params', array( &$this, 'addition_expert_search_params' ) );
		add_filter( 'jbp_job_search_params', array( &$this, 'addition_search_params' ) );

		add_action( 'jobs_search_widget_form_after', array( &$this, 'extend_job_search_widget' ) );
	}

	function extend_job_search_widget( $form_id ) {
		wp_enqueue_script( 'jbp_ion_slider' );
		wp_enqueue_style( 'jbp_ion_slider_style' );
		wp_enqueue_style( 'jbp_ion_slider_flat' );

		wp_enqueue_style( 'jobs-advanced-search' );

		global $wpdb;
		$range_max = $wpdb->get_var( "select meta_value from " . $wpdb->prefix . "postmeta where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; " );;

		$job_min_price = je()->settings()->job_min_search_budget;
		$job_max_price = $range_max + 100;
		if ( isset( $_GET['min_price'] ) && ! empty( $_GET['min_price'] ) ) {
			$job_min_price = $_GET['min_price'];
		}

		if ( isset( $_GET['max_price'] ) && ! empty( $_GET['max_price'] ) ) {
			$job_max_price = $_GET['max_price'];
		}
		$order_by = isset( $_GET['order_by'] ) ? $_GET['order_by'] : 'latest';
		$cat      = ( isset( $_GET['job_cat'] ) && $_GET['job_cat'] > 0 ) ? $_GET['job_cat'] : null;

		?>
        <input type="hidden" name="advance_search" value="1">
        <label><?php _e( 'Price Range', je()->domain ) ?></label>
        <input class="job-price-range" type="text"/>
        <input type="hidden" name="min_price">
        <input type="hidden" name="max_price">
        <br/>
        <label><?php _e( 'Category', je()->domain ) ?></label>
		<?php
		$data = array_combine( wp_list_pluck( get_terms( 'jbp_category', 'hide_empty=0' ), 'term_id' ), wp_list_pluck( get_terms( 'jbp_category', 'hide_empty=0' ), 'name' ) );
		echo IG_Form::select( array(
			'name'       => 'job_cat',
			'data'       => $data,
			'selected'   => $cat,
			'attributes' => array(
				'class' => 'input-sm',
				'style' => 'width:100%'
			),
			'nameless'   => __( '--SELECT--', je()->domain )
		) );
		?>
        <label><?php _e( 'Order By', je()->domain ) ?></label>
        <label style="display: inline"><input type="radio" value="name" name="order_by">&nbsp;
			<?php _e( 'Name', je()->domain ) ?>
        </label>&nbsp;&nbsp;
        <label style="display: inline">
            <input type="radio" value="latest" name="order_by"> <?php _e( 'Latest', je()->domain ) ?>
        </label> &nbsp;&nbsp;
        <label style="display: inline"><input type="radio" value="ending" name="order_by">&nbsp;
			<?php _e( 'About to End', je()->domain ) ?>
        </label>
		<?php
		//build the format
		$setting = je()->settings();

		$pos = $setting->curr_symbol_position;
		if ( $pos == 1 || $pos == 2 ) {
			$pos = 'prefix';
		} else {
			$pos = 'postfix';
		}
		?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var price_data = {
                    from:<?php echo $job_min_price ?>,
                    to:<?php echo $job_max_price ?>
                };
                var form = $('#<?php echo $form_id ?>');
                var order_by = '<?php echo $order_by ?>';

                form.find(".job-price-range").ionRangeSlider({
                    min: <?php echo $setting->job_min_search_budget ?>,
                    max: '<?php echo $range_max + 100 ?>',
                    type: "double",
				<?php echo $pos ?>:
                "<?php echo JobsExperts_Helper::format_currency() ?>",
                    maxPostfix
            :
                "+",
                    prettify
            :
                false,
                    hasGrid
            :
                true,
                    from
            :
                price_data.from,
                    to
            :
                price_data.to,
                    gridMargin
            :
                7,
                    onChange
            :

                function (obj) {      // callback is called on every slider change
                    price_data = {
                        from: obj.from,
                        to: obj.to
                    };
                    form.find('input[name="min_price"]').val(price_data.from);
                    form.find('input[name="max_price"]').val(price_data.to);
                }
            })
                ;
                form.find('input[name="order_by"][value="' + order_by + '"]').prop('checked', true);
            })
        </script>
		<?php
	}

	function expert_search_form() {
		add_action( 'wp_footer', array( &$this, 'form_template' ) );

		wp_enqueue_style( 'jobs-advanced-search' );
		wp_enqueue_style( 'webuipopover' );
		wp_enqueue_script( 'webuipopover' );
		$translation_array = array(
			'title' => __( 'Advance Search Form', je()->domain ),
		);
		wp_localize_script( 'jobs-main', 'job_search_form', $translation_array );
		?>
        <button type="button" class="btn btn-link job-advance-search pro-advance-search">
			<?php _e( 'Advanced Search', je()->domain ) ?>
        </button>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.pro-advance-search').webuiPopover({
                    title: job_search_form.title,
                    content: function () {
                        var content = $('<div class="ig-container"></div>');
                        var html = $('#expert-search-container').html();
                        content.html(html);
                        return content;
                    }
                });
                $('body').on('click', '.cancel_search_form', function () {
                    $('.job-advance-search').webuiPopover('hide');
                });
            })
        </script>
        <style>
            .post-type-archive-jbp_pro .webui-popover {
                width: 40% !important;
            }
        </style>
		<?php
	}

	function addition_expert_search_params( $args ) {
		global $wpdb;
		//do the manual query
		$sql   = 'SELECT ID FROM ' . $wpdb->prefix . 'posts posts {{join}} {{where}} {{group}} {{order}}';
		$join  = array();
		$where = array();
		$order = array();
		$group = array();

		$where[] = $wpdb->prepare( 'post_status=%s AND post_type=%s', 'publish', 'jbp_pro' );

		if ( isset( $_GET['order_by'] ) && ! empty( $_GET['order_by'] ) ) {
			switch ( $_GET['order_by'] ) {
				case 'name':
					$order[] = 'ORDER BY posts.post_title ASC';
					break;
				case 'popular':
					$join[]  = $wpdb->prepare( "INNER JOIN " . $wpdb->prefix . "postmeta view_count ON posts.ID = view_count.post_id AND view_count.meta_key=%s", 'jbp_pro_view_count' );
					$order[] = 'ORDER BY ABS(view_count.meta_value) DESC';
					break;
				case 'like':
					$join[]  = $wpdb->prepare( "INNER JOIN " . $wpdb->prefix . "postmeta like_count ON posts.ID = like_count.post_id AND like_count.meta_key=%s", 'jbp_pro_like_count' );
					$order[] = 'ORDER BY ABS(like_count.meta_value) DESC';
					break;
			}

		}

		if ( isset( $_GET['country'] ) && ! empty( $_GET['country'] ) ) {
			$join[]  = $wpdb->prepare( "INNER JOIN " . $wpdb->prefix . "postmeta location ON posts.ID = location.post_id AND location.meta_key=%s", '_ct_jbp_pro_Location' );
			$where[] = $wpdb->prepare( 'location.meta_value = %s', $_GET['country'] );
		}

		//build query
		$group[] = 'GROUP BY ID';

		$sql   = str_replace( '{{join}}', implode( ' ', $join ), $sql );
		$where = ' WHERE ' . implode( ' AND ', $where );
		$sql   = str_replace( '{{where}}', $where, $sql );
		$group = implode( ' ', $group );
		$sql   = str_replace( '{{group}}', $group, $sql );
		$sql   = str_replace( '{{order}}', implode( ' ', $order ), $sql );

		$data = $wpdb->get_col( $sql );
		if ( empty( $data ) ) {
			$args['post__in'] = array( '-1' );
		} else {
			if ( isset( $_GET['skill'] ) && ! empty( $_GET['skill'] ) ) {
				$skill = $_GET['skill'];
				foreach ( $data as $key => $val ) {
					//get the skill of this expert

					$expert_skill = get_post_meta( $val, '_ct_jbp_pro_Skills', true );
					$has_skill    = false;
					$expert_skill = explode( ',', $expert_skill );
					$expert_skill = array_filter( $expert_skill );
					foreach ( $expert_skill as $s ) {
						$compare = strcmp( trim( strtolower( $s ) ), trim( strtolower( $skill ) ) );
						if ( $compare === 0 ) {
							$has_skill = true;
							break;
						}
					}
					if ( $has_skill == false ) {
						unset( $data[ $key ] );
					}
				}
				if ( empty( $data ) ) {
					$args['post__in'] = array( - 1 );
				} else {
					$args['post__in'] = $data;
				}
			} else {
				$args['post__in'] = $data;
			}

		}
		$args['orderby'] = 'post__in';

		return $args;
	}

	function addition_search_params( $args ) {
		global $wpdb;
		//do the manual query
		$sql   = 'SELECT ID FROM ' . $wpdb->prefix . 'posts posts {{join}} {{where}} {{group}} {{order}}';
		$join  = array();
		$where = array();
		$order = array();
		$group = array();

		$where[] = $wpdb->prepare( 'post_status=%s AND post_type=%s', 'publish', 'jbp_job' );

		$plugin    = je();
		$tax_query = array();
		$order_by  = isset( $_GET['order_by'] ) ? $_GET['order_by'] : 'latest';
		$cat       = ( isset( $_GET['job_cat'] ) && $_GET['job_cat'] > 0 ) ? $_GET['job_cat'] : null;
		if ( ! empty( $cat ) ) {
			$join[] = '
            INNER JOIN ' . $wpdb->prefix . 'term_relationships tr ON posts.ID = tr.object_id
            INNER JOIN ' . $wpdb->prefix . 'term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
';

			$where[] = $wpdb->prepare( " tt.term_id=%d", $cat );
		}
		//process order
		if ( $order_by == 'latest' ) {
			$order[] = 'ORDER BY posts.ID DESC';
		} elseif ( $order_by == 'ending' ) {
			$order[] = 'ORDER BY deadline.meta_value ASC';
			$join[]  = $wpdb->prepare( 'INNER JOIN ' . $wpdb->prefix . 'postmeta deadline ON deadline.post_id = posts.ID AND deadline.meta_key=%s', '_ct_jbp_job_Due' );
		} elseif ( $order_by == 'name' ) {
			$order[] = 'ORDER BY posts.post_title ASC';
		}

		//price
		if ( $plugin->settings()->job_budget_range == 1 ) {
			if ( isset( $_GET['min_price'] ) && ! empty( $_GET['min_price'] ) ) {
				$job_min_price = $_GET['min_price'];
			} else {
				$job_min_price = $plugin->settings()->job_min_search_budget;
			}

			if ( isset( $_GET['max_price'] ) && ! empty( $_GET['max_price'] ) ) {
				$job_max_price = $_GET['max_price'];
			} else {
				$range_max = $wpdb->get_var( "select meta_value from " . $wpdb->prefix . "postmeta where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; " );;
				if ( empty( $range_max ) ) {
					$range_max = 5000;
				}
				$job_max_price = $range_max;
			}

			$join[] = $wpdb->prepare( '
            INNER JOIN ' . $wpdb->prefix . 'postmeta min_price ON min_price.post_id = posts.ID AND min_price.meta_key=%s
INNER JOIN ' . $wpdb->prefix . 'postmeta max_price ON max_price.post_id = posts.ID AND max_price.meta_key=%s
            ', '_jbp_job_budget_min', '_jbp_job_budget_max' );

			$where[] = $wpdb->prepare( '
            min_price.meta_value >=%d AND max_price.meta_value <=%d
            ', $job_min_price, $job_max_price );

		} else {
			if ( isset( $_GET['min_price'] ) && ! empty( $_GET['min_price'] ) ) {
				$job_min_price = $_GET['min_price'];
			} else {
				$job_min_price = $plugin->settings()->job_min_search_budget;
			}
			if ( isset( $_GET['max_price'] ) && ! empty( $_GET['max_price'] ) ) {
				$job_max_price = $_GET['max_price'];
			} else {
				$range_max = $wpdb->get_var( "select meta_value from " . $wpdb->prefix . "postmeta where meta_key='_ct_jbp_job_Budget' ORDER BY ABS(meta_value) DESC LIMIT 1; " );;
				$job_max_price = $range_max;
			}

			$join[] = $wpdb->prepare( '
            INNER JOIN ' . $wpdb->prefix . 'postmeta price ON price.post_id = posts.ID AND price.meta_key=%s
            ', '_ct_jbp_job_Budget' );

			$where[] = $wpdb->prepare( '
            ABS(price.meta_value) BETWEEN %d AND %d
            ', $job_min_price, $job_max_price );
		}


		//build query
		$group[] = 'GROUP BY ID';

		$sql   = str_replace( '{{join}}', implode( ' ', $join ), $sql );
		$where = ' WHERE ' . implode( ' AND ', $where );
		$sql   = str_replace( '{{where}}', $where, $sql );
		$group = implode( ' ', $group );
		$sql   = str_replace( '{{group}}', $group, $sql );
		$sql   = str_replace( '{{order}}', implode( ' ', $order ), $sql );

		$data = $wpdb->get_col( $sql );
		if ( empty( $data ) ) {
			$args['post__in'] = array( '-1' );
		} else {
			$args['post__in'] = $data;
		}
		$args['orderby'] = 'post__in';

		$meta_query = array();
		if ( $order_by == 'latest' ) {
			$args['orderby'] = 'post_date';
			$args['order']   = 'DESC';
		} elseif ( $order_by == 'ending' ) {
			$args['orderby']  = 'meta_value';
			$args['meta_key'] = '_ct_jbp_job_Due ';
			$args['order']    = 'ASC';
		} elseif ( $order_by == 'name' ) {
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
		}

		//category
		if ( ! empty( $cat ) ) {
			$tax_query[] = array(
				'taxonomy' => 'jbp_category',
				'field'    => 'term_id',
				'terms'    => $cat
			);
		}

		$args['tax_query'] = array_merge( $args['tax_query'], $tax_query );

		$job_min_price = $plugin->settings()->job_min_search_budget;
		//get the max price
		global $wpdb;
		$job_max_price = PHP_INT_MAX;
		if ( isset( $_GET['min_price'] ) && ! empty( $_GET['min_price'] ) ) {
			$job_min_price = $_GET['min_price'];
		}

		if ( isset( $_GET['max_price'] ) && ! empty( $_GET['max_price'] ) ) {
			$job_max_price = $_GET['max_price'];
		}

		if ( $plugin->settings()->job_budget_range ) {
			//todo find another way to inject wp query to have better search, the logic should be min price > min price search and less than job max price
			/*$meta_query[]           = array(
				'key'     => '_jbp_job_budget_min',
				'value'   => $job_max_price,
				'type'    => 'numeric',
				'compare' => '<=',
			);*/
			$meta_query[] = array(
				'key'     => '_jbp_job_budget_max',
				'value'   => $job_min_price,
				'type'    => 'numeric',
				'compare' => '>=',
			);
		} else {
			$meta_query[] = array(
				'key'     => '_ct_jbp_job_Budget',
				'value'   => array( $job_min_price, $job_max_price ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN'
			);
		}
		$args['meta_query'] = $meta_query;
		if ( je()->settings()->hide_expired_from_archive == 1 ) {
			$args['post__in'] = array_diff( $args['post__in'], $args['post__not_in'] );
		}

		return $args;
	}


	function scripts() {
		wp_register_style( 'jbp_ion_slider_style', je()->plugin_url . 'app/addons/je-advanced-search/assets/ion-range-slider/css/ion.rangeSlider.css' );
		wp_register_style( 'jbp_ion_slider_flat', je()->plugin_url . 'app/addons/je-advanced-search/assets/ion-range-slider/css/ion.rangeSlider.skinFlat.css' );
		wp_register_script( 'jbp_ion_slider', je()->plugin_url . 'app/addons/je-advanced-search/assets/ion-range-slider/js/ion.rangeSlider.min.js' );

		wp_register_style( 'jobs-advanced-search', je()->plugin_url . 'app/addons/je-advanced-search/assets/style.css' );
	}

	function job_search_form() {
		add_action( 'wp_footer', array( &$this, 'form_template' ) );
		wp_enqueue_script( 'jbp_ion_slider' );
		wp_enqueue_style( 'jbp_ion_slider_style' );
		wp_enqueue_style( 'jbp_ion_slider_flat' );

		wp_enqueue_style( 'jobs-advanced-search' );
		wp_enqueue_style( 'webuipopover' );
		wp_enqueue_script( 'webuipopover' );
		$translation_array = array(
			'title' => __( 'Advance Search Form', je()->domain ),
		);
		wp_localize_script( 'jobs-main', 'job_search_form', $translation_array );
		global $wpdb;
		$range_max = $wpdb->get_var( "select meta_value from " . $wpdb->postmeta . " where meta_key='_jbp_job_budget_max' ORDER BY ABS(meta_value) DESC LIMIT 1; " );;
		if ( $range_max < 1000 ) {
			$range_max = 1000;
		}
		$job_min_price = je()->settings()->job_min_search_budget;
		$job_max_price = $range_max + 100;
		if ( isset( $_GET['min_price'] ) && ! empty( $_GET['min_price'] ) ) {
			$job_min_price = $_GET['min_price'];
		}

		if ( isset( $_GET['max_price'] ) && ! empty( $_GET['max_price'] ) ) {
			$job_max_price = $_GET['max_price'];
		}


		$pos = je()->settings()->curr_symbol_position;
		if ( $pos == 1 || $pos == 2 ) {
			$pos = 'prefix';
		} else {
			$pos = 'postfix';
		}
		?>
        <button type="button" class="btn btn-link job-advance-search"><?php _e( 'Advanced Search', je()->domain ) ?>
        </button>
        <script type="text/javascript">
            jQuery(function ($) {
                var price_data = {
                    from:<?php echo $job_min_price ?>,
                    to:<?php echo $job_max_price ?>
                };
                $('.job-advance-search').webuiPopover({
                    title: job_search_form.title,
                    content: function () {
                        var content = $('<div class="ig-container"></div>');
                        var html = $('#job-search-container').html();
                        content.html(html);
                        return content;
                    }
                }).on('shown.webui.popover', function () {
                    var pop = $(this).data('plugin_webuiPopover');
                    var holder = pop.$target;
                    var form = holder.find('form').first();
                    var element = form.find('input.job-price-range').first();
                    if (element.hasClass('turn') == false) {
                        element.ionRangeSlider({
                            min: <?php echo je()->settings()->job_min_search_budget ?>,
                            max: '<?php echo $range_max + 100 ?>',
                            type: "double",
                            '<?php echo $pos ?>': "<?php echo JobsExperts_Helper::format_currency( je()->settings()->currency, false ) ?>",
                            maxPostfix: "+",
                            prettify: false,
                            hasGrid: true,
                            from: price_data.from,
                            to: price_data.to,
                            gridMargin: 7,
                            onChange: function (obj) {      // callback is called on every slider change
                                price_data = {
                                    from: obj.from,
                                    to: obj.to
                                };
                                form.find('input[name="min_price"]').val(price_data.from);
                                form.find('input[name="max_price"]').val(price_data.to);
                            }
                        });
                        element.addClass('turn');
                        window.slider = element.data("ionRangeSlider");
                    } else {
                        var from = form.find('input[name="min_price"]').val();
                        var to = form.find('input[name="max_price"]').val();

                        if (from.length == 0) {
                            from = price_data.from;
                        }
                        if (to.length == 0) {
                            to = price_data.to;
                        }
                        window.slider.update({
                            from: from,
                            to: to
                        });
                    }
                })
                $('body').on('click', '.cancel_search_form', function () {
                    $('.job-advance-search').webuiPopover('hide');
                });


            })
        </script>
        <style>
            .post-type-archive-jbp_job .webui-popover {
                width: 40% !important;
            }
        </style>
		<?php
	}

	function form_template() {
		$order_by = isset( $_GET['order_by'] ) ? $_GET['order_by'] : 'latest';
		$cat      = ( isset( $_GET['job_cat'] ) && $_GET['job_cat'] > 0 ) ? $_GET['job_cat'] : null;
		?>
        <div class="ig-container">
            <div id="job-search-container" class="hide">
                <form class="job_advanced_search_form form-horizontal" method="get"
                      action="<?php echo is_singular() ? get_permalink( get_the_ID() ) : get_post_type_archive_link( 'jbp_job' ) ?>">
                    <input type="hidden" name="advance_search" value="1">

                    <table class="table" style="margin-bottom: 0">
                        <tr>
                            <td style="width: 20%"><?php _e( 'Price Range', je()->domain ) ?></td>
                            <td style="width: 80%">
                                <div class="job-price-range">
                                    <input class="job-price-range" type="text"/>
                                    <input type="hidden" name="min_price">
                                    <input type="hidden" name="max_price">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><?php _e( 'Category', je()->domain ) ?></td>
                            <td style="width: 80%">
								<?php
								$data = array_combine( wp_list_pluck( get_terms( 'jbp_category', 'hide_empty=0' ), 'term_id' ), wp_list_pluck( get_terms( 'jbp_category', 'hide_empty=0' ), 'name' ) );
								echo IG_Form::select( array(
									'name'       => 'job_cat',
									'data'       => $data,
									'selected'   => $cat,
									'attributes' => array(
										'style' => 'width:100%',
									),
									'nameless'   => __( '--SELECT--', je()->domain )
								) );
								?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><?php _e( 'Order By', je()->domain ) ?></td>
                            <td style="width: 80%">
                                <label><input <?php checked( $order_by, 'name' ) ?> type="radio" value="name"
                                                                                    name="order_by">&nbsp;
									<?php _e( 'Name', je()->domain ) ?>
                                </label>&nbsp;&nbsp;
                                <label>
                                    <input <?php checked( $order_by, 'latest' ) ?> type="radio" value="latest"
                                                                                   name="order_by"> <?php _e( 'Latest', je()->domain ) ?>
                                </label> &nbsp;&nbsp;
                                <label><input <?php checked( $order_by, 'ending' ) ?> type="radio" value="ending"
                                                                                      name="order_by">&nbsp;
									<?php _e( 'About to End', je()->domain ) ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button type="submit"
                                        class="btn btn-xs btn-primary"><?php _e( 'Search', je()->domain ) ?></button>
                                &nbsp;
                                <button type="button"
                                        class="btn btn-xs cancel_search_form btn-default"><?php _e( 'Cancel', je()->domain ) ?></button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <div id="expert-search-container" class="hide">
                <form class="job_advanced_search_form" method="get">
                    <input type="hidden" name="advance_search" value="1">
                    <table class="table">
                        <tr>
                            <td style="width: 20%"><?php _e( 'Location', je()->domain ) ?></td>
                            <td style="width: 80%">
								<?php
								$selected = ( isset( $_GET['country'] ) && ! empty( $_GET['country'] ) ) ? $_GET['country'] : null;
								echo IG_Form::country_select( array(
									'name'     => 'country',
									'selected' => $selected,
									'nameless' => __( '--Select--', je()->domain )
								) );
								?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><?php _e( 'Skill', je()->domain ) ?></td>
                            <td style="width: 80%">
								<?php
								$selected = ( isset( $_GET['skill'] ) && ! empty( $_GET['skill'] ) ) ? $_GET['skill'] : null;
								$skills   = JE_Expert_Model::model()->get_all_skills( true );
								$skills   = array_combine( $skills, $skills );
								echo IG_Form::select( array(
									'name'     => 'skill',
									'data'     => $skills,
									'selected' => $selected,
									'nameless' => __( '--Select--', je()->domain )
								) );
								?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><?php _e( 'Order By', je()->domain ) ?></td>
                            <td style="width: 80%">
                                <label><input <?php checked( $order_by, 'name' ) ?> type="radio" value="name"
                                                                                    name="order_by">&nbsp;
									<?php _e( 'Name', je()->domain ) ?>
                                </label>&nbsp;&nbsp;
                                <label>
                                    <input <?php checked( $order_by, 'popular' ) ?> type="radio" value="popular"
                                                                                    name="order_by"> <?php _e( 'Most Popular', je()->domain ) ?>
                                </label> &nbsp;&nbsp;
                                <label><input <?php checked( $order_by, 'like' ) ?> type="radio" value="like"
                                                                                    name="order_by">&nbsp;
									<?php _e( 'Most Likes', je()->domain ) ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button type="submit"
                                        class="btn btn-xs btn-primary"><?php _e( 'Search', je()->domain ) ?></button>
                                &nbsp;
                                <button type="button"
                                        class="btn btn-xs cancel_search_form btn-default"><?php _e( 'Cancel', je()->domain ) ?></button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
		<?php
	}
}

new JE_Advanced_Search();