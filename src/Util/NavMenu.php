<?php

namespace WPDev\Util;

class NavMenu
{
  static function create(string $menu_location, string $menu_name, array $items)
  {
    $menu_id = \wp_create_nav_menu($menu_name);

    if (\is_wp_error($menu_id) && key_exists('menu_exists', $menu_id->errors)) {
      $menu_id = \get_term_by('name', $menu_name, 'nav_menu')->term_id;
    } else {
      foreach ($items as &$item)
        if ($item)
          self::create_menu_item($menu_id, $item);

      \WPDev\Theme::admin_print(
        'Navigation menu created at "' . $menu_location . '" location with ' . sizeof($items) . ' items.',
        'success',
        true
      );
    }

    add_action('init', function () use ($menu_id, $menu_location) {
      $locations = \get_theme_mod('nav_menu_locations');
      $locations[$menu_location] = $menu_id;
      \set_theme_mod('nav_menu_locations', $locations);
    }, 999);
  }

  private static function create_menu_item($menu_id, $item, $parent_item = null)
  {
    @[
      'title' => $title,
      'object-id' => $object_id,
      'object' => $object,
      'type' => $type,
      'url' => $url,

      'sub_items' => $sub_items,
    ] = $item;

    if (!$title || !$type) return;

    $item_id = \wp_update_nav_menu_item($menu_id, 0, [
      'menu-item-title'   =>  $title,
      'menu-item-object-id' => $object_id,
      'menu-item-object' => $object,
      'menu-item-url' => $url,
      'menu-item-status' => 'publish',
      'menu-item-type' => $type,
      'menu-item-parent-id' => $parent_item
    ]);

    if ($sub_items)
      foreach ($sub_items as &$sub_item)
        self::create_menu_item($menu_id, $sub_item, $item_id);
  }
}
