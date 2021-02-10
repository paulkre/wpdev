<?php

namespace WPDev;

class Enqueue
{
  const WPACKIO_ENTRIES_FILTER_KEY = 'theme/wpackio';

  public function __construct(string $app_name, string $entry_group = 'app', string $output_path = 'dist', string $entry_namespace = 'Src\\\UI', array $static_entries = [])
  {
    $this->entry_namespace = $entry_namespace;

    \add_action('wp_enqueue_scripts', function () use ($app_name, $entry_group, $output_path, $static_entries) {
      $enqueue = new \WPackio\Enqueue($app_name ?? 'app', $output_path, null, 'theme');
      $entries = \apply_filters(self::WPACKIO_ENTRIES_FILTER_KEY, $static_entries);
      foreach ($entries as &$entry)
        $enqueue->enqueue($entry_group, $entry, []);
    });
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

      preg_match('/^' . $this->entry_namespace . '\\\(\w+)\\\/', get_class($data), $matches);
      if (@$entry = $matches[1])
        if (!in_array($entry, $entries))
          $entries[] = $entry;

      if (!$data::$do_not_enqueue_children)
        self::collect_entries($entries, $data->children);
    }
  }
}
