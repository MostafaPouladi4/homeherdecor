<?php
/**
 * Services archive and single service detail.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_is_single               = hhd_is_service_single();
$GLOBALS['hhd_header_solid'] = true;
$GLOBALS['hhd_header_dark']  = $hhd_is_single;

get_header();

if ( $hhd_is_single ) {
	get_template_part( 'template-parts/services/single' );
} else {
	$hhd_services = hhd_services();
	?>

	<?php
	hhd_page_hero(
		array(
			'eyebrow' => 'خدمات استودیو',
			'title'   => 'خدمات ما',
			'text'    => 'از طراحی داخلی و بازسازی تا طراحی سه‌بعدی و انتخاب متریال؛ هر خدمت با فرآیندی روشن و خروجی قابل تحویل ارائه می‌شود.',
			'image'   => 'hero-interior',
			'trail'   => array(
				array( 'label' => 'خدمات' ),
			),
		)
	);
	?>

	<section class="section">
		<div class="container container--wide">
			<div class="service-list" data-service-list>
				<?php foreach ( $hhd_services as $hhd_index => $hhd_service ) : ?>
					<div class="service-row reveal" data-preview="<?php echo esc_attr( $hhd_service['slug'] ); ?>" style="--reveal-delay:<?php echo esc_attr( (string) ( $hhd_index * 45 ) ); ?>ms">
						<a class="service-row__link" href="<?php echo esc_url( hhd_service_url( $hhd_service['slug'] ) ); ?>">
							<span class="service-row__number"><?php echo esc_html( $hhd_service['number'] ); ?></span>
							<span class="service-row__title"><?php echo esc_html( $hhd_service['title'] ); ?></span>
							<span class="service-row__summary"><?php echo esc_html( $hhd_service['summary'] ); ?></span>
							<span class="service-row__arrow" aria-hidden="true"><?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<div class="service-preview" aria-hidden="true">
		<?php foreach ( $hhd_services as $hhd_service ) : ?>
			<?php
			echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$hhd_service['image'],
				'',
				array(
					'sizes'      => '340px',
					'decorative' => true,
					'attrs'      => array( 'data-key' => $hhd_service['slug'] ),
				)
			);
			?>
		<?php endforeach; ?>
	</div>

	<?php get_template_part( 'template-parts/sections/process' ); ?>

	<?php
	hhd_cta(
		array(
			'eyebrow' => 'شروع کنیم',
			'title'   => 'نمی‌دانید کدام خدمت مناسب شماست؟',
			'text'    => 'در یک جلسه‌ی کوتاه، نیاز پروژه شما بررسی می‌شود و مسیر مناسب پیشنهاد می‌گردد.',
		)
	);
	?>

	<?php
}

get_footer();
