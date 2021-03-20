<?php

namespace Src\UI\Main\PageTitle;

class Component extends \WPDev\Component
{
  static function render($props = null)
  {
?>
    <div style="padding: 1.5rem; background: #00aacc">
      <?php if ($title = $props['title'] ?? false) : ?>
        <span><?= $title ?></span>
      <?php endif ?>
    </div>
<?php
  }
}
