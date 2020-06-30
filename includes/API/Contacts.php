<?php

namespace JWP\JCL\API;
use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

/**
 * Contacts API handler class
 */
class Contacts extends WP_REST_Controller {

    /**
     * Class constructor
     */
    function __construct() {
        $this->namespace = 'jwp/v1';
        $this->rest_base = 'contacts';
    }

    /**
     * Registers API routes
     *
     * @return void
     */
    public function register_routes() {
        
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base, 
            array(
                array(
                    "methods"             => WP_REST_Server::READABLE,
                    "callback"            => [ $this, 'get_items' ],
                    "permission_callback" => [ $this, 'get_items_permissions_check' ],
                    "args"                => $this->get_collection_params()
                ),
                array(
                    "methods"             => WP_REST_Server::CREATABLE,
                    "callback"            => [ $this, 'create_item' ],
                    "permission_callback" => [ $this, 'create_item_permissions_check' ],
                    "args"                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                ),
                'schema' => [ $this, 'get_item_schema' ]
            )
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/(?P<id>[\d]+)', 
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the object' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_item' ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                    'args'                => array(
                        'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                    ),
                ),
                array(
                    "methods"             => WP_REST_Server::EDITABLE,
                    "callback"            => [ $this, 'update_item' ],
                    "permission_callback" => [ $this, 'update_item_permissions_check' ],
                    "args"                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'delete_item' ],
                    'permission_callback' => [ $this, 'delete_item_permissions_check' ],
                ),
                'schema' => $this->get_item_schema(),
            )
        );
    }

    /**
     * Checks if a given request has access to read contacts
     *
     * @param \WP_REST_Request $request
     * 
     * @return \WP_REST_Response
     */
    public function get_items_permissions_check( $request ) {

        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        return false;
    }

    /**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function get_items( $request ) {
        $args = [];
        $params = $this->get_collection_params();

        foreach ( $params as $key => $value ) {
            if ( isset( $request[ $key ] ) ) {
                $args[ $key ] = $request[ $key ];
            }
        }

        $args['number'] = $args['per_page'];
        $args['offset'] = $args['number'] * ( $args['page'] - 1 );

        unset( $args['per_page'] );
        unset( $args['page'] );

        $contacts = jwp_cl_get_contacts( $args );

        foreach ( $contacts as $contact ) {
            $response = $this->prepare_item_for_response( $contact, $request );
            $data[]   = $this->prepare_response_for_collection( $response );
        }

        $total = jwp_cl_contacts_count();
        $max_pages = ceil( $total / (int) $args['number'] );

        $response = rest_ensure_response( $data );

        $response->header( "X-WP-Total", (int) $total);
        $response->header( "X-WP-TotalPages", (int) $max_pages);

        return $response;
    }

    /**
     * Checks whether a contact is available
     *
     * @param int $id
     * 
     * @return object|\WP_Error
     */
    protected function get_contact( $id ) {
        $contact = jwp_cl_get_contact( $id );

        if ( ! $contact ) {
            return new WP_Error(
                'rest_contact_invalid_id',
                'Invalid contact id',
                array(
                    'status' => 404,
                ),
            );
        }

        return $contact;
    }

    /**
     * Checks if a given request has access to read a specific contact
     *
     * @param \WP_REST_Request $request
     * 
     * @return \WP_REST_Response
     */
    public function get_item_permissions_check( $request ) {

        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        $contact = $this->get_contact( $request['id'] );

        if ( is_wp_error( $contact ) ) {
            return $contact;
        }

        return true;
    }

    /**
	 * Retrieves a specific item
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function get_item( $request ) {
        $contact = $this->get_contact( $request['id'] );
        $response = $this->prepare_item_for_response( $contact, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }

    
	/**
	 * Checks if a given request has access to create items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Creates one item from the collection.
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
        $contact = $this->prepare_item_for_database( $request );

        if ( is_wp_error( $contact ) ) {
            return $contact;
        }

        $contact_id = jwp_cl_insert_contact( $contact );

        if ( is_wp_error( $contact_id ) ) {
            $contact_id->add_data( array( 'status' => 400 ) );

            return $contact_id;
        }

        $contact = $this->get_contact( $contact_id );
        $response = $this->prepare_item_for_response( $contact, $request );
        
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $contact_id ) ) );

        return rest_ensure_response( $response );
    }
    
    /**
	 * Checks if a given request has access to update a specific item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return $this->get_item_permissions_check( $request );
	}

	/**
	 * Updates one item from the collection.
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
        $contact  = $this->get_contact( $request['id'] );
        $prepared = $this->prepare_item_for_database( $request );

        $prepared = array_merge( (array) $contact, $prepared );

        $updated  = jwp_cl_insert_contact( $prepared );

        if ( ! $updated ) {
            return new WP_Error(
                'rest_not_updated',
                'Contact could not be updated',
                array(
                    'status' => 400,
                ),
            );
        }

        $contact = $this->get_contact( $request['id'] );
        $response = $this->prepare_item_for_response( $contact, $request );

        return rest_ensure_response( $response );
	}

    /**
	 * Checks if a given request has access to delete a specific item.
     * 
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		return $this->get_item_permissions_check( $request );
	}

	/**
	 * Deletes one item from the collection.
	 *
	 * @param WP_REST_Request $request Full details about the request.
     * 
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
        $contact  = $this->get_contact( $request['id'] );
        $previous = $this->prepare_item_for_response( $contact, $request );
        $deleted  = jwp_cl_delete_contact( $request['id'] );

        if ( ! $deleted ) {
            return new WP_Error(
                'rest_not_deleted',
                'Contact cannot be deleted',
                array(
                    'status' => 400,
                ),
            );
        }

        $data = array(
            'deleted'  => true,
            'previous' => $previous->get_data(),
        );

        $response = rest_ensure_response( $data );

        return $response;
	}

    /**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
    public function get_item_schema() {
        
        if ( $this->schema ) {
            return $this->add_additional_fields_schema( $this->schema );
        }

        $schema = array(
            '$schema'    => 'http: //json-schema.org/draft-04/schema#',
            'title'      => 'contact',
            'type'       => 'object',
            'properties' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object' ),
                    'type'        => 'integer',
                    'context'     => array( 'view' ),
                    'readonly'    => true,
                ),
                'name' => array(
                    'description' => __( 'Name of the contact' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'required'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'address' => array(
                    'description' => __( 'Address of the contact' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                ),
                'phone' => array(
                    'description' => __( 'Phone number of the contact' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'required'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'email' => array(
                    'description' => __( 'Email address of the contact' ),
                    'type'        => 'string',
                    'format'      => 'email',
                    'context'     => array( 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'date' => array(
                    'description' => __( 'Publishig date of the object in the site\'s time' ),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => array( 'view' ),
                    'readonly'    => true,
                ),
            )
        );

        $this->schema = $schema;

        return $this->add_additional_fields_schema( $this->schema );
    }

    /**
     * Retrieves the query params for collection
     * 
     * @return array
     */
    public function get_collection_params() {
        $params = parent::get_collection_params();

        unset( $params['search'] );

        return $params;
    }

    /**
	 * Prepares the item for the REST response.
     * 
	 * @param mixed           $item    WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
     * 
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
        $data   = [];
        $fields = $this->get_fields_for_response( $request );

        if ( in_array( 'id', $fields, true ) ) {
            $data['id'] = (int) $item->id;
        }

        if ( in_array( 'name', $fields, true ) ) {
            $data['name'] = $item->name;
        }

        if ( in_array( 'address', $fields, true ) ) {
            $data['address'] = $item->address;
        }

        if ( in_array( 'phone', $fields, true ) ) {
            $data['phone'] = $item->phone;
        }

        if ( in_array( 'email', $fields, true ) ) {
            $data['email'] = $item->email;
        }

        if ( in_array( 'date', $fields, true ) ) {
            $data['date'] = mysql_to_rfc3339( $item->created_at );
        }

        $context  = ! empty( $request['context'] ) ? $request['context']: 'view';
        $data     = $this->filter_response_by_context( $data, $context );

        $response = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $item ) );

        return $response;
    }
    
    /**
     * Prepares links for the request
     *
     * @param object $item
     * 
     * @return array
     */
    protected function prepare_links( $item ) {
        $base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

        $links = array(
            'self' => array(
                'href' => rest_url( trailingslashit( $base ) . $item->id ),
            ),
            'collection' => array(
                'href' => rest_url( $base ),
            ),
        );

        return $links;
    }

    /**
	 * Prepares one item for create or update operation.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return object|WP_Error The prepared item, or WP_Error object on failure.
	 */
	protected function prepare_item_for_database( $request ) {
        $prepared = [];

        if ( isset( $request['name'] ) ) {
            $prepared['name'] = $request['name'];
        }

        if ( isset( $request['address'] ) ) {
            $prepared['address'] = $request['address'];
        }

        if ( isset( $request['phone'] ) ) {
            $prepared['phone'] = $request['phone'];
        }

        if ( isset( $request['email'] ) ) {
            $prepared['email'] = $request['email'];
        }
        
        return $prepared;
	}
}