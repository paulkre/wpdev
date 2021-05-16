<?php

namespace WPDev\Lib\ACF;

use WPDev\Theme;

class OptionsPage
{
  private static $registered_count = 0;

  static function register(string $name, $props)
  {
    if (!Util::acf_active()) return;

    @[
      'title' => $title,
      'icon_url' => $icon_url,
      'use_tabs' => $use_tabs,
      'acf_groups' => $groups,
    ] = $props;

    if (!$groups) return;

    $groups = Util::parse_groups($name, $groups);

    \acf_add_options_page([
      'page_title'   => $title,
      'menu_title'  => $title,
      'menu_slug'   => $name,
      'capability'  => 'manage_options',
      'redirect'    => false,
      'icon_url' => $icon_url,
      'position' => 100 + self::$registered_count
    ]);
    self::$registered_count++;

    $location = [[[
      'param' => 'options_page',
      'operator' => '==',
      'value' => $name
    ]]];

    if (!$use_tabs) {
      foreach ($groups as $group) {
        $group['location'] = $location;
        \acf_add_local_field_group($group);
      }
    } else {
      $fields = [];

      foreach ($groups as $group) {
        $fields[] = [
          'key' => $group['key'],
          'name' => $group['key'],
          'label' => $group['title'],
          'type' => 'tab',
        ];

        foreach ($group['fields'] as $field)
          $fields[] = $field;

        \acf_add_local_field_group([
          'key' => $name,
          'title' => __('Settings'),
          'fields' => $fields,
          'location' => $location,
          'style' => 'seamless'
        ]);
      }
    }
  }

  static function initialize_fields($field_data)
  {
    if (!Util::acf_active() || !is_array($field_data)) return;

    foreach ($field_data as $key => $value) {
      if (!empty(\get_field($key, 'options'))) continue;
      \update_field($key, $value, 'options');
    }
  }
}
