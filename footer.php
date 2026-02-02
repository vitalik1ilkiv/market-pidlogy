    </div>
    <!-- END: Main wrapper -->

    <!-- Site Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer__wrap">
          <div class="footer__column" style="padding-right: 2rem;">
            <div class="footer__logo">
              <a href="/">
                <img
                  src="<?php echo esc_url( THEME_ASSETS . '/img/logo.png' ); ?>"
                  alt="Маркет підлоги"
                  width="250"
                  height="47"
                  loading="lazy"
                  decoding="async"
                >
              </a>
            </div>

            <div class="footer__image">
              <?php
                $image_pay = get_field('image_pay', 'option');

                if ( ! empty($image_pay) && is_array($image_pay) ) : ?>
                  <img
                    class="lazyload"
                    src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                    data-src="<?php echo esc_url( $image_pay['url'] ?? '' ); ?>"
                    alt="<?php echo esc_attr( $image_pay['alt'] ?? '' ); ?>"
                    width="<?php echo esc_attr( $image_pay['width'] ?? '' ); ?>"
                    height="<?php echo esc_attr( $image_pay['height'] ?? '' ); ?>"
                    loading="lazy"
                    decoding="async"
                  >
                <?php endif; ?>
            </div>
          </div>

          <?php
            $title_col_1 = get_field('title_col_1', 'option');
            $title_col_2 = get_field('title_col_2', 'option');
            $title_col_3 = get_field('title_col_3', 'option');
            $title_col_4 = get_field('title_col_4', 'option');

            $location_text = get_field('location_text', 'option');
            $location_link = get_field('location_link', 'option');
            $schedule_work = get_field('schedule_work', 'option');
            $email = get_field('email', 'option');
          ?>

          <?php if ( !empty($title_col_1) ): ?>
            <div class="footer__column">
              <h4 class="footer__column-title"><?php echo esc_html( $title_col_1 ); ?></h4>
              <div class="footer__column-body">
                <?php
                  wp_nav_menu([
                      'theme_location' => 'footer_menu',
                  ]);
                ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( !empty($title_col_2) ): ?>
            <div class="footer__column">
              <h4 class="footer__column-title"><?php echo esc_html( $title_col_2 ); ?></h4>
              <div class="footer__column-body">
                <?php
                  wp_nav_menu([
                      'theme_location' => 'footer_menu2',
                  ]);
                ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( !empty($title_col_3) || !empty($location_text) || ( get_field_object('phones', 'option') && have_rows('phones', 'option') ) ): ?>
            <div class="footer__column">
              <?php if ( !empty($title_col_3) ): ?>
                <h4 class="footer__column-title"><?php echo esc_html( $title_col_3 ); ?></h4>
              <?php endif; ?>

              <div class="footer__column-body">
                <?php if ( !empty($location_text) && !empty($location_link) ): ?>
                  <p>
                    <a class="action action-color text-start" href="<?php echo esc_url( $location_link ); ?>" style="text-wrap: wrap;">
                      <?php echo esc_html( $location_text ); ?>
                    </a>
                  </p>
                <?php endif; ?>

                <?php if ( get_field_object('phones', 'option') && have_rows('phones', 'option') ): ?>
                  <?php while ( have_rows('phones', 'option') ) : the_row();
                    $phone = get_sub_field('phone');
                    if ( empty($phone) ) continue;
                  ?>
                    <p class="mb-0">
                      <a class="action action-color" href="tel:<?php echo esc_attr( sanitize_phone( $phone ) ); ?>">
                        <?php echo esc_html( $phone ); ?>
                      </a>
                    </p>
                  <?php endwhile; ?>
                <?php endif; ?>

                <?php if ($email) : ?>
                  <p class= "mt-1">
                    <a class="action action-color" href="mailto:<?php echo $email ?>"><?php echo $email; ?></a>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( !empty($title_col_4) || !empty($schedule_work) ): ?>
            <div class="footer__column">
              <?php if ( !empty($title_col_4) ): ?>
                <h4 class="footer__column-title"><?php echo esc_html( $title_col_4 ); ?></h4>
              <?php endif; ?>

              <div class="footer__column-body">
                <?php if ( !empty($schedule_work) ): ?>
                  <div class="schedule-work">
                    <?php echo wp_kses_post( $schedule_work ); ?>
                  </div>

                  <?php if ( !empty($location_link) ): ?>
                    <p class="mt-1">
                      <a class=" action action-color" href="<?php echo esc_url( $location_link ); ?>">
                        <?php _e('Travel card', 'market-pidlogy'); ?>
                      </a>
                    </p>
                  <?php endif; ?>
                <?php endif; ?>

                <?php get_template_part('./template-parts/social'); ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </footer>
    <!-- END: Site Footer -->

  <?php wp_footer(); ?>
</body>
</html>
