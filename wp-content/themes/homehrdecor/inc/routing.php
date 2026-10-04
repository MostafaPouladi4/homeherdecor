<?php
/**
 * Routing for project and service detail views.
 *
 * Projects and services are plain PHP arrays for now, so their detail
 * views are served by the archive page through a query var instead of a
 * custom post type. The URL shape (/projects/<slug>/) is the same one a
 * CPT would produce later, so swapping the source will not break links.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the rewrite rules and query vars.
 *
 * @return void
 */
function hhd_register_rewrites() {
	add_rewrite_rule( '^projects/([^/]+)/?$', 'index.php?pagename=projects&hhd_project=$matches[1]', 'top' );
	add_rewrite_rule( '^services/([^/]+)/?$', 'index.php?pagename=services&hhd_service=$matches[1]', 'top' );
}
add_action( 'init', 'hhd_register_rewrites' );

/**
 * Make the custom query vars visible to WordPress.
 *
 * @param array<int, string> $vars Existing query vars.
 * @return array<int, string>
 */
function hhd_query_vars( $vars ) {
	$vars[] = 'hhd_project';
	$vars[] = 'hhd_service';
	return $vars;
}
add_filter( 'query_vars', 'hhd_query_vars' );

/**
 * Slug of the project currently being viewed, if any.
 *
 * @return string
 */
function hhd_current_project_slug() {
	$slug = get_query_var( 'hhd_project' );
	return is_string( $slug ) ? sanitize_title( $slug ) : '';
}

/**
 * Slug of the service currently being viewed, if any.
 *
 * @return string
 */
function hhd_current_service_slug() {
	$slug = get_query_var( 'hhd_service' );
	return is_string( $slug ) ? sanitize_title( $slug ) : '';
}

/**
 * Whether the current request renders a single project.
 *
 * @return bool
 */
function hhd_is_project_single() {
	$slug = hhd_current_project_slug();
	return '' !== $slug && (bool) hhd_get_project( $slug );
}

/**
 * Whether the current request renders a single service.
 *
 * @return bool
 */
function hhd_is_service_single() {
	$slug = hhd_current_service_slug();
	return '' !== $slug && (bool) hhd_get_service( $slug );
}

/**
 * Permalink for a project detail view.
 *
 * @param string $slug Project slug.
 * @return string
 */
function hhd_project_url( $slug ) {
	return trailingslashit( hhd_page_url( 'projects' ) ) . $slug . '/';
}

/**
 * Permalink for a service detail view.
 *
 * @param string $slug Service slug.
 * @return string
 */
function hhd_service_url( $slug ) {
	return trailingslashit( hhd_page_url( 'services' ) ) . $slug . '/';
}

/**
 * URL of a theme page by slug, falling back to a clean guess so the
 * layout still works before the pages are created.
 *
 * @param string $slug Page slug.
 * @return string
 */
function hhd_page_url( $slug ) {
	static $cache = array();

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$page = get_page_by_path( $slug );
	$url  = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );

	$cache[ $slug ] = $url;
	return $url;
}

/**
 * Title of a theme page by slug.
 *
 * @param string $slug Page slug.
 * @param string $fallback Fallback label.
 * @return string
 */
function hhd_page_title( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return get_the_title( $page );
	}
	return $fallback ? $fallback : $slug;
}

/**
 * Canonical redirect for unknown project/service slugs so bad URLs 404
 * instead of rendering an archive.
 *
 * @return void
 */
function hhd_validate_detail_slug() {
	if ( is_admin() || ! is_page() ) {
		return;
	}

	$project = hhd_current_project_slug();
	if ( $project && ! hhd_get_project( $project ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		return;
	}

	$service = hhd_current_service_slug();
	if ( $service && ! hhd_get_service( $service ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'hhd_validate_detail_slug' );
