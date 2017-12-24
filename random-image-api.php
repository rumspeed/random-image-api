<?php
/*
Plugin Name: Random Image API
Description: WordPress Plugin with REST API Route to Serve Up Images
Version: 0.2.0
Author: Scot Rumery
Author URI: https://rumspeed.com/
License: GPLv2
*/




define( 'RANDOM_IMAGE_API_VERSION', '0.2.0' );
define( 'RANDOM_IMAGE_API_DIR', dirname( __FILE__ ) );
define( 'RANDOM_IMAGE_API_URI', plugins_url( '' , __FILE__ ) );




/**
 * Register the REST route.
 * 
 * @see http://example.com/wp-json/fullscreenimage/v1/random-image/ 
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'fullscreenimage/v1', '/random-image/', array(
        'methods'            => 'GET, POST',
        'callback'           => 'fullscreenimage__show_random_image',
        'show_in_index'      => false,
    ) );
} );




/**
 * Show random image
 */
function fullscreenimage__show_random_image(){
    $image_ids = get_posts(
        array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'numberposts'    => 1,
            'orderby'        => 'rand',
            'fields'         => 'ids',
        ) );

    // convert ids to urls
    // $images = array_map( "wp_get_attachment_url", $image_ids );

    // convert ids to paths
    $images     = array_map( "get_attached_file", $image_ids );


    $image_id   = $image_ids[ 0 ];
    $image_path = $images[ 0 ];


    // make sure url is readable -- if not, stop!
    if ( ! is_readable( $image_path ) ) {
        wp_die( "File is not readable: $image_path" );
    }


    $image = file_get_contents( $image_path );
    $type  = get_post_mime_type( $image_id );
    if ( empty ( $type ) ) {
        $type = "image/jpg";
    }


    // output headers and image data
    nocache_headers();
    header( "Content-type: $type;" );
    header( "Content-Length: " . strlen( $image ) );


    echo $image;
    die();
}