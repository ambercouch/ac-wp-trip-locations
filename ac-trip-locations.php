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

require_once  'lib/setup.php';

require_once  'lib/tax-subjects.php';

require_once  'lib/taxonomies.php';

require_once  'lib/tax-destinations.php';


add_action('loop_start', 'ac_trip_location');
function ac_trip_location($post_object){

    $posts = $post_object->posts;
    $subjects = '<ul><li>subject 1</li> <li>Subject 2</li></ul>';

    foreach ($posts as $post){
        $post_id = $post->ID;

        $trip_gallery = get_field('trip_gallery',  $post_id );

        $subjectArray = wp_get_post_terms($post_id,'subject');
        $output = '';

        if($trip_gallery){
            $output .= '<div class="c-trip-gallery">';
            $output .= '<ul class="c-trip-gallery__list">';
            foreach ($trip_gallery as $image){
                $output .= '<li class="c-trip-gallery__item" >';
                $output .= '<a class="c-trip-gallery__link" href="'.$image['url'].'">';
                $output .= '<img class="c-trip-gallery__img" src="' . $image['sizes']['location-gallery-thumb'] . '" >';
                $output .= '</a>';
                $output .= '</li>';
                //$output .= $image['sizes']['thumbnail'];
            }
            $output .='</ul>';
            $output .='</div>';

        }



        if($post->post_type == 'trip_location'){
            $post->post_content = $post->post_content . $output;
        }
    }

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
    $subject_icon = get_field('subject_icon_2', $term_tax.'_'.$term_id);
    $output = '';

    $args = array(
        'taxonomy' => 'destination',
        'hide_empty' => false,
        'parent' => "0"
    );
    $destinations = get_terms($args);

    $output .= "<div class='subject-destinations'>";
    $output .= "<div class='ac-column-wrapper'>";
    $output .= "<div class='ac-column-wrapper--column-1'>";
    $output .= "<div class='subject-destinations__subject-header'>";
    $output .= "<div class='subject-header'>";

    $output .= "<div class='subject-header__title'>";
    $output .= "<h1 class='subject-header__heading h3'>". $term_name ."</h1>";
    $output .= "</div>";

    $output .= "<div class='subject-header__icon'>";
    $output .= "<img src='".$subject_icon['sizes']['thumbnail']. "'  alt='".$term_name."' class='subject-thumb__img'>";
    $output .= "</div>";

    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "<div class='ac-column-wrapper__column-2'>";

    $output .= "<div class='subject-destinations__destination-list'>";
    $output .= "<div class='destination-title-list'>";
    $output .= "<ul class='destination-title-list__list'>";

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

                        $output .= "<li class='destination-title-list__item'>";

                            $output .= "<div class='destination'>";
                                $output .= "<div class='destination__title'>";
                                    $output .= "<h2 class='destination__heading h3'>" . $destination_name . "</h2>";
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
                        $output .= "</li><!-- /.destination-list__li -->";


        }

    }
    $output .= "</ul><!-- .destination-list__list -->";
    $output .= "</div><!-- .destination-list -->";
    $output .= "</div><!-- .subject__destination-list -->";

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

/*
 * Create the header banners for the destination pages
 */

function ac_term_description(){
    $term = get_term(get_queried_object()->term_id);
     $term_name = $term->name;
     $term_slug = $term->slug;
     $term_description = $term->description;
     $tax_slug = get_query_var('taxonomy');
     $term_id = $tax_slug.'_'.get_queried_object()->term_id;
//     $term_image = get_field($tax_slug.'_image', $term_id);
//     $term_title_image = get_field($tax_slug.'_title_image', $term_id);
    require('term-description-template.php');
}
