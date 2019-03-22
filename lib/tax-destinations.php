<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 30/01/2019
 * Time: 07:42
 */

/*
 * Test if the current view is a destination archive and add a header and sub category navigation
 */

if (!function_exists('ac_destination')){
    function ac_destination(){
        if( is_tax('destination')){
            add_action('loop_start', 'ac_term_header');
            add_action('loop_start', 'ac_child_destinations');
            add_action('loop_start', 'ac_term_description');
            add_action('loop_start', 'ac_trip_subjects');
        }else{
            //echo 'not destination';
        }
    }
    add_action('archive_template', 'ac_destination');
}




function ac_trip_subjects($post_object){

    $posts = $post_object->posts;
    $subjects = '<ul><li>subject 1</li> <li>Subject 2</li></ul>';

    foreach ($posts as $post){
        $post_id = $post->ID;

        $subjectArray = wp_get_post_terms($post_id,'subject');

        $subjects = '<div class="c-trip-subjects">';
        $subjects .= '<ul class="c-trip-subjects__list">';

        foreach ($subjectArray as $subject){

            $term_id = 'subject_'. $subject->term_id;

            $subject_icon = get_field('subject_icon_2', $term_id);
            // $term_title_image = get_field($tax_slug.'_title_image', $term_id);

            $term_link = get_term_link($subject);

            $subjects .= '<li class="c-trip-subjects__item">';

            ob_start();
            ?>
            <?php require(__DIR__ .'/../'.'subject-icon-thumb.php') ?>
            <?php
            $subjects .= ob_get_contents();
            ob_end_clean();

            $subjects .= "</li>";
        }
        $subjects .= '</ul>';
        $subjects .= '</div>';

        $post->post_content = $subjects . $post->post_content;
    }

}