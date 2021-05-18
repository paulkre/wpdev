<?php

require_once get_template_directory() . '/vendor/autoload.php';
require_once dirname(dirname(get_template_directory())) . '/wpdev/vendor/autoload.php';

use WPDev\Theme;

$landing_page_template_path = 'templates/landing-page.php';

Theme::register_page_template($landing_page_template_path, [
  'disable_editor' => true,

  'acf_groups' => [
    'settings' => [
      'title' => 'Settings',
      'fields' => [
        'publication' => [
          'type' => 'text',
          'label' => 'Publication',
        ],
        'authors' => [
          'type' => 'repeater',
          'label' => 'Authors',
          'sub_fields' => [
            'name' => [
              'type' => 'text',
              'label' => 'Name',
            ],
            'age' => [
              'type' => 'number',
              'label' => 'Age',
            ],
            'possessions' => [
              'type' => 'repeater',
              'label' => 'Possessions',
              'sub_fields' => [
                'value' => [
                  'label' => 'Value',
                  'type' => 'text',
                ],
                'name' => [
                  'label' => 'Name',
                  'type' => 'text',
                ]
              ]
            ]
          ]
        ],
      ]
    ],

    'metadata' => [
      'title' => 'Metadata',
      'fields' => [
        'value' => [
          'label' => 'Value',
          'type' => 'text',
        ]
      ]
    ]
  ]
]);

Theme::register_activation_callback(function () use ($landing_page_template_path) {
  $pid = Theme::create_page('landing-page', 'WPDev', 'This is the landing page.', $landing_page_template_path, []);

  if (!\get_option('page_on_front')) {
    \update_option('page_on_front', $pid);
    \update_option('show_on_front', 'page');
  }

  if (!\get_post_thumbnail_id($pid)) {
    $id = \WPDev\Util\ThemeMedia::get_id('static/sample.png');
    \set_post_thumbnail($pid, $id);
  }
});

$enqueue = new WPDev\Enqueue('WPDev');

$enqueue->add_entry('main');

Theme::init($enqueue);

add_theme_support('post-thumbnails');
