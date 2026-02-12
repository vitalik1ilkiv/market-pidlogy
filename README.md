# Market Pidlogy - WordPress Theme

WordPress-theme for the online store **Market Pidlogy** with WooCommerce integration.

**Author:** Klif (Vitalii I.) — [studio.klifcom.net](https://studio.klifcom.net/uk/)

## Requirements

- WordPress 6.x
- WooCommerce
- ACF Pro
- PHP 8.x
- Node.js 18+

## Installation

```bash
npm install
```

## Development

```bash
# Watch mode with BrowserSync (localhost:3000, proxy -> localhost/market-pidlogy)
npm run dev

# Production build
npm run build

# Clear dist folder
npm run clear
```

## Structure

```
market-pidlogy/
├── assets/
│   ├── fonts/                  # Custom fonts
│   ├── img/                    # Images, logos, icons
│   ├── js/                     # JS utilities (utils.js, favorites, etc.)
│   ├── scss/
│   │   ├── base/               # Variables, mixins, media queries, reset, core
│   │   ├── layout/             # Header, footer, actions, animations
│   │   ├── components/         # Breadcrumbs, dropdowns, forms, filters, search
│   │   ├── woocommerce/        # Product cards, archive, single product, cart
│   │   ├── blocks/             # Section products, main slider, brands, news grid
│   │   ├── lib/                # Swiper, Arctic Modal
│   │   └── styles.scss         # Main entry (imports all partials)
│   └── index.js                # JS entry point
├── dist/                       # Compiled assets (styles.css, scripts.js)
├── inc/
│   ├── woocommerce/
│   │   ├── woocommerce.php     # WC hooks, filters, customizations
│   │   ├── live-search.php     # AJAX product search
│   │   ├── product-filters.php # Sidebar filters (price, attributes)
│   │   └── send-order-in-telegram.php
│   ├── constants.php           # Theme constants
│   ├── utils.php               # Helpers (breadcrumbs, sanitize, etc.)
│   └── svg-sprite.php          # SVG icon sprite
├── template-pages/
│   ├── template-home.php       # Homepage
│   ├── template-brands.php     # Brands listing (A-Z)
│   ├── template-favorites.php  # Favorites page
│   └── template-sale.php       # Sale page
├── template-parts/
│   ├── blocks/                 # Reusable page sections
│   ├── woocommerce/product/    # Product card components
│   └── news-item.php           # News/article card
├── woocommerce/                # WooCommerce template overrides
├── archive.php                 # Blog/news archive (4-column grid)
├── index.php                   # Default template
├── search.php                  # Search results
├── header.php
├── footer.php
├── functions.php               # Theme setup, assets, menus
└── style.css                   # Theme metadata
```

## SCSS Architecture

**Breakpoints:**

| Mixin            | Width       | Usage         |
|------------------|-------------|---------------|
| `mobile-small`   | max 390px   | Small phones  |
| `mobile`         | max 767px   | Mobile        |
| `tablet-p`       | min 768px   | Tablet portrait |
| `tablet-l`       | min 1024px  | Tablet landscape |
| `laptop`         | min 1280px  | Laptop        |
| `desktop`        | min 1440px  | Desktop       |
| `desktop-xxl`    | min 1920px  | Large desktop |

**Key mixins:**

- `@include hover { ... }` - hover styles (only on hover-capable devices)
- `@include anim-change-color($color, $hover-color)` - color transition on hover
- `@include underline-normal` / `underline-reverse` - animated underline
- `res($min, $max)` - fluid responsive value (clamp between 430px-1920px)

**Conventions:**

- BEM naming: `.block__element--modifier`
- Lazy loading: `class="lazyload"` + `data-src="..."`
- SVG icons via sprite: `<use xlink:href="#icon-name"></use>`

## Menus

- `header_menu` - Main navigation
- `footer_menu` - Footer column 1
- `footer_menu2` - Footer column 2

## Image Sizes

- `product-card` - 350x350 (cropped)
- `medium_large` - 768px (WP default, used for news cards)
