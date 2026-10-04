<?php
/**
 * Front-end assets.
 *
 * Stylesheets are split along the same seams as the design system:
 * tokens/base, layout (header, footer, sections), components, pages.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme file.
 *
 * @param string $relative Path relative to the theme root.
 * @return string
 */
function hhd_asset_version( $relative ) {
	$path = get_template_directory() . '/' . ltrim( $relative, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : HHD_VERSION;
}

/**
 * Enqueue styles and scripts.
 *
 * @return void
 */
function hhd_enqueue_assets() {
	$uri = get_template_directory_uri();

	// Base stylesheet holds the theme header; the rest of the system follows.
	wp_enqueue_style( 'hhd-style', get_stylesheet_uri(), array(), hhd_asset_version( 'style.css' ) );

	$styles = array(
		'hhd-base'       => 'assets/css/base.css',
		'hhd-layout'     => 'assets/css/layout.css',
		'hhd-components' => 'assets/css/components.css',
		'hhd-pages'      => 'assets/css/pages.css',
	);

	$deps = array( 'hhd-style' );
	foreach ( $styles as $handle => $relative ) {
		wp_enqueue_style( $handle, $uri . '/' . $relative, $deps, hhd_asset_version( $relative ) );
		$deps = array( $handle );
	}

	wp_enqueue_script(
		'hhd-main',
		$uri . '/assets/js/main.js',
		array(),
		hhd_asset_version( 'assets/js/main.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	wp_localize_script(
		'hhd-main',
		'hhdData',
		array(
			'homeUrl'   => esc_url_raw( home_url( '/' ) ),
			'isRtl'     => is_rtl(),
			'projects'  => esc_url_raw( hhd_page_url( 'projects' ) ),
			'restUrl'   => esc_url_raw( rest_url( HHD_REST_NS ) ),
			/*
			 * Sent so a logged-in visitor gets CSRF protection on the public
			 * forms. It is never the gate for an anonymous submission: a
			 * logged-out nonce is session-tied and goes stale behind page
			 * caching, so the server treats it as optional there. See
			 * hhd_rest_public_nonce().
			 */
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'i18n'      => array(
				'sending' => 'در حال ارسال…',
				'error'   => 'ارسال انجام نشد. لطفاً دوباره تلاش کنید.',
				'invalid' => 'لطفاً نام و شماره تماس را کامل وارد کنید.',
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'hhd_enqueue_assets' );

/**
 * Preload the variable font — it is used above the fold on every page.
 *
 * @return void
 */
function hhd_preload_font() {
	$uri = get_template_directory_uri();
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( $uri . '/assets/fonts/Vazirmatn-Variable.woff2' )
	);
}
add_action( 'wp_head', 'hhd_preload_font', 1 );

/**
 * Theme colour for mobile browser chrome.
 *
 * @return void
 */
function hhd_meta_theme_color() {
	echo '<meta name="theme-color" content="#14181b">' . "\n";
}
add_action( 'wp_head', 'hhd_meta_theme_color', 2 );
