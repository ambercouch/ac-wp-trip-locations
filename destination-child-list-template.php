
<div class="destination-child-list">
  <ul class="destination-child-list__list">
    <li class="destination-child-list__item" ><a href="<?php echo get_term_link( $term_oldest_parent, 'destination') ?>"><?php echo $term_oldest_parent->name; ?>  </a></li>
    <?php foreach (  $child_terms as $child ) :
    $term = get_term_by( 'id', $child, 'destination'); ?>
        <li class="destination-child-list__item">
    <a href="<?php echo get_term_link( $child, 'destination') ?>"><?php echo $term->name; ?></a>
        </li>
        <?php
        endforeach;
        ?>
  </ul>
</div>