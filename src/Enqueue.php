<?php

namespace WPDev;

class Enqueue
{
  const WPACKIO_ENTRIES_FILTER_KEY = 'theme/wpackio';

  public function __construct(string $app_name, string $entry_group = 'app', string $output_path = 'dist', string $entry_resolver_regex = '/^Src\\\UI\\\(\w+)\\\/')
  {
    $this->entry_resolver_regex = $entry_resolver_regex;
    $this->entry_group = $entry_group;
    $this->enqueue = new \WPackio\Enqueue($app_name ?? 'app', $output_path, null, 'theme');

    \add_action('wp_enqueue_scripts', function () {
      $entries = \apply_filters(self::WPACKIO_ENTRIES_FILTER_KEY, []);
      foreach ($entries as &$entry)
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

    \add_filter(self::WPACKIO_ENTRIES_FILTER_KEY, function ($entries) use ($content_entries) {
      return array_merge($entries, $content_entries);
    });
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
