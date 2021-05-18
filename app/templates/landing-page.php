<?php /* Template Name: Landing Page */

WPDev\Theme::get_enqueue()->add_entries('content');

Src\UI\Main\Layout\Component::render([
  'children' => new Src\UI\Main\PageTitle\Component([
    'title' => WPDev\Theme::get_field('settings/publication')
  ]),
]);
