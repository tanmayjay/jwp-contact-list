<?php

namespace JWP\JCL\Traits;

/**
 * Error handler trait
 */
trait Form_Errors {

    /**
     * Stores the errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Checks if there is any error
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has_error( $key ) {
        return isset( $this->errors[ $key ] );
     }

    /**
     * Fetch the errors
     *
     * @param string $key
     *
     * @return string|null
     */
     public function get_error( $key ) {
         if( $this->has_error( $key ) ) {
             return $this->errors[ $key ];
         }

         return null;
     }
}