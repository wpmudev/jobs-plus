<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Author: hoangngo
 */
class JE_AddOn_Table extends WP_List_Table
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

    function get_table_classes()
    {
        return array('widefat', 'fixed', $this->_args['plural'], 'table', 'table-bordered');
    }

    function get_columns()
    {
        return $columns = array(
            'col_name' => __('Name', je()->domain),
            'col_description' => __('Description', je()->domain),
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
        $setting = new JE_Settings_Model();
        $setting->load();
        $components = $setting->plugins;
        if (!is_array($components)) {
            $components = array();
        }
        if (in_array($item['col_id'], $components)) {
            $html .= '<br><a class="mm-plugin" data-type="deactive" data-id="' . esc_attr($item['col_id']) . '" href="#">' . __('Deactivate <i class="fa fa-circle-o-notch fa-spin loader-ani hide"></i>', je()->domain) . '</a>';
        } else {
            //check the requirement
            if (!empty($item['required'])) {
                if (is_plugin_active($item['required'])) {
                    $html .= '<br><a class="mm-plugin" data-type="active" data-id="' . esc_attr($item['col_id']) . '"  href="#">' . __('Activate <i class="fa fa-circle-o-notch fa-spin loader-ani hide"></i>', je()->domain) . '</a>';
                } else {
                    //find the required plugin data
                    $plugin_path = ABSPATH . 'wp-content/plugins/' . $item['required'];
                    $plugin_data = null;
                    if( file_exists( $plugin_path ) ) {
                        $plugin_data = get_plugin_data($plugin_path);
                    }
                    $text = '';
                    if ( is_array( $plugin_data ) && count( $plugin_data ) ) {
                        $text = sprintf(__("Plugin <strong>%s</strong> required", je()->domain), $plugin_data['Name']);
                    } else {
                        $text = sprintf(__("Plugin <strong>%s</strong> required", je()->domain), $item['required']);
                    }
                    $html .= '<br/>' . $text;
                }
            } else {
                $html .= '<br><a class="mm-plugin" data-type="active" data-id="' . esc_attr($item['col_id']) . '"  href="#">' . __('Activate <i class="fa fa-circle-o-notch fa-spin loader-ani hide"></i>', je()->domain) . '</a>';
            }
        }

        return $html;
    }

    public function column_col_description($item)
    {
        return $item['col_description'] . '<br/>' . __('Created by: ', je()->domain) . '<strong>' . $item['col_author'] . '</strong>';
    }

    public function column_col_action($item)
    {

    }

    public function display()
    {
        $singular = $this->_args['singular'];

        ?>
        <table class="wp-list-table <?php echo implode(' ', $this->get_table_classes()); ?>">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <?php $this->print_column_headers(false); ?>
            </tr>
            </tfoot>

            <tbody id="the-list"<?php
            if ($singular) {
                echo " data-wp-lists='list:$singular'";
            } ?>>
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
        </table>
        <?php
        $this->display_tablenav('bottom');
    }

    function prepare_items()
    {
        $data = je()->get_available_addon();
        $items = array();
        foreach ($data as $key => $val) {
            $items[] = array(
                'col_id' => $key,
                'col_name' => $val['Name'],
                'col_description' => $val['Description'],
                'col_author' => $val['Author'],
                'required' => $val['Required']
            );
        }

        //How many to display per page?
        $perpage = 10;
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