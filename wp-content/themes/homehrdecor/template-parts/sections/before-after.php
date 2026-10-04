<?php
/**
 * Before / after comparison.
 *
 * The two images are real halves of one photographed kitchen; the slider
 * reveals the renovation. A real range input drives it, so it is
 * operable with a keyboard as well as by pointer.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_before = hhd_image_dimensions( 'compare-before.jpg' );
$hhd_after  = hhd_image_dimensions( 'compare-after.jpg' );
$hhd_uri    = get_template_directory_uri() . '/assets/images/';
?>
<section class="section section--surface" id="before-after">
	<div class="container container--wide">
		<div class="intro-grid intro-grid--reverse">
			<div class="intro-grid__media reveal-media">
				<div class="compare" data-compare>
					<div class="compare__pane compare__pane--before">
						<img src="<?php echo esc_url( $hhd_uri . 'compare-before.jpg' ); ?>"
							<?php if ( $hhd_before ) : ?>width="<?php echo esc_attr( (string) $hhd_before[0] ); ?>" height="<?php echo esc_attr( (string) $hhd_before[1] ); ?>"<?php endif; ?>
							alt="<?php esc_attr_e( 'آشپزخانه پیش از بازسازی، با کابینت‌های چوبی روشن و صفحه‌ی گرانیتی', 'homehrdecor' ); ?>"
							loading="lazy" decoding="async">
						<span class="compare__label compare__label--before">قبل</span>
					</div>

					<div class="compare__pane compare__pane--after">
						<img src="<?php echo esc_url( $hhd_uri . 'compare-after.jpg' ); ?>"
							<?php if ( $hhd_after ) : ?>width="<?php echo esc_attr( (string) $hhd_after[0] ); ?>" height="<?php echo esc_attr( (string) $hhd_after[1] ); ?>"<?php endif; ?>
							alt="<?php esc_attr_e( 'همان آشپزخانه پس از بازسازی، با کابینت‌های سفید براق و جزیره‌ی سنگ تیره', 'homehrdecor' ); ?>"
							loading="lazy" decoding="async">
						<span class="compare__label compare__label--after">بعد</span>
					</div>

					<input class="compare__range" type="range" min="0" max="100" value="50" step="1"
						aria-label="<?php esc_attr_e( 'مقایسه‌ی تصویر قبل و بعد از بازسازی', 'homehrdecor' ); ?>">

					<div class="compare__handle" aria-hidden="true">
						<span class="compare__grip">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" focusable="false">
								<path d="M9 6 4 12l5 6"></path>
								<path d="m15 6 5 6-5 6"></path>
							</svg>
						</span>
					</div>
				</div>
			</div>

			<div class="intro-grid__body">
				<?php
				echo hhd_eyebrow( 'قبل و بعد' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<h2 class="reveal">تغییر، همیشه نیاز به تخریب ندارد.</h2>
				<p class="lead reveal" style="--reveal-delay:90ms">
					این آشپزخانه بدون تغییر در سازه بازسازی شد. چیدمان بر پایه‌ی مثلث کاری ساماندهی شد، سطوح روشن جایگزین شدند و نور زیرکابینتی عمق فضا را تغییر داد. دستگیره را روی تصویر بکشید تا تفاوت را ببینید.
				</p>

				<ul class="meta-list reveal" style="--reveal-delay:180ms">
					<li class="meta-list__item">
						<span class="meta-list__label">نوع پروژه</span>
						<span class="meta-list__value">بازسازی آشپزخانه</span>
					</li>
					<li class="meta-list__item">
						<span class="meta-list__label">مدت اجرا</span>
						<span class="meta-list__value">۹ هفته</span>
					</li>
					<li class="meta-list__item">
						<span class="meta-list__label">تغییر سازه</span>
						<span class="meta-list__value">بدون تغییر</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>
