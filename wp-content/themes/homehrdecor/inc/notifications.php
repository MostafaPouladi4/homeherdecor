<?php
/**
 * Notifications.
 *
 * A thin layer between the repositories and the REST handlers: when a new
 * row lands in a source table, one call here raises the matching dashboard
 * notification. Keeping the copy and the deep links in one place means the
 * REST layer never has to know what a notification looks like.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Deep link into a dashboard list screen.
 *
 * @param string              $page Admin page slug.
 * @param array<string,mixed> $args Extra query args.
 * @return string
 */
function hhd_admin_link( $page, $args = array() ) {
	return add_query_arg( array_merge( array( 'page' => $page ), $args ), admin_url( 'admin.php' ) );
}

/**
 * Raise a notification for a newly stored request.
 *
 * Called after a successful insert so the row id can be carried into the
 * deep link, which is what makes the notification actionable.
 *
 * @param int    $request_id New request id.
 * @param string $type       contact | consultation.
 * @return int|WP_Error Notification id.
 */
function hhd_notify_new_request( $request_id, $type ) {
	$request = hhd_request_get( $request_id );

	if ( ! $request ) {
		return new WP_Error( 'hhd_missing_request', 'درخواست مورد نظر پیدا نشد.' );
	}

	$is_consultation = ( 'consultation' === $type );

	$title = $is_consultation
		? sprintf( 'درخواست مشاوره‌ی جدید از %s', $request['name'] )
		: sprintf( 'پیام تماس جدید از %s', $request['name'] );

	$parts = array();
	if ( $request['project_type'] ) {
		$parts[] = $request['project_type'];
	}
	if ( $request['city'] ) {
		$parts[] = $request['city'];
	}
	if ( $request['phone'] ) {
		$parts[] = $request['phone'];
	}

	$page = $is_consultation ? 'hhd-consultation' : 'hhd-contact';

	return hhd_notification_insert(
		array(
			'type'      => 'request',
			'object_id' => (int) $request_id,
			'title'     => $title,
			'message'   => implode( ' — ', $parts ),
			'url'       => hhd_admin_link(
				$page,
				array(
					'view' => (int) $request_id,
				)
			),
		)
	);
}

/**
 * Raise a notification for a newly submitted review.
 *
 * Reviews arrive pending, so the notification is really a moderation cue.
 *
 * @param int $review_id New review id.
 * @return int|WP_Error Notification id.
 */
function hhd_notify_new_review( $review_id ) {
	$review = hhd_review_get( $review_id );

	if ( ! $review ) {
		return new WP_Error( 'hhd_missing_review', 'نظر مورد نظر پیدا نشد.' );
	}

	$label = hhd_review_row_label( $review );

	return hhd_notification_insert(
		array(
			'type'      => 'review',
			'object_id' => (int) $review_id,
			'title'     => sprintf( 'نظر جدید برای «%s»', $label ),
			'message'   => sprintf( '%s — امتیاز %s از ۵ (در انتظار تأیید)', $review['name'], hhd_fa_num( (int) $review['rating'] ) ),
			'url'       => hhd_admin_link(
				'hhd-reviews',
				array(
					'view' => (int) $review_id,
				)
			),
		)
	);
}
