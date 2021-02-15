<?php

namespace WPDev;

class Enqueue
{
  public function __construct(string $app_name, string $entry_group = 'app', string $output_path = 'dist', string $entry_resolver_regex = '/^Src\\\UI\\\(\w+)\\\/')
  {
    $this->entry_resolver_regex = $entry_resolver_regex;
    $this->entry_group = $entry_group;
    $this->enqueue = new \WPackio\Enqueue($app_name ?? 'app', $output_path, null, 'theme');
    $this->main_entries = [];

    \add_action('wp_enqueue_scripts', function () {
      foreach ($this->main_entries as &$entry)
        $this->enqueue($entry);
    });
  }

  function enqueue(string $entry, $entry_group = null)
  {
    $this->enqueue->enqueue($entry_group ?? $this->entry_group, $entry, []);
  }

  function enqueue_assets($content)
  {
    $content_entries = [];
    self::collect_entries($content_entries, $content);
    if (!$content_entries) return;

    $this->main_entries = array_merge($this->main_entries, $content_entries);
  }

  private function collect_entries(array &$entries, &$data)
  {
    if (!$data) return;

    if (is_array($data))
      foreach ($data as &$item)
        self::collect_entries($entries, $item);

    if ($data instanceof \WPDev\Component) {
      if ($data->do_not_enqueue) return;

      preg_match($this->entry_resolver_regex, get_class($data), $matches);
      if (@$entry = $matches[1])
        if (!in_array($entry, $entries))
          $entries[] = $entry;

      if (!$data::$do_not_enqueue_children)
        self::collect_entries($entries, $data->children);
    }
  }
}
