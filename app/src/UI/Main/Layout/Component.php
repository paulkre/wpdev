<?php

namespace Src\UI\Main\Layout;

use WPDev\Theme;

class Component extends \WPDev\Component
{
  function render()
  {
?>
    <!DOCTYPE html>
    <html lang="<?= \get_locale() ?>">

    <head>

      <meta charset="<?= \get_bloginfo('charset') ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

      <?php \wp_head(); ?>

      <?php
      $this->render_favicon_links();
      $this->render_metadata();
      ?>

    </head>

    <body data-rest-url="<?= \get_rest_url() ?>" <?php body_class(); ?>>

      <?php

      \wp_body_open();

      $this->render_children();

      \wp_footer();

      ?>

    </body>

    </html>
  <?php
  }

  private function render_metadata()
  {
    if (\is_front_page()) {
      $title = \get_bloginfo('name');
      if ($tag_line = \get_bloginfo('description')) $title .= ' – ' . $tag_line;
    } else {
      $post = \get_post();
      $title = ($post ? $post->post_title . ' – ' : null) . \get_bloginfo('name');
    }
    $title = esc_html($title);

    $img_id = $this->props['img-id'] ?? \get_post_thumbnail_id();
    $img_data = \wp_get_attachment_image_src($img_id, 'large');

    $url = $this->props['url'] ?? \get_permalink();
    $desc = $this->props['description'] ?? \get_the_excerpt();
    if (!$desc) $desc = Theme::get_field('website__general-settings__description', 'options');
    if ($desc) $desc = esc_html($desc);

  ?>
    <?php if ($keywords = self::get_keywords()) : ?>
      <meta name="keywords" content="<?= $keywords ?>" />
    <?php endif ?>
    <meta property="og:title" content="<?= $title ?>" />
    <meta property="og:url" content="<?= $url ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="<?= \get_locale() ?>" />
    <?php if ($desc) : ?>
      <meta property="og:description" content="<?= $desc ?>" />
      <meta name="description" content="<?= $desc ?>" />
    <?php endif ?>
    <?php if ($img_data) : ?>
      <meta property="og:image" content="<?= $img_data[0] ?>" />
      <meta property="og:image:width" content="<?= $img_data[1] ?>" />
      <meta property="og:image:height" content="<?= $img_data[2] ?>" />
    <?php endif ?>
  <?php
  }

  private function render_favicon_links()
  {
    $url = $this->props['favicon-url'] ?? \get_template_directory_uri() . '/static/meta';
  ?>
    <link rel="apple-touch-icon" sizes="57x57" href="<?= $url ?>/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= $url ?>/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= $url ?>/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $url ?>/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= $url ?>/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= $url ?>/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= $url ?>/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= $url ?>/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $url ?>/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $url ?>/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $url ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $url ?>/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $url ?>/favicon-16x16.png">
    <link rel="manifest" href="<?= $url ?>/manifest.json">
    <meta name="msapplication-TileColor" content="#1c1c1c">
    <meta name="msapplication-TileImage" content="<?= $url ?>/ms-icon-144x144.png">
    <meta name="theme-color" content="#1c1c1c">
<?php
  }

  private static function get_keywords()
  {
    $post = \get_post();
    if (!$post) return;

    $tag_terms = \get_the_terms($post, 'post_tag');
    if (!$tag_terms) return;

    return implode(
      ", ",
      array_map(function ($term) {
        return esc_html($term->name);
      }, $tag_terms)
    );
  }
}
