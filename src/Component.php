<?php

namespace WPDev;

class Component
{
  public $props;

  function __construct($props = null)
  {
    $this->props = $props;
  }

  static function render_children($content)
  {
    if (!$content) return;

    if (is_array($content))
      foreach ($content as &$item)
        self::render_children($item);
    else if ($content instanceof Component)
      $content->render_instance();
    else if (is_string($content))
      echo $content;
    else if (is_callable($content))
      $content();
    else
      throw new \Exception('Child component is invalid (' . self::class . ')');
  }

  static function render($props = null)
  {
    self::render_children(@$props['children']);
  }

  function render_instance()
  {
    static::render($this->props);
  }
}
