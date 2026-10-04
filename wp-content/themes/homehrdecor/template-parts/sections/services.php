<?php
/**
 * Services — an editorial list rather than a grid of icon cards.
 *
 * On wide screens a preview image follows the pointer; on small screens
 * each row shows its own summary line instead.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_services = hhd_services();
?>
<section class="section" id="services">
	<div class="container container--wide">
		<div class="services-intro">
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => 'خدمات',
					'title'   => 'خدمات ما',
					'text'    => 'از طراحی یک اتاق تا بازسازی کامل یک فضا؛ هر خدمت با فرآیندی مشخص و جزئیاتی قابل پیگیری انجام می‌شود.',
				)
			);
			?>
			<div class="services-intro__media reveal-media">
				<?php
				echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'proj-kitchen-grey',
					'آشپزخانه‌ای با کابینت‌های روشن و صفحه‌ی سنگی',
					array( 'sizes' => '(max-width: 1080px) 100vw, 45vw' )
				);
				?>
			</div>
		</div>

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
