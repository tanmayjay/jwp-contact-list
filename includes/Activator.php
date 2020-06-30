<?php

namespace JWP\JCL;

/**
 * Plugin activator handler class
 */
class Activator {

    /**
     * Runs the plugin activator
     * 
     * @return void
     */
    public function run() {
        $this->add_info();
        $this->create_tables();
    }

    /**
     * Adds pluging activation info
     * 
     * @return void
     */
    public function add_info(){
        $installed = get_option( 'jwp_cl_installed' );

        if( ! $installed ) {
            update_option( 'jwp_cl_installed', time() );
        }

        update_option( 'jwp_cl_version', JWP_CL_VERSION );
    }

    /**
     * Creates the required tables
     * 
     * @return void
     */
    public function create_tables(){
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}jcl_contacts` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `address` varchar(255) DEFAULT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `email` varchar(20) DEFAULT NULL,
            `created_by` int(20) UNSIGNED NOT NULL,
            `created_at` datetime NOT NULL DEFAULT current_timestamp(),
            `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
          ) $charset_collate;";

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $schema );
    }
}