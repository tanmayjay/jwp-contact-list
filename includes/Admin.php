<?php

namespace JWP\JCL;

/**
 * The Admin class
 */
class Admin {
    
    /**
     * Class constructor
     */
    function __construct() {
        $contacts = new Admin\Contacts();
        
        $this->dispatch_actions( $contacts );
        
        new Admin\Menu( $contacts );
    }

    /**
     * Dispatches Admin actions
     * 
     * @return void
     */
    public function dispatch_actions ( $contacts ) {
       add_action( 'admin_init', [ $contacts, 'form_handler' ] );
       add_action( 'admin_post_jwp_cl_delete_contact', [ $contacts, 'delete_contact' ] );
    }
}