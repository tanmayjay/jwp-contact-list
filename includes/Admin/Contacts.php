<?php

namespace JWP\JCL\Admin;

use JWP\JCL\Traits\Form_Errors;

/**
 * AContacts handler class
 */
class Contacts {

    use Form_Errors;

    /**
     * Determines the plugin page
     *
     * @return void
     */
    public function page() {

        $action = isset( $_GET['action'] ) ? $_GET['action']   : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ( $action ) {
            case 'new':
                $contact = jwp_cl_get_contact( $id );
                $view = __DIR__ . '/views/contact-new.php';
                break;
            
            case 'edit':
                $contact = jwp_cl_get_contact( $id );
                $view = __DIR__ . '/views/contact-edit.php';
                break;
            
            case 'view':
                $contact = jwp_cl_get_contact( $id );
                $view = __DIR__ . '/views/contact-view.php';
                break;
            
            default:
                $view = __DIR__ . '/views/contact-list.php';
                break;  
        }

        if ( file_exists( $view ) ) {
            include $view;
        }
    }

    /**
     * 
     * Handles the form
     * 
     * @return void
     */
     public function form_handler() {

        if ( ! isset( $_POST['submit_contact'] ) ) {
            return;   
        }

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact-new' ) ) {
            wp_die( 'cheater detected!' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'cheater detected!' );
        }

        $id      = isset( $_POST['id'] ) ? intval( $_POST['id'] )                            : 0;
        $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] )           : ''; 
        $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] )         : ''; 
        $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] )              : ''; 
        $address = isset( $_POST['address'] ) ? sanitize_textarea_field( $_POST['address'] ) : ''; 

        if ( empty( $name ) ) {
            $this->errors['name']   = __( 'Name cannot be empty', JWP_CL_DOMAIN );
        }

        if ( empty( $phone ) ) {
            $this->errors['phone'] = __( 'Phone cannot be empty', JWP_CL_DOMAIN );
        }

        if ( ! empty( $this->errors ) ) {
            return;
        }

        $args = [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email,
            'address' => $address
        ];

        if ( $id ) {
            $args['id'] = $id;
        }

        jwp_cl_insert_contact( $args );

        if ( is_wp_error( $insert_id ) ) {
            wp_die( $insert_id->get_error_message );
        }
        
        if ( $id ) {
            $redirected_to = admin_url( 'admin.php?page=jwp-contacts&updated=true', 'admin' );
        } else {
            $redirected_to = admin_url( 'admin.php?page=jwp-contacts&inserted=true', 'admin' );
        }
        
        wp_redirect( $redirected_to );
        
        exit;
     }

    /**
     * Deletes a contact
     *
     * @param int $id
     *
     * @return void
     */
     public function delete_contact() {

        if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'jwp-cl-delete-contact' ) ) {
            wp_die( 'cheater detected!' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'cheater detected!' );
        }

        $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ): 0;

        if ( jwp_cl_delete_contact( $id ) ) {
            $redirected_to = admin_url( 'admin.php?page=jwp-contacts&contact-deleted=true' );
        } else {
            $redirected_to = admin_url( 'admin.php?page=jwp-contacts&contact-deleted=false' );
        }

        wp_redirect( $redirected_to );
        exit;
     }
}