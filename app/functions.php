<?php

require_once get_template_directory() . '/vendor/autoload.php';
require_once dirname(dirname(get_template_directory())) . '/wpdev/vendor/autoload.php';

Src\Entities\LandingPage::register();

WPDev\Theme::init(new WPDev\Enqueue('WPDev'));

add_theme_support('post-thumbnails');
