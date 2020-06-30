<?php

/**
 * Plugin Name:       JWP Contact List
 * Plugin URI:        https://github.com/tanmayjay/jwp-contact-list
 * Description:       A plugin to store contacts by creating a admin menu and contain REST API CRUD services.
 * Version:           1.1.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Tanmay Kirtania
 * Author URI:        https://linkedin.com/in/tanmay-kirtania
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jwp-contact-list
 * 
 * 
 * Copyright (c) 2020 Tanmay Kirtania (jktanmay@gmail.com). All rights reserved.
 * 
 * This program is a free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see the License URI.
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class JWP_Contact_List {
    
    /**
     * Static class object
     *
     * @var object
     */
    private static $instance;

    const version   = '1.1.2';
    const domain    = 'jwp-contact-list';

    /**
     * Private class constructor
     */
    private function __construct() {
        $this->define_constants();
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Private class cloner
     */
    private function __clone() {}

    /**
     * Initializes a singleton instance
     * 
     * @return \JWP_Contact_List
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Defines the required constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'JWP_CL_VERSION', self::version );
        define( 'JWP_CL_FILE', __FILE__ );
        define( 'JWP_CL_PATH', __DIR__ );
        define( 'JWP_CL_URL', plugins_url( '', JWP_CL_FILE ) );
        define( 'JWP_CL_ASSETS', JWP_CL_URL . '/assets' );
        define( 'JWP_CL_DOMAIN', self::domain );
    }

    /**
     * Updates info on plugin activation
     *
     * @return void
     */
    public function activate() {
        $activator = new JWP\JCL\Activator();
        $activator->run();
    }

    /**
     * Initializes the plugin
     *
     * @return void
     */
    public function init_plugin() {

        load_plugin_textdomain( JWP_CL_DOMAIN, false, dirname( plugin_basename( __file__ ) ) . '/assets/languages' );

        new JWP\JCL\Assets();
        
        if ( is_admin() ) {
            new JWP\JCL\Admin();
        }

        new JWP\JCL\API();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \JWP_Contact_List
 */
function jwp_contact_list() {
    return JWP_Contact_List::get_instance();
}

//kick off the plugin
jwp_contact_list();