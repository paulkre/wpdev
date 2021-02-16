<?php

namespace WPDev;

class Component
{
  public $props;
  public $children;

  function __construct($props = null, $children = null)
  {
    $this->props = $props;
    $this->children = $children;
  }

  protected function render_children()
  {
    if ($this->children === null) return;

    if (!self::is_valid_component($this->children))
      throw new \Exception('Child component is invalid (' . self::class . ')');

    self::render_content($this->children);
  }

  static function render_content($content)
  {
    if (!$content) return;
    if (is_array($content))
      foreach ($content as &$item)
        self::render_content($item);
    else if ($content instanceof Component)
      $content->render();
    else if (is_string($content))
      echo $content;
    else if (is_callable($content))
      $content();
  }

  static function is_valid_component($value)
  {
    return is_array($value) || $value instanceof Component || is_string($value) || is_callable($value);
  }

  function render()
  {
    $this->render_children();
  }
}
