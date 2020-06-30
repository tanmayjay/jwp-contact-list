<?php

namespace JWP\JCL\Admin;

/**
 * Settings handler class
 */
class Settings {

    /**
     * Initializes settings page
     *
     * @return void
     */
    public function settings_page_init() {
        register_setting( 'jwp-settings', 'jwp_cl_number_contacts' );
        register_setting( 'jwp-settings', 'jwp_cl_order' );

        add_settings_section( 
            'jwp_cl_settings_section', 
            __( 'Contact List Settings', JWP_CL_DOMAIN ), 
            null, 
            'jwp-settings' 
        );

        add_settings_field( 
            'jwp_cl_number_contacts', 
            __( 'Number of Contacts', JWP_CL_DOMAIN ), 
            [ $this, 'number_contacts_select_list' ], 
            'jwp-settings', 
            'jwp_cl_settings_section',
            [ 
                'label_for'       => 'jwp_cl_number_contacts',
                'total_contacts'  => jwp_cl_contacts_count(),
                'number_contacts' => 10   
            ]
        );

        add_settings_field( 
            'jwp_cl_order', 
            __( 'Select Order', JWP_CL_DOMAIN ), 
            [ $this, 'order_select_list' ], 
            'jwp-settings', 
            'jwp_cl_settings_section',
            [ 
                'label_for' => 'jwp_cl_order',
                'orders'    => [
                    __( 'Ascending', JWP_CL_DOMAIN )  => 'asc', 
                    __( 'Descending', JWP_CL_DOMAIN ) => 'desc',
                ]  
            ]
        );
    }

    /**
     * Renders select list for number of contacts
     *
     * @param array $args
     * 
     * @return void
     */
    public function number_contacts_select_list( $args ) {
        $field_id       = $args['label_for'];
        $total_contacts = $args['total_contacts'];
        $value          = get_option( $field_id );
        
        if ( ! $value ) {
            $value = $args['number_contacts'];
        }
        
        printf( "<select name='%s' id='%s'>", $field_id, $field_id );

        for ( $i = 1; $i <= $total_contacts; ++ $i ) {
            $selected = '';

            if ( $i == $value ) {
                $selected = 'selected';
            }

            printf( "<option value=%d %s>%d</option>", $i, $selected, $i );
        }
        
        printf( "</select>" );
    }

    /**
     * Renders select list for order
     *
     * @param array $args
     * 
     * @return void
     */
    public function order_select_list( $args ) {
        $field_id     = $args['label_for'];
        $orders       = $args['orders'];
        $option_value = get_option( $field_id );
        
        if ( ! $option_value ) {
            $option_value = $orders['Ascending'];
        }
        
        printf( "<select name='%s' id='%s'>", $field_id, $field_id );

        foreach ( $orders as $order => $value ) {
            $selected = '';

            if ( $value == $option_value ) {
                $selected = 'selected';
            }

            printf( "<option value=%s %s>%s</option>", $value, $selected, $order );
        }
        
        printf( "</select>" );
    }

    /**
     * Renders setting actions
     *
     * @return void
     */
    public function settings_actions() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'jwp_cl_messages', 'jwp_cl_message', __( 'Settings Saved', JWP_CL_DOMAIN ), 'updated' );
        }
        
        settings_errors( 'jwp_cl_messages' );
        ?>
        <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
        <?php
        settings_fields( 'jwp-settings' );

        do_settings_sections( 'jwp-settings' );

        submit_button( __( 'Save Settings', JWP_CL_DOMAIN ) );
        ?>
        </form>
        </div>
        <?php
    }
}