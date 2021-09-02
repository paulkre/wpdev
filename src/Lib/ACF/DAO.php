<?php

namespace WPDev\Lib\ACF;

class DAO
{
  private $entity;
  private $entity_name;
  private $acf_inactive;

  function __construct($entity)
  {
    $is_options = is_string($entity);

    $this->entity_name = $is_options
      ? $entity : ($entity->post_type === 'page'
        ? Page::template_path_to_name(
          get_page_template_slug($entity)
        )
        : $entity->post_type);

    $this->entity = $is_options ? 'options' : $entity;
    $this->acf_inactive = !Util::acf_active();
  }

  function get($key_value, $format_value = true)
  {
    if ($this->acf_inactive || !$key_value) return false;
    return \get_field($this->parse_key($key_value), $this->entity, $format_value);
  }

  function update($key_value, $value)
  {
    if ($this->acf_inactive || !$value) return false;
    return \update_field($this->parse_key($key_value), $value, $this->entity);
  }

  private function parse_key($value)
  {
    if (is_string($value)) $value = explode('/', $value);
    array_unshift($value, $this->entity_name);
    return implode(Util::SEPARATOR, $value);
  }
}
