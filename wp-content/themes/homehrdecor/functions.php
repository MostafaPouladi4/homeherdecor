<?php
/**
 * homehrdecor — theme bootstrap.
 *
 * A custom, editorial front end for a Persian interior design studio.
 * Content lives in inc/data.php behind accessor functions, and the site's
 * own submissions — contact and consultation requests, project reviews —
 * are stored in three custom tables (inc/db.php) reached through
 * inc/repositories.php. Public submission and admin management both go
 * through the hhd/v1 REST API in inc/rest.php, so the dashboard and the
 * front end share one code path to the database.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

define( 'HHD_VERSION', '1.0.0' );

/**
 * Load the theme's modules.
 *
 * @return void
 */
function hhd_require_modules() {
	$modules = array(
		'/inc/setup.php',
		'/inc/assets.php',
		'/inc/data.php',
		'/inc/template-tags.php',
		'/inc/routing.php',
		'/inc/db.php',
		'/inc/repositories.php',
		'/inc/reviews.php',
		'/inc/notifications.php',
		'/inc/rest.php',
		'/inc/admin-ui.php',
		'/inc/admin.php',
	);

	foreach ( $modules as $module ) {
		$path = get_template_directory() . $module;
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
hhd_require_modules();

/**
 * Give the demo content a home: create the pages the theme links to and
 * point the front page at the home template.
 *
 * Idempotent: it runs on theme activation and is safe to call again.
 * Existing content is never overwritten, and it re-runs if a previous
 * attempt did not finish (for example when the front page is missing).
 *
 * @param bool $force Skip the "already installed" shortcut.
 * @return void
 */
function hhd_install_demo_content( $force = false ) {
	if ( ! $force && get_option( 'hhd_content_installed' ) && get_page_by_path( 'home' ) ) {
		return;
	}

	$pages = array(
		'home'         => array(
			'title'   => 'خانه',
			'content' => '',
		),
		'projects'     => array(
			'title'   => 'پروژه‌ها',
			'content' => '',
		),
		'services'     => array(
			'title'   => 'خدمات',
			'content' => '',
		),
		'about'        => array(
			'title'   => 'درباره ما',
			'content' => '',
		),
		'journal'      => array(
			'title'   => 'مجله',
			'content' => '',
		),
		'contact'      => array(
			'title'   => 'تماس با ما',
			'content' => '',
		),
		'consultation' => array(
			'title'   => 'درخواست مشاوره',
			'content' => '',
		),
	);

	$ids = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}

		$inserted = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $page['content'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 1,
			),
			true
		);

		if ( is_wp_error( $inserted ) ) {
			error_log( 'homehrdecor: could not create page "' . $slug . '" — ' . $inserted->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			continue;
		}

		$ids[ $slug ] = (int) $inserted;
	}

	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}

	if ( ! empty( $ids['journal'] ) ) {
		update_option( 'page_for_posts', $ids['journal'] );
	}

	// Journal articles live under /journal/ so the archive and single
	// views share one clean prefix.
	update_option( 'permalink_structure', '/journal/%postname%/' );

	// Brand identity.
	update_option( 'blogname', 'homehrdecor' );
	update_option( 'blogdescription', 'استودیو طراحی داخلی و دکوراسیون' );

	update_option( 'hhd_content_installed', 1 );

	hhd_register_rewrites();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'hhd_install_demo_content' );

/**
 * Keep the brand name correct in the document title.
 *
 * @param array<string,string> $parts Title parts.
 * @return array<string,string>
 */
function hhd_document_title_parts( $parts ) {
	if ( is_front_page() ) {
		$parts['title']   = 'homehrdecor';
		$parts['tagline'] = 'استودیو طراحی داخلی و دکوراسیون';
	}
	return $parts;
}
add_filter( 'document_title_parts', 'hhd_document_title_parts' );

/**
 * Title separator.
 *
 * @return string
 */
function hhd_document_title_separator() {
	return '—';
}
add_filter( 'document_title_separator', 'hhd_document_title_separator' );

/**
 * Drop the "Category:" style prefixes from archive titles.
 *
 * @param string $title Archive title.
 * @return string
 */
function hhd_archive_title( $title ) {
	if ( is_category() || is_tag() || is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'hhd_archive_title' );

/**
 * Anchor links in the primary menu get a data attribute so the script
 * can handle smooth scrolling without hijacking normal navigation.
 *
 * @param array<int, array<string, mixed>> $items Menu items.
 * @return array<int, array<string, mixed>>
 */
function hhd_nav_anchor_attributes( $items ) {
	foreach ( $items as $item ) {
		if ( ! empty( $item->url ) && false !== strpos( $item->url, '#' ) ) {
			$item->classes[] = 'menu-item--anchor';
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'hhd_nav_anchor_attributes' );
