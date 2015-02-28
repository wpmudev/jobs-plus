<?php

/**
 * @author:Hoang Ngo
 */
class JE_Custom_Content
{
    public function __construct()
    {
        /**
         * Create jbp_job post type
         */
        if (!post_type_exists('jbp_job')) {
            $jbp_job = array(
                'labels' => array(
                    'name' => __('Jobs', je()->domain),
                    'singular_name' => __('Job', je()->domain),
                    'add_new' => __('Add New', je()->domain),
                    'add_new_item' => __('Add New Job', je()->domain),
                    'edit_item' => __('Edit Job', je()->domain),
                    'new_item' => __('New Job', je()->domain),
                    'view_item' => __('View Job', je()->domain),
                    'search_items' => __('Search Jobs', je()->domain),
                    'not_found' => __('No jobs found', je()->domain),
                    'not_found_in_trash' => __('No jobs found in Trash', je()->domain),
                    'custom_fields_block' => __('Jobs Fields', je()->domain),
                ),
                'supports' => array(
                    'title' => 'title',
                    'editor' => 'editor',
                    'author' => 'author',
                    'thumbnail' => 'thumbnail',
                    'excerpt' => false,
                    'custom_fields' => 'custom-fields',
                    'revisions' => 'revisions',
                    'page_attributes' => 'page-attributes',
                    'comments' => 'comments'
                ),
                'supports_reg_tax' => array(
                    'category' => '',
                    'post_tag' => '',
                ),
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'description' => __('Job offerings', je()->domain),
                'menu_position' => '',
                'public' => true,
                'hierarchical' => true,
                'has_archive' => apply_filters('jbp_job_archive_slug', 'jobs'),
                'rewrite' => array(
                    'slug' => apply_filters('jbp_job_single_slug', 'job'),
                    'with_front' => false,
                    'feeds' => true,
                    'pages' => true,
                    'ep_mask' => 4096,
                ),
                'query_var' => true,
                'can_export' => true,
                'cf_columns' => NULL,
                'menu_icon' => je()->plugin_url . 'assets/image/backend/icons/16px/16px_Jobs_Bright.svg',
            );

            register_post_type('jbp_job', apply_filters('je_job_posttype_param', $jbp_job));

        } //jbp_job post type complete

        /**
         * Create jbp_pro post type
         */
        if (!post_type_exists('jbp_pro')) {

            $jbp_pro = array(
                'labels' =>
                    array(
                        'name' => __('Experts', je()->domain),
                        'singular_name' => __('Expert', je()->domain),
                        'add_new' => __('Add New', je()->domain),
                        'add_new_item' => __('Add New Expert', je()->domain),
                        'edit_item' => __('Edit Expert', je()->domain),
                        'new_item' => __('New Expert', je()->domain),
                        'view_item' => __('View Expert', je()->domain),
                        'search_items' => __('Search Expert', je()->domain),
                        'not_found' => __('No experts found', je()->domain),
                        'not_found_in_trash' => __('No experts found in Trash', je()->domain),
                        'custom_fields_block' => __('Expert fields', je()->domain),
                    ),
                'supports' =>
                    array(
                        'title' => 'title',
                        'editor' => 'editor',
                        'author' => 'author',
                        'thumbnail' => 'thumbnail',
                        'excerpt' => false,
                        'revisions' => 'revisions',
                        'post-formats' => 'post-formats'
                        //'page_attributes' => 'page-attributes',
                    ),
                'supports_reg_tax' =>
                    array(
                        'category' => '',
                        'post_tag' => '',
                    ),
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'description' => __('Expert and extended profile', je()->domain),
                'menu_position' => '',
                'public' => true,
                'hierarchical' => true,
                'has_archive' => apply_filters('jbp_pro_archive_slug', 'experts'),
                'rewrite' =>
                    array(
                        'slug' => apply_filters('jbp_expert_single_slug', 'expert'),
                        'with_front' => false,
                        'feeds' => true,
                        'pages' => true,
                        'ep_mask' => 4096,
                    ),
                'query_var' => true,
                'can_export' => true,
                'cf_columns' => NULL,
                'menu_icon' => je()->plugin_url . 'assets/image/backend/icons/16px/16px_Expert_Bright.svg',
            );
            register_post_type('jbp_pro', apply_filters('je_experts_posttype_param', $jbp_pro));
        } //jbp_pro post type complete

        if (!taxonomy_exists('jbp_category')) {
            $jbp_category = array(
                'object_type' => array(
                    0 => 'jbp_job',
                ),
                'hide_type' => array(
                    0 => 'jbp_job',
                ),
                'args' => array(
                    'labels' => array(
                        'name' => __('Job Categories', je()->domain),
                        'singular_name' => __('Job Category', je()->domain),
                        'add_new_item' => __('Add New Job Categories', je()->domain),
                        'new_item_name' => __('New Job Category', je()->domain),
                        'edit_item' => __('Edit Job Category', je()->domain),
                        'update_item' => __('Update Job Category', je()->domain),
                        'popular_items' => __('Search Job Categories', je()->domain),
                        'all_items' => __('All Job Categories', je()->domain),
                        'parent_item' => __('Job Categories', je()->domain),
                        'parent_item_colon' => __('Job Categories: ', je()->domain),
                        'add_or_remove_items' => __('Add or Remove Job Categories', je()->domain),
                        'choose_from_most_used' => __('All Job Categories', je()->domain),
                    ),
                    'public' => true,
                    'show_admin_column' => NULL,
                    'hierarchical' => true,
                    'rewrite' => array(
                        'slug' => 'jobs-category',
                        'with_front' => true,
                        'hierarchical' => false,
                        'ep_mask' => 0,
                    ),
                    'query_var' => true,
                    'capabilities' => array(
                        'manage_terms' => 'manage_categories',
                        'edit_terms' => 'manage_categories',
                        'delete_terms' => 'manage_categories',
                        //'assign_terms' => 'edit_jobs',
                    ),
                ),

            );

            register_taxonomy('jbp_category', array('jbp_job'), $jbp_category['args']);
        }

        if (!taxonomy_exists('jbp_skills_tag')) {
            $jbp_tag = array(
                'object_type' => array(
                    0 => 'jbp_job',
                ),
                'hide_type' => array(
                    0 => 'jbp_job',
                ),
                'args' => array(
                    'labels' => array(
                        'name' => __('Job Skills Tags', je()->domain),
                        'singular_name' => __('Job Skills Tag', je()->domain),
                        'add_new_item' => __('Add New Job Skills Tag', je()->domain),
                        'new_item_name' => __('New Job Skills Tag', je()->domain),
                        'edit_item' => __('Edit Job Skills Tag', je()->domain),
                        'update_item' => __('Update Job Skills Tag', je()->domain),
                        'search_items' => __('Search Job Skills Tags', je()->domain),
                        'popular_items' => __('Popular Job Skills Tags', je()->domain),
                        'all_items' => __('All Job Skills Tags', je()->domain),
                        'parent_item_colon' => __('Jobs tags:', je()->domain),
                        'add_or_remove_items' => __('Add or Remove Job Skills Tags', je()->domain),
                        'choose_from_most_used' => __('All Job Skills Tags', je()->domain),
                    ),
                    'public' => true,
                    'hierarchical' => false,
                    'rewrite' =>
                        array(
                            'slug' => 'job-skills',
                            'with_front' => true,
                            'hierarchical' => false,
                            'ep_mask' => 0,
                        ),
                    'query_var' => true,
                    'capabilities' => array(
                        'manage_terms' => 'manage_categories',
                        'edit_terms' => 'manage_categories',
                        'delete_terms' => 'manage_categories',
                        //'assign_terms' => 'edit_jobs',
                    ),
                ),
            );

            register_taxonomy('jbp_skills_tag', array('jbp_job'), $jbp_tag['args']);
        }

        if (is_admin()) {
            register_post_status('virtual', array(
                'label' => __('Virtual', je()->domain),
                'public' => false,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => false,
                'show_in_admin_status_list' => apply_filters('jpb_virtual_status_show_in_admin_status_list', true),
                'label_count' => _n_noop('Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>'),
            ));
        } else {
            //we allowed this status available on frontend to make it compatibility with other themes
            register_post_status('virtual', array(
                'label' => __('Virtual', je()->domain),
                'public' => apply_filters('jpb_virtual_status_public', true),
                'exclude_from_search' => true,
                'show_in_admin_all_list' => false,
                'show_in_admin_status_list' => apply_filters('jpb_virtual_status_show_in_admin_status_list', true),
                'label_count' => _n_noop('Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>'),
            ));
        }
        register_post_status('je-draft', array(
            'label' => __('Temp', je()->domain),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => false,
            'show_in_admin_status_list' => false,
        ));

        if (get_option('je_rewrite') != 1) {
            flush_rewrite_rules();
            update_option('je_rewrite', 1);
        }
    }
}