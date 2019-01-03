<?php
/*
  Plugin Name: AC Trip Locations
  Plugin URI: https://github.com/ambercouch/ac-wp-custom-loop-shortcode
  Description:
  Version: 1.0
  Author: AmberCouch
  Author URI: http://ambercouch.co.uk
  Author Email: richard@ambercouch.co.uk
  Text Domain: ac-trip-locations
  Domain Path: /lang/
  License:
  Copyright 2018 AmberCouch
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') or die('You do not have the required permissions');

if (!function_exists('ac_trip_locations_setup'))
{
    function ac_trip_locations_setup(){
        add_image_size( 'location-header', 2000, 550, true );

        wp_register_style( 'ac-trip-location-styles', plugin_dir_url( __FILE__ ) . 'assets/css/ac-trip-location-styles.css', array(), '20190103' );
        wp_enqueue_style( 'ac-trip-location-styles' );

    }
}
add_action('init', 'ac_trip_locations_setup');

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
            'rewrite'           => array( 'slug' => 'destination' ),
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

/*
 * Displays the list of destinations
 */

if (!function_exists('ac_destination_list'))
{

    function ac_destination_list($atts)
    {
        $args = array(
            'taxonomy' => 'destination',
            'hide_empty' => false,
            'parent' => 0
        );
        $terms = get_terms($args);
        $output = '';

        foreach ($terms as $term){
            $destination_title =$term->name;
            $term_id = $term->taxonomy.'_'.$term->term_id;
            $destination_image = get_field('destination_image', $term_id);
            $term_link = get_term_link($term);
//            $output .= $term_id.'<br>';
//            $output .= get_field('test_text', $term_id);
            ob_start();
            ?>
            <?php require('loop-template.php') ?>
            <?php
            $output .= ob_get_contents();
            ob_end_clean();
        }
        //print_r($terms);
        return $output;
    }

    add_shortcode('ac_destinations', 'ac_destination_list');

}

/*
 * Test if the current view is a destination archive and add a header and sub category navigation
 */

if (!function_exists('ac_destination')){
    function ac_destination(){
        if( is_tax('destination')){
            add_action('loop_start', 'ac_term_header');
            add_action('loop_start', 'ac_child_destinations');
        }else{
            echo 'not destination';
        }
    }
    add_action('archive_template', 'ac_destination');
}

function ac_get_term_oldest_parent($term_id){

    $term = get_term( $term_id, get_query_var('taxonomy') );
    $term_parent = $term->parent;

    if($term->parent == 0){
        return $term;
    }else{
        $term_id = $term_parent;
        return ac_get_term_oldest_parent($term_id);
    }

}

/*
 * Creates the list of child destination to display on the destination archives
 */

function ac_child_destinations(){

    $term_id = get_queried_object()->term_id;
    $term_oldest_parent = ac_get_term_oldest_parent($term_id);
    $child_terms = get_term_children($term_oldest_parent->term_id, 'destination');

    require('destination-child-list-template.php');

}

/*
 * Create the header banners for the destination pages
 */

function ac_term_header(){
    $term = get_term(get_queried_object()->term_id);
    $term_name = $term->name;
    $term_id = get_query_var('taxonomy').'_'.get_queried_object()->term_id;
    $destination_image = get_field('destination_image', $term_id);
    $destination_title_image = get_field('destination_title_image', $term_id);

    require('destination-header-template.php');

}
