<?php
/**
 * Global footer.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_footer_pages = array(
	'home'         => 'خانه',
	'projects'     => 'پروژه‌ها',
	'services'     => 'خدمات',
	'about'        => 'درباره ما',
	'journal'      => 'مجله',
	'contact'      => 'تماس با ما',
	'consultation' => 'درخواست مشاوره',
);

$hhd_footer_services = array_slice( hhd_services(), 0, 6 );
$hhd_contact         = hhd_contact();
?>
</main>

<footer class="site-footer">
	<?php echo hhd_watermark( array( 'class' => 'watermark--footer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="container container--wide site-footer__inner">
		<div class="site-footer__top">
			<div class="site-footer__brand">
				<?php echo hhd_logo( array( 'tag' => 'span' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="site-footer__about">
					استودیو طراحی داخلی و دکوراسیون؛ طراحی فضاهای مسکونی، تجاری و اداری با تمرکز بر زیبایی‌شناسی، عملکرد و جزئیات اجرایی.
				</p>
				<div class="site-footer__social">
					<a class="social-link" href="<?php echo esc_url( 'https://instagram.com/' . $hhd_contact['instagram'] ); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php esc_attr_e( 'اینستاگرام استودیو', 'homehrdecor' ); ?>">
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" focusable="false">
							<rect x="3" y="3" width="18" height="18" rx="5"></rect>
							<circle cx="12" cy="12" r="4"></circle>
							<circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"></circle>
						</svg>
						<span dir="ltr"><?php echo esc_html( $hhd_contact['instagram'] ); ?></span>
					</a>
				</div>
			</div>

			<nav aria-label="<?php esc_attr_e( 'پیوندهای فوتر', 'homehrdecor' ); ?>">
				<h2 class="site-footer__heading">صفحات</h2>
				<ul class="site-footer__list">
					<?php foreach ( $hhd_footer_pages as $hhd_slug => $hhd_label ) : ?>
						<li><a href="<?php echo esc_url( 'home' === $hhd_slug ? home_url( '/' ) : hhd_page_url( $hhd_slug ) ); ?>"><?php echo esc_html( $hhd_label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<nav aria-label="<?php esc_attr_e( 'خدمات', 'homehrdecor' ); ?>">
				<h2 class="site-footer__heading">خدمات</h2>
				<ul class="site-footer__list">
					<?php foreach ( $hhd_footer_services as $hhd_service ) : ?>
						<li><a href="<?php echo esc_url( hhd_service_url( $hhd_service['slug'] ) ); ?>"><?php echo esc_html( $hhd_service['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div>
				<h2 class="site-footer__heading">تماس</h2>
				<ul class="site-footer__list site-footer__contact">
					<li>
						<span>تلفن</span>
						<a href="tel:<?php echo esc_attr( $hhd_contact['phone_href'] ); ?>" dir="ltr"><?php echo esc_html( $hhd_contact['phone_label'] ); ?></a>
					</li>
					<li>
						<span>ایمیل</span>
						<a href="mailto:<?php echo esc_attr( $hhd_contact['email'] ); ?>" dir="ltr"><?php echo esc_html( $hhd_contact['email'] ); ?></a>
					</li>
					<li>
						<span>نشانی</span>
						<span><?php echo esc_html( $hhd_contact['address'] ); ?></span>
					</li>
					<li>
						<span>ساعات کاری</span>
						<span><?php echo esc_html( $hhd_contact['hours'] ); ?></span>
					</li>
				</ul>
			</div>
		</div>

		<div class="site-footer__bottom">
			<p class="site-footer__legal">
				<span>&copy; <?php echo esc_html( hhd_fa_num( wp_date( 'Y' ) ) ); ?></span>
				<span class="site-footer__wordmark" dir="ltr">homehrdecor</span>
				<span>— تمامی حقوق محفوظ است.</span>
			</p>
			<p>طراحی و اجرا: محمد مهدی رنجبران</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
