<?php
defined('ABSPATH') || exit;
global $product;

$gallery_ids = $product->get_gallery_image_ids();
$post_thumbnail_id = $product->get_image_id();

if ($post_thumbnail_id) {
    array_unshift($gallery_ids, $post_thumbnail_id);
}

// Якщо немає жодного зображення, використовуємо placeholder
if (empty($gallery_ids)) {
    $gallery_ids = [0]; // 0 означає placeholder
}
?>

<div class="custom-product-gallery">
  <?php get_template_part('./template-parts/woocommerce/product/badges', null, ['product_id' => $product->get_id()]); ?>
  <!-- Thumbs -->
  <div class="custom-product-gallery__small hidden-mobile">
    <div class="custom-gallery-thumbs swiper" data-wp="slider" data-slider-id="thumbs"
          data-wp-config='{
            "settings": {
              "direction": "vertical",
              "slidesPerView": 3,
              "spaceBetween": 10,
              "watchSlidesProgress": true,
              "navigation": { 
                "nextEl": ".custom-gallery-thumbs-next", 
                "prevEl": ".custom-gallery-thumbs-prev" 
              }
            }
          }'
    >
      <div class="swiper-wrapper">
        <?php foreach($gallery_ids as $id): ?>
          <div class="swiper-slide">
            <?php
              if ($id) {
                echo wp_get_attachment_image($id, 'thumbnail');
              } else {
                echo wc_placeholder_img('thumbnail');
              }
            ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="swiper-button swiper-button-prev custom-gallery-thumbs-prev" aria-label="Попередній слайд">
      <svg class="icon" width="12" height="12">
        <use xlink:href="#icon-prev"></use>
      </svg>
    </div>
    <div class="swiper-button swiper-button-next custom-gallery-thumbs-next" aria-label="Наступний слайд">
      <svg class="icon" width="12" height="12">
        <use xlink:href="#icon-next"></use>
      </svg>
    </div>
  </div>

  <div class="custom-product-gallery__big">
    <!-- Main -->
    <div class="custom-gallery-main swiper" data-wp="slider" data-slider-id="main" data-thumbs-target="thumbs"
        data-wp-config='{
          "settings": {
            "slidesPerView": 1
          }
        }'>
       <div class="swiper-wrapper">
        <?php
          $i = 0;
          foreach($gallery_ids as $id):
              if ($id) {
                  // Є зображення
                  $full = wp_get_attachment_image_url($id, 'full');
                  $large = wp_get_attachment_image_url($id, 'large');
                  $image_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
                  if (!$image_alt) {
                      $image_alt = 'Фото об\'єкта ' . pathinfo($large, PATHINFO_FILENAME);
                  }
              } else {
                  // Placeholder
                  $placeholder = wc_placeholder_img_src('woocommerce_single');
                  $full = $placeholder;
                  $large = $placeholder;
                  $image_alt = __('Awaiting product image', 'woocommerce');
              }
          ?>
              <div class="swiper-slide">
                  <a href="<?php echo esc_url($full); ?>" data-fancybox="gallery" aria-label="<?php echo esc_attr($image_alt); ?>">

                      <?php if($i === 0): ?>
                          <img
                              src="<?php echo esc_url($large); ?>"
                              alt="<?php echo esc_attr($image_alt); ?>"
                              fetchpriority="high"
                          >
                      <?php else: ?>
                          <img
                              decoding="async"
                              class="lazyload"
                              data-expand="1"
                              src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                              data-src="<?php echo esc_url($large); ?>"
                              alt="<?php echo esc_attr($image_alt); ?>"
                          >
                      <?php endif; ?>

                  </a>
              </div>
          <?php
              $i++;
          endforeach;
        ?>
    </div>
    </div>
  </div>
  
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const thumbsEl = document.querySelector('[data-slider-id="thumbs"]');
      const mainEl = document.querySelector('[data-slider-id="main"]');

      if (!thumbsEl || !mainEl) return;

      const wait = setInterval(() => {
        const thumbs = thumbsEl?.wpInstance?.slider;
        const main = mainEl?.wpInstance?.slider;

        if (!thumbs || !main) return;

        clearInterval(wait);

        // -----------------------------
        // 1️⃣ СИНХРОНІЗАЦІЯ WooCommerce
        // -----------------------------
        main.thumbs.swiper = thumbs;
        main.thumbs.init();
        main.thumbs.update();

        // -----------------------------
        // 2️⃣ Клік на картинку → перехід
        // -----------------------------
        thumbs.slides.forEach((slide, index) => {
          slide.addEventListener("click", () => {
            main.slideTo(index);
          });
        });

        // -----------------------------
        // 3️⃣ Прив’язуємо стрілки thumbs
        // -----------------------------
        const nextBtn = document.querySelector(".custom-gallery-thumbs-next");
        const prevBtn = document.querySelector(".custom-gallery-thumbs-prev");

        if (nextBtn) {
          nextBtn.addEventListener("click", () => {
            thumbs.slideNext();
          });
        }
        if (prevBtn) {
          prevBtn.addEventListener("click", () => {
            thumbs.slidePrev();
          });
        }

        // -----------------------------
        // 4️⃣ Тримаємо активний слайд синхронізованим
        // -----------------------------
        thumbs.on("slideChange", () => {
          main.slideTo(thumbs.activeIndex);
        });

        main.on("slideChange", () => {
          thumbs.slideTo(main.activeIndex);
        });

        // -----------------------------
        // 5️⃣ Підтримка варіацій WooCommerce
        // -----------------------------
        const variationForm = document.querySelector('.variations_form');

        if (variationForm) {
          jQuery(variationForm).on('found_variation', function(event, variation) {
            if (variation.image && variation.image.src) {
              // Варіація має зображення - оновлюємо галерею
              updateGalleryForVariation(variation);
            }
          });

          jQuery(variationForm).on('reset_data', function() {
            // Повертаємо оригінальну галерею при скиданні варіації
            resetGallery();
          });
        }

        // Збереження оригінального першого слайда
        const originalFirstMainSlide = main.slides[0].cloneNode(true);

        function updateGalleryForVariation(variation) {
          // Замінюємо тільки перший слайд у головному слайдері
          const firstMainSlide = main.slides[0];

          if (firstMainSlide) {
            const newMainSlideHTML = `
              <a href="${variation.image.full_src}" data-fancybox="gallery" aria-label="${variation.image.alt}">
                <img src="${variation.image.src}" alt="${variation.image.alt}" fetchpriority="high">
              </a>
            `;

            firstMainSlide.innerHTML = newMainSlideHTML;

            // Оновлюємо головний слайдер і переходимо на перший слайд
            main.update();
            main.slideTo(0);

            // Re-initialize Fancybox
            if (window.Fancybox) {
              Fancybox.bind('[data-fancybox="gallery"]');
            }
          }
        }

        function resetGallery() {
          // Повертаємо оригінальний перший слайд
          const firstMainSlide = main.slides[0];

          if (firstMainSlide && originalFirstMainSlide) {
            firstMainSlide.innerHTML = originalFirstMainSlide.innerHTML;

            // Оновлюємо головний слайдер і переходимо на перший слайд
            main.update();
            main.slideTo(0);

            // Re-initialize Fancybox
            if (window.Fancybox) {
              Fancybox.bind('[data-fancybox="gallery"]');
            }
          }
        }

      }, 100);

      setTimeout(() => clearInterval(wait), 5000);
    });
  </script>
</div>



