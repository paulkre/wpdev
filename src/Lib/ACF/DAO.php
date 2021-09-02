<?php

namespace WPDev\Lib\ACF;

class DAO
{
  private $post;
  private $entity_name;

  function __construct(\WP_Post $post)
  {
    $this->entity_name = $post->post_type === 'page'
      ? Page::template_path_to_name(
        get_page_template_slug($post)
      )
      : $post->post_type;

    $this->post = $post;
  }

  function get($key_value, $format_value = true)
  {
    if (!Util::acf_active() || !$key_value) return false;
    return \get_field($this->parse_key($key_value), $this->post, $format_value);
  }

  function update($key_value, $value)
  {
    if (!Util::acf_active() || !$value) return false;
    return \update_field($this->parse_key($key_value), $value, $this->post);
  }

  private function parse_key($value)
  {
    if (is_string($value)) $value = explode('/', $value);
    array_unshift($value, $this->entity_name);
    return implode(Util::SEPARATOR, $value);
  }
}
