<?php /* Template Name: Landing Page */

WPDev\Theme::get_enqueue()->add_entries('content');

WPDev\Theme::render(
  new Src\UI\Main\Layout\Component(null, 'landing-page.php')
);
