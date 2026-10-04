<?php
/**
 * Global header.
 *
 * The header starts transparent over the hero and turns solid once the
 * page scrolls. Interior pages pass `hhd_header_solid` through the
 * template before this file is loaded.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_header_classes = array( 'site-header' );

if ( ! empty( $GLOBALS['hhd_header_solid'] ) ) {
	$hhd_header_classes[] = 'site-header--solid';
}

if ( ! empty( $GLOBALS['hhd_header_dark'] ) ) {
	$hhd_header_classes[] = 'site-header--dark';
}

$hhd_nav_items = array(
	'home'     => array(
		'label' => 'خانه',
		'url'   => home_url( '/' ),
	),
	'projects' => array(
		'label' => 'پروژه‌ها',
		'url'   => hhd_page_url( 'projects' ),
	),
	'services' => array(
		'label' => 'خدمات',
		'url'   => hhd_page_url( 'services' ),
	),
	'about'    => array(
		'label' => 'درباره ما',
		'url'   => hhd_page_url( 'about' ),
	),
	'journal'  => array(
		'label' => 'مجله',
		'url'   => hhd_page_url( 'journal' ),
	),
	'contact'  => array(
		'label' => 'تماس با ما',
		'url'   => hhd_page_url( 'contact' ),
	),
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="icon" href="<?php echo esc_url( hhd_brand_uri( 'homehrdecor-favicon-32.png' ) ); ?>" sizes="32x32">
	<link rel="apple-touch-icon" href="<?php echo esc_url( hhd_brand_uri( 'homehrdecor-favicon-180.png' ) ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'رفتن به محتوای اصلی', 'homehrdecor' ); ?></a>

<header class="<?php echo esc_attr( implode( ' ', $hhd_header_classes ) ); ?>">
	<div class="container container--wide site-header__inner">
		<div class="site-header__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'homehrdecor — صفحه اصلی', 'homehrdecor' ); ?>">
				<?php echo hhd_logo( array( 'tag' => 'span' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'homehrdecor' ); ?>">
			<ul class="site-nav__list">
				<?php foreach ( $hhd_nav_items as $hhd_key => $hhd_item ) : ?>
					<li class="menu-item<?php echo is_front_page() && 'home' === $hhd_key ? ' current-menu-item' : ''; ?>">
						<a href="<?php echo esc_url( $hhd_item['url'] ); ?>"><?php echo esc_html( $hhd_item['label'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="site-header__actions">
			<?php
			echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label' => 'درخواست مشاوره',
					'url'   => hhd_page_url( 'consultation' ),
					'style' => 'outline',
					'class' => 'btn--header',
				)
			);
			?>

			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav">
				<span class="nav-toggle__label"><?php esc_html_e( 'منو', 'homehrdecor' ); ?></span>
				<span class="nav-toggle__bars" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</div>
</header>

<div class="mobile-nav" id="mobile-nav" inert>
	<?php echo hhd_watermark( array( 'class' => 'mobile-nav__watermark' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<nav aria-label="<?php esc_attr_e( 'منوی موبایل', 'homehrdecor' ); ?>">
		<ul class="mobile-nav__list">
			<?php $hhd_index = 0; ?>
			<?php foreach ( $hhd_nav_items as $hhd_item ) : ?>
				<?php $hhd_index++; ?>
				<li>
					<a href="<?php echo esc_url( $hhd_item['url'] ); ?>">
						<span class="mobile-nav__index" dir="ltr"><?php echo esc_html( str_pad( (string) $hhd_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span><?php echo esc_html( $hhd_item['label'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<div class="mobile-nav__footer">
		<?php
		echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label' => 'درخواست مشاوره',
				'url'   => hhd_page_url( 'consultation' ),
				'style' => 'light',
			)
		);
		?>
		<div class="mobile-nav__contact">
			<span dir="ltr"><?php echo esc_html( hhd_contact()['phone_label'] ); ?></span>
			<span dir="ltr"><?php echo esc_html( hhd_contact()['email'] ); ?></span>
		</div>
	</div>
</div>

<main id="main" class="site-main">
