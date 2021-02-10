<?php

namespace Src\Entities;

use WPDev\Theme;

class LandingPage
{
  const TEMPLATE = 'templates/landing-page.php';

  static function register()
  {
    Theme::register_page_template(self::TEMPLATE, [
      'disable_editor' => true,
    ]);

    Theme::register_activation_callback(function () {
      $pid = Theme::create_page('landing-page', 'WPDev', 'This is the landing page.', self::TEMPLATE, []);

      if (!\get_option('page_on_front')) {
        \update_option('page_on_front', $pid);
        \update_option('show_on_front', 'page');
      }

      if (!\get_post_thumbnail_id($pid)) {
        $id = \WPDev\Util\ThemeMedia::get_id('static/sample.png');
        \set_post_thumbnail($pid, $id);
      }
    });
  }
}
