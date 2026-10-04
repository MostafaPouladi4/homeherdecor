<?php
/**
 * Admin dashboard.
 *
 * A top-level homehrdecor menu with five screens: the dashboard, the two
 * request inboxes, review moderation and the notification log. Screens
 * render their first paint from the repositories — the same functions the
 * REST layer uses — and assets/js/admin.js takes over from there.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/** Top-level menu slug, and the parent every screen hangs off. */
define( 'HHD_ADMIN_MENU', 'hhd-dashboard' );

/** Rows per page in every admin list. */
define( 'HHD_ADMIN_PER_PAGE', 20 );

/* -------------------------------------------------------------------------
   1. Menu
   ------------------------------------------------------------------------- */

/**
 * Register the menu.
 *
 * @return void
 */
function hhd_admin_menu() {
	$capability = 'manage_options';
	$unread     = hhd_notifications_unread_count();

	$bubble = $unread
		? sprintf(
			' <span class="update-plugins count-%1$d"><span class="plugin-count">%1$d</span></span>',
			$unread
		)
		: '';

	add_menu_page(
		'homehrdecor',
		'homehrdecor' . $bubble,
		$capability,
		HHD_ADMIN_MENU,
		'hhd_screen_dashboard',
		'dashicons-admin-customizer',
		58
	);

	$screens = array(
		HHD_ADMIN_MENU        => array( 'داشبورد', 'hhd_screen_dashboard' ),
		'hhd-contact'         => array( 'درخواست‌های تماس', 'hhd_screen_contact' ),
		'hhd-consultation'    => array( 'درخواست‌های مشاوره', 'hhd_screen_consultation' ),
		'hhd-reviews'         => array( 'نظرات', 'hhd_screen_reviews' ),
		'hhd-notifications'   => array( 'اعلان‌ها', 'hhd_screen_notifications' ),
	);

	foreach ( $screens as $slug => $screen ) {
		add_submenu_page(
			HHD_ADMIN_MENU,
			$screen[0],
			$screen[0],
			$capability,
			$slug,
			$screen[1]
		);
	}
}
add_action( 'admin_menu', 'hhd_admin_menu' );

/**
 * Admin assets, only on this theme's screens.
 *
 * The stylesheet re-declares the design tokens under .hhd-admin rather than
 * loading the front-end bundle, so stock wp-admin is left untouched and the
 * public CSS never grows.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function hhd_admin_enqueue( $hook ) {
	if ( false === strpos( $hook, '_page_hhd-' ) ) {
		return;
	}

	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'hhd-admin',
		$uri . '/assets/css/admin.css',
		array(),
		hhd_asset_version( 'assets/css/admin.css' )
	);

	wp_enqueue_script(
		'hhd-admin',
		$uri . '/assets/js/admin.js',
		array(),
		hhd_asset_version( 'assets/js/admin.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	$screen = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

	wp_localize_script(
		'hhd-admin',
		'hhdAdmin',
		array(
			'restUrl'     => esc_url_raw( rest_url( HHD_REST_NS ) ),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
			'screen'      => $screen,
			'requestType' => hhd_admin_request_type( $screen ),
			'perPage'     => HHD_ADMIN_PER_PAGE,
			'i18n'        => array(
				'confirmTitle'  => 'تأیید حذف',
				'confirmDelete' => 'این مورد برای همیشه حذف می‌شود و قابل بازگردانی نیست. مطمئن هستید؟',
				'empty'         => 'موردی ثبت نشده است.',
				'emptyText'     => 'با تغییر فیلترها دوباره تلاش کنید.',
				'error'         => 'خطا در ارتباط با سرور. دوباره تلاش کنید.',
				'page'          => 'صفحه %1$s از %2$s',
				'prev'          => 'قبلی',
				'next'          => 'بعدی',
				'view'          => 'جزئیات',
				'close'         => 'بستن',
				'readAll'       => 'همه خوانده شد',
				'saved'         => 'ذخیره شد.',
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'hhd_admin_enqueue' );

/**
 * Request type a screen is scoped to.
 *
 * @param string $screen Admin page slug.
 * @return string
 */
function hhd_admin_request_type( $screen ) {
	if ( 'hhd-contact' === $screen ) {
		return 'contact';
	}
	if ( 'hhd-consultation' === $screen ) {
		return 'consultation';
	}
	return '';
}

/* -------------------------------------------------------------------------
   2. Shared screen furniture
   ------------------------------------------------------------------------- */

/**
 * The notification bell and its dropdown.
 *
 * Rendered server-side so it is correct on first paint, then refreshed by
 * admin.js after any action that changes the unread count.
 *
 * @return string
 */
function hhd_admin_notification_bell() {
	$unread = hhd_notifications_unread_count();
	$recent = hhd_notifications_query( array( 'per_page' => 8 ) );

	ob_start();
	?>
	<div class="hhd-bell" data-hhd-bell data-hhd-endpoint="admin/notifications">
		<button type="button" class="hhd-bell__toggle" data-hhd-bell-toggle
			aria-expanded="false" aria-controls="hhd-bell-panel">
			<span class="hhd-bell__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
			</span>
			<span class="hhd-bell__label">اعلان‌ها</span>
			<span class="hhd-bell__count<?php echo $unread ? '' : ' is-empty'; ?>" data-hhd-bell-count>
				<?php echo esc_html( hhd_fa_num( $unread ) ); ?>
			</span>
		</button>

		<div class="hhd-bell__panel" id="hhd-bell-panel" hidden>
			<div class="hhd-bell__head">
				<span>اعلان‌های تازه</span>
				<button type="button" class="hhd-btn hhd-btn--ghost hhd-btn--sm" data-hhd-read-all>
					<?php echo esc_html( 'همه را خوانده‌شده کن' ); ?>
				</button>
			</div>

			<div class="hhd-bell__list" data-hhd-bell-list data-hhd-endpoint="admin/notifications">
				<?php if ( ! $recent['items'] ) : ?>
					<?php hhd_admin_empty( 'اعلان تازه‌ای ندارید.' ); ?>
				<?php else : ?>
					<?php foreach ( $recent['items'] as $notification ) : ?>
						<?php hhd_admin_notification_item( hhd_rest_prepare_notification( $notification ) ); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<a class="hhd-bell__foot" href="<?php echo esc_url( hhd_admin_link( 'hhd-notifications' ) ); ?>">
				مشاهده‌ی همه‌ی اعلان‌ها
			</a>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Actions spec for a request row.
 *
 * @return array<int, array<string,string>>
 */
function hhd_admin_request_actions() {
	return array(
		array(
			'action' => 'view',
			'label'  => 'مشاهده',
			'style'  => 'ghost',
		),
		array(
			'action' => 'status',
			'status' => 'in_progress',
			'label'  => 'در حال پیگیری',
			'style'  => 'ghost',
		),
		array(
			'action' => 'status',
			'status' => 'done',
			'label'  => 'انجام شد',
			'style'  => 'ghost',
		),
		array(
			'action' => 'status',
			'status' => 'spam',
			'label'  => 'اسپم',
			'style'  => 'ghost',
		),
		array(
			'action' => 'delete',
			'label'  => 'حذف',
			'style'  => 'danger',
		),
	);
}

/**
 * Actions spec for a review row.
 *
 * @return array<int, array<string,string>>
 */
function hhd_admin_review_actions() {
	return array(
		array(
			'action' => 'view',
			'label'  => 'مشاهده',
			'style'  => 'ghost',
		),
		array(
			'action' => 'status',
			'status' => 'approved',
			'label'  => 'تأیید',
			'style'  => 'primary',
		),
		array(
			'action' => 'status',
			'status' => 'rejected',
			'label'  => 'رد',
			'style'  => 'ghost',
		),
		array(
			'action' => 'status',
			'status' => 'hidden',
			'label'  => 'پنهان',
			'style'  => 'ghost',
		),
		array(
			'action' => 'delete',
			'label'  => 'حذف',
			'style'  => 'danger',
		),
	);
}

/**
 * Columns for a request table.
 *
 * @return array<string,array<string,string>>
 */
function hhd_admin_request_columns() {
	return array(
		'name'         => array(
			'label' => 'نام',
			'type'  => 'strong',
		),
		'phone'        => array(
			'label' => 'شماره تماس',
			'type'  => 'ltr',
		),
		'project_type' => array( 'label' => 'نوع پروژه' ),
		'city'         => array( 'label' => 'شهر' ),
		'status'       => array(
			'label' => 'وضعیت',
			'type'  => 'badge',
		),
		'created'      => array( 'label' => 'تاریخ' ),
		'actions'      => array(
			'label' => 'عملیات',
			'type'  => 'actions',
		),
	);
}

/**
 * Columns for a review table.
 *
 * `rating_label` rather than `rating`: the REST shape carries the raw 1–5
 * integer for API consumers and the Persian label for display, and both this
 * table and assets/js/admin.js render the label.
 *
 * @return array<string,array<string,string>>
 */
function hhd_admin_review_columns() {
	return array(
		'name'         => array(
			'label' => 'نام',
			'type'  => 'strong',
		),
		'target_label' => array( 'label' => 'پروژه' ),
		'rating_label' => array( 'label' => 'امتیاز' ),
		'message'      => array( 'label' => 'متن نظر' ),
		'status'       => array(
			'label' => 'وضعیت',
			'type'  => 'badge',
		),
		'created'      => array( 'label' => 'تاریخ' ),
		'actions'      => array(
			'label' => 'عملیات',
			'type'  => 'actions',
		),
	);
}

/* -------------------------------------------------------------------------
   3. Screens
   ------------------------------------------------------------------------- */

/**
 * Dashboard: counters, recent activity and the unread queue.
 *
 * @return void
 */
function hhd_screen_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html( 'دسترسی به این صفحه مجاز نیست.' ) );
	}

	$counts  = hhd_dashboard_counts();
	$recent  = hhd_requests_query( array( 'per_page' => 6 ) );
	$pending = hhd_reviews_query(
		array(
			'status'   => 'pending',
			'per_page' => 5,
		)
	);

	hhd_admin_open(
		array(
			'title'    => 'داشبورد',
			'subtitle' => 'نمای کلی درخواست‌ها، نظرات و اعلان‌های تازه.',
			'actions'  => hhd_admin_notification_bell(),
		)
	);
	?>
	<div class="hhd-stats">
		<?php
		hhd_admin_stat_card(
			array(
				'label'  => 'درخواست‌های جدید',
				'value'  => $counts['requests']['new'],
				'hint'   => 'در انتظار بررسی',
				'status' => 'new',
				'url'    => hhd_admin_link( 'hhd-contact' ),
			)
		);
		hhd_admin_stat_card(
			array(
				'label' => 'در حال پیگیری',
				'value' => $counts['requests']['in_progress'],
				'hint'  => 'درخواست‌های باز',
				'url'   => hhd_admin_link( 'hhd-contact', array( 'status' => 'in_progress' ) ),
			)
		);
		hhd_admin_stat_card(
			array(
				'label' => 'درخواست مشاوره',
				'value' => array_sum( $counts['consultation'] ),
				'hint'  => 'مجموع',
				'url'   => hhd_admin_link( 'hhd-consultation' ),
			)
		);
		hhd_admin_stat_card(
			array(
				'label'  => 'نظرات در انتظار',
				'value'  => $counts['reviews']['pending'],
				'hint'   => 'نیازمند تأیید',
				'status' => $counts['reviews']['pending'] ? 'attention' : '',
				'url'    => hhd_admin_link( 'hhd-reviews', array( 'status' => 'pending' ) ),
			)
		);
		hhd_admin_stat_card(
			array(
				'label'  => 'اعلان‌های خوانده‌نشده',
				'value'  => $counts['notifications_unread'],
				'hint'   => 'تازه',
				'status' => $counts['notifications_unread'] ? 'attention' : '',
				'url'    => hhd_admin_link( 'hhd-notifications' ),
				// Read and delete happen from the bell dropdown, which does not
				// reload the page — admin.js needs to find this card to keep it
				// in step with the badge.
				'key'    => 'notifications_unread',
			)
		);
		hhd_admin_stat_card(
			array(
				'label' => 'نظرات تأییدشده',
				'value' => $counts['reviews']['approved'],
				'hint'  => 'منتشرشده روی سایت',
				'url'   => hhd_admin_link( 'hhd-reviews', array( 'status' => 'approved' ) ),
			)
		);
		?>
	</div>

	<section class="hhd-panel">
		<h2 class="hhd-panel__title">آخرین درخواست‌ها</h2>
		<?php
		hhd_admin_table(
			hhd_admin_request_columns(),
			array_map( 'hhd_rest_prepare_request', $recent['items'] ),
			array(
				'id'          => 'hhd-dashboard-requests',
				'actions'     => hhd_admin_request_actions(),
				'empty_title' => 'هنوز درخواستی ثبت نشده است.',
				'endpoint'    => 'admin/requests',
			)
		);
		?>
	</section>

	<section class="hhd-panel">
		<h2 class="hhd-panel__title">نظرات در انتظار تأیید</h2>
		<?php
		hhd_admin_table(
			hhd_admin_review_columns(),
			array_map( 'hhd_rest_prepare_review', $pending['items'] ),
			array(
				'id'          => 'hhd-dashboard-reviews',
				'actions'     => hhd_admin_review_actions(),
				'empty_title' => 'نظر جدیدی در انتظار تأیید نیست.',
				'endpoint'    => 'admin/reviews',
			)
		);
		?>
	</section>

	<?php hhd_admin_modals(); ?>
	<?php
	hhd_admin_close();
}

/**
 * Contact requests inbox.
 *
 * @return void
 */
function hhd_screen_contact() {
	hhd_admin_render_request_screen( 'contact' );
}

/**
 * Consultation requests inbox.
 *
 * @return void
 */
function hhd_screen_consultation() {
	hhd_admin_render_request_screen( 'consultation' );
}

/**
 * Shared request inbox for both types.
 *
 * @param string $type contact | consultation.
 * @return void
 */
function hhd_admin_render_request_screen( $type ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html( 'دسترسی به این صفحه مجاز نیست.' ) );
	}

	$status  = hhd_admin_current_status( hhd_request_statuses() );
	$search  = hhd_admin_current_search();
	$page    = hhd_admin_current_page();
	$is_con  = ( 'consultation' === $type );
	$screen  = $is_con ? 'hhd-consultation' : 'hhd-contact';

	$result = hhd_requests_query(
		array(
			'type'     => $type,
			'status'   => $status,
			'search'   => $search,
			'page'     => $page,
			'per_page' => HHD_ADMIN_PER_PAGE,
		)
	);

	$status_options = array( '' => 'همه‌ی وضعیت‌ها' );
	foreach ( hhd_request_statuses() as $key ) {
		$status_options[ $key ] = hhd_request_status_label( $key );
	}

	hhd_admin_open(
		array(
			'title'    => $is_con ? 'درخواست‌های مشاوره' : 'درخواست‌های تماس',
			'subtitle' => $is_con
				? 'درخواست‌های مشاوره‌ی ثبت‌شده از فرم سایت.'
				: 'پیام‌های ثبت‌شده از فرم تماس سایت.',
			'actions'  => hhd_admin_notification_bell(),
		)
	);

	hhd_admin_filter_bar(
		array(
			'id'           => 'hhd-request-filters',
			'search_value' => $search,
			'selects'      => array(
				array(
					'name'    => 'status',
					'label'   => 'وضعیت',
					'value'   => $status,
					'options' => $status_options,
				),
			),
		)
	);

	hhd_admin_table(
		hhd_admin_request_columns(),
		array_map( 'hhd_rest_prepare_request', $result['items'] ),
		array(
			'id'          => 'hhd-requests-table',
			'actions'     => hhd_admin_request_actions(),
			'empty_title' => 'درخواستی با این فیلترها پیدا نشد.',
			'empty_text'  => 'فیلترها را تغییر دهید یا جست‌وجو را پاک کنید.',
			'total'       => $result['total'],
			'endpoint'    => 'admin/requests',
		)
	);

	// Both modals, not just the inspector: the row actions include delete, and
	// confirmAction() resolves false when it cannot find the confirm dialog —
	// which would make the delete button do nothing at all.
	hhd_admin_modals();
	hhd_admin_close();
}

/**
 * Review moderation.
 *
 * @return void
 */
function hhd_screen_reviews() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html( 'دسترسی به این صفحه مجاز نیست.' ) );
	}

	$status = hhd_admin_current_status( hhd_review_statuses() );
	$search = hhd_admin_current_search();
	$page   = hhd_admin_current_page();
	$target = isset( $_GET['project'] ) ? sanitize_text_field( wp_unslash( $_GET['project'] ) ) : '';

	$args = array(
		'status'   => $status,
		'search'   => $search,
		'page'     => $page,
		'per_page' => HHD_ADMIN_PER_PAGE,
	);

	if ( '' !== $target ) {
		$args['target'] = $target;
	}

	$result = hhd_reviews_query( $args );

	$status_options = array( '' => 'همه‌ی وضعیت‌ها' );
	foreach ( hhd_review_statuses() as $key ) {
		$status_options[ $key ] = hhd_review_status_label( $key );
	}

	$target_options = array( '' => 'همه‌ی پروژه‌ها' );
	foreach ( hhd_review_targets() as $ref => $label ) {
		$target_options[ $ref ] = $label;
	}

	hhd_admin_open(
		array(
			'title'    => 'نظرات',
			'subtitle' => 'نظرات تنها پس از تأیید روی سایت نمایش داده می‌شوند.',
			'actions'  => hhd_admin_notification_bell(),
		)
	);

	hhd_admin_filter_bar(
		array(
			'id'           => 'hhd-review-filters',
			'search_value' => $search,
			'selects'      => array(
				array(
					'name'    => 'status',
					'label'   => 'وضعیت',
					'value'   => $status,
					'options' => $status_options,
				),
				array(
					'name'    => 'project',
					'label'   => 'پروژه',
					'value'   => $target,
					'options' => $target_options,
				),
			),
		)
	);

	hhd_admin_table(
		hhd_admin_review_columns(),
		array_map( 'hhd_rest_prepare_review', $result['items'] ),
		array(
			'id'          => 'hhd-reviews-table',
			'actions'     => hhd_admin_review_actions(),
			'empty_title' => 'نظری با این فیلترها پیدا نشد.',
			'empty_text'  => 'فیلترها را تغییر دهید یا جست‌وجو را پاک کنید.',
			'total'       => $result['total'],
			'endpoint'    => 'admin/reviews',
		)
	);

	// See the note in hhd_admin_render_request_screen(): the confirm dialog
	// has to be on the page for the delete action to work.
	hhd_admin_modals();
	hhd_admin_close();
}

/**
 * Notification log.
 *
 * @return void
 */
function hhd_screen_notifications() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html( 'دسترسی به این صفحه مجاز نیست.' ) );
	}

	$page   = hhd_admin_current_page();
	$unread = isset( $_GET['unread'] ) && '1' === (string) $_GET['unread'];

	$result = hhd_notifications_query(
		array(
			'unread'   => $unread,
			'page'     => $page,
			'per_page' => HHD_ADMIN_PER_PAGE,
		)
	);

	hhd_admin_open(
		array(
			'title'    => 'اعلان‌ها',
			'subtitle' => 'هر درخواست یا نظر تازه، یک اعلان می‌سازد.',
			'actions'  => hhd_admin_notification_bell(),
		)
	);

	hhd_admin_filter_bar(
		array(
			'id'      => 'hhd-notification-filters',
			'selects' => array(
				array(
					'name'    => 'unread',
					'label'   => 'نمایش',
					'value'   => $unread ? '1' : '',
					'options' => array(
						''  => 'همه‌ی اعلان‌ها',
						'1' => 'فقط خوانده‌نشده‌ها',
					),
				),
			),
			'actions' => hhd_admin_button(
				array(
					'label' => 'همه را خوانده‌شده کن',
					'style' => 'ghost',
					'attrs' => 'data-hhd-read-all',
				)
			),
		)
	);
	?>
	<div class="hhd-notice-list" data-hhd-notice-list data-hhd-endpoint="admin/notifications">
		<?php if ( ! $result['items'] ) : ?>
			<?php hhd_admin_empty( 'اعلانی پیدا نشد.', 'با تغییر فیلترها دوباره تلاش کنید.' ); ?>
		<?php else : ?>
			<?php foreach ( $result['items'] as $notification ) : ?>
				<?php hhd_admin_notification_item( hhd_rest_prepare_notification( $notification ) ); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="hhd-table__foot" data-hhd-pagination
		data-hhd-total="<?php echo (int) $result['total']; ?>"
		data-hhd-endpoint="admin/notifications"></div>

	<?php hhd_admin_modals(); ?>
	<?php
	hhd_admin_close();
}

/* -------------------------------------------------------------------------
   4. Current-view helpers
   ------------------------------------------------------------------------- */

/**
 * Sanitized status filter from the query string.
 *
 * @param array<int,string> $allowed Allowed statuses.
 * @return string
 */
function hhd_admin_current_status( $allowed ) {
	$status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';

	return hhd_is_allowed( $status, $allowed ) ? $status : '';
}

/**
 * Sanitized search term from the query string.
 *
 * @return string
 */
function hhd_admin_current_search() {
	return isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
}

/**
 * Sanitized page number from the query string.
 *
 * @return int
 */
function hhd_admin_current_page() {
	$page = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;

	return max( 1, $page );
}
