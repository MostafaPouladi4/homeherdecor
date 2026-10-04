<?php
/**
 * REST API — namespace hhd/v1.
 *
 * Two audiences with different threat models:
 *
 * - Public routes accept anonymous submissions. A wp_rest nonce is
 *   session-tied for a logged-out visitor, expires, and goes stale behind
 *   page caching, so it cannot be the gate. Protection is layered
 *   server-side instead: strict validation, sanitization, a honeypot, a
 *   per-IP rate limit and content heuristics.
 * - Admin routes are cookie-authenticated. There the nonce *is* required:
 *   WordPress clears the current user when no nonce accompanies a cookie
 *   and rejects an invalid one with 403, so cookie auth and the nonce rise
 *   or fall together.
 *
 * This file validates and delegates. It owns no SQL.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/** REST namespace. */
define( 'HHD_REST_NS', 'hhd/v1' );

/** Submissions allowed per IP inside the rate-limit window. */
define( 'HHD_RATE_LIMIT_MAX', 5 );

/** Rate-limit window, in seconds. */
define( 'HHD_RATE_LIMIT_WINDOW', 600 );

/** Honeypot field name. Real users never see it, so it is always empty. */
define( 'HHD_HONEYPOT_FIELD', 'hhd_website' );

/* -------------------------------------------------------------------------
   1. Route registration
   ------------------------------------------------------------------------- */

/**
 * Register every route.
 *
 * @return void
 */
function hhd_register_rest_routes() {
	$public = '__return_true';

	register_rest_route(
		HHD_REST_NS,
		'/contact',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hhd_rest_contact',
			'permission_callback' => $public,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/consultation',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hhd_rest_consultation',
			'permission_callback' => $public,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/reviews',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'hhd_rest_reviews_list',
				'permission_callback' => $public,
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'hhd_rest_review_create',
				'permission_callback' => $public,
			),
		)
	);

	$admin = 'hhd_rest_admin_permission';

	register_rest_route(
		HHD_REST_NS,
		'/admin/stats',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hhd_rest_admin_stats',
			'permission_callback' => $admin,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/requests',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hhd_rest_admin_requests',
			'permission_callback' => $admin,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/requests/(?P<id>\d+)',
		array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => 'hhd_rest_admin_request_update',
				'permission_callback' => $admin,
				'args'                => array(
					'id'     => array( 'validate_callback' => 'hhd_rest_validate_id' ),
					'status' => array( 'required' => true ),
				),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'hhd_rest_admin_request_delete',
				'permission_callback' => $admin,
				'args'                => array(
					'id' => array( 'validate_callback' => 'hhd_rest_validate_id' ),
				),
			),
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/reviews',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hhd_rest_admin_reviews',
			'permission_callback' => $admin,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/reviews/(?P<id>\d+)',
		array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => 'hhd_rest_admin_review_update',
				'permission_callback' => $admin,
				'args'                => array(
					'id'     => array( 'validate_callback' => 'hhd_rest_validate_id' ),
					'status' => array( 'required' => true ),
				),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'hhd_rest_admin_review_delete',
				'permission_callback' => $admin,
				'args'                => array(
					'id' => array( 'validate_callback' => 'hhd_rest_validate_id' ),
				),
			),
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/notifications',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hhd_rest_admin_notifications',
			'permission_callback' => $admin,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/notifications/read-all',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hhd_rest_admin_notifications_read_all',
			'permission_callback' => $admin,
		)
	);

	register_rest_route(
		HHD_REST_NS,
		'/admin/notifications/(?P<id>\d+)',
		array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => 'hhd_rest_admin_notification_update',
				'permission_callback' => $admin,
				'args'                => array(
					'id' => array( 'validate_callback' => 'hhd_rest_validate_id' ),
				),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'hhd_rest_admin_notification_delete',
				'permission_callback' => $admin,
				'args'                => array(
					'id' => array( 'validate_callback' => 'hhd_rest_validate_id' ),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'hhd_register_rest_routes' );

/**
 * Capability gate for every admin route.
 *
 * WordPress's rest_cookie_check_errors() clears the current user when a
 * cookie arrives without a nonce, and returns 403 for an invalid one — so
 * this single check covers both capability and nonce.
 *
 * @return true|WP_Error
 */
function hhd_rest_admin_permission() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
			'hhd_forbidden',
			'دسترسی به این بخش تنها برای مدیران مجاز است.',
			array( 'status' => rest_authorization_required_code() )
		);
	}

	return true;
}

/**
 * Validate an :id path segment.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function hhd_rest_validate_id( $value ) {
	return is_numeric( $value ) && (int) $value > 0;
}

/* -------------------------------------------------------------------------
   2. Public submission guards
   ------------------------------------------------------------------------- */

/**
 * Verify the REST nonce only where it can mean something.
 *
 * For a logged-out visitor the nonce is session-tied and expires long
 * before a cached page is served, so a stale one must never reject a real
 * submission. For a logged-in user it is genuine CSRF protection, and
 * there it is enforced.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function hhd_rest_public_nonce( $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );

	if ( ! $nonce || ! is_user_logged_in() ) {
		return true;
	}

	if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error(
			'hhd_stale_session',
			'نشست شما منقضی شده است. صفحه را دوباره بارگذاری کنید.',
			array( 'status' => 403 )
		);
	}

	return true;
}

/**
 * Client IP, used only to key the rate-limit transient.
 *
 * Never stored, never displayed.
 *
 * @return string
 */
function hhd_rest_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	return $ip ? $ip : 'unknown';
}

/**
 * Transient key for this client's submission counter.
 *
 * @return string
 */
function hhd_rest_rate_key() {
	return 'hhd_rl_' . substr( wp_hash( hhd_rest_client_ip() ), 0, 20 );
}

/**
 * Whether this client has submissions left in the window.
 *
 * @return bool
 */
function hhd_rest_rate_ok() {
	return (int) get_transient( hhd_rest_rate_key() ) < HHD_RATE_LIMIT_MAX;
}

/**
 * Record one submission against this client.
 *
 * @return void
 */
function hhd_rest_rate_bump() {
	$key   = hhd_rest_rate_key();
	$count = (int) get_transient( $key );

	set_transient( $key, $count + 1, HHD_RATE_LIMIT_WINDOW );
}

/**
 * Run the shared guard for anonymous submissions.
 *
 * Only the nonce lives here because it is the one check both public handlers
 * need in the same place. The honeypot, the rate limit and field validation
 * are applied by each handler in the order that matters to it: honeypot
 * first so a bot is discarded quietly, then the counter — and the counter is
 * bumped before the fields are validated, so a loop of malformed posts still
 * exhausts its quota.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function hhd_rest_public_guard( $request ) {
	$nonce = hhd_rest_public_nonce( $request );
	if ( is_wp_error( $nonce ) ) {
		return $nonce;
	}

	return true;
}

/**
 * Whether the honeypot was filled.
 *
 * @param WP_REST_Request $request Request.
 * @return bool
 */
function hhd_rest_honeypot_tripped( $request ) {
	$value = $request->get_param( HHD_HONEYPOT_FIELD );

	return is_string( $value ) && '' !== trim( $value );
}

/**
 * The "quiet discard" response.
 *
 * Deliberately shaped like a success so a bot learns nothing about why its
 * submission vanished, while no row is written.
 *
 * @return WP_REST_Response
 */
function hhd_rest_discard_response() {
	return new WP_REST_Response(
		array(
			'stored'  => false,
			'message' => 'درخواست شما ثبت شد.',
		),
		202
	);
}

/**
 * Reject an over-quota client.
 *
 * @return WP_Error
 */
function hhd_rest_rate_error() {
	return new WP_Error(
		'hhd_rate_limited',
		'تعداد ارسال‌ها بیش از حد مجاز است. لطفاً چند دقیقه‌ دیگر دوباره تلاش کنید.',
		array( 'status' => 429 )
	);
}

/**
 * Cheap content heuristics for drive-by spam.
 *
 * Length bounds plus a link-count check — enough to catch the common
 * cases without a CAPTCHA or an external service.
 *
 * @param string $message Message body.
 * @return true|WP_Error
 */
function hhd_rest_content_heuristics( $message ) {
	$message = (string) $message;

	if ( strlen( $message ) > 5000 ) {
		return new WP_Error( 'hhd_too_long', 'متن پیام بیش از حد طولانی است.', array( 'status' => 400 ) );
	}

	$links = preg_match_all( '#https?://#i', $message );
	if ( $links > 3 ) {
		return new WP_Error( 'hhd_too_many_links', 'متن پیام شامل پیوندهای بیش از حد است.', array( 'status' => 400 ) );
	}

	return true;
}

/* -------------------------------------------------------------------------
   3. Field validation
   ------------------------------------------------------------------------- */

/**
 * Length-checked, sanitized single-line text.
 *
 * @param mixed  $value    Raw value.
 * @param int    $max      Maximum length.
 * @param string $required Whether an empty value is an error.
 * @return string|WP_Error
 */
function hhd_rest_text( $value, $max, $required ) {
	$value = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	$value = hhd_normalize_digits( $value );
	$value = trim( $value );

	if ( '' === $value ) {
		return $required
			? new WP_Error( 'hhd_missing_field', 'پر کردن این فیلد الزامی است.', array( 'status' => 400 ) )
			: '';
	}

	if ( mb_strlen( $value ) > $max ) {
		return new WP_Error( 'hhd_field_too_long', 'مقدار واردشده بیش از حد طولانی است.', array( 'status' => 400 ) );
	}

	return $value;
}

/**
 * Normalize and validate a phone number.
 *
 * Persian and Arabic digits are converted first, because that is what a
 * Persian keyboard produces and a tel: link cannot use them.
 *
 * @param mixed $value Raw value.
 * @return string|WP_Error Digits with an optional leading +.
 */
function hhd_rest_phone( $value ) {
	$value = is_scalar( $value ) ? hhd_normalize_digits( (string) $value ) : '';
	$value = preg_replace( '/[^\d+]/', '', $value );
	$value = trim( $value, '+' ) === '' ? '' : $value;

	if ( '' === $value ) {
		return new WP_Error( 'hhd_missing_phone', 'شماره تماس را وارد کنید.', array( 'status' => 400 ) );
	}

	$digits = preg_replace( '/\D/', '', $value );

	if ( strlen( $digits ) < 8 || strlen( $digits ) > 15 ) {
		return new WP_Error( 'hhd_bad_phone', 'شماره تماس معتبر نیست.', array( 'status' => 400 ) );
	}

	return $value;
}

/**
 * Sanitized multi-line text.
 *
 * @param mixed  $value    Raw value.
 * @param int    $max      Maximum length.
 * @param bool   $required Whether an empty value is an error.
 * @param string $label    Field label, for the error message.
 * @return string|WP_Error
 */
function hhd_rest_textarea( $value, $max, $required, $label ) {
	$value = is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : '';
	$value = trim( $value );

	if ( '' === $value ) {
		return $required
			? new WP_Error( 'hhd_missing_message', sprintf( 'نوشتن %s الزامی است.', $label ), array( 'status' => 400 ) )
			: '';
	}

	if ( mb_strlen( $value ) > $max ) {
		return new WP_Error( 'hhd_message_too_long', sprintf( '%s بیش از حد طولانی است.', $label ), array( 'status' => 400 ) );
	}

	return $value;
}

/**
 * Validate a project type against the same list the form offers.
 *
 * An allow-list, not a length check: the value comes from a <select> the
 * theme itself renders, so anything else is a forged request.
 *
 * @param mixed $value Raw value.
 * @return string|WP_Error
 */
function hhd_rest_project_type( $value ) {
	$value = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	$value = trim( $value );

	if ( '' === $value ) {
		return '';
	}

	if ( ! in_array( $value, hhd_project_types(), true ) ) {
		return new WP_Error( 'hhd_bad_project_type', 'نوع پروژه‌ی انتخاب‌شده معتبر نیست.', array( 'status' => 400 ) );
	}

	return $value;
}

/**
 * Validate a call-time preference against the <select> options.
 *
 * @param mixed $value Raw value.
 * @return string|WP_Error
 */
function hhd_rest_call_time( $value ) {
	$value = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	$value = trim( $value );

	if ( '' === $value ) {
		return '';
	}

	if ( ! in_array( $value, array( 'صبح', 'بعدازظهر', 'عصر' ), true ) ) {
		return new WP_Error( 'hhd_bad_call_time', 'زمان تماس انتخاب‌شده معتبر نیست.', array( 'status' => 400 ) );
	}

	return $value;
}

/**
 * Validate a rating.
 *
 * @param mixed $value Raw value.
 * @return int|WP_Error
 */
function hhd_rest_rating( $value ) {
	$value = hhd_normalize_digits( is_scalar( $value ) ? (string) $value : '' );
	$value = trim( $value );

	if ( '' === $value || ! is_numeric( $value ) ) {
		return new WP_Error( 'hhd_bad_rating', 'امتیاز را انتخاب کنید.', array( 'status' => 400 ) );
	}

	$value = (int) $value;

	if ( $value < 1 || $value > 5 ) {
		return new WP_Error( 'hhd_bad_rating', 'امتیاز باید بین ۱ تا ۵ باشد.', array( 'status' => 400 ) );
	}

	return $value;
}

/**
 * Build a validated request payload from raw REST params.
 *
 * @param WP_REST_Request $request Request.
 * @param string          $type    contact | consultation.
 * @param array<string,array{0:string,1:int,2:bool}> $map field => [param, max, required].
 * @return array<string,mixed>|WP_Error
 */
function hhd_rest_collect_request( $request, $type, $map ) {
	$data = array( 'type' => $type );

	foreach ( $map as $field => $spec ) {
		list( $param, $max, $required ) = $spec;

		$value = hhd_rest_text( $request->get_param( $param ), $max, $required );
		if ( is_wp_error( $value ) ) {
			return $value;
		}

		$data[ $field ] = $value;
	}

	$project_type = hhd_rest_project_type( $request->get_param( 'project_type' ) );
	if ( is_wp_error( $project_type ) ) {
		return $project_type;
	}
	$data['project_type'] = $project_type;

	$message = hhd_rest_textarea( $request->get_param( 'message' ), 2000, false, 'توضیحات' );
	if ( is_wp_error( $message ) ) {
		return $message;
	}

	$heuristics = hhd_rest_content_heuristics( $message );
	if ( is_wp_error( $heuristics ) ) {
		return $heuristics;
	}
	$data['message'] = $message;

	$phone = hhd_rest_phone( $request->get_param( 'phone' ) );
	if ( is_wp_error( $phone ) ) {
		return $phone;
	}
	$data['phone'] = $phone;

	return $data;
}

/* -------------------------------------------------------------------------
   4. Public handlers
   ------------------------------------------------------------------------- */

/**
 * POST /contact
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_contact( $request ) {
	return hhd_rest_store_request( $request, 'contact' );
}

/**
 * POST /consultation
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_consultation( $request ) {
	return hhd_rest_store_request( $request, 'consultation' );
}

/**
 * Shared handler for both request forms.
 *
 * @param WP_REST_Request $request Request.
 * @param string          $type    contact | consultation.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_store_request( $request, $type ) {
	$guard = hhd_rest_public_guard( $request );
	if ( is_wp_error( $guard ) ) {
		return $guard;
	}

	if ( hhd_rest_honeypot_tripped( $request ) ) {
		return hhd_rest_discard_response();
	}

	if ( ! hhd_rest_rate_ok() ) {
		return hhd_rest_rate_error();
	}
	hhd_rest_rate_bump();

	$map = array(
		'name' => array( 'name', 120, true ),
		'area' => array( 'area', 60, false ),
		'city' => array( 'city', 120, false ),
	);

	$data = hhd_rest_collect_request( $request, $type, $map );
	if ( is_wp_error( $data ) ) {
		return $data;
	}

	if ( 'consultation' === $type ) {
		$call_time = hhd_rest_call_time( $request->get_param( 'call_time' ) );
		if ( is_wp_error( $call_time ) ) {
			return $call_time;
		}
		$data['call_time'] = $call_time;
	}

	$id = hhd_request_insert( $data );
	if ( is_wp_error( $id ) ) {
		return $id;
	}

	hhd_notify_new_request( $id, $type );

	return new WP_REST_Response(
		array(
			'stored'  => true,
			'id'      => $id,
			'message' => 'درخواست شما ثبت شد. به‌زودی با شما تماس می‌گیریم.',
		),
		201
	);
}

/**
 * GET /reviews — approved reviews for one project.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_reviews_list( $request ) {
	$ref = hhd_review_target_ref( (string) $request->get_param( 'project' ) );

	if ( '' === $ref ) {
		return new WP_Error( 'hhd_bad_project', 'پروژه‌ی مورد نظر پیدا نشد.', array( 'status' => 400 ) );
	}

	$page     = max( 1, (int) $request->get_param( 'page' ) );
	$per_page = (int) $request->get_param( 'per_page' );
	$per_page = $per_page > 0 ? min( 50, $per_page ) : 10;

	$result  = hhd_reviews_for_target(
		$ref,
		array(
			'status'   => 'approved',
			'page'     => $page,
			'per_page' => $per_page,
		)
	);
	$summary = hhd_review_summary_for_target( $ref );

	return new WP_REST_Response(
		array(
			'items'   => array_map( 'hhd_rest_prepare_review', $result['items'] ),
			'total'   => $result['total'],
			'page'    => $page,
			'average' => $summary['average'],
			'count'   => $summary['count'],
		),
		200
	);
}

/**
 * POST /reviews — submit a review for moderation.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_review_create( $request ) {
	$guard = hhd_rest_public_guard( $request );
	if ( is_wp_error( $guard ) ) {
		return $guard;
	}

	if ( hhd_rest_honeypot_tripped( $request ) ) {
		return hhd_rest_discard_response();
	}

	if ( ! hhd_rest_rate_ok() ) {
		return hhd_rest_rate_error();
	}
	hhd_rest_rate_bump();

	$ref = hhd_review_target_ref( (string) $request->get_param( 'project' ) );
	if ( '' === $ref ) {
		return new WP_Error( 'hhd_bad_project', 'پروژه‌ی مورد نظر پیدا نشد.', array( 'status' => 400 ) );
	}

	$name = hhd_rest_text( $request->get_param( 'name' ), 120, true );
	if ( is_wp_error( $name ) ) {
		return $name;
	}

	$rating = hhd_rest_rating( $request->get_param( 'rating' ) );
	if ( is_wp_error( $rating ) ) {
		return $rating;
	}

	$message = hhd_rest_textarea( $request->get_param( 'message' ), 1500, true, 'متن نظر' );
	if ( is_wp_error( $message ) ) {
		return $message;
	}

	$heuristics = hhd_rest_content_heuristics( $message );
	if ( is_wp_error( $heuristics ) ) {
		return $heuristics;
	}

	if ( mb_strlen( $message ) < 10 ) {
		return new WP_Error( 'hhd_message_too_short', 'متن نظر بسیار کوتاه است.', array( 'status' => 400 ) );
	}

	$id = hhd_review_insert(
		array(
			'target'  => $ref,
			'name'    => $name,
			'rating'  => $rating,
			'message' => $message,
		)
	);

	if ( is_wp_error( $id ) ) {
		return $id;
	}

	hhd_notify_new_review( $id );

	return new WP_REST_Response(
		array(
			'stored'  => true,
			'id'      => $id,
			'status'  => 'pending',
			'message' => 'نظر شما ثبت شد و پس از تأیید نمایش داده می‌شود.',
		),
		201
	);
}

/* -------------------------------------------------------------------------
   5. Admin handlers
   ------------------------------------------------------------------------- */

/**
 * GET /admin/stats
 *
 * @return WP_REST_Response
 */
function hhd_rest_admin_stats() {
	return new WP_REST_Response( hhd_dashboard_counts(), 200 );
}

/**
 * GET /admin/requests
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function hhd_rest_admin_requests( $request ) {
	$result = hhd_requests_query( hhd_rest_list_args( $request, array( 'type', 'status' ) ) );

	return new WP_REST_Response(
		array(
			'items' => array_map( 'hhd_rest_prepare_request', $result['items'] ),
			'total' => $result['total'],
			'stats' => hhd_dashboard_counts(),
		),
		200
	);
}

/**
 * PATCH /admin/requests/<id>
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_admin_request_update( $request ) {
	$id          = (int) $request['id'];
	$request_row = hhd_request_get( $id );

	if ( ! $request_row ) {
		return new WP_Error( 'hhd_not_found', 'درخواست مورد نظر پیدا نشد.', array( 'status' => 404 ) );
	}

	$status = sanitize_text_field( (string) $request->get_param( 'status' ) );

	if ( ! hhd_is_allowed( $status, hhd_request_statuses() ) ) {
		return new WP_Error( 'hhd_bad_status', 'وضعیت انتخاب‌شده معتبر نیست.', array( 'status' => 400 ) );
	}

	if ( ! hhd_request_update_status( $id, $status ) ) {
		return new WP_Error( 'hhd_update_failed', 'به‌روزرسانی وضعیت انجام نشد.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response(
		array(
			'item'  => hhd_rest_prepare_request( hhd_request_get( $id ) ),
			'stats' => hhd_dashboard_counts(),
		),
		200
	);
}

/**
 * DELETE /admin/requests/<id>
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_admin_request_delete( $request ) {
	$id = (int) $request['id'];

	if ( ! hhd_request_get( $id ) ) {
		return new WP_Error( 'hhd_not_found', 'درخواست مورد نظر پیدا نشد.', array( 'status' => 404 ) );
	}

	if ( ! hhd_request_delete( $id ) ) {
		return new WP_Error( 'hhd_delete_failed', 'حذف درخواست انجام نشد.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response(
		array(
			'deleted' => true,
			'id'      => $id,
			'stats'   => hhd_dashboard_counts(),
		),
		200
	);
}

/**
 * GET /admin/reviews
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function hhd_rest_admin_reviews( $request ) {
	$args = hhd_rest_list_args( $request, array( 'status' ) );

	$target = (string) $request->get_param( 'project' );
	if ( '' !== $target ) {
		$args['target'] = $target;
	}

	$result = hhd_reviews_query( $args );

	return new WP_REST_Response(
		array(
			'items' => array_map( 'hhd_rest_prepare_review', $result['items'] ),
			'total' => $result['total'],
			'stats' => hhd_dashboard_counts(),
		),
		200
	);
}

/**
 * PATCH /admin/reviews/<id>
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_admin_review_update( $request ) {
	$id = (int) $request['id'];

	if ( ! hhd_review_get( $id ) ) {
		return new WP_Error( 'hhd_not_found', 'نظر مورد نظر پیدا نشد.', array( 'status' => 404 ) );
	}

	$status = sanitize_text_field( (string) $request->get_param( 'status' ) );

	if ( ! hhd_is_allowed( $status, hhd_review_statuses() ) ) {
		return new WP_Error( 'hhd_bad_status', 'وضعیت انتخاب‌شده معتبر نیست.', array( 'status' => 400 ) );
	}

	if ( ! hhd_review_update_status( $id, $status ) ) {
		return new WP_Error( 'hhd_update_failed', 'به‌روزرسانی وضعیت انجام نشد.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response(
		array(
			'item'  => hhd_rest_prepare_review( hhd_review_get( $id ) ),
			'stats' => hhd_dashboard_counts(),
		),
		200
	);
}

/**
 * DELETE /admin/reviews/<id>
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_admin_review_delete( $request ) {
	$id = (int) $request['id'];

	if ( ! hhd_review_get( $id ) ) {
		return new WP_Error( 'hhd_not_found', 'نظر مورد نظر پیدا نشد.', array( 'status' => 404 ) );
	}

	if ( ! hhd_review_delete( $id ) ) {
		return new WP_Error( 'hhd_delete_failed', 'حذف نظر انجام نشد.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response(
		array(
			'deleted' => true,
			'id'      => $id,
			'stats'   => hhd_dashboard_counts(),
		),
		200
	);
}

/**
 * GET /admin/notifications
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function hhd_rest_admin_notifications( $request ) {
	$result = hhd_notifications_query(
		array(
			'unread'   => (bool) $request->get_param( 'unread' ),
			'page'     => max( 1, (int) $request->get_param( 'page' ) ),
			'per_page' => hhd_rest_per_page( $request ),
		)
	);

	return new WP_REST_Response(
		array(
			'items'  => array_map( 'hhd_rest_prepare_notification', $result['items'] ),
			'total'  => $result['total'],
			'unread' => $result['unread'],
		),
		200
	);
}

/**
 * PATCH /admin/notifications/<id> — toggles the read flag.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_admin_notification_update( $request ) {
	$id = (int) $request['id'];

	if ( ! hhd_notification_get( $id ) ) {
		return new WP_Error( 'hhd_not_found', 'اعلان مورد نظر پیدا نشد.', array( 'status' => 404 ) );
	}

	$is_read = $request->get_param( 'is_read' );
	$is_read = null === $is_read ? true : (bool) $is_read;

	if ( ! hhd_notification_set_read( $id, $is_read ) ) {
		return new WP_Error( 'hhd_update_failed', 'به‌روزرسانی اعلان انجام نشد.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response(
		array(
			'item'   => hhd_rest_prepare_notification( hhd_notification_get( $id ) ),
			'unread' => hhd_notifications_unread_count(),
		),
		200
	);
}

/**
 * DELETE /admin/notifications/<id>
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hhd_rest_admin_notification_delete( $request ) {
	$id = (int) $request['id'];

	if ( ! hhd_notification_get( $id ) ) {
		return new WP_Error( 'hhd_not_found', 'اعلان مورد نظر پیدا نشد.', array( 'status' => 404 ) );
	}

	if ( ! hhd_notification_delete( $id ) ) {
		return new WP_Error( 'hhd_delete_failed', 'حذف اعلان انجام نشد.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response(
		array(
			'deleted' => true,
			'id'      => $id,
			'unread'  => hhd_notifications_unread_count(),
		),
		200
	);
}

/**
 * POST /admin/notifications/read-all
 *
 * @return WP_REST_Response
 */
function hhd_rest_admin_notifications_read_all() {
	$updated = hhd_notifications_mark_all_read();

	return new WP_REST_Response(
		array(
			'updated' => $updated,
			'unread'  => hhd_notifications_unread_count(),
		),
		200
	);
}

/* -------------------------------------------------------------------------
   6. Shared helpers and response shapes
   ------------------------------------------------------------------------- */

/**
 * Pagination and filter args shared by the admin list routes.
 *
 * @param WP_REST_Request   $request Request.
 * @param array<int,string> $filters Filter params to carry through.
 * @return array<string,mixed>
 */
function hhd_rest_list_args( $request, $filters = array() ) {
	$args = array(
		'search'   => sanitize_text_field( (string) $request->get_param( 'search' ) ),
		'page'     => max( 1, (int) $request->get_param( 'page' ) ),
		'per_page' => hhd_rest_per_page( $request ),
	);

	foreach ( $filters as $filter ) {
		$args[ $filter ] = sanitize_text_field( (string) $request->get_param( $filter ) );
	}

	return $args;
}

/**
 * Requested page size, clamped.
 *
 * @param WP_REST_Request $request Request.
 * @return int
 */
function hhd_rest_per_page( $request ) {
	$per_page = (int) $request->get_param( 'per_page' );

	return $per_page > 0 ? max( 1, min( 100, $per_page ) ) : 20;
}

/**
 * JSON shape of a request row.
 *
 * @param array<string,mixed>|null $row Row.
 * @return array<string,mixed>
 */
function hhd_rest_prepare_request( $row ) {
	if ( ! $row ) {
		return array();
	}

	return array(
		'id'           => (int) $row['id'],
		'type'         => $row['type'],
		'type_label'   => hhd_request_type_label( $row['type'] ),
		'name'         => $row['name'],
		'phone'        => $row['phone'],
		'project_type' => $row['project_type'],
		'area'         => $row['area'],
		'city'         => $row['city'],
		'call_time'    => $row['call_time'],
		'message'      => $row['message'],
		'status'       => $row['status'],
		'status_label' => hhd_request_status_label( $row['status'] ),
		'created_at'   => $row['created_at'],
		// Display-ready, so the server-rendered table and the JavaScript
		// re-render show an identical Jalali date without a second calendar
		// implementation in the browser. Same reasoning as
		// hhd_rest_prepare_notification().
		'created'      => hhd_date_label( $row['created_at'] ),
	);
}

/**
 * JSON shape of a review row.
 *
 * The target reference is exposed as-is; consumers treat it as opaque and
 * hand it back to the API, never parsing it.
 *
 * @param array<string,mixed>|null $row Row.
 * @return array<string,mixed>
 */
function hhd_rest_prepare_review( $row ) {
	if ( ! $row ) {
		return array();
	}

	$ref = hhd_review_row_target( $row );

	return array(
		'id'           => (int) $row['id'],
		'target'       => $ref,
		'target_label' => hhd_review_row_label( $row ),
		'name'         => $row['name'],
		'rating'       => (int) $row['rating'],
		'rating_label' => hhd_fa_num( (int) $row['rating'] ) . ' از ۵',
		'message'      => $row['message'],
		'status'       => $row['status'],
		'status_label' => hhd_review_status_label( $row['status'] ),
		'created_at'   => $row['created_at'],
		'created'      => hhd_date_label( $row['created_at'] ),
	);
}

/**
 * JSON shape of a notification row.
 *
 * @param array<string,mixed>|null $row Row.
 * @return array<string,mixed>
 */
function hhd_rest_prepare_notification( $row ) {
	if ( ! $row ) {
		return array();
	}

	return array(
		'id'         => (int) $row['id'],
		'type'       => $row['type'],
		'object_id'  => (int) $row['object_id'],
		'title'      => $row['title'],
		'message'    => $row['message'],
		'url'        => $row['url'],
		'is_read'    => (bool) $row['is_read'],
		'created_at' => $row['created_at'],
		// Formatted here so the server-rendered list and the JavaScript
		// re-render show identical Jalali dates without a second calendar
		// implementation in the browser.
		'created_label' => hhd_date_label( $row['created_at'] ),
	);
}
