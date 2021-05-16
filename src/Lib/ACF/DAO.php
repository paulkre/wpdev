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
        get_page_template_slug()
      )
      : $post->post_type;

    $this->post = $post;
  }

  function get($args)
  {
    if (!Util::acf_active()) return false;
    return \get_field($this->args_to_key($args), $this->post);
  }

  function update($args, $value)
  {
    if (!Util::acf_active()) return false;
    return \update_field($this->args_to_key($args), $value, $this->post);
  }

  private function args_to_key($args)
  {
    array_unshift($args, $this->entity_name);
    return implode(Util::SEPARATOR, $args);
  }
}
