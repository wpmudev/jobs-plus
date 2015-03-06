<?php

/**
 * @author:Hoang Ngo
 */
class Credit_Plan_Model extends IG_Model
{
    public $title;
    public $credits;
    public $cost;
    public $sale_price;
    public $description;
    public $product_id;
    public $append_credits_info;

    protected $rules = array(
        'title' => 'required',
        'credits' => 'required|integer|min_numeric,0',
        'cost' => 'required|numeric|min_numeric,0',
        'sale_price' => 'numeric|min_numeric,0'
    );

    public static function find_all()
    {
        $options = get_option('ig_credit_plan');
        if (!$options) {
            $options = array();
        }
        $data = array();
        foreach ($options as $option) {
            $model = new Credit_Plan_Model();
            $model->import($option);
            $data[] = $model;
        }
        return $data;
    }

    public static function find($id)
    {
        $options = get_option('ig_credit_plan');
        if (!$options) {
            $options = array();
        }
        foreach ($options as $row) {
            if ($row['product_id'] == $id) {
                $model = new Credit_Plan_Model();
                $model->import($row);
                return $model;
            }
        }
        return null;
    }

    public static function delete_plan($id)
    {
        $options = get_option('ig_credit_plan');
        if (!$options) {
            $options = array();
        }
        foreach ($options as $key => $row) {
            if ($row['product_id'] == $id) {
                unset($options[$key]);
            }
        }
        update_option('ig_credit_plan', $options);
    }

    public function add_plan($name, $detail, $cost, $credits, $sale_price, $product_id = '', $append_info = '')
    {
        if ($product_id) {
            //update the product
            $product['ID'] = $product_id;
            $product['post_title'] = $name;
            $product['post_content'] = $detail;
            wp_update_post($product);
            $id = $product_id;
        } else {
            //create new product
            //import product
            $product['post_title'] = $name;
            $product['post_content'] = $detail;
            $product['post_type'] = 'product';
            $product['comment_status'] = 'closed';
            $product['comment_count'] = 0;
            $product['post_status'] = 'publish';
            $id = wp_insert_post($product); //create the post
        }
        //update meta
        //add product meta
        update_post_meta($id, 'mp_price', array(round((float)preg_replace('/[^0-9.]/', '', $cost), 2))); //add price
        update_post_meta($id, 'mp_var_name', array('')); //add blank var name
        update_post_meta($id, 'mp_track_inventory', 0);
        update_post_meta($id, 'mp_file', esc_url_raw(home_url()));
        //assign category
        wp_set_object_terms($id, 'je-credits', 'product_category');

        if ($this->sale_price) {
            update_post_meta($id, 'mp_is_sale', 1);
            update_post_meta($id, 'mp_sale_price', array(round((float)preg_replace('/[^0-9.]/', '', $sale_price), 2)));
            update_post_meta($id, 'mp_price_sort', round((float)preg_replace('/[^0-9.]/', '', $sale_price), 2));
        } else {
            update_post_meta($id, 'mp_is_sale', 0);
            update_post_meta($id, 'mp_price_sort', round((float)preg_replace('/[^0-9.]/', '', $sale_price), 2));
        }
        update_post_meta($id, 'je_wallet_append_info', 1);

        $options = get_option('ig_credit_plan');
        if (!$options) {
            $options = array();
        }
        if ($product_id) {
            foreach ($options as $key => $row) {
                if ($row['product_id'] == $product_id) {
                    $options[$key] = array(
                        'title' => $name,
                        'description' => $detail,
                        'cost' => $cost,
                        'credits' => $credits,
                        'product_id' => $id,
                        'sale_price' => $sale_price,
                        'append_credits_info' => $append_info,
                    );
                    break;
                }
            }
        } else {
            $options[] = array(
                'title' => $name,
                'description' => $detail,
                'cost' => $cost,
                'credits' => $credits,
                'product_id' => $id,
                'sale_price' => $sale_price,
                'append_credits_info' => $append_info
            );
        }
        update_option('ig_credit_plan', $options);
        return $product_id;
    }
}