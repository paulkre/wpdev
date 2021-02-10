<?php

namespace WPDev\Util;

class Image
{
  const PREFIX = 'theme__';

  private static $sizes = [
    'thumb' => 48,
    'sm' => 512,
    'md' => 1024,
    'lg' => 1536,
    'xl' => 2048,
    'xxl' => 2560
  ];

  private static $size_names;

  static function init()
  {
    self::$size_names = [];
    foreach (self::$sizes as $id => $size) {
      $name = self::PREFIX . $id;
      \add_image_size($name, $size, $size, false);
      self::$size_names[$id] = $name;
    }
  }

  static function get_image_source(int $id, string $name)
  {
    return \wp_get_attachment_image_src($id, self::$size_names[$name]);
  }

  static function get_thumb_source(int $id)
  {
    return self::get_image_source($id, 'thumb');
  }

  static function get_large_source(int $id)
  {
    return self::get_image_source($id, 'lg');
  }

  static function get_xxl_source(int $id)
  {
    return self::get_image_source($id, 'xxl');
  }
}
