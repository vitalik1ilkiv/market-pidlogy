<div class="social">
  <?php if (get_field('instagram', 'option')) : ?>
  <a class="action-social action-social--instagram" href="<?php the_field('instagram', 'option'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Відкрити інстаграм">
    <svg class="icon icon--instagram" width="24" height="24">
      <use xlink:href="#icon-instagram"></use>
    </svg>
  </a>
  <?php endif; ?>
  <?php if (get_field('facebook', 'option')) : ?>
  <a class="action-social action-social--facebook" href="<?php the_field('facebook', 'option'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Відкрити фейсбук">
    <svg class="icon icon--facebook" width="24" height="24">
      <use xlink:href="#icon-facebook"></use>
    </svg>
  </a>
  <?php endif; ?>
  <?php if (get_field('viber', 'option')) : ?>
  <a class="action-social action-social--viber" href="viber://chat?number=<?php echo sanitize_phone(get_field('viber', 'option')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Відкрити вайбер">
    <svg class="icon icon--viber" width="24" height="24">
      <use xlink:href="#icon-viber"></use>
    </svg>
  </a>
  <?php endif; ?>
  <?php if (get_field('telegram', 'option')) : ?>
  <a class="action-social action-social--telegram" href="<?php the_field('telegram', 'option'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Відкрити телеграм">
    <svg class="icon icon--telegram" width="24" height="24">
      <use xlink:href="#icon-telegram"></use>
    </svg>
  </a>
  <?php endif; ?>
</div>
