<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Edit Contact', JWP_CL_DOMAIN ) ?></h1>
    <form action="" method="post">
        <table class="form-table">
            <tr class="row <?php echo $this->has_error( 'name' ) ? 'form-invalid' : ''; ?>">
                <th scope="row">
                    <label for="name"><?php _e( 'Name', JWP_CL_DOMAIN ) ?></label>
                </th>
                <th>
                    <input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_attr( $contact->name ); ?>">

                    <?php if ( $this->has_error( 'name' ) ) { ?>
                        <p class="description error"><?php echo $this->get_error( 'name' ) ?></p>
                    <?php } ?>

                </th>
            </tr>
            <tr class="row <?php echo $this->has_error( 'name' ) ? 'form-invalid' : ''; ?>">
                <th scope="row">
                    <label for="phone"><?php _e( 'Phone', JWP_CL_DOMAIN ) ?></label>
                </th>
                <th>
                    <input type="text" name="phone" id="phone" class="regular-text" value="<?php echo esc_attr( $contact->phone ); ?>">
                    
                    <?php if ( $this->has_error( 'phone' ) ) { ?>
                        <p class="description error"><?php echo $this->get_error( 'phone' ) ?></p>
                    <?php } ?>

                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="email"><?php _e( 'Email', JWP_CL_DOMAIN ) ?></label>
                </th>
                <th>
                    <input type="email" name="email" id="email" class="regular-text" value="<?php echo esc_attr( $contact->email ); ?>">
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="address"><?php _e( 'Address', JWP_CL_DOMAIN ) ?></label>
                </th>
                <th>
                    <textarea name="address" id="address" class="regular-text"><?php echo esc_textarea( $contact->address ); ?></textarea>
                </th>
            </tr>
        </table>
        <input type="hidden" name="id" value="<?php echo esc_attr( $contact->id ); ?>">
        <?php 
        wp_nonce_field( 'contact-new' );
        submit_button( __('Update Contact', JWP_CL_DOMAIN), 'primary', 'submit_contact', true ) 
        ?>
    </form>
</div>