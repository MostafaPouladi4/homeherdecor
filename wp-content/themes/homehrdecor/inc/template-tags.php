<?php
/**
 * Template tags — the presentation layer's public API.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Convert Latin digits in a string to Persian digits.
 *
 * @param string|int $value Value to convert.
 * @return string
 */
function hhd_fa_num( $value ) {
	$latin   = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	return str_replace( $latin, $persian, (string) $value );
}

/**
 * Convert Persian and Arabic-Indic digits back to Latin.
 *
 * The inverse of hhd_fa_num(), used on the way in rather than the way out.
 * A Persian keyboard produces ۰-۹ (and some layouts ٠-٩), which a tel: link,
 * a numeric comparison or an admin search cannot use, so input is
 * normalised before it is validated or stored.
 *
 * @param string $value Value to convert.
 * @return string
 */
function hhd_normalize_digits( $value ) {
	$persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	$arabic  = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
	$latin   = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );

	return str_replace( array_merge( $persian, $arabic ), array_merge( $latin, $latin ), (string) $value );
}

/**
 * Convert a Gregorian date to the Jalali calendar.
 *
 * The standard arithmetic conversion — a few lines of integer maths rather
 * than a dependency, since the dashboard and the review list both need to
 * show dates the way a Persian reader expects them.
 *
 * @param int $gy Gregorian year.
 * @param int $gm Gregorian month.
 * @param int $gd Gregorian day.
 * @return array<int, int> [year, month, day]
 */
function hhd_jalali( $gy, $gm, $gd ) {
	$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$gy2   = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;

	$days = 355666
		+ ( 365 * $gy )
		+ (int) ( ( $gy2 + 3 ) / 4 )
		- (int) ( ( $gy2 + 99 ) / 100 )
		+ (int) ( ( $gy2 + 399 ) / 400 )
		+ $gd
		+ $g_d_m[ $gm - 1 ];

	$jy    = -1595 + ( 33 * (int) ( $days / 12053 ) );
	$days %= 12053;
	$jy   += 4 * (int) ( $days / 1461 );
	$days %= 1461;

	if ( $days > 365 ) {
		$jy  += (int) ( ( $days - 1 ) / 365 );
		$days = ( $days - 1 ) % 365;
	}

	if ( $days < 186 ) {
		$jm = 1 + (int) ( $days / 31 );
		$jd = 1 + ( $days % 31 );
	} else {
		$jm = 7 + (int) ( ( $days - 186 ) / 30 );
		$jd = 1 + ( ( $days - 186 ) % 30 );
	}

	return array( $jy, $jm, $jd );
}

/**
 * Persian month names, in calendar order.
 *
 * @return array<int, string>
 */
function hhd_jalali_months() {
	return array( 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );
}

/**
 * Format a MySQL datetime as a Jalali date, optionally with the time.
 *
 * @param string $mysql    MySQL datetime.
 * @param bool   $with_time Include the clock time.
 * @return string
 */
function hhd_date_label( $mysql, $with_time = true ) {
	// The value is site-local time (see hhd_now()), so its components are
	// read directly — passing it through strtotime()/date() would apply the
	// server offset a second time and shift the date.
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2}))?/', (string) $mysql, $m ) ) {
		return '';
	}

	// The column default is the zero date. Nothing this theme writes leaves
	// it there, but a hand-edited row could, and the conversion below has no
	// meaning for year 0.
	if ( (int) $m[1] < 1 || (int) $m[2] < 1 || (int) $m[3] < 1 ) {
		return '';
	}

	list( $jy, $jm, $jd ) = hhd_jalali( (int) $m[1], (int) $m[2], (int) $m[3] );

	$months = hhd_jalali_months();
	$label  = sprintf(
		'%s %s %s',
		hhd_fa_num( $jd ),
		$months[ $jm - 1 ],
		hhd_fa_num( $jy )
	);

	if ( $with_time && isset( $m[4], $m[5] ) ) {
		$label .= ' — ' . hhd_fa_num( $m[4] . ':' . $m[5] );
	}

	return $label;
}

/**
 * Available widths for a generated image slug, read from disk once.
 *
 * @param string $slug Image slug.
 * @return array<int, int> Sorted, descending.
 */
function hhd_image_widths( $slug ) {
	static $cache = array();

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$dir     = get_template_directory() . '/assets/images/';
	$widths  = array();
	$matches = glob( $dir . $slug . '-*.jpg' );

	if ( $matches ) {
		foreach ( $matches as $file ) {
			if ( preg_match( '/-(\d+)\.jpg$/', $file, $m ) ) {
				$widths[] = (int) $m[1];
			}
		}
	}

	rsort( $widths );
	$cache[ $slug ] = $widths;
	return $widths;
}

/**
 * Render a responsive image from a generated slug.
 *
 * Emits a srcset built from the width set produced at build time, so the
 * browser always downloads the smallest usable file.
 *
 * @param string              $slug    Image slug, e.g. "proj-dining".
 * @param string              $alt     Alt text.
 * @param array<string,mixed> $args    Optional. sizes, class, loading, fetchpriority, decoding, decorative, attrs.
 * @return string Image markup, or an empty string when the slug is missing.
 */
function hhd_img( $slug, $alt = '', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'sizes'         => '(max-width: 900px) 100vw, 50vw',
			'class'         => '',
			'loading'       => 'lazy',
			'fetchpriority' => '',
			'decoding'      => 'async',
			'decorative'    => false,
			'attrs'         => array(),
		)
	);

	$widths = hhd_image_widths( $slug );
	if ( ! $widths ) {
		return '';
	}

	$uri      = get_template_directory_uri() . '/assets/images/';
	$largest  = $widths[0];
	$smallest = $widths[ count( $widths ) - 1 ];

	$srcset = array();
	foreach ( $widths as $width ) {
		$srcset[] = esc_url( $uri . $slug . '-' . $width . '.jpg' ) . ' ' . $width . 'w';
	}

	$dimensions = hhd_image_dimensions( $slug . '-' . $largest . '.jpg' );

	$attributes = array(
		'src'      => esc_url( $uri . $slug . '-' . $smallest . '.jpg' ),
		'srcset'   => implode( ', ', $srcset ),
		'sizes'    => $args['sizes'],
		'width'    => $dimensions ? $dimensions[0] : '',
		'height'   => $dimensions ? $dimensions[1] : '',
		'loading'  => $args['loading'],
		'decoding' => $args['decoding'],
	);

	if ( $args['decorative'] ) {
		$attributes['alt'] = '';
		$attributes['aria-hidden'] = 'true';
	} else {
		$attributes['alt'] = $alt;
	}

	if ( $args['fetchpriority'] ) {
		$attributes['fetchpriority'] = $args['fetchpriority'];
	}

	if ( $args['class'] ) {
		$attributes['class'] = $args['class'];
	}

	foreach ( (array) $args['attrs'] as $extra_name => $extra_value ) {
		$attributes[ sanitize_key( $extra_name ) ] = $extra_value;
	}

	$html = '<img';
	foreach ( $attributes as $name => $value ) {
		if ( '' === $value || null === $value ) {
			continue;
		}
		$html .= ' ' . $name . '="' . ( 'src' === $name || 'srcset' === $name ? $value : esc_attr( $value ) ) . '"';
	}
	$html .= '>';

	return $html;
}

/**
 * Pixel dimensions of a generated image file.
 *
 * @param string $basename File name inside assets/images/.
 * @return array<int, int>|null [width, height]
 */
function hhd_image_dimensions( $basename ) {
	static $cache = array();

	if ( array_key_exists( $basename, $cache ) ) {
		return $cache[ $basename ];
	}

	$path = get_template_directory() . '/assets/images/' . $basename;
	$size = file_exists( $path ) ? @getimagesize( $path ) : false;

	$cache[ $basename ] = $size ? array( (int) $size[0], (int) $size[1] ) : null;
	return $cache[ $basename ];
}

/**
 * URL of a brand asset.
 *
 * @param string $file File name inside assets/images/brand/.
 * @return string
 */
function hhd_brand_uri( $file ) {
	return get_template_directory_uri() . '/assets/images/brand/' . $file;
}

/**
 * The brand wordmark.
 *
 * The brand name is always written "homehrdecor" with no spaces, and is
 * kept LTR so it renders correctly inside the RTL layout.
 *
 * @param string $class Optional extra class.
 * @param string $tag   Wrapping tag.
 * @return string
 */
function hhd_wordmark( $class = '', $tag = 'span' ) {
	return sprintf(
		'<%1$s class="wordmark %2$s" dir="ltr" translate="no">homehrdecor</%1$s>',
		tag_escape( $tag ),
		esc_attr( $class )
	);
}

/**
 * Logo lockup: mark plus wordmark.
 *
 * @param array<string,mixed> $args Optional. variant, class, show_word, href.
 * @return string
 */
function hhd_logo( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'variant'   => 'color',   // color | knockout.
			'class'     => '',
			'show_word' => true,
			'tag'       => 'div',
		)
	);

	if ( 'knockout' === $args['variant'] ) {
		$src = hhd_brand_uri( 'homehrdecor-mark-knockout-light-512.png' );
	} else {
		$src = hhd_brand_uri( 'homehrdecor-mark-256.png' );
	}

	$html  = sprintf(
		'<%1$s class="logo logo--%2$s %3$s">',
		tag_escape( $args['tag'] ),
		esc_attr( $args['variant'] ),
		esc_attr( $args['class'] )
	);
	$html .= sprintf(
		'<img class="logo__mark" src="%s" width="44" height="44" alt="%s" decoding="async">',
		esc_url( $src ),
		esc_attr__( 'نشان homehrdecor', 'homehrdecor' )
	);

	if ( $args['show_word'] ) {
		$html .= '<span class="logo__text">' . hhd_wordmark() . '<span class="logo__tagline">استودیو طراحی داخلی</span></span>';
	}

	$html .= sprintf( '</%s>', tag_escape( $args['tag'] ) );

	return $html;
}

/**
 * Oversized, low-opacity brand watermark.
 *
 * @param array<string,mixed> $args Optional. class, size, variant, position.
 * @return string
 */
function hhd_watermark( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'   => '',
			'variant' => 'knockout',
			'alt'     => '',
		)
	);

	$file = 'knockout' === $args['variant']
		? 'homehrdecor-mark-knockout-light-512.png'
		: 'homehrdecor-mark-256.png';

	return sprintf(
		'<div class="watermark %s" aria-hidden="true"><img src="%s" alt="%s" loading="lazy" decoding="async"></div>',
		esc_attr( $args['class'] ),
		esc_url( hhd_brand_uri( $file ) ),
		esc_attr( $args['alt'] )
	);
}

/**
 * Directional arrow for links and buttons.
 *
 * In RTL, "forward" points left.
 *
 * @param string $class Optional extra class.
 * @return string
 */
function hhd_arrow( $class = '' ) {
	return sprintf(
		'<svg class="arrow %s" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M19 12H5"/><path d="M11 18l-6-6 6-6"/></svg>',
		esc_attr( $class )
	);
}

/**
 * Section eyebrow: a small label with a rule.
 *
 * @param string $text Label text.
 * @param string $class Optional extra class.
 * @return string
 */
function hhd_eyebrow( $text, $class = '' ) {
	return sprintf(
		'<p class="eyebrow %s"><span class="eyebrow__rule" aria-hidden="true"></span><span class="eyebrow__text">%s</span></p>',
		esc_attr( $class ),
		esc_html( $text )
	);
}

/**
 * Section heading block.
 *
 * @param array<string,mixed> $args title, eyebrow, text, align, level, class, id.
 * @return string
 */
function hhd_section_head( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => '',
			'title'   => '',
			'text'    => '',
			'align'   => 'start',
			'level'   => 'h2',
			'class'   => '',
			'id'      => '',
		)
	);

	$level = in_array( $args['level'], array( 'h1', 'h2', 'h3' ), true ) ? $args['level'] : 'h2';

	$html = sprintf(
		'<header class="section-head section-head--%s %s">',
		esc_attr( $args['align'] ),
		esc_attr( $args['class'] )
	);

	if ( $args['eyebrow'] ) {
		$html .= hhd_eyebrow( $args['eyebrow'] );
	}

	if ( $args['title'] ) {
		$html .= sprintf(
			'<%1$s class="section-head__title"%2$s>%3$s</%1$s>',
			$level,
			$args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '',
			wp_kses_post( $args['title'] )
		);
	}

	if ( $args['text'] ) {
		$html .= sprintf( '<p class="section-head__text">%s</p>', wp_kses_post( $args['text'] ) );
	}

	$html .= '</header>';

	return $html;
}

/**
 * Primary / secondary button.
 *
 * @param array<string,mixed> $args label, url, style, class, arrow, attrs.
 * @return string
 */
function hhd_button( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label' => '',
			'url'   => '#',
			'style' => 'solid',   // solid | outline | ghost | light.
			'class' => '',
			'arrow' => true,
			'attrs' => '',
		)
	);

	$html  = sprintf(
		'<a class="btn btn--%s %s" href="%s"%s>',
		esc_attr( $args['style'] ),
		esc_attr( $args['class'] ),
		esc_url( $args['url'] ),
		$args['attrs'] ? ' ' . $args['attrs'] : ''
	);
	$html .= sprintf( '<span class="btn__label">%s</span>', esc_html( $args['label'] ) );

	if ( $args['arrow'] ) {
		$html .= '<span class="btn__icon">' . hhd_arrow() . '</span>';
	}

	$html .= '</a>';

	return $html;
}

/**
 * Text link with a moving arrow.
 *
 * @param array<string,mixed> $args label, url, class.
 * @return string
 */
function hhd_link( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label' => '',
			'url'   => '#',
			'class' => '',
		)
	);

	return sprintf(
		'<a class="link-arrow %s" href="%s"><span>%s</span>%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $args['url'] ),
		esc_html( $args['label'] ),
		hhd_arrow()
	);
}

/**
 * Editorial project composition used across the archive and home page.
 *
 * @param array<string,mixed> $project Project array from hhd_projects().
 * @param array<string,mixed> $args    Optional. layout, sizes, class, index.
 * @return void
 */
function hhd_project_card( $project, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'layout' => 'portrait', // portrait | landscape | wide | tall.
			'sizes'  => '(max-width: 900px) 100vw, 50vw',
			'class'  => '',
			'index'  => 0,
		)
	);

	$url = hhd_project_url( $project['slug'] );
	?>
	<article class="project-card project-card--<?php echo esc_attr( $args['layout'] ); ?> <?php echo esc_attr( $args['class'] ); ?>"
		data-category="<?php echo esc_attr( $project['category'] ); ?>">
		<a class="project-card__link" href="<?php echo esc_url( $url ); ?>">
			<div class="project-card__media">
				<?php
				echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$project['image'],
					sprintf(
						/* translators: 1: project title, 2: category, 3: location */
						__( 'پروژه %1$s — %2$s در %3$s', 'homehrdecor' ),
						$project['title'],
						hhd_project_category_label( $project['category'] ),
						$project['location']
					),
					array(
						'sizes' => $args['sizes'],
						'class' => 'project-card__img',
					)
				);
				?>
				<span class="project-card__scrim" aria-hidden="true"></span>

				<span class="project-card__cta">
					<span><?php esc_html_e( 'مشاهده پروژه', 'homehrdecor' ); ?></span>
					<?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>

			<div class="project-card__body">
				<div class="project-card__meta">
					<span class="project-card__number" dir="ltr"><?php echo esc_html( $project['number'] ); ?></span>
					<span class="project-card__category"><?php echo esc_html( hhd_project_category_label( $project['category'] ) ); ?></span>
				</div>

				<h3 class="project-card__title"><?php echo esc_html( $project['title'] ); ?></h3>

				<p class="project-card__facts">
					<span><?php echo esc_html( $project['location'] ); ?></span>
					<span aria-hidden="true">—</span>
					<span><?php echo esc_html( $project['style'] ); ?></span>
					<span aria-hidden="true">—</span>
					<span><?php echo esc_html( $project['year'] ); ?></span>
				</p>
			</div>
		</a>
	</article>
	<?php
}

/**
 * Journal teaser used on the home page and archive.
 *
 * When no URL is supplied the card renders as a static placeholder — used
 * for the demo article set, which has no real post behind it yet.
 *
 * @param array<string,mixed> $args Post-like array: title, url, category, date, image, excerpt.
 * @param string              $layout portrait | row.
 * @return void
 */
function hhd_journal_card( $args, $layout = 'portrait' ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'    => '',
			'url'      => '',
			'category' => '',
			'date'     => '',
			'image'    => 'proj-detail',
			'excerpt'  => '',
		)
	);

	$hhd_is_link = (bool) $args['url'];
	$hhd_tag     = $hhd_is_link ? 'a' : 'div';
	?>
	<article class="journal-card journal-card--<?php echo esc_attr( $layout ); ?>">
		<<?php echo esc_attr( $hhd_tag ); ?> class="journal-card__link<?php echo $hhd_is_link ? '' : ' journal-card__link--static'; ?>"
			<?php if ( $hhd_is_link ) : ?>href="<?php echo esc_url( $args['url'] ); ?>"<?php endif; ?>>
			<div class="journal-card__media">
				<?php
				echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$args['image'],
					$args['title'],
					array(
						'sizes' => '(max-width: 900px) 100vw, 33vw',
						'class' => 'journal-card__img',
					)
				);
				?>
			</div>
			<div class="journal-card__body">
				<p class="journal-card__meta">
					<span class="journal-card__cat"><?php echo esc_html( $args['category'] ); ?></span>
					<span class="journal-card__date"><?php echo esc_html( $args['date'] ); ?></span>
				</p>
				<h3 class="journal-card__title"><?php echo esc_html( $args['title'] ); ?></h3>
				<p class="journal-card__excerpt"><?php echo esc_html( $args['excerpt'] ); ?></p>
				<span class="journal-card__more">
					<span><?php echo $hhd_is_link ? esc_html__( 'ادامه مطلب', 'homehrdecor' ) : esc_html__( 'نمونه‌ی چیدمانی', 'homehrdecor' ); ?></span>
					<?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>
		</<?php echo esc_attr( $hhd_tag ); ?>>
	</article>
	<?php
}

/**
 * Consultation call to action.
 *
 * @param array<string,mixed> $args title, text, class, compact.
 * @return void
 */
function hhd_cta( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'   => 'فضای بعدی شما از همین‌جا شروع می‌شود.',
			'text'    => 'اگر برای طراحی، بازسازی یا تغییر فضای خود به دنبال یک راهکار اختصاصی هستید، با ما در ارتباط باشید.',
			'class'   => '',
			'eyebrow' => 'شروع یک پروژه',
		)
	);
	?>
	<section class="cta <?php echo esc_attr( $args['class'] ); ?>">
		<?php echo hhd_watermark( array( 'class' => 'watermark--cta' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="container">
			<div class="cta__inner">
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => $args['eyebrow'],
						'title'   => $args['title'],
						'text'    => $args['text'],
						'align'   => 'center',
						'class'   => 'section-head--invert',
					)
				);
				?>
				<div class="cta__actions">
					<?php
					echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'درخواست مشاوره',
							'url'   => hhd_page_url( 'consultation' ),
							'style' => 'solid',
						)
					);
					echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'تماس با ما',
							'url'   => hhd_page_url( 'contact' ),
							'style' => 'light',
						)
					);
					?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Breadcrumb trail. Structure only — markup is ready for a plugin later.
 *
 * @param array<int, array<string,string>> $trail Items with label/url.
 * @return void
 */
function hhd_breadcrumb( $trail ) {
	if ( ! $trail ) {
		return;
	}
	?>
	<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'homehrdecor' ); ?>">
		<ol class="breadcrumb__list">
			<li class="breadcrumb__item">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'homehrdecor' ); ?></a>
			</li>
			<?php foreach ( $trail as $index => $item ) : ?>
				<li class="breadcrumb__item">
					<?php if ( ! empty( $item['url'] ) && $index < count( $trail ) - 1 ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * Page hero for interior pages (not the home hero).
 *
 * @param array<string,mixed> $args eyebrow, title, text, image, trail, size.
 * @return void
 */
function hhd_page_hero( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => '',
			'title'   => '',
			'text'    => '',
			'image'   => '',
			'trail'   => array(),
			'size'    => 'default',
		)
	);
	?>
	<section class="page-hero page-hero--<?php echo esc_attr( $args['size'] ); ?>">
		<?php if ( $args['image'] ) : ?>
			<div class="page-hero__media">
				<?php
				echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$args['image'],
					'',
					array(
						'sizes'      => '100vw',
						'class'      => 'page-hero__img',
						'loading'    => 'eager',
						'decorative' => true,
					)
				);
				?>
				<span class="page-hero__scrim" aria-hidden="true"></span>
			</div>
		<?php endif; ?>

		<div class="container page-hero__inner">
			<?php hhd_breadcrumb( $args['trail'] ); ?>
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => $args['eyebrow'],
					'title'   => $args['title'],
					'text'    => $args['text'],
					'level'   => 'h1',
					'class'   => 'section-head--invert',
				)
			);
			?>
		</div>
	</section>
	<?php
}
