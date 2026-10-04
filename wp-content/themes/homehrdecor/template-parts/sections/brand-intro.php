<?php
/**
 * Brand introduction — an editorial statement, deliberately not a
 * two-column WordPress block.
 *
 * @package homehrdecor
 */

defined('ABSPATH') || exit;
?>
<section class="section section--lg" id="brand-intro">
	<div class="container container--wide">
		<div class="intro-grid">
			<div class="intro-grid__media reveal-media">
				<div class="media media--portrait">
					<?php
					echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'proj-dining',
						'فضای غذاخوری با چوب گرم و نور طبیعی',
						array(
							'sizes' => '(max-width: 1080px) 100vw, 45vw',
							'class' => '',
						)
					);
					?>
				</div>
				<div class="intro-grid__aside">
					<?php
					echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'proj-detail',
						'جزئیات کشوهای چوبی با نور زیرکابینتی',
						array(
							'sizes' => '240px',
							'class' => '',
						)
					);
					?>
				</div>
			</div>

			<div class="intro-grid__body">
				<?php
				echo hhd_eyebrow('درباره استودیو'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<h2 class="reveal">ما فضا را فقط طراحی نمی‌کنیم؛ تجربه زندگی در آن را خلق می‌کنیم.</h2>
				<p class="lead reveal" style="--reveal-delay:90ms">
					در homehrdecor، طراحی داخلی ترکیبی از زیبایی، عملکرد و هویت است. هر پروژه از شناخت نیازهای انسان و
					ویژگی‌های فضا آغاز می‌شود و تا خلق جزئیاتی ادامه پیدا می‌کند که یک محیط معمولی را به فضایی ماندگار
					تبدیل می‌کنند.
				</p>

				<div class="intro-grid__signature reveal" style="--reveal-delay:180ms">
					<img src="<?php echo esc_url(hhd_brand_uri('homehrdecor-mark-128.png')); ?>" width="56" height="56"
						alt="" aria-hidden="true" loading="lazy" decoding="async">
					<span class="intro-grid__signature-text">
						<span class="intro-grid__signature-name">محمد مهدی رنجبران</span>
						<span class="intro-grid__signature-role">طراح داخلی و مدیر خلاق homehrdecor</span>
					</span>
				</div>

				<div class="reveal" style="--reveal-delay:240ms">
					<?php
					echo hhd_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'درباره استودیو',
							'url' => hhd_page_url('about'),
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>
</section>