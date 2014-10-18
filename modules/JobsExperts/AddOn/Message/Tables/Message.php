<?php

class JobsExperts_AddOn_Message_Tables_Message extends WP_List_Table
{

    public function __construct($model, $per_page = 20)
    {
        $this->model = $model;
        $this->post_type = 'jbp_message';
        $this->per_page = $per_page;

        $pt_object = get_post_type_object($this->post_type);
        parent::__construct(array(
            'singular' => $pt_object->labels->singular_name, //Singular label
            'plural' => $pt_object->labels->name, //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
    }

    public function get_columns()
    {
        //columns will base on the $model
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'subject' => __("Subject", JBP_TEXT_DOMAIN),
            'from' => __("From", JBP_TEXT_DOMAIN),
            'to' => __("To", JBP_TEXT_DOMAIN),
            'action' => ''
        );
        return $columns;
    }

    public function get_bulk_actions()
    {
        return array(
            'delete' => __('Delete', JBP_TEXT_DOMAIN),
        );
    }

    public function process_bulk_action()
    {
        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

            $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die('Nope! Security check failed!');

        }

        $action = $this->current_action();

        switch ($action) {
            case 'delete':
                $class = get_class($this->model);
                if (isset($_POST[$class])) {
                    foreach ($_POST[$class] as $id) {
                        $model = $class::find($id);
                        if (is_object($model)) {
                            $model->delete();
                        }
                    }
                }
                break;
        }

        return;
    }


    public function get_hidden_columns()
    {
        return array('id');
    }

    public function get_sortable_columns()
    {
        $columns = array(
            'subject' => "post_title",
        );
        return $columns;
    }

    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="%s[]" value="%d" />', $this->_args['singular'], $item->id);
    }

    function column_subject($item)
    {
        return $item->subject;
    }

    function column_from($item)
    {
        global $jbp_message;
        return $jbp_message->getFullName($item->send_from);
    }

    function column_to($item)
    {
        global $jbp_message;
        return $jbp_message->getFullName($item->send_to);
    }

    function column_action($item)
    {
        return sprintf('<a href="%s">' . __('View', JBP_TEXT_DOMAIN) . '</a>',admin_url('edit.php?post_type=jbp_pro&page=message-main-view&id='.$item->id));
    }

    function prepare_items()
    {
        $this->process_bulk_action();
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();

        $args = array();
        $class = get_class($this->model);
        //getting items
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
        if (!empty($orderby) & !empty($order)) {
            $args['orderby'] = $orderby;
            $args['order'] = $order;
        }
        $tmp_items = $class::instance()->get_all();
        $totalitems = $tmp_items['total'];
        $perpage = $this->per_page;
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        //getting the items for this page
        $args['pagination'] = true;
        $args['posts_per_page'] = $this->per_page;
        $args['paged'] = $paged;
        //update the ordering

        $this->items = $class::instance()->get_all($args);
        $this->items = $this->items['data'];
        /* -- Register the pagination -- */
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    }

}