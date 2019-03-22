
<div class="subject-thumb">
  <div class="subject-thumb__content">
    <a href="<?php echo $term_link; ?>" class="subject__link">
    <div class="subject-thumb__image">
      <img src="<?php echo $subject_icon['sizes']['thumbnail'] ?>" alt="<?php echo $subject_title ?>" class="subject-thumb__img">
    </div>
      <?php if(isset($subject_title)) : ?>
    <div class="subject-thumb__title">
      <h4 class="subject-thumb__heading"><?php echo $subject_title ?></h4>
    </div>
      <?php endif; ?>
    </a>
  </div>
</div>
