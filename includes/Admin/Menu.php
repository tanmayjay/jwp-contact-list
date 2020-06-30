<?php

namespace JWP\JCL\Admin;

/**
 * Menu handler class
 */
class Menu {

    public $contacts;
    public $settings;

    /**
     * Class constructor
     */
    function __construct( $contacts ) {
        $this->contacts = $contacts;
        $this->settings = new Settings();
        $this->init_actions();
    }

    /**
     * Initializes the actions
     *
     * @return void
     */
    public function init_actions() {
        add_action( 'admin_init', [ $this->settings, 'settings_page_init' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Adds an adin menu and submenu
     *
     * @return void
     */
    public function admin_menu() {
        wp_enqueue_script( 'jwp-cl-admin-style' );

        $parent_slug = 'jwp-contacts';
        $capability  = 'manage_options';

        add_menu_page( 
            __( 'JWP Contact List', JWP_CL_DOMAIN ), 
            __( 'Contacts', JWP_CL_DOMAIN ), 
            $capability, 
            $parent_slug, 
            [ $this, 'contact_page' ], 
            'dashicons-list-view'
        );

        add_submenu_page( 
            $parent_slug, 
            __( 'JWP Contact List', JWP_CL_DOMAIN ), 
            __( 'View Contacts', JWP_CL_DOMAIN ), 
            $capability, 
            $parent_slug, 
            [ $this, 'contact_page' ]
        );

        add_submenu_page(
            $parent_slug, 
            __( 'Settings', JWP_CL_DOMAIN ), 
            __( 'Settings', JWP_CL_DOMAIN ), 
            $capability, 
            'jwp-settings', 
            [ $this, 'settings_page' ] 
        );
    }

    /**
     * Handles the contact menu
     *
     * @return void
     */
    public function contact_page() {
        $this->contacts->page();
    }

    /**
     * Handles the settings menu
     *
     * @return void
     */
    public function settings_page() {
        $this->settings->settings_actions();
    }
}