<?php
/**
 * Reviews — the public side, and the one place that knows how a review is
 * attached to a project.
 *
 * Projects are plain PHP arrays keyed by slug, so a review stores a slug.
 * That is an implementation detail: everything outside this file deals in
 * an opaque "target reference" and asks these helpers for it. When projects
 * move to a custom post type with stable IDs, hhd_review_target_ref() and
 * the two readers below change, plus one UPDATE — no template, REST
 * handler or admin screen has to be touched.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Canonical reference for a review target.
 *
 * Accepts a project array (as returned by hhd_get_project()) or a raw
 * slug, and returns the value stored in the reviews table — or an empty
 * string when the target does not exist, which is what makes this usable
 * as a validator on the way in.
 *
 * @param array<string,mixed>|string $project Project array or slug.
 * @return string Reference, or '' when unknown.
 */
function hhd_review_target_ref( $project ) {
	if ( is_array( $project ) ) {
		$slug = isset( $project['slug'] ) ? $project['slug'] : '';
	} else {
		$slug = (string) $project;
	}

	$slug = sanitize_title( $slug );

	if ( '' === $slug || ! hhd_get_project( $slug ) ) {
		return '';
	}

	return $slug;
}

/**
 * Human label for a target reference.
 *
 * @param string $ref Target reference.
 * @return string
 */
function hhd_review_target_label( $ref ) {
	$project = hhd_get_project( $ref );
	return $project ? $project['title'] : $ref;
}

/**
 * Target reference carried by a stored review row.
 *
 * This is the only reader of the row's stored column outside the repository,
 * and it exists so that a move from slugs to stable project IDs is a change
 * to this file and one UPDATE — not a hunt through the REST and notification
 * layers for the column name.
 *
 * @param array<string,mixed> $review Review row.
 * @return string
 */
function hhd_review_row_target( $review ) {
	return isset( $review['project_slug'] ) ? (string) $review['project_slug'] : '';
}

/**
 * Project label for a stored review row.
 *
 * @param array<string,mixed> $review Review row.
 * @return string
 */
function hhd_review_row_label( $review ) {
	$ref = hhd_review_row_target( $review );

	return '' === $ref ? '' : hhd_review_target_label( $ref );
}

/**
 * Every selectable review target, as ref => label.
 *
 * @return array<string,string>
 */
function hhd_review_targets() {
	$targets = array();

	foreach ( hhd_projects() as $project ) {
		$ref = hhd_review_target_ref( $project );
		if ( $ref ) {
			$targets[ $ref ] = $project['title'];
		}
	}

	return $targets;
}

/**
 * Reviews for a target.
 *
 * @param string              $ref  Target reference.
 * @param array<string,mixed> $args status, page, per_page.
 * @return array{items: array<int, array<string,mixed>>, total: int}
 */
function hhd_reviews_for_target( $ref, $args = array() ) {
	$args['target'] = (string) $ref;
	return hhd_reviews_query( $args );
}

/**
 * Published reviews for a target — the only ones the public ever sees.
 *
 * @param string $ref   Target reference.
 * @param int    $limit Maximum rows.
 * @return array<int, array<string,mixed>>
 */
function hhd_approved_reviews_for_target( $ref, $limit = 20 ) {
	if ( '' === (string) $ref ) {
		return array();
	}

	$result = hhd_reviews_for_target(
		$ref,
		array(
			'status'   => 'approved',
			'per_page' => max( 1, (int) $limit ),
			'page'     => 1,
		)
	);

	return $result['items'];
}

/**
 * Average rating and count for a target, over approved reviews only.
 *
 * The average is taken over the most recent 100 approved reviews — the
 * ceiling the repository applies to per_page — which is far past the point
 * where one more review moves the displayed figure. The count is the real
 * total from the same query, not the size of that window, so a project with
 * more than 100 reviews still reports how many it has.
 *
 * Keeping the arithmetic here rather than in SQL is what lets the repository
 * stay the only file that writes queries.
 *
 * @param string $ref Target reference.
 * @return array{average: float, count: int}
 */
function hhd_review_summary_for_target( $ref ) {
	if ( '' === (string) $ref ) {
		return array(
			'average' => 0.0,
			'count'   => 0,
		);
	}

	$result = hhd_reviews_for_target(
		$ref,
		array(
			'status'   => 'approved',
			'per_page' => 100,
			'page'     => 1,
		)
	);

	if ( ! $result['items'] ) {
		return array(
			'average' => 0.0,
			'count'   => 0,
		);
	}

	$sum = 0;
	foreach ( $result['items'] as $review ) {
		$sum += (int) $review['rating'];
	}

	return array(
		'average' => round( $sum / count( $result['items'] ), 1 ),
		'count'   => (int) $result['total'],
	);
}

/**
 * Persian label for a review status.
 *
 * @param string $status Status key.
 * @return string
 */
function hhd_review_status_label( $status ) {
	$labels = array(
		'pending'  => 'در انتظار تأیید',
		'approved' => 'تأییدشده',
		'rejected' => 'ردشده',
		'hidden'   => 'پنهان',
	);

	return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
}

/**
 * Star rating markup, in the same editorial register as the rest of the site.
 *
 * @param float|int $rating Rating out of five.
 * @param string    $class  Optional extra class.
 * @return string
 */
function hhd_rating_stars( $rating, $class = '' ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$full   = (int) floor( $rating );
	$half   = ( $rating - $full ) >= 0.5;

	$html = sprintf(
		'<span class="%s" role="img" aria-label="%s">',
		esc_attr( trim( 'rating ' . $class ) ),
		esc_attr( sprintf( 'امتیاز %s از ۵', hhd_fa_num( rtrim( rtrim( number_format( $rating, 1, '.', '' ), '0' ), '.' ) ) ) )
	);

	for ( $i = 1; $i <= 5; $i++ ) {
		if ( $i <= $full ) {
			$state = 'is-full';
		} elseif ( $i === $full + 1 && $half ) {
			$state = 'is-half';
		} else {
			$state = 'is-empty';
		}

		$html .= sprintf(
			'<svg class="rating__star %s" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path d="M12 2.6l2.9 6.03 6.6.86-4.83 4.57 1.2 6.54L12 17.5l-5.87 3.1 1.2-6.54L2.5 9.49l6.6-.86z"/></svg>',
			esc_attr( $state )
		);
	}

	return $html . '</span>';
}

/**
 * Status label for a request, used by the dashboard.
 *
 * @param string $status Status key.
 * @return string
 */
function hhd_request_status_label( $status ) {
	$labels = array(
		'new'         => 'جدید',
		'in_progress' => 'در حال پیگیری',
		'done'        => 'انجام‌شده',
		'spam'        => 'اسپم',
	);

	return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
}

/**
 * Status label for a request type.
 *
 * @param string $type Request type.
 * @return string
 */
function hhd_request_type_label( $type ) {
	$labels = array(
		'contact'      => 'تماس',
		'consultation' => 'مشاوره',
	);

	return isset( $labels[ $type ] ) ? $labels[ $type ] : $type;
}
