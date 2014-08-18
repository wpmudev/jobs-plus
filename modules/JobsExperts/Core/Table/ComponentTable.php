<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Table_ComponentTable extends WP_List_Table
{
    /**
     * Constructor.
     *
     * @since 1.0.0
     *
     * @access public
     * @param array $args The array of arguments.
     */
    public function __construct($args = array())
    {
        parent::__construct(array_merge(array(
            'plural' => 'plugins',
            'autoescape' => false,
        ), $args));
    }

    function get_columns()
    {
        return $columns = array(
            'col_name' => __('Name', JBP_TEXT_DOMAIN),
            'col_description' => __('Description', JBP_TEXT_DOMAIN),
        );
    }

    function column_default($item, $column_name)
    {
        $value = '';
        switch ($column_name) {
            default        :
                $value = $item[$column_name];
                break;
        }
        return $value;
    }

    public function column_col_name($item)
    {
        $html = $item['col_name'];
        $setting = JobsExperts_Plugin::instance()->settings();
        $components = explode(',', $setting->plugins);
        if (!is_array($components)) {
            $components = array();
        }
        if (in_array($item['col_id'], $components)) {
            $html .= '<br><a class="plugin" data-type="deactive" data-id="' . esc_attr($item['col_id']) . '" href="#">' . __('Deactive', JBP_TEXT_DOMAIN) . '</a>';
        } else {
            $html .= '<br><a class="plugin" data-type="active" data-id="' . esc_attr($item['col_id']) . '"  href="#">' . __('Active', JBP_TEXT_DOMAIN) . '</a>';
        }

        return $html;
    }

    public function column_col_description($item)
    {
        return $item['col_description'] . '<br/>' . __('Created by: ', JBP_TEXT_DOMAIN) . '<strong>' . $item['col_author'] . '</strong>';
    }

    public function column_col_action($item)
    {


    }

    function prepare_items()
    {
        $data = JobsExperts_Components::get_available_components();
        $items = array();
        foreach ($data as $key => $val) {
            $items[] = array(
                'col_id' => $key,
                'col_name' => $val['Name'],
                'col_description' => $val['Description'],
                'col_author' => $val['Author']
            );
        }

        //How many to display per page?
        $perpage = 5;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $offset = ($this->get_pagenum() - 1) * $perpage;
        //How many pages do we have in total?
        $totalpages = ceil(count($items) / $perpage);
        //adjust the query to take pagination into account
        /* -- Register the pagination -- */
        $this->set_pagination_args(array(
            "total_items" => count($items),
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters

        /* — Register the Columns — */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->items = array_slice($items, $offset, $perpage);
    }
}