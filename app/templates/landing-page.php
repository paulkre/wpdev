<?php /* Template Name: Landing Page */

WPDev\Theme::render(
  new Src\UI\Main\Layout\Component(['seo' => ['title' => \get_bloginfo('name')]], 'landing-page.php')
);
