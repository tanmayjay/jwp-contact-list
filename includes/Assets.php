<?php

namespace JWP\JCL;

/**
 * Assets handler class
 */
class Assets {
    /**
     * Class constructor
     */
    function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets'] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets'] );
    }

    /**
     * Retrives stylesheet info
     *
     * @return object
     */
    public function get_styles() {

        return [
            'jwp-cl-admin-style' => [
                'src'  => JWP_CL_ASSETS . '/css/style.css',
                'deps' => false,
                'ver'  => filemtime( JWP_CL_PATH . '/assets/css/style.css' ),
            ]
        ];
    }

    /**
     * Registers the assets
     *
     * @return void
     */
    public function register_assets() {

        $styles = $this->get_styles();

        foreach ( $styles as $handle => $style ) {

            wp_register_style( $handle, $style['src'], $style['deps'], $style['ver'] );
        }
    }
}