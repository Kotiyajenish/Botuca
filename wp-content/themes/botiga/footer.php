<?php
do_action('botiga_main_wrapper_end');

do_action('botiga_after_main_wrapper');

do_action('botiga_footer_before');

if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer')) {
	do_action('botiga_footer');
}


do_action('botiga_footer_after'); ?>

</div>

<?php do_action('botiga_after_site'); ?>

<?php wp_footer(); ?>

</body>

</html>