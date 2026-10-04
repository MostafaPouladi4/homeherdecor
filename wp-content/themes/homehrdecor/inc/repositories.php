<?php
/**
 * Data access.
 *
 * Every query in the theme lives here. Nothing in this file renders HTML,
 * reads request data or knows about HTTP — callers hand it validated,
 * sanitized values and get arrays back. That keeps the REST layer and the
 * admin screens reading from one source of truth.
 *
 * All SQL goes through $wpdb->prepare(); table names come from hhd_table().
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
   1. Shared vocabularies
   ------------------------------------------------------------------------- */

/**
 * Allowed request types.
 *
 * @return array<int, string>
 */
function hhd_request_types() {
	return array( 'contact', 'consultation' );
}

/**
 * Allowed request statuses.
 *
 * @return array<int, string>
 */
function hhd_request_statuses() {
	return array( 'new', 'in_progress', 'done', 'spam' );
}

/**
 * Allowed review statuses.
 *
 * @return array<int, string>
 */
function hhd_review_statuses() {
	return array( 'pending', 'approved', 'rejected', 'hidden' );
}

/**
 * Whether a value is one of an allowed set.
 *
 * @param string            $value   Candidate.
 * @param array<int,string> $allowed Allowed values.
 * @return bool
 */
function hhd_is_allowed( $value, $allowed ) {
	return in_array( (string) $value, $allowed, true );
}

/* -------------------------------------------------------------------------
   2. Requests — contact and consultation
   ------------------------------------------------------------------------- */

/**
 * Insert a request.
 *
 * @param array<string,mixed> $data Validated fields.
 * @return int|WP_Error New row id.
 */
function hhd_request_insert( $data ) {
	global $wpdb;

	$now = hhd_now();

	$row = array(
		'type'         => isset( $data['type'] ) ? (string) $data['type'] : 'contact',
		'name'         => isset( $data['name'] ) ? (string) $data['name'] : '',
		'phone'        => isset( $data['phone'] ) ? (string) $data['phone'] : '',
		'project_type' => isset( $data['project_type'] ) ? (string) $data['project_type'] : '',
		'area'         => isset( $data['area'] ) ? (string) $data['area'] : '',
		'city'         => isset( $data['city'] ) ? (string) $data['city'] : '',
		'call_time'    => isset( $data['call_time'] ) ? (string) $data['call_time'] : '',
		'message'      => isset( $data['message'] ) ? (string) $data['message'] : '',
		'status'       => 'new',
		'created_at'   => $now,
		'updated_at'   => $now,
	);

	// `email` is deliberately absent rather than set to null: the column is
	// in the schema for a later phase, and leaving it out of the statement
	// lets MySQL apply its own DEFAULT NULL. Every column listed here is a
	// non-null string, so one format for all of them is unambiguous.
	$inserted = $wpdb->insert( hhd_table( 'requests' ), $row, '%s' );

	if ( ! $inserted ) {
		return new WP_Error( 'hhd_db_error', 'ثبت درخواست در پایگاه داده انجام نشد.', array( 'status' => 500 ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Fetch one request.
 *
 * @param int $id Row id.
 * @return array<string,mixed>|null
 */
function hhd_request_get( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return null;
	}

	$table = hhd_table( 'requests' );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

	return $row ? $row : null;
}

/**
 * Query requests with filters, search and pagination.
 *
 * @param array<string,mixed> $args type, status, search, page, per_page, orderby.
 * @return array{items: array<int, array<string,mixed>>, total: int}
 */
function hhd_requests_query( $args = array() ) {
	global $wpdb;

	$args = wp_parse_args(
		$args,
		array(
			'type'     => '',
			'status'   => '',
			'search'   => '',
			'page'     => 1,
			'per_page' => 20,
		)
	);

	$table  = hhd_table( 'requests' );
	$where  = array( '1=1' );
	$params = array();

	if ( $args['type'] && hhd_is_allowed( $args['type'], hhd_request_types() ) ) {
		$where[]  = 'type = %s';
		$params[] = $args['type'];
	}

	if ( $args['status'] && hhd_is_allowed( $args['status'], hhd_request_statuses() ) ) {
		$where[]  = 'status = %s';
		$params[] = $args['status'];
	}

	if ( '' !== trim( (string) $args['search'] ) ) {
		$like     = '%' . $wpdb->esc_like( trim( (string) $args['search'] ) ) . '%';
		$where[]  = '(name LIKE %s OR phone LIKE %s OR city LIKE %s OR project_type LIKE %s OR message LIKE %s)';
		$params[] = $like;
		$params[] = $like;
		$params[] = $like;
		$params[] = $like;
		$params[] = $like;
	}

	$clause = implode( ' AND ', $where );

	// Count first so pagination knows the real total.
	$count_sql = "SELECT COUNT(*) FROM $table WHERE $clause";
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared below when params exist.
	$total = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );

	$per_page = max( 1, min( 100, (int) $args['per_page'] ) );
	$page     = max( 1, (int) $args['page'] );
	$offset   = ( $page - 1 ) * $per_page;

	$sql           = "SELECT * FROM $table WHERE $clause ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d";
	$list_params   = $params;
	$list_params[] = $per_page;
	$list_params[] = $offset;

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared here.
	$items = $wpdb->get_results( $wpdb->prepare( $sql, $list_params ), ARRAY_A );

	return array(
		'items' => $items ? $items : array(),
		'total' => $total,
	);
}

/**
 * Move a request to a new status.
 *
 * @param int    $id     Row id.
 * @param string $status One of hhd_request_statuses().
 * @return bool
 */
function hhd_request_update_status( $id, $status ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 || ! hhd_is_allowed( $status, hhd_request_statuses() ) ) {
		return false;
	}

	$updated = $wpdb->update(
		hhd_table( 'requests' ),
		array(
			'status'     => $status,
			'updated_at' => hhd_now(),
		),
		array( 'id' => $id ),
		array( '%s', '%s' ),
		array( '%d' )
	);

	return false !== $updated;
}

/**
 * Delete a request permanently.
 *
 * @param int $id Row id.
 * @return bool
 */
function hhd_request_delete( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return false;
	}

	return (bool) $wpdb->delete( hhd_table( 'requests' ), array( 'id' => $id ), array( '%d' ) );
}

/**
 * Request counts grouped by status, optionally limited to one type.
 *
 * @param string $type Optional request type.
 * @return array<string,int> Keyed by status; every status present.
 */
function hhd_request_status_counts( $type = '' ) {
	global $wpdb;

	$table = hhd_table( 'requests' );
	$counts = array_fill_keys( hhd_request_statuses(), 0 );

	if ( $type && hhd_is_allowed( $type, hhd_request_types() ) ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT status, COUNT(*) AS total FROM $table WHERE type = %s GROUP BY status", $type ), ARRAY_A );
	} else {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
		$rows = $wpdb->get_results( "SELECT status, COUNT(*) AS total FROM $table GROUP BY status", ARRAY_A );
	}

	foreach ( (array) $rows as $row ) {
		if ( isset( $counts[ $row['status'] ] ) ) {
			$counts[ $row['status'] ] = (int) $row['total'];
		}
	}

	return $counts;
}

/* -------------------------------------------------------------------------
   3. Reviews
   -------------------------------------------------------------------------
   `project_slug` is written and read in this section and in inc/reviews.php,
   and nowhere else. Every other caller passes an opaque target reference,
   which keeps a future move to stable project IDs contained.
   ------------------------------------------------------------------------- */

/**
 * Insert a review.
 *
 * @param array<string,mixed> $data target, name, rating, message.
 * @return int|WP_Error New row id.
 */
function hhd_review_insert( $data ) {
	global $wpdb;

	$now = hhd_now();

	$row = array(
		'project_slug' => isset( $data['target'] ) ? (string) $data['target'] : '',
		'name'         => isset( $data['name'] ) ? (string) $data['name'] : '',
		'rating'       => isset( $data['rating'] ) ? max( 1, min( 5, (int) $data['rating'] ) ) : 5,
		'message'      => isset( $data['message'] ) ? (string) $data['message'] : '',
		'status'       => 'pending',
		'created_at'   => $now,
		'updated_at'   => $now,
	);

	$inserted = $wpdb->insert( hhd_table( 'reviews' ), $row, array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' ) );

	if ( ! $inserted ) {
		return new WP_Error( 'hhd_db_error', 'ثبت نظر در پایگاه داده انجام نشد.', array( 'status' => 500 ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Fetch one review.
 *
 * @param int $id Row id.
 * @return array<string,mixed>|null
 */
function hhd_review_get( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return null;
	}

	$table = hhd_table( 'reviews' );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

	return $row ? $row : null;
}

/**
 * Query reviews with filters, search and pagination.
 *
 * @param array<string,mixed> $args target, status, search, page, per_page.
 * @return array{items: array<int, array<string,mixed>>, total: int}
 */
function hhd_reviews_query( $args = array() ) {
	global $wpdb;

	$args = wp_parse_args(
		$args,
		array(
			'target'   => '',
			'status'   => '',
			'search'   => '',
			'page'     => 1,
			'per_page' => 20,
		)
	);

	$table  = hhd_table( 'reviews' );
	$where  = array( '1=1' );
	$params = array();

	if ( '' !== (string) $args['target'] ) {
		$where[]  = 'project_slug = %s';
		$params[] = (string) $args['target'];
	}

	if ( $args['status'] && hhd_is_allowed( $args['status'], hhd_review_statuses() ) ) {
		$where[]  = 'status = %s';
		$params[] = $args['status'];
	}

	if ( '' !== trim( (string) $args['search'] ) ) {
		$like     = '%' . $wpdb->esc_like( trim( (string) $args['search'] ) ) . '%';
		$where[]  = '(name LIKE %s OR message LIKE %s)';
		$params[] = $like;
		$params[] = $like;
	}

	$clause = implode( ' AND ', $where );

	$count_sql = "SELECT COUNT(*) FROM $table WHERE $clause";
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared below when params exist.
	$total = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );

	$per_page = max( 1, min( 100, (int) $args['per_page'] ) );
	$page     = max( 1, (int) $args['page'] );
	$offset   = ( $page - 1 ) * $per_page;

	$sql           = "SELECT * FROM $table WHERE $clause ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d";
	$list_params   = $params;
	$list_params[] = $per_page;
	$list_params[] = $offset;

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared here.
	$items = $wpdb->get_results( $wpdb->prepare( $sql, $list_params ), ARRAY_A );

	return array(
		'items' => $items ? $items : array(),
		'total' => $total,
	);
}

/**
 * Move a review to a new status.
 *
 * @param int    $id     Row id.
 * @param string $status One of hhd_review_statuses().
 * @return bool
 */
function hhd_review_update_status( $id, $status ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 || ! hhd_is_allowed( $status, hhd_review_statuses() ) ) {
		return false;
	}

	$updated = $wpdb->update(
		hhd_table( 'reviews' ),
		array(
			'status'     => $status,
			'updated_at' => hhd_now(),
		),
		array( 'id' => $id ),
		array( '%s', '%s' ),
		array( '%d' )
	);

	return false !== $updated;
}

/**
 * Delete a review permanently.
 *
 * @param int $id Row id.
 * @return bool
 */
function hhd_review_delete( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return false;
	}

	return (bool) $wpdb->delete( hhd_table( 'reviews' ), array( 'id' => $id ), array( '%d' ) );
}

/**
 * Review counts grouped by status.
 *
 * @return array<string,int> Keyed by status; every status present.
 */
function hhd_review_status_counts() {
	global $wpdb;

	$table  = hhd_table( 'reviews' );
	$counts = array_fill_keys( hhd_review_statuses(), 0 );

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
	$rows = $wpdb->get_results( "SELECT status, COUNT(*) AS total FROM $table GROUP BY status", ARRAY_A );

	foreach ( (array) $rows as $row ) {
		if ( isset( $counts[ $row['status'] ] ) ) {
			$counts[ $row['status'] ] = (int) $row['total'];
		}
	}

	return $counts;
}

/* -------------------------------------------------------------------------
   4. Notifications
   ------------------------------------------------------------------------- */

/**
 * Insert a notification.
 *
 * @param array<string,mixed> $data type, object_id, title, message, url.
 * @return int|WP_Error New row id.
 */
function hhd_notification_insert( $data ) {
	global $wpdb;

	$row = array(
		'type'       => isset( $data['type'] ) ? (string) $data['type'] : '',
		'object_id'  => isset( $data['object_id'] ) ? (int) $data['object_id'] : 0,
		'title'      => isset( $data['title'] ) ? (string) $data['title'] : '',
		'message'    => isset( $data['message'] ) ? (string) $data['message'] : '',
		'url'        => isset( $data['url'] ) ? (string) $data['url'] : '',
		'is_read'    => 0,
		'created_at' => hhd_now(),
	);

	$inserted = $wpdb->insert(
		hhd_table( 'notifications' ),
		$row,
		array( '%s', '%d', '%s', '%s', '%s', '%d', '%s' )
	);

	if ( ! $inserted ) {
		return new WP_Error( 'hhd_db_error', 'ثبت اعلان در پایگاه داده انجام نشد.', array( 'status' => 500 ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Fetch one notification.
 *
 * @param int $id Row id.
 * @return array<string,mixed>|null
 */
function hhd_notification_get( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return null;
	}

	$table = hhd_table( 'notifications' );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

	return $row ? $row : null;
}

/**
 * Query notifications, newest first.
 *
 * @param array<string,mixed> $args unread, page, per_page.
 * @return array{items: array<int, array<string,mixed>>, total: int, unread: int}
 */
function hhd_notifications_query( $args = array() ) {
	global $wpdb;

	$args = wp_parse_args(
		$args,
		array(
			'unread'   => false,
			'page'     => 1,
			'per_page' => 20,
		)
	);

	$table  = hhd_table( 'notifications' );
	$where  = array( '1=1' );
	$params = array();

	if ( $args['unread'] ) {
		$where[] = 'is_read = 0';
	}

	$clause = implode( ' AND ', $where );

	$count_sql = "SELECT COUNT(*) FROM $table WHERE $clause";
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- no user input in this clause.
	$total = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );

	$per_page = max( 1, min( 100, (int) $args['per_page'] ) );
	$page     = max( 1, (int) $args['page'] );
	$offset   = ( $page - 1 ) * $per_page;

	$sql           = "SELECT * FROM $table WHERE $clause ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d";
	$list_params   = $params;
	$list_params[] = $per_page;
	$list_params[] = $offset;

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared here.
	$items = $wpdb->get_results( $wpdb->prepare( $sql, $list_params ), ARRAY_A );

	return array(
		'items'  => $items ? $items : array(),
		'total'  => $total,
		'unread' => hhd_notifications_unread_count(),
	);
}

/**
 * Number of unread notifications. Indexed, so cheap enough to poll.
 *
 * @return int
 */
function hhd_notifications_unread_count() {
	global $wpdb;

	$table = hhd_table( 'notifications' );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE is_read = 0" );
}

/**
 * Set the read flag on one notification.
 *
 * @param int  $id     Row id.
 * @param bool $is_read Desired state.
 * @return bool
 */
function hhd_notification_set_read( $id, $is_read ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return false;
	}

	$updated = $wpdb->update(
		hhd_table( 'notifications' ),
		array( 'is_read' => $is_read ? 1 : 0 ),
		array( 'id' => $id ),
		array( '%d' ),
		array( '%d' )
	);

	return false !== $updated;
}

/**
 * Mark every unread notification as read.
 *
 * @return int Rows touched.
 */
function hhd_notifications_mark_all_read() {
	global $wpdb;

	$table = hhd_table( 'notifications' );

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from hhd_table().
	$updated = $wpdb->query( "UPDATE $table SET is_read = 1 WHERE is_read = 0" );

	return (int) $updated;
}

/**
 * Delete a notification.
 *
 * @param int $id Row id.
 * @return bool
 */
function hhd_notification_delete( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id < 1 ) {
		return false;
	}

	return (bool) $wpdb->delete( hhd_table( 'notifications' ), array( 'id' => $id ), array( '%d' ) );
}

/* -------------------------------------------------------------------------
   5. Dashboard counters
   ------------------------------------------------------------------------- */

/**
 * Every counter the dashboard needs, in one place.
 *
 * @return array<string,mixed>
 */
function hhd_dashboard_counts() {
	$request_statuses = hhd_request_status_counts();
	$review_statuses  = hhd_review_status_counts();

	return array(
		'requests'              => array(
			'total'       => array_sum( $request_statuses ),
			'new'         => $request_statuses['new'],
			'in_progress' => $request_statuses['in_progress'],
			'done'        => $request_statuses['done'],
			'spam'        => $request_statuses['spam'],
		),
		'contact'               => hhd_request_status_counts( 'contact' ),
		'consultation'          => hhd_request_status_counts( 'consultation' ),
		'reviews'               => array(
			'total'    => array_sum( $review_statuses ),
			'pending'  => $review_statuses['pending'],
			'approved' => $review_statuses['approved'],
			'rejected' => $review_statuses['rejected'],
			'hidden'   => $review_statuses['hidden'],
		),
		'notifications_unread'  => hhd_notifications_unread_count(),
	);
}
