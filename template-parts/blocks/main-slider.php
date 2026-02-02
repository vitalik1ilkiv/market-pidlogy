<?php
$slides = get_field('main_slider');

if ( $slides && is_array($slides) ): ?>
  <section class="section section-slider-main">
    <div
      class="swiper slider-main"
      data-wp="slider"
      data-wp-config='{
        "settings": {
          "autoplay": {
            "delay": 5000,
            "disableOnInteraction": false
          },
          "loop": true,
          "speed": 1000,
          "pagination": {
            "el": ".slider-main__pagination.swiper-pagination",
            "clickable": true
          }
        }
      }'
    >
      <div class="swiper-wrapper">
        <?php $index = 0; ?>
        <?php foreach ($slides as $slide): 

          $image = $slide['image'] ?? null;
          $image_ru = $slide['image_ru'] ?? null;

          $image_mobile = $slide['image_mobile'] ?? null;
          $image_mobile_ru = $slide['image_mobile_ru'] ?? null;

          $button_text = $slide['button_text'] ?? '';
          $button_link = $slide['button_link'] ?? '';

          // визначаємо мову (qTranslate)
          $is_ru = function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() === 'ru';

          $final_image = $is_ru && $image_ru ? $image_ru : $image;
          $final_image_mobile = $is_ru && $image_mobile_ru ? $image_mobile_ru : $image_mobile;
        ?>
        <div class="swiper-slide">
          <?php if ($final_image): ?>

            <?php if ($index === 0): ?>
              <!-- LCP -->
              <picture>
                <?php if ($final_image_mobile): ?>
                  <source
                    media="(max-width: 767px)"
                    srcset="<?php echo esc_url($final_image_mobile['url']); ?>"
                  />
                <?php endif; ?>

                <img
                  src="<?php echo esc_url($final_image['url']); ?>"
                  alt="<?php echo esc_attr($final_image['alt'] ?? ''); ?>"
                  fetchpriority="high"
                  decoding="async"
                  loading="eager"
                  width="<?php echo esc_attr($final_image['width']); ?>"
                  height="<?php echo esc_attr($final_image['height']); ?>"
                >
              </picture>

            <?php else: ?>
              <!-- Non-LCP -->
              <picture>
                <?php if ($final_image_mobile): ?>
                  <source
                    media="(max-width: 767px)"
                    srcset="<?php echo esc_url($final_image_mobile['url']); ?>"
                  />
                <?php endif; ?>

                <img
                  class="lazyload"
                  src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                  data-src="<?php echo esc_url($final_image['url']); ?>"
                  alt="<?php echo esc_attr($final_image['alt'] ?? ''); ?>"
                  loading="lazy"
                  decoding="async"
                >
              </picture>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ($button_text && $button_link): ?>
            <div class="slider-main__button">
              <a class="action action-secondary" href="<?php echo esc_url($button_link); ?>">
                <?php echo esc_html($button_text); ?>
              </a> 
            </div>
          <?php endif; ?>

        </div>
        <?php $index++; ?>
        <?php endforeach; ?>
      </div>
      <div class="slider-main__pagination swiper-pagination"></div>
    </div>
  </section>
<?php endif; ?>
