<?php

namespace JWP\JCL\Admin;

if ( ! class_exists( 'WP_List_Table') ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class List_Contact extends \WP_List_Table {

    /**
     * Class constructor
     */
    function __construct() {
        
        parent::__construct( [
            'singular' => 'contact',
            'plural'   => 'contacts',
            'ajax'     => false
        ] );
    }

    public function get_columns() {
        
        return [
            'cb'         => '<input type="checkbox" />',
            'name'       => __( 'Name', JWP_CL_DOMAIN ),
            'phone'      => __( 'Phone', JWP_CL_DOMAIN ),
            'email'      => __( 'Email', JWP_CL_DOMAIN ),
            'address'    => __( 'Address', JWP_CL_DOMAIN ),
            'created_at' => __( 'Date', JWP_CL_DOMAIN )
        ];
    }

    public function get_sortable_columns() {
        $sortable_columns = [
            'name'       => [ 'name', true ],
            'created_at' => [ 'created_at', true ]
        ];

        return $sortable_columns;
    }

    protected function column_default( $item, $column_name ) {
        return isset( $item->$column_name ) ? $item->$column_name : '';
    }

    public function column_name( $item ) {
        $actions = [];

        $actions['edit'] = sprintf(
            '<a href="%s" title="%s">%s</a>',
            admin_url( 'admin.php?page=jwp-contacts&action=edit&id=' . $item->id ),
            __( 'Edit', JWP_CL_DOMAIN ),
            __( 'Edit', JWP_CL_DOMAIN )
        );

        $actions['delete'] = sprintf(
            '<a href="%s" class="submitdelete" onclick="return confirm(\'Are yoy sure?\')" title="%s">%s</a>',
            wp_nonce_url( admin_url( 'admin-post.php?action=jwp_cl_delete_contact&id=' . $item->id ), 'jwp-cl-delete-contact' ),
            __( 'Delete', JWP_CL_DOMAIN ),
            __( 'Delete', JWP_CL_DOMAIN )
        );

        return sprintf(
            '<a href="%1$s"><strong>%2$s</strong></a> %3$s', 
            admin_url( 'admin.php?page=jwp-contacts&action=view&id=' . $item->id ), 
            $item->name,
            $this->row_actions( $actions )
        );
    }

    protected function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="contact_id[]" value="%d" />',
            $item->id
        );
    }

    public function prepare_items() {
        $column       = $this->get_columns();
        $hidden       = [];
        $sortable     = $this->get_sortable_columns();
        $per_page     = get_option( 'jwp_cl_number_contacts', 10 );
        $current_page = $this->get_pagenum();
        $offset       = ( $current_page - 1 ) * $per_page;
        $orderby      = 'id';
        $order        = get_option( 'jwp_cl_order', 'asc' );

        $args = [
            'number'  => $per_page,
            'offset'  => $offset,
            'orderby' => $orderby,
            'order'   => $order
        ];

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order']   = $_REQUEST['order'];
        }

        $this->_column_headers = [ $column, $hidden, $sortable ];
        $this->items           = jwp_cl_get_contacts( $args );
        
        $this->set_pagination_args( [
            'total_items' => jwp_cl_contacts_count(),
            'per_page'    => $per_page
        ] );
    }
}