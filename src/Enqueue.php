<?php

namespace WPDev;

class Enqueue
{
  public function __construct(string $app_name, string $entry_group = 'app', string $output_path = 'dist')
  {
    $this->entry_group = $entry_group;
    $this->enqueue = new \WPackio\Enqueue($app_name, $output_path, null, 'theme');
    $this->manifests = [];
    $this->entries = [];

    \add_action('wp_enqueue_scripts', function () {
      foreach ($this->entries as &$entry)
        $this->enqueue($entry[0], $entry[1]);
    });
  }

  function add_entry(string $entry, $entry_group = null)
  {
    $this->entries[] = [$entry, $entry_group];
  }

  function add_entries(...$entries)
  {
    foreach ($entries as $entry) {
      if (is_array($entry)) $this->add_entry($entry[0], $entry[1]);
      else $this->add_entry($entry);
    }
  }

  function enqueue(string $entry, $entry_group = null)
  {
    $this->enqueue->enqueue($entry_group ?? $this->entry_group, $entry, []);
  }

  function get_asset_by_filename($filename, $entry_group = null)
  {
    if (!$entry_group) $entry_group = $this->entry_group;
    @$manifest = $this->manifests[$entry_group];

    if (!$manifest)
      $manifest = $manifests[$entry_group] = $this->enqueue->get_manifest($entry_group);

    return @$manifest["$this->entry_group/assets/$filename"];
  }
}
