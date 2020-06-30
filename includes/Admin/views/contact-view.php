<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'View Contact', JWP_CL_DOMAIN ) ?></h1>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label class="jcl-label" for="name"><?php _e( 'Name', JWP_CL_DOMAIN ) ?></label>
            </th>
            <th>
                <p class="single-contact"><?php echo esc_attr( $contact->name ); ?></p>
            </th>
        </tr>
        <tr class="row <?php echo $this->has_error( 'name' ) ? 'form-invalid' : ''; ?>">
            <th scope="row">
                <label class="jcl-label" for="phone"><?php _e( 'Phone', JWP_CL_DOMAIN ) ?></label>
            </th>
            <th>
                <p class="single-contact"><?php echo esc_attr( $contact->phone ); ?></p>
            </th>
        </tr>
        <tr>
            <th scope="row">
                <label class="jcl-label" for="email"><?php _e( 'Email', JWP_CL_DOMAIN ) ?></label>
            </th>
            <th>
                <p class="single-contact"><?php echo esc_attr( $contact->email ); ?></p>
            </th>
        </tr>
        <tr>
            <th scope="row">
                <label class="jcl-label" for="address"><?php _e( 'Address', JWP_CL_DOMAIN ) ?></label>
            </th>
            <th>
                <p class="single-contact"><?php echo esc_textarea( $contact->address ); ?></p>
            </th>
        </tr>
    </table>
</div>