<?php

namespace WPDev;

class Theme
{
  const ACTIVATE_ACTION = 'theme/activate';
  const REST_NAMESPACE = 'theme/v1';

  const PROPS_FILTER_KEY = 'theme/props';
  const DEFAULT_PROPS = ['enqueue' => null, 'static_component' => null];

  private static $initialized = false;

  static function init(Enqueue $enqueue = null, string $static_component = null)
  {
    if (self::$initialized) return;
    self::$initialized = true;

    self::handle_activation();

    \add_filter(self::PROPS_FILTER_KEY, function () use ($enqueue, $static_component) {
      return ['enqueue' => $enqueue, 'static_component' => $static_component];
    });
  }

  private static function get_props()
  {
    return \apply_filters(self::PROPS_FILTER_KEY, self::DEFAULT_PROPS);
  }

  static function get_enqueue()
  {
    return self::get_props()['enqueue'];
  }

  static function register_post_type(string $name, array $props)
  {
    Lib\WP\PostType::register($name, $props);
    Lib\ACF\PostType::register($name, $props);
  }

  static function register_page_template(string $path, array $props)
  {
    Lib\WP\Page::register_template($path, $props);
    Lib\ACF\Page::register_template($path, $props);
  }

  static function register_options(string $name, array $props)
  {
    Lib\ACF\OptionsPage::register($name, $props);
  }

  static function register_activation_callback(callable $cb, $priority = 10)
  {
    \add_action(self::ACTIVATE_ACTION, $cb, $priority);
  }

  private static function handle_activation()
  {
    \add_action('init', function () {
      if (!empty($_GET['activated']) && is_admin())
        do_action(self::ACTIVATE_ACTION);
    });
  }

  static function render($content = null)
  {
    ['static_component' => $static_component, 'enqueue' => $enqueue] = self::get_props();
    if ($static_component) $content = $static_component;

    if ($enqueue) $enqueue->enqueue_assets($content);

    Component::render_content($content);
  }

  static function get_post_by_slug(string $slug, string $post_type = 'page')
  {
    $res = \get_posts([
      'name' => $slug,
      'post_type' => $post_type,
      'post_status' => 'publish',
    ]);
    return $res ? $res[0] : null;
  }

  static function create_page(string $slug, string $title, string $excerpt, $template_path = null, $field_data = null)
  {
    $id = Lib\WP\Page::create($slug, $title, $excerpt, $template_path);
    if ($field_data) Lib\ACF\Page::initialize_fields($id, $field_data);
    return $id;
  }

  static function get_field($selector, $post_id = false, $format_value = true)
  {
    return \function_exists('get_field') ? \get_field($selector, $post_id, $format_value) : false;
  }

  static function update_field($selector, $value, $post_id = false)
  {
    return \function_exists('update_field') ? \update_field($selector, $value, $post_id) : false;
  }

  static function admin_print($msg, $type = null, $is_dismissable = false)
  {
    \add_action('admin_notices', function () use ($msg, $type, $is_dismissable) {
?>
      <div class="notice<?= $type ? " notice-$type" : null ?><?= $is_dismissable ? ' is-dismissable' : null ?>">
        <p><?= $msg ?></p>
      </div>
<?php
    });
  }
}
