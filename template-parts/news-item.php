<?php
/**
 * Template part for displaying a news/article card in the archive grid.
 */

$thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
?>

<a href="<?php the_permalink(); ?>" class="news-item">
  <?php if ($thumbnail_url) : ?>
    <div class="news-item__image-box">
      <img
        class="news-item__image lazyload"
        src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
        data-src="<?php echo esc_url($thumbnail_url); ?>"
        alt="<?php echo esc_attr(get_the_title()); ?>"
      />
    </div>
  <?php endif; ?>

  <h3 class="news-item__title"><?php the_title(); ?></h3>
</a>
