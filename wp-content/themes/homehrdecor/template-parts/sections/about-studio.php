<?php
/**
 * About the designer — a personal-brand section.
 *
 * The statistics are demo placeholders for the layout and are labelled
 * as such in the markup, so they are never mistaken for verified facts.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_stats = hhd_stats();
?>
<section class="section section--surface" id="about">
	<div class="container container--wide">
		<div class="intro-grid">
			<div class="intro-grid__media reveal-media">
				<div class="media media--portrait">
					<?php
					echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'city-tehran',
						'نمای شهر تهران با کوه‌های البرز در پس‌زمینه',
						array( 'sizes' => '(max-width: 1080px) 100vw, 45vw' )
					);
					?>
				</div>
			</div>

			<div class="intro-grid__body">
				<?php
				echo hhd_eyebrow( 'درباره طراح' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<h2 class="about-name reveal">محمد مهدی رنجبران</h2>
				<p class="about-role reveal" style="--reveal-delay:60ms">طراح داخلی و مدیر خلاق homehrdecor</p>
				<p class="lead reveal" style="--reveal-delay:120ms">
					محمد مهدی رنجبران در homehrdecor تلاش می‌کند میان زیبایی‌شناسی، عملکرد و هویت شخصی هر پروژه تعادل ایجاد کند و برای هر فضا راهکاری متناسب با نیازهای آن ارائه دهد.
				</p>

				<div class="stats reveal" style="--reveal-delay:180ms">
					<?php foreach ( $hhd_stats as $hhd_stat ) : ?>
						<div class="stat">
							<span class="stat__value">
								<span data-count-to="<?php echo esc_attr( $hhd_stat['value'] ); ?>" dir="ltr"><?php echo esc_html( hhd_fa_num( $hhd_stat['value'] ) ); ?></span>
								<?php if ( ! empty( $hhd_stat['plus'] ) ) : ?>
									<span class="stat__plus" aria-hidden="true">+</span>
								<?php endif; ?>
							</span>
							<span class="stat__label"><?php echo esc_html( $hhd_stat['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="about-note reveal">
					اعداد بالا نمونه‌ی موقت برای چیدمان این بخش هستند و به‌عنوان آمار رسمی استودیو ارائه نمی‌شوند.
				</p>
			</div>
		</div>
	</div>
</section>
