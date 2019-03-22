
<div class="term-header <?php echo $term_slug; ?>-header <?php echo $tax_slug; ?>-header">
  <div class="term-header__image <?php echo $term_slug; ?>-header__image">
    <img src="<?php echo $term_image['sizes']['location-header']; ?>" alt="<?php echo $term_name; ?>" class="term-header_img <?php echo $term_slug; ?>-header_img">
  </div>
  <?php if ($term_title_image['url']) : ?>
  <div class="term-header__title-image <?php echo $term_slug; ?>-header__title-image">
    <img src="<?php echo $term_title_image['url']; ?>" alt="<?php echo $term_name; ?>" class="term-header__title-img <?php echo $term_slug; ?>-header__title-img">
  </div>
  <?php else : ?>
    <div class="term-header__header-text <?php echo $term_slug; ?>-header__title">
      <h1 class="term-header__heading" ><?php echo $term_name; ?></h1>
    </div>
  <?php endif ?>
</div>
