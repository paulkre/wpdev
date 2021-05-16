<?php

namespace WPDev\Lib\ACF;

use WPDev\Theme;

class Page
{
  static function register_template(string $path, $props)
  {
    if (!function_exists('acf_add_local_field_group')) return;

    @$groups = $props['acf_groups'];
    if (!$groups) return;

    $groups = Util::parse_groups(
      explode('.', basename($path))[0],
      $groups
    );

    foreach ($groups as $group) {
      $group['location'] = [[[
        'param' => 'page_template',
        'operator' => '==',
        'value' => $path
      ]]];

      \acf_add_local_field_group($group);
    }
  }

  static function initialize_fields(int $pid, $field_data)
  {
    if (!is_array($field_data)) return;

    foreach ($field_data as $key => $value) {
      if (Theme::get_field($key, $pid)) continue;
      Theme::update_field($key, $value, $pid);
    }
  }
}
