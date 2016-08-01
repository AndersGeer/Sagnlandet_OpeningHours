<?php
/**
*	Register Styles
*/
function op_register_styles_backend() {

	if (is_admin()) {
		wp_enqueue_style('opening-hours-backend', apply_filters('op_backend_stylesheet', op_baseurl() . '/css/backend.css'), false, false, 'all');


		wp_enqueue_style('jQuery-ui-timepicker', op_baseurl() . '/js/jQuery.ui.timepicker/jquery.ui.timepicker.css', false, false, 'all');
		wp_enqueue_style('jQuery-ui-style', 'https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css', false, false, 'all');

		wp_register_script('jQuery-ui', 'https://code.jquery.com/ui/1.11.3/jquery-ui.js', false, null, false);
		wp_enqueue_script('jQuery-ui');

		wp_register_script('jQuery-ui-timepicker', op_baseurl() . '/js/jQuery.ui.timepicker/jquery.ui.timepicker.js', false, null, false);
		wp_enqueue_script('jQuery-ui-timepicker');

	}
}

add_action('admin_init', 'op_register_styles_backend');

function op_register_styles_frontend() {
	if (!is_admin()) {
		wp_register_style('opening-hours-frontend', apply_filters('op_frontend_stylesheet', op_baseurl() . '/css/frontend.css'), false, false, 'all');
		wp_enqueue_style('opening-hours-frontend');

		if (file_exists(op_assets_path() . '/custom_style.css')) {
			wp_register_style('opening-hours-user-stylesheet', op_assets_path() . '/custom_style.css', false, false, all);
			wp_enqueue_style('opening-hours-user-stylesheet');
		}
	}
}

add_action('wp_enqueue_scripts', 'op_register_styles_frontend');

/**
 *	Create Opening Hours Instance
 */
$wp_opening_hours	= new OpeningHours;

/**
*	Register Backend Options Pages
*/
function op_register_options_pages() {
	// Top level menu item
	add_menu_page(
		apply_filters( 'op_menu_title_opening_hours', __('Opening Hours', op_textdomain()) ),
		apply_filters( 'op_menu_title_opening_hours', __('Opening Hours', op_textdomain()) ),
		apply_filters( 'op_min_user_capability', 'manage_options' ),
		'opening-hours',
		'op_setup_page'
	);
	
	// Holidays Page
	add_submenu_page(
		'opening-hours',
		apply_filters( 'op_menu_title_holidays', op__('Holidays') ),
		apply_filters( 'op_menu_title_holidays', op__('Holidays') ),
		apply_filters( 'op_min_user_capability', 'manage_options' ),
		'opening-hours-holidays',
		'op_holidays_page'
	);
	
	// Special Openings Page
	add_submenu_page(
		'opening-hours',
		apply_filters( 'op_menu_title_special_openings', op__('Special Openings') ),
		apply_filters( 'op_menu_title_special_openings', op__('Special Openings') ),
		apply_filters( 'op_min_user_capability', 'manage_options' ),
		'opening-hours-special-openings',
		'op_special_openings_page'
	);
	
	// Settings Page
	add_submenu_page (
		'opening-hours',
		apply_filters( 'op_menu_title_settings', op__('Settings') ),
		apply_filters( 'op_menu_title_settings', op__('Settings') ),
		apply_filters( 'op_min_user_capability', 'manage_options' ),
		'opening-hours-settings',
		'op_settings_page'
	);
}

function op_setup_page() {
	global $wp_opening_hours;
	// include template
	require_once	op_basepath() . '/templates/setup-page.php';
}

function op_holidays_page() {
	global $wp_opening_hours;
	// include template
	require_once	op_basepath() . '/templates/holidays-page.php';
}

function op_special_openings_page() {
	global $wp_opening_hours;
	// include template
	require_once	op_basepath() . '/templates/special-openings-page.php';
}

function op_settings_page() {
	global $wp_opening_hours;
	// include template
	require_once	op_basepath() . '/templates/settings-page.php';
}

add_action ('admin_menu', 'op_register_options_pages');
?>