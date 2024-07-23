<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> <?php botiga_schema('html'); ?>>
	<?php wp_body_open(); ?>

	<?php do_action('botiga_before_site'); ?>

	<div id="page" class="site">

		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'botiga'); ?></a>

		<?php

		if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('header')) {

			do_action('botiga_header');
		}

		do_action('botiga_before_page_header');

		do_action('botiga_page_header');

		do_action('botiga_after_page_header');

		do_action('botiga_before_main_wrapper');

		do_action('botiga_main_wrapper_start');
