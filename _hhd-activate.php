<?php
/**
 * One-off: activate the homehrdecor theme and install its demo pages.
 *
 * Run from the WordPress root:
 *   php _hhd-activate.php
 *
 * Delete this file once the theme is live.
 */

define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/wp-load.php';

$theme = 'homehrdecor';

if ( get_stylesheet() !== $theme ) {
	switch_theme( $theme );
}

// The theme's functions.php is not guaranteed to be loaded at the moment
// switch_theme() fires its hook, so load it and run the installer
// explicitly rather than relying on after_switch_theme alone.
require_once get_template_directory() . '/functions.php';

echo 'Active theme : ' . get_stylesheet() . "\n";

hhd_install_demo_content( true );

$pages = array( 'home', 'projects', 'services', 'about', 'journal', 'contact', 'consultation' );
foreach ( $pages as $slug ) {
	$page = get_page_by_path( $slug );
	printf(
		"  %-14s %s\n",
		$slug,
		$page ? '#' . $page->ID . '  ' . get_permalink( $page ) : 'MISSING'
	);
}

printf( "show_on_front   : %s\n", get_option( 'show_on_front' ) );
printf( "page_on_front   : %s\n", get_option( 'page_on_front' ) );
printf( "page_for_posts  : %s\n", get_option( 'page_for_posts' ) );
printf( "permalink       : %s\n", get_option( 'permalink_structure' ) );
printf( "blogname        : %s\n", get_option( 'blogname' ) );
printf( "blogdescription : %s\n", get_option( 'blogdescription' ) );
