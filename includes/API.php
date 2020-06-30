<?php

namespace JWP\JCL;

/**
 * API class
 */
class API {
    
    /**
     * Class constructor
     */
    function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_api' ] );
    }

    /**
     * Registers API
     *
     * @return void
     */
    public function register_api() {
        $contacts = new API\Contacts;
        $contacts->register_routes();
    }
}