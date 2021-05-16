<?php

namespace WPDev\Lib\ACF;

use WPDev\Theme;

class Page
{
  static function register_template(string $path, $props)
  {
    if (!Util::acf_active()) return;

    @$groups = $props['acf_groups'];
    if (!$groups) return;

    $groups = Util::parse_groups(
      self::template_path_to_name($path),
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
    if (!Util::acf_active() || !is_array($field_data)) return;

    foreach ($field_data as $key => $value) {
      if (\get_field($key, $pid)) continue;
      \update_field($key, $value, $pid);
    }
  }

  static function template_path_to_name($path)
  {
    return explode('.', basename($path))[0];
  }
}
