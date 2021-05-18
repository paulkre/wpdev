<?php

namespace WPDev\Lib\ACF;

class Util
{
  const SEPARATOR = '__';

  static function acf_active()
  {
    return class_exists('ACF');
  }

  static function &parse_groups(string $name, &$groups)
  {
    $parsed = [];

    foreach ($groups as $group_key => &$group) {
      @$fields = $group['fields'];
      if (!$fields) return;

      $key = $name . self::SEPARATOR . $group_key;
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

      $key = $parent_key . self::SEPARATOR . $field_key;
      $field['key'] = $field['name'] = $key;

      if (@$field['type'] == 'repeater' && is_array(@$field['sub_fields'])) {
        $field['sub_fields'] = self::parse_fields($key, $field['sub_fields']);

        \add_filter(
          "acf/load_value/key=$key",
          function ($items) use ($key) {
            $clean_items = [];

            $prefix_length = strlen($key) + strlen(self::SEPARATOR);
            foreach ($items as $item) {
              $clean_item = [];
              foreach ($item as $prefixed_key => $value)
                $clean_item[substr($prefixed_key, $prefix_length)] = $value;
              $clean_items[] = $clean_item;
            }

            return $clean_items;
          },
          99
        );
      }

      $parsed[] = $field;
    }

    return $parsed;
  }
}
