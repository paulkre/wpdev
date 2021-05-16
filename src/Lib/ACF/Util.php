<?php

namespace WPDev\Lib\ACF;

class Util
{
  const SEP = '__';

  static function &parse_groups(string $name, &$groups)
  {
    $parsed = [];

    foreach ($groups as $group_key => &$group) {
      @$fields = $group['fields'];
      if (!$fields) return;

      $key = $name . self::SEP . $group_key;
      $group['key'] = $key;
      $group['menu_order'] = count($parsed);
      $group['fields'] = self::parse_fields($key, $fields);
      $parsed[] = $group;
    }

    return $parsed;
  }

  private static function parse_fields(string $parent_key, $fields)
  {
    $parsed = [];

    foreach ($fields as $field_key => $field) {
      if (empty($field['type']) || empty($field['label'])) continue;

      $key = $parent_key . self::SEP . $field_key;
      $field['key'] = $field['name'] = $key;

      if (@$field['type'] == 'repeater' && is_array(@$field['sub_fields']))
        $field['sub_fields'] = self::parse_fields($key, $field['sub_fields']);

      $parsed[] = $field;
    }

    return $parsed;
  }
}
