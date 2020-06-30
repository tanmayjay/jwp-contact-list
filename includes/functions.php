<?php

/**
 * Inserts a contact in the database
 * 
 * @param array $args
 * 
 * @return int/WP_Error
 */
function jwp_cl_insert_contact( $args = [] ) {
    
    global $wpdb;

    if ( empty( $args['name'] ) )  return new \WP_Error( 'no-name' , __( 'Name cannot be empty', JWP_CL_DOMAIN ) );
    if ( empty( $args['phone'] ) )  return new \WP_Error( 'no-phone' , __( 'Phone number cannot be empty', JWP_CL_DOMAIN ) );

    $defaults = [
        'name'       => '',
        'phone'      => '',
        'address'    => '',
        'email'      => '',
        'created_by' => get_current_user_id(),
        'created_at' => current_time( "mysql" ),
        'updated_at' => current_time( "mysql" ),
    ];

    $data = wp_parse_args( $args, $defaults );

    if ( isset( $data['id'] ) ) {

        $id = $data['id'];
        unset( $data['id'] );

        $updated = $wpdb->update(
            "{$wpdb->prefix}jcl_contacts",
            $data,
            [ 'id' => $id ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            ],
            [ '%d' ]
        );

        if( ! $updated ) return new \WP_Error( 'failed-to-insert' , __( 'Failed to update data', JWP_CL_DOMAIN ));

        jwp_cl_contact_purge_cache( $id );
    
        return $updated;

    } else {

        $inserted = $wpdb->insert(
            "{$wpdb->prefix}jcl_contacts",
            $data,
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            ]
        );
    
        if( ! $inserted ) return new \WP_Error( 'failed-to-insert' , __( 'Failed to insert data', JWP_CL_DOMAIN ));
    
        jwp_cl_contact_purge_cache();

        return $wpdb->insert_id;
    }

}

/**
 * Fetches the contacts
 *
 * @param array $args
 * 
 * @return array
 */
function jwp_cl_get_contacts( $args = [] ) {
    global $wpdb;

    $defaults = [
        'number' => 10,
        'offset' => 0,
        'orderby' => 'id',
        'order' => 'ASC'
    ];

    $args         = wp_parse_args( $args, $defaults );
    $last_changed = wp_cache_get_last_changed( 'contact' );
    $key          = md5( serialize( array_diff_assoc( $args, $defaults ) ) );
    $cache_key    = "all: $key";
    $results      = wp_cache_get( $cache_key, 'contact' );

    if ( false === $results ) {

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * 
                FROM {$wpdb->prefix}jcl_contacts
                ORDER BY {$args['orderby']} {$args['order']}
                LIMIT %d, %d",
                $args['offset'],
                $args['number']
            )
        );

        foreach ( $results as $result ) {
            $result->created_at = date_i18n( 'F j, Y | g:i A', strtotime( $result->created_at ) );
        }

        wp_cache_set( $cache_key, $results, 'contact' );
    }

    return $results;
}

/**
 * Fetches a single contact
 *
 * @param int $id
 * 
 * @return object
 */
function jwp_cl_get_contact( $id ) {
    global $wpdb;

    $result = wp_cache_get( 'list-' . $id, 'contact' );

    if ( false === $result ) {
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * 
                FROM {$wpdb->prefix}jcl_contacts
                WHERE id = %d",
                $id
            )
        );

        wp_cache_set( 'list-' . $id, $result, 'contact' );
    }


    return $result;
}

/**
 * Deletes a single contact
 *
 * @param int $id
 * 
 * @return void
 */
function jwp_cl_delete_contact( $id ) {
    global $wpdb;

    $deleted = $wpdb->delete(
        $wpdb->prefix . 'jcl_contacts',
        [ 'id' => $id ],
        [ '%d' ]
    );

    jwp_cl_contact_purge_cache( $id );

    return $deleted;
}

/**
 * Counts the number of contacts
 *
 * @return int
 */
function jwp_cl_contacts_count() {
    global $wpdb;

    $count = wp_cache_get( 'count', 'contact' );

    if ( false === $count ) {
        
        $count = $wpdb->get_var(
            "SELECT count(id) 
            FROM {$wpdb->prefix}jcl_contacts"
        );

        wp_cache_set( 'count', $count, 'contact' );
    }


    return (int) $count;
}

/**
 * Purges the cache for contact
 *
 * @param int $contact_id
 * 
 * @return void
 */
function jwp_cl_contact_purge_cache( $contact_id = null ) {
    $group = 'contact';

    if ( $contact_id ) {
        wp_cache_delete( 'list-' . $contact_id, $group );
    }

    wp_cache_delete( 'count', $group );
    wp_cache_set( 'last_changed', microtime(), $group );
}