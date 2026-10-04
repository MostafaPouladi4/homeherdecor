<?php
/**
 * Admin UI components.
 *
 * The reusable pieces every dashboard screen is built from: page shell,
 * tables, status badges, filter bars, pagination, notification items,
 * modals and form fields. Nothing here queries the database or reads a
 * request — callers pass prepared rows in.
 *
 * Tables and lists are re-rendered from JSON by assets/js/admin.js after
 * filtering, so each component's column and action spec is emitted as a
 * data attribute. PHP owns the first paint, JavaScript owns every paint
 * after it, and both read the same spec.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Open a dashboard screen.
 *
 * wp-admin has no Persian locale installed on this site, so it renders
 * English and LTR. Rather than depend on a language pack, each screen
 * declares its own direction and ships Persian strings directly — the same
 * approach the front end already takes.
 *
 * @param array<string,mixed> $args title, subtitle, actions (pre-escaped HTML).
 * @return void
 */
function hhd_admin_open( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'    => '',
			'subtitle' => '',
			'actions'  => '',
		)
	);

	echo '<div class="hhd-admin" dir="rtl" lang="fa-IR">';
	echo '<div class="hhd-admin__inner">';

	if ( $args['title'] ) {
		echo '<header class="hhd-page-head">';
		echo '<div>';
		printf( '<h1 class="hhd-page-head__title">%s</h1>', esc_html( $args['title'] ) );

		if ( $args['subtitle'] ) {
			printf( '<p class="hhd-page-head__text">%s</p>', esc_html( $args['subtitle'] ) );
		}

		echo '</div>';

		if ( $args['actions'] ) {
			// Callers build this from hhd_admin_button(), which escapes.
			echo '<div class="hhd-page-head__actions">' . $args['actions'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</header>';
	}
}

/**
 * Close a dashboard screen.
 *
 * @return void
 */
function hhd_admin_close() {
	echo '</div></div>';
}

/**
 * A status pill.
 *
 * @param string $status Status key.
 * @param string $label  Display label.
 * @return string
 */
function hhd_admin_badge( $status, $label = '' ) {
	$status = sanitize_html_class( (string) $status );
	$label  = '' !== $label ? $label : $status;

	return sprintf(
		'<span class="hhd-badge hhd-badge--%s">%s</span>',
		esc_attr( $status ),
		esc_html( $label )
	);
}

/**
 * A dashboard counter card.
 *
 * `key` names a counter the REST layer also reports, and is emitted as
 * `data-hhd-stat` so admin.js can bring the card up to date after an action
 * without a full page reload. It is optional: a card without one is simply
 * not live-updated.
 *
 * @param array<string,mixed> $args label, value, hint, status, url, key.
 * @return void
 */
function hhd_admin_stat_card( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'  => '',
			'value'  => 0,
			'hint'   => '',
			'status' => '',
			'url'    => '',
			'key'    => '',
		)
	);

	$tag = $args['url'] ? 'a' : 'div';
	?>
	<<?php echo esc_attr( $tag ); ?> class="hhd-stat<?php echo $args['status'] ? ' hhd-stat--' . esc_attr( $args['status'] ) : ''; ?>"
		<?php if ( $args['url'] ) : ?>href="<?php echo esc_url( $args['url'] ); ?>"<?php endif; ?>>
		<span class="hhd-stat__label"><?php echo esc_html( $args['label'] ); ?></span>
		<span class="hhd-stat__value num"<?php if ( $args['key'] ) : ?> data-hhd-stat="<?php echo esc_attr( $args['key'] ); ?>"<?php endif; ?>><?php echo esc_html( hhd_fa_num( (int) $args['value'] ) ); ?></span>
		<?php if ( $args['hint'] ) : ?>
			<span class="hhd-stat__hint"><?php echo esc_html( $args['hint'] ); ?></span>
		<?php endif; ?>
	</<?php echo esc_attr( $tag ); ?>>
	<?php
}

/**
 * Empty state.
 *
 * @param string $title Title.
 * @param string $text  Supporting line.
 * @return void
 */
function hhd_admin_empty( $title, $text = '' ) {
	?>
	<div class="hhd-empty">
		<p class="hhd-empty__title"><?php echo esc_html( $title ); ?></p>
		<?php if ( $text ) : ?>
			<p class="hhd-empty__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Encode a spec for a data attribute.
 *
 * @param array<mixed> $spec Spec.
 * @return string
 */
function hhd_admin_spec( $spec ) {
	return esc_attr( (string) wp_json_encode( $spec ) );
}

/**
 * Render one table cell.
 *
 * Column types are shared with admin.js so a re-rendered row is identical
 * to the server-rendered one.
 *
 * @param array<string,mixed> $column Column spec.
 * @param array<string,mixed> $row    Row data.
 * @param array<int,mixed>    $actions Action spec for `actions` columns.
 * @return void
 */
function hhd_admin_cell( $column, $row, $actions = array() ) {
	// A column is identified by its field name, which the caller passes as the
	// array key. Guarded rather than read directly so a column spec that
	// forgot it renders empty instead of raising a warning per cell.
	$key   = isset( $column['key'] ) ? $column['key'] : '';
	$type  = isset( $column['type'] ) ? $column['type'] : 'text';
	$value = isset( $row[ $key ] ) ? $row[ $key ] : '';

	switch ( $type ) {
		case 'badge':
			$label = isset( $row[ $key . '_label' ] ) ? $row[ $key . '_label' ] : $value;
			echo hhd_admin_badge( $value, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;

		case 'ltr':
			printf( '<span dir="ltr">%s</span>', esc_html( $value ) );
			break;

		case 'strong':
			printf( '<strong>%s</strong>', esc_html( $value ) );
			break;

		case 'actions':
			echo '<div class="hhd-row-actions">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			foreach ( $actions as $action ) {
				printf(
					'<button type="button" class="hhd-btn hhd-btn--%s hhd-btn--sm" data-hhd-action="%s" data-hhd-status="%s" data-hhd-id="%d">%s</button>',
					esc_attr( isset( $action['style'] ) ? $action['style'] : 'ghost' ),
					esc_attr( $action['action'] ),
					esc_attr( isset( $action['status'] ) ? $action['status'] : '' ),
					(int) $row['id'],
					esc_html( $action['label'] )
				);
			}
			echo '</div>';
			break;

		default:
			echo esc_html( $value );
	}
}

/**
 * A data table.
 *
 * Passing `total` is what makes a table a paginated list: it gains a
 * pagination slot and a `data-hhd-list` marker, which is how admin.js knows
 * this is a table to drive from the API. A table without it is a preview —
 * the dashboard's "recent requests" panels show exactly the rows they were
 * handed and get neither.
 *
 * @param array<string,array<string,mixed>> $columns Column specs keyed by field.
 * @param array<int, array<string,mixed>>   $rows    Row data.
 * @param array<string,mixed>               $args    actions, empty_title, empty_text, id, total, endpoint.
 * @return void
 */
function hhd_admin_table( $columns, $rows, $args = array() ) {
	$is_list = isset( $args['total'] );

	$args = wp_parse_args(
		$args,
		array(
			'actions'     => array(),
			'empty_title' => 'موردی ثبت نشده است.',
			'empty_text'  => '',
			'id'          => 'hhd-table',
			'total'       => count( $rows ),
			'endpoint'    => '',
		)
	);

	// The spec admin.js re-renders from: same keys, same types, same order.
	$spec = array();
	foreach ( $columns as $key => $column ) {
		$spec[] = array(
			'key'   => $key,
			'label' => isset( $column['label'] ) ? $column['label'] : '',
			'type'  => isset( $column['type'] ) ? $column['type'] : 'text',
		);
	}
	?>
	<div class="hhd-table-wrap" id="<?php echo esc_attr( $args['id'] ); ?>"
		data-hhd-columns="<?php echo hhd_admin_spec( $spec ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
		data-hhd-actions="<?php echo hhd_admin_spec( $args['actions'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
		data-hhd-total="<?php echo (int) $args['total']; ?>"
		<?php if ( $is_list ) : ?>data-hhd-list<?php endif; ?>
		<?php if ( $args['endpoint'] ) : ?>data-hhd-endpoint="<?php echo esc_attr( $args['endpoint'] ); ?>"<?php endif; ?>>

		<table class="hhd-table">
			<thead>
				<tr>
					<?php foreach ( $columns as $column ) : ?>
						<th scope="col"><?php echo esc_html( isset( $column['label'] ) ? $column['label'] : '' ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody data-hhd-rows>
				<?php if ( ! $rows ) : ?>
					<tr class="hhd-table__empty-row">
						<td colspan="<?php echo (int) count( $columns ); ?>">
							<?php hhd_admin_empty( $args['empty_title'], $args['empty_text'] ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<tr data-hhd-id="<?php echo (int) $row['id']; ?>">
							<?php
							// The field name IS the array key, and that is how a column
							// is identified: hhd_admin_cell() reads the row by it, and
							// data-hhd-key is what the inspector and admin.js's
							// re-render look up. Iterating by value would drop it.
							foreach ( $columns as $hhd_key => $column ) :
								$column['key'] = $hhd_key;
								?>
								<td data-hhd-key="<?php echo esc_attr( $hhd_key ); ?>">
									<?php hhd_admin_cell( $column, $row, $args['actions'] ); ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php if ( $is_list ) : ?>
			<div class="hhd-table__foot" data-hhd-pagination></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Search box and select filters.
 *
 * @param array<string,mixed> $args id, search_label, search_value, selects.
 *                                  Each select: name, label, value, options (value => label).
 * @return void
 */
function hhd_admin_filter_bar( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'id'            => 'hhd-filters',
			'search_label'  => 'جست‌وجو',
			'search_value'  => '',
			'search_holder' => 'نام، شماره تماس، متن…',
			'selects'       => array(),
			'actions'       => '',
		)
	);
	?>
	<form class="hhd-filters" id="<?php echo esc_attr( $args['id'] ); ?>" data-hhd-filters onsubmit="return false;">
		<div class="hhd-filters__field hhd-filters__field--search">
			<label class="hhd-filters__label" for="<?php echo esc_attr( $args['id'] ); ?>-search">
				<?php echo esc_html( $args['search_label'] ); ?>
			</label>
			<input class="hhd-input" type="search" id="<?php echo esc_attr( $args['id'] ); ?>-search"
				data-hhd-search value="<?php echo esc_attr( $args['search_value'] ); ?>"
				placeholder="<?php echo esc_attr( $args['search_holder'] ); ?>">
		</div>

		<?php foreach ( $args['selects'] as $select ) : ?>
			<?php $select_id = $args['id'] . '-' . sanitize_html_class( $select['name'] ); ?>
			<div class="hhd-filters__field">
				<label class="hhd-filters__label" for="<?php echo esc_attr( $select_id ); ?>">
					<?php echo esc_html( $select['label'] ); ?>
				</label>
				<select class="hhd-select" id="<?php echo esc_attr( $select_id ); ?>"
					data-hhd-filter="<?php echo esc_attr( $select['name'] ); ?>">
					<?php foreach ( $select['options'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( (string) $select['value'], (string) $value ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endforeach; ?>

		<?php if ( $args['actions'] ) : ?>
			<div class="hhd-filters__actions">
				<?php echo $args['actions']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</form>
	<?php
}

/**
 * A button.
 *
 * `style` names a modifier class. The base `.hhd-btn` rule is already the
 * filled/solid treatment, so there is no `--solid` modifier and the default
 * here is the empty string: an unstyled button is the solid one.
 *
 * @param array<string,mixed> $args label, style, attrs.
 * @return string
 */
function hhd_admin_button( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label' => '',
			'style' => '',
			'attrs' => '',
		)
	);

	return sprintf(
		'<button type="button" class="hhd-btn%s"%s>%s</button>',
		$args['style'] ? ' hhd-btn--' . esc_attr( $args['style'] ) : '',
		$args['attrs'] ? ' ' . $args['attrs'] : '',
		esc_html( $args['label'] )
	);
}

/**
 * A modal dialog.
 *
 * `dismiss_only` renders a single dismiss button instead of the cancel/confirm
 * pair, and makes it a submit inside the `<form method="dialog">` that wraps
 * every modal — so the browser closes the dialog itself, with no script
 * involved. A reader must never be able to trap themselves in a dialog, and
 * that guarantee is worth more than the wiring.
 *
 * A confirm dialog keeps the pair: its outcome is a value the caller awaits,
 * and only script can settle that, so its buttons stay type="button".
 *
 * @param array<string,mixed> $args id, title, text, confirm_label, dismiss_only.
 * @return void
 */
function hhd_admin_modal( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'id'            => 'hhd-modal',
			'title'         => 'تأیید عملیات',
			'text'          => 'آیا از انجام این کار مطمئن هستید؟',
			'confirm_label' => 'بله، انجام بده',
			'dismiss_only'  => false,
		)
	);
	?>
	<dialog class="hhd-modal" id="<?php echo esc_attr( $args['id'] ); ?>" data-hhd-modal>
		<form method="dialog" class="hhd-modal__form">
			<h2 class="hhd-modal__title"><?php echo esc_html( $args['title'] ); ?></h2>
			<p class="hhd-modal__text" data-hhd-modal-text><?php echo esc_html( $args['text'] ); ?></p>
			<div class="hhd-modal__actions">
				<?php if ( $args['dismiss_only'] ) : ?>
					<button type="submit" class="hhd-btn hhd-btn--ghost" data-hhd-modal-cancel>
						<?php echo esc_html( $args['confirm_label'] ); ?>
					</button>
				<?php else : ?>
					<button type="button" class="hhd-btn hhd-btn--ghost" data-hhd-modal-cancel>انصراف</button>
					<button type="button" class="hhd-btn hhd-btn--danger" data-hhd-modal-confirm>
						<?php echo esc_html( $args['confirm_label'] ); ?>
					</button>
				<?php endif; ?>
			</div>
		</form>
	</dialog>
	<?php
}

/**
 * The two modals every screen carries: an item inspector and a
 * confirmation prompt. admin.js fills both in place.
 *
 * @return void
 */
function hhd_admin_modals() {
	hhd_admin_modal(
		array(
			'id'            => 'hhd-modal-view',
			'title'         => 'جزئیات',
			'text'          => '',
			'confirm_label' => 'بستن',
			// Read-only, so it gets one dismiss button and no confirm pair —
			// and no danger styling on the control that closes it.
			'dismiss_only'  => true,
		)
	);

	hhd_admin_modal(
		array(
			'id'            => 'hhd-modal-confirm',
			'title'         => 'تأیید عملیات',
			'text'          => 'آیا از انجام این کار مطمئن هستید؟',
			'confirm_label' => 'بله، انجام بده',
		)
	);
}

/**
 * A single notification row, used in the dropdown and the list screen.
 *
 * @param array<string,mixed> $notification Notification row.
 * @return void
 */
function hhd_admin_notification_item( $notification ) {
	$is_read = ! empty( $notification['is_read'] );
	?>
	<article class="hhd-notice<?php echo $is_read ? ' is-read' : ''; ?>" data-hhd-id="<?php echo (int) $notification['id']; ?>">
		<div class="hhd-notice__body">
			<p class="hhd-notice__title"><?php echo esc_html( $notification['title'] ); ?></p>
			<p class="hhd-notice__text"><?php echo esc_html( $notification['message'] ); ?></p>
			<?php
			$hhd_date = ! empty( $notification['created_label'] )
				? $notification['created_label']
				: ( ! empty( $notification['created_at'] ) ? hhd_date_label( $notification['created_at'] ) : '' );
			?>
			<?php if ( $hhd_date ) : ?>
				<p class="hhd-notice__date"><?php echo esc_html( $hhd_date ); ?></p>
			<?php endif; ?>
		</div>

		<div class="hhd-notice__actions">
			<?php if ( ! empty( $notification['url'] ) ) : ?>
				<a class="hhd-btn hhd-btn--ghost hhd-btn--sm" href="<?php echo esc_url( $notification['url'] ); ?>">مشاهده</a>
			<?php endif; ?>
			<button type="button" class="hhd-btn hhd-btn--ghost hhd-btn--sm" data-hhd-action="<?php echo $is_read ? 'unread' : 'read'; ?>" data-hhd-id="<?php echo (int) $notification['id']; ?>">
				<?php echo $is_read ? 'خوانده‌نشده' : 'خوانده شد'; ?>
			</button>
			<button type="button" class="hhd-btn hhd-btn--ghost hhd-btn--sm hhd-btn--danger" data-hhd-action="delete" data-hhd-id="<?php echo (int) $notification['id']; ?>">
				حذف
			</button>
		</div>
	</article>
	<?php
}
