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
 * Displays the list of Subjects and icons
 */

if (!function_exists('ac_subject_list'))
{
    function ac_subject_list($atts)
    {
        $args = array(
            'taxonomy' => 'subject',
            'hide_empty' => false
        );
        $terms = get_terms($args);
        $output = '';
        $output .= '<div class="subject-icon-list">';
        $output .= '<ul class="subject-icon-list__list">';
        foreach ($terms as $term){
            $subject_title =$term->name;
            $term_id = $term->taxonomy.'_'.$term->term_id;
            $subject_icon = get_field('subject_icon', $term_id);

            $term_link = get_term_link($term);
            $output .= '<li class="subject-icon-list__item">';
            ob_start();
            ?>
            <?php require('subject-icon-thumb.php') ?>
            <?php
            $output .= ob_get_contents();
            ob_end_clean();
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }

    add_shortcode('ac_subjects', 'ac_subject_list');
}

/*
 * Displays the list of taxonomies destinations and subjects
 */

if (!function_exists('ac_tax_list'))
{

    function ac_tax_list($atts)
    {
        extract(shortcode_atts(array(
            'tax' => 'destination',
        ), $atts));

        $args = array(
            'taxonomy' => $tax,
            'hide_empty' => false,
            'parent' => 0
        );
        $terms = get_terms($args);
        $output = '';
        $output .= '<div class="'.$tax.'-list tax-list">';
        $output .= '<ul class="'.$tax.'-list__list tax-list__list">';
        foreach ($terms as $term){
            $tax_title =$term->name;
            $term_id = $term->taxonomy.'_'.$term->term_id;
            if (get_field($tax.'_image', $term_id)){
                $tax_image = get_field($tax.'_image', $term_id)['sizes']['thumbnail'];
            }else{
                $tax_image = 'https://via.placeholder.com/400?text=Placeholder+'.$tax_title;
            }

            $tax_link = get_term_link($term);
            $output .= '<li class="'.$tax.'-list__item tax-list__item">';
            ob_start();
            ?>
            <?php require($tax.'-thumb.php') ?>
            <?php
            $output .= ob_get_contents();
            ob_end_clean();
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        return $output;
    }

    add_shortcode('ac_tax_list', 'ac_tax_list');

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
            //echo 'not destination';
        }
    }
    add_action('archive_template', 'ac_destination');
}


/*
 * Test if the current view is a destination archive and add a header and sub category navigation
 */
if (!function_exists('ac_subject')){
    function ac_subject($template){
        if( is_tax('subject')){
            $template = dirname( __FILE__ ) . '/archive-subject.php';
            add_action('after_header', 'ac_term_header');
            add_action('after_header', 'ac_location_by_subject');
        }else{
            //echo 'not destination';
        }
        return $template;
    }
    add_action('archive_template', 'ac_subject');
}

function ac_location_by_subject(){

    $term_id = get_queried_object()->term_id;
    $term_tax = 'subject';
    $term = get_term($term_id);
    $term_name = $term->name;
    $subject_icon = get_field('subject_icon', $term_tax.'_'.$term_id);
    $output = '';

    $args = array(
        'taxonomy' => 'destination',
        'hide_empty' => false,
        'parent' => "0"
    );
    $destinations = get_terms($args);

    $output .= "<div class='subject-destinations'>";
    $output .= "<div class='ac-column-wrapper'>";
    $output .= "<div class='ac-column-wrapper--column'>";
    $output .= "<div class='subject-destinations__subject-header'>";
    $output .= "<div class='subject-header'>";

    $output .= "<div class='subject-header__title'>";
    $output .= "<h1 class='subject-header__heading'>". $term_name ."</h1>";
    $output .= "</div>";

    $output .= "<div class='subject-header__icon'>";
    $output .= "<img src='".$subject_icon['sizes']['thumbnail']. "'  alt='".$term_name."' class='subject-thumb__img'>";
    $output .= "</div>";

    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "<div class='ac-column-wrapper__column'>";

    foreach ($destinations as $destination){

        $destination_id =  $destination->term_id;

        $args = array(
            'taxonomy' => 'destination',
            'hide_empty' => true,
            'parent' => $destination_id
        );

        $destination_children = get_terms($args);

        foreach ( $destination_children as  $destination_child){

            $destination_name = $destination_child->name;
            $output .= "<div class='subject-destinations__destination-list'>";
            $output .= "<div class='destination-list'>";
            $output .= "<ul class='destination-list__list'>";
            $output .= "<li class='destination-list__li'>";

            $output .= "<div class='destination'>";
            $output .= "<div class='destination__title'>";
            $output .= "<h2 class='destination__heading h1'>" . $destination_name . "</h2>";
            $output .= "</div><!-- /.destination__location-title -->";
            $output .= "<div class='destination__location-list' >";
            $output .= "<div class='location-list' >";


            $args = array(
                'subject' => $term->slug,
                'destination' => $destination_child->slug,
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) :
                $output .= "<ul class='location-list__list' >";
                while ($query->have_posts()) : $query->the_post();
                    $output .= "<li class='location-list__item' >";
                    $output .= "<div class='location' >";
                    $output .= "<a class='location__link' href='". get_permalink() ."'";
                    $output .= "<span>". get_the_title() . "</span>";
                    $output .= "</a>";
                    $output .= "</div>";
                    $output .= "</li><!-- /.location-list__item -->";
                endwhile;
                $output .= "</ul><!-- /.location-list__list -->";
            endif;

            $output .= "</div><!-- /.location-list -->";
            $output .= "</div><!-- /.destination__location-list -->";
            $output .= "</div><!-- /.destination -->";

        }

    }

    $output .= "</div><!-- /.ac-column-wrapper__column -->";
    $output .= "</div><!-- /.ac-column-wrapper--3 -->";
    $output .= "</div><!-- /.subject-destinations -->";

    echo $output;

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
    $term_slug = $term->slug;
    $tax_slug = get_query_var('taxonomy');
    $term_id = $tax_slug.'_'.get_queried_object()->term_id;
    $term_image = get_field($tax_slug.'_image', $term_id);
    $term_title_image = get_field($tax_slug.'_title_image', $term_id);


    require('term-header-template.php');

}
