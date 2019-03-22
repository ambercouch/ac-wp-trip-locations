<?php

/*
    Add images and scripts
*/

if (!function_exists('ac_trip_locations_setup'))
{
    function ac_trip_locations_setup(){

        add_image_size( 'location-header', 2000, 550, true );
        add_image_size( 'tax-thumb', 650, 650, true );
        add_image_size( 'location-gallery', 1000, 750, true );
        add_image_size( 'location-gallery-thumb', 500, 375, true );

        wp_register_style( 'ac-trip-location-styles', plugin_dir_url( __FILE__ ) . '../assets/css/ac-trip-location-styles.css', array(), '20190124' );
        wp_enqueue_style( 'ac-trip-location-styles' );
    }
}
add_action('init', 'ac_trip_locations_setup');

/*
    Setup the custom post type and taxonomies
*/

if (!function_exists('ac_trip_locations'))
{

    function  ac_trip_locations() {
//Tiles
        $labels = array(
            'name' => _x('Trip Locations', 'post type general name'),
            'singular_name' => _x('Trip Location', 'post type singular name'),
            'add_new' => _x('Add New', 'Trip Location'),
            'add_new_item' => __('Add New Trip Location'),
            'edit_item' => __('Edit Trip Location'),
            'new_item' => __('New Trip Location'),
            'all_items' => __('All Trip Locations'),
            'view_item' => __('View Trip Location'),
            'search_items' => __('Search Trip Locations'),
            'not_found' => __('No Trip Locations found'),
            'not_found_in_trash' => __('No Trip Locations found in the trash'),
            'parent_item_colon' => '',
            'menu_name' => 'Trip Locations'
        );
        $args = array(
            'labels' => $labels,
            'menu_icon' => 'dashicons-performance',
            'description' => 'Trip Locations',
            'public' => true,
            'menu_position' => 20,
            'supports' => array('title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'),
            'has_archive' => 'trip'
        );
        register_post_type('trip_location', $args);
        //Destinations
        $labels = array(
            'name'              => _x( 'Destinations', 'taxonomy general name' ),
            'singular_name'     => _x( 'Destination', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Destinations' ),
            'all_items'         => __( 'All Destinations' ),
            'edit_item'         => __( 'Edit Destination' ),
            'update_item'       => __( 'Update Destination' ),
            'add_new_item'      => __( 'Add New Destination' ),
            'new_item_name'     => __( 'New Tile Destination' ),
            'menu_name'         => __( 'Destinations' ),
        );
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'destinations' ),
        );
        register_taxonomy( 'destination', array( 'trip_location' ), $args );

        //Subjects
        $labels = array(
            'name'              => _x( 'Subjects', 'taxonomy general name' ),
            'singular_name'     => _x( 'Subject', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Subjects' ),
            'all_items'         => __( 'All Subjects' ),
            'edit_item'         => __( 'Edit Subject' ),
            'update_item'       => __( 'Update Subject' ),
            'add_new_item'      => __( 'Add New Subject' ),
            'new_item_name'     => __( 'New Tile Subject' ),
            'menu_name'         => __( 'Subjects' ),
        );
        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'subject' ),
        );
        register_taxonomy( 'subject', array( 'trip_location' ), $args );
    }
    add_action('init', 'ac_trip_locations');

}