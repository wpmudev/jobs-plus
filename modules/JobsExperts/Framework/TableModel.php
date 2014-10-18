<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_TableModel extends JobsExperts_Framework_Model {

    public function get_one($query = null, $params = array()) {
        global $wpdb;
        $sql = "SELECT * FROM " . $wpdb->prefix . $this->storage_name();

        if ( ! empty( $query ) ) {
            $sql .= ' WHERE ' . $query;
        }
        $row = $wpdb->get_row( $wpdb->prepare( $sql, $params ) );

        if ( ! empty( $row ) ) {
            return $this->fetch_model( $row );
        } else {
            return null;
        }
    }

    public function get_all( $query = null, $params = array(), $order = '', $order_by = '', $limit = '') {
        global $wpdb;
        $sql = "SELECT * FROM " . $wpdb->prefix . $this->storage_name();
        if ( ! empty( $query ) ) {
            $sql .= ' WHERE ' . $query;
        }

        if ( ! empty( $order_by ) ) {
            $sql .= 'ORDER BY ' . $order_by . trim( ' ' . $order );
        }

        if ( ! empty( $limit ) ) {
            $sql .= ' LIMIT ' . $limit;
        }

        $results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
        $models  = array();
        if ( ! empty( $results ) && is_array( $results ) ) {
            $class = get_called_class();
            foreach ( $results as $row ) {
                $models[] = $this->fetch_model( $row );
            }
        }

        return $models;
    }

    public function load( $id ) {
        global $wpdb;
        $sql = "SELECT * FROM " . $wpdb->prefix . $this->storage_name() . ' WHERE id=%d';
        $row = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );

        if ( ! empty( $row ) ) {
            return $this->fetch_model( $row );
        } else {
            return null;
        }

    }

    public function save() {
        global $wpdb;
        $this->before_save();
        $wpdb->insert( $wpdb->prefix . $this->tbl_name(), $this->export() );
        $this->after_save();
        $this->id = $wpdb->insert_id;

        return true;
    }

    public function update() {
        global $wpdb;
        $this->before_save();
        $wpdb->update( $wpdb->prefix . $this->tbl_name(), $this->export(), array(
            'id' => $this->id
        ) );
        $this->after_save();

        return true;
    }

    public function delete() {
        global $wpdb;
        $wpdb->delete( $wpdb->prefix . $this->tbl_name(), array(
            'id' => $this->id
        ) );
    }

    protected function fetch_model( $data ) {
        $class = get_called_class();
        $model = new $class();
        $model->import( $data );

        return $model;
    }
}