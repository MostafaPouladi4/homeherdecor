<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 *
 * @return void
 */
function hhd_setup() {
	load_theme_textdomain( 'homehrdecor', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'منوی اصلی', 'homehrdecor' ),
			'footer'  => __( 'منوی فوتر', 'homehrdecor' ),
		)
	);

	// Editor palette mirrors the design tokens in assets/css/base.css.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'زغالی', 'homehrdecor' ),
				'slug'  => 'ink',
				'color' => '#14181b',
			),
			array(
				'name'  => __( 'برنز', 'homehrdecor' ),
				'slug'  => 'bronze',
				'color' => '#b4834f',
			),
			array(
				'name'  => __( 'عاج', 'homehrdecor' ),
				'slug'  => 'ivory',
				'color' => '#f7f4ef',
			),
		)
	);

	add_image_size( 'hhd-card', 1200, 1500, false );
	add_image_size( 'hhd-wide', 2000, 1125, false );
}
add_action( 'after_setup_theme', 'hhd_setup' );

/**
 * Content width.
 *
 * @return void
 */
function hhd_content_width() {
	$GLOBALS['content_width'] = 1440;
}
add_action( 'after_setup_theme', 'hhd_content_width', 0 );

/**
 * Body classes that describe the current view to the stylesheet.
 *
 * @param array<int, string> $classes Existing classes.
 * @return array<int, string>
 */
function hhd_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}
	if ( is_singular() || hhd_is_project_single() || hhd_is_service_single() ) {
		$classes[] = 'is-detail';
	}
	$classes[] = 'brand-homehrdecor';
	return $classes;
}
add_filter( 'body_class', 'hhd_body_classes' );

/**
 * Use the theme's own markup for excerpts.
 *
 * @return string
 */
function hhd_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'hhd_excerpt_more' );

/**
 * Excerpt length tuned for editorial teasers.
 *
 * @return int
 */
function hhd_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'hhd_excerpt_length' );

/**
 * Flush rewrite rules once when the theme is activated so the
 * project/service detail URLs resolve immediately.
 *
 * @return void
 */
function hhd_after_switch_theme() {
	hhd_register_rewrites();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'hhd_after_switch_theme' );
