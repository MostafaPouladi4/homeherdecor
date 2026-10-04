<?php
/**
 * Home hero.
 *
 * A near-full-screen architectural composition: one dominant interior,
 * a second portrait image for editorial contrast, the brand mark, an
 * oversized ghost watermark and a metadata rail.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__media">
		<?php
		echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'hero-interior',
			'',
			array(
				'sizes'         => '100vw',
				'class'         => 'hero__img',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decorative'    => true,
			)
		);
		?>
		<span class="hero__scrim" aria-hidden="true"></span>
	</div>

	<?php echo hhd_watermark( array( 'class' => 'watermark--hero', 'variant' => 'knockout' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="container container--wide hero__inner">
		<div class="hero__content">
			<img class="hero__logo" src="<?php echo esc_url( hhd_brand_uri( 'homehrdecor-mark-knockout-light-512.png' ) ); ?>" width="62" height="62" alt="<?php esc_attr_e( 'نشان homehrdecor', 'homehrdecor' ); ?>" decoding="async">

			<p class="hero__eyebrow">
				<?php echo hhd_wordmark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span aria-hidden="true"></span>
				<span>استودیو طراحی داخلی</span>
			</p>

			<h1 class="hero__title" id="hero-title">
				فضاهایی برای زندگی،<br>
				<em>طراحی‌شده برای شما</em>
			</h1>

			<p class="hero__text">
				طراحی داخلی، بازسازی و خلق فضاهایی منحصربه‌فرد با نگاهی متفاوت به معماری و زندگی.
			</p>

			<div class="hero__actions">
				<?php
				echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label' => 'مشاهده پروژه‌ها',
						'url'   => hhd_page_url( 'projects' ),
						'style' => 'solid',
						'class' => 'btn--on-dark',
					)
				);
				echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label' => 'درخواست مشاوره',
						'url'   => hhd_page_url( 'consultation' ),
						'style' => 'light',
					)
				);
				?>
			</div>
		</div>

		<figure class="hero__aside">
			<?php
			echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'hero-facade',
				'نمای شبانه‌ی یک ساختمان با تیغه‌های چوبی عمودی و نور گرم',
				array(
					'sizes' => '(max-width: 1080px) 0px, 15vw',
					'class' => 'hero__aside-img',
				)
			);
			?>
			<figcaption class="hero__aside-caption">
				نمای شبانه — نور و ریتم
			</figcaption>
		</figure>
	</div>

	<div class="container container--wide hero__foot">
		<a class="hero__scroll" href="#brand-intro">
			<span class="hero__scroll-line" aria-hidden="true"></span>
			<span>اسکرول کنید</span>
		</a>

		<dl class="hero__meta">
			<div class="hero__meta-item">
				<dt class="hero__meta-label">حوزه فعالیت</dt>
				<dd class="hero__meta-value">مسکونی — تجاری — اداری</dd>
			</div>
			<div class="hero__meta-item">
				<dt class="hero__meta-label">محدوده خدمات</dt>
				<dd class="hero__meta-value">اصفهان و سراسر ایران</dd>
			</div>
			<div class="hero__meta-item">
				<dt class="hero__meta-label">خدمات</dt>
				<dd class="hero__meta-value">طراحی — بازسازی — نظارت اجرا</dd>
			</div>
		</dl>
	</div>
</section>
