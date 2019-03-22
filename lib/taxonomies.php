<?php

/*
* Displays the list of taxonomies destinations and subjects
*/

if (!function_exists('ac_tax_list'))
{

    function ac_tax_list($atts)
    {
        extract(shortcode_atts(array(
            'tax' => 'destination',
            'number' => ''
        ), $atts));

        $args = array(
            'taxonomy' => $tax,
            'hide_empty' => false,
            'parent' => 0,
            'number' => $number
        );
        $terms = get_terms($args);
        $output = '';
        $output .= '<div class="animate_when_almost_visible alpha-anim">';
        $output .= '<div class="'.$tax.'-list tax-list isotope-system"> ';
        $output .= '<ul class="'.$tax.'-list__list tax-list__list isotope-container" style="opacity:1">';
        foreach ($terms as $key => $term){
            //$ani_delay = $key * 200;
            $ani_delay = 200;
            $tax_title =$term->name;
            $term_id = $term->taxonomy.'_'.$term->term_id;
            if (get_field($tax.'_image', $term_id)){
                $tax_image = get_field($tax.'_image', $term_id)['sizes']['tax-thumb'];
            }else{
                $tax_image = 'https://via.placeholder.com/400?text=Placeholder+'.$tax_title;
            }

            $tax_link = get_term_link($term);
            $output .= '<li data-delay="'. $ani_delay.'" data-speed="800"  class="'.$tax.'-list__item tax-list__item animate_when_almost_visible alpha-anim">';
            ob_start();
            ?>
            <?php require( __DIR__ .'/../'.$tax.'-thumb.php') ?>
            <?php
            $output .= ob_get_contents();
            ob_end_clean();
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    add_shortcode('ac_tax_list', 'ac_tax_list');

}