<?php
/**
 * Schema installation.
 *
 * The theme keeps its own three tables rather than leaning on posts and
 * comments: submissions are private records with their own status
 * workflows, and reviews attach to projects that are plain PHP arrays —
 * there is no post for a WP comment to hang off.
 *
 * This file owns the schema and nothing else. It runs no queries beyond
 * dbDelta, and no other module may create a table.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option name holding the installed schema version.
 */
define( 'HHD_DB_VERSION_OPTION', 'hhd_db_version' );

/**
 * Schema version. Bump this whenever a table or column changes so
 * hhd_install_tables() runs again on the next request.
 */
define( 'HHD_DB_VERSION', '1.0.0' );

/**
 * Full table name for a logical table.
 *
 * Every table name in the theme comes from here, so the prefix is never
 * interpolated by hand and no query has to guess.
 *
 * @param string $name One of requests, reviews, notifications.
 * @return string
 */
function hhd_table( $name ) {
	global $wpdb;
	return $wpdb->prefix . 'hhd_' . $name;
}

/**
 * CREATE TABLE statements, keyed by table name.
 *
 * dbDelta is whitespace-sensitive: one column per line, two spaces after
 * PRIMARY KEY, and no backticks around the column list.
 *
 * @return array<string, string>
 */
function hhd_db_schema() {
	global $wpdb;

	$collate = $wpdb->get_charset_collate();

	$requests      = hhd_table( 'requests' );
	$reviews       = hhd_table( 'reviews' );
	$notifications = hhd_table( 'notifications' );

	$schema = array();

	// Contact and consultation share every column except call_time, so
	// they share a table and are told apart by `type`.
	$schema[ $requests ] = "CREATE TABLE $requests (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		type varchar(20) NOT NULL DEFAULT 'contact',
		name varchar(190) NOT NULL DEFAULT '',
		phone varchar(40) NOT NULL DEFAULT '',
		project_type varchar(190) NOT NULL DEFAULT '',
		area varchar(60) NOT NULL DEFAULT '',
		city varchar(120) NOT NULL DEFAULT '',
		call_time varchar(40) NOT NULL DEFAULT '',
		message text NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'new',
		email varchar(190) DEFAULT NULL,
		created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY type_status (type,status),
		KEY status (status),
		KEY created_at (created_at)
	) $collate;";

	$schema[ $reviews ] = "CREATE TABLE $reviews (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		project_slug varchar(190) NOT NULL DEFAULT '',
		name varchar(190) NOT NULL DEFAULT '',
		rating tinyint(3) unsigned NOT NULL DEFAULT 5,
		message text NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'pending',
		created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY target_status (project_slug,status),
		KEY status (status),
		KEY created_at (created_at)
	) $collate;";

	$schema[ $notifications ] = "CREATE TABLE $notifications (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		type varchar(40) NOT NULL DEFAULT '',
		object_id bigint(20) unsigned NOT NULL DEFAULT 0,
		title varchar(190) NOT NULL DEFAULT '',
		message text NOT NULL,
		url varchar(255) NOT NULL DEFAULT '',
		is_read tinyint(1) NOT NULL DEFAULT 0,
		created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY is_read (is_read,created_at),
		KEY object (type,object_id)
	) $collate;";

	return $schema;
}

/**
 * Create or update the tables when the schema version moves.
 *
 * Runs on `init` rather than only on activation: the theme is already
 * active on the install this ships to, so waiting for a re-activation
 * would leave the dashboard querying tables that do not exist yet.
 *
 * @return void
 */
function hhd_install_tables() {
	if ( get_option( HHD_DB_VERSION_OPTION ) === HHD_DB_VERSION ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	foreach ( hhd_db_schema() as $sql ) {
		dbDelta( $sql );
	}

	update_option( HHD_DB_VERSION_OPTION, HHD_DB_VERSION );
}
add_action( 'init', 'hhd_install_tables', 5 );

/**
 * Install on theme activation too, so a fresh install is ready before the
 * first front-end request.
 *
 * @return void
 */
function hhd_install_tables_on_activation() {
	delete_option( HHD_DB_VERSION_OPTION );
	hhd_install_tables();
}
add_action( 'after_switch_theme', 'hhd_install_tables_on_activation', 20 );

/**
 * Timestamp in the site's own timezone, which is what wp-admin displays.
 *
 * @return string MySQL datetime.
 */
function hhd_now() {
	return current_time( 'mysql' );
}
