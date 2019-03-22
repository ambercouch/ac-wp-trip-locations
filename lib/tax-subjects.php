<?php

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
            <?php require( __DIR__ .'/../'.'subject-icon-thumb.php') ?>
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
