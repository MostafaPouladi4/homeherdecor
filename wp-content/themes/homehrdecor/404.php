<?php
/**
 * 404 — page not found.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_dark'] = true;

get_header();
?>

<section class="error-page">
	<?php echo hhd_watermark( array( 'class' => 'watermark--philosophy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="container container--wide">
		<p class="error-page__code" dir="ltr" aria-hidden="true">۴۰۴</p>

		<div class="section-head section-head--invert">
			<?php echo hhd_eyebrow( 'صفحه پیدا نشد' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h1 class="section-head__title">این صفحه در دسترس نیست.</h1>
			<p class="section-head__text">ممکن است نشانی تغییر کرده باشد یا صفحه حذف شده باشد. از مسیرهای زیر می‌توانید ادامه دهید.</p>
		</div>

		<div class="cta__actions" style="justify-content:flex-start">
			<?php
			echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label' => 'بازگشت به خانه',
					'url'   => home_url( '/' ),
					'style' => 'solid',
				)
			);
			echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label' => 'مشاهده پروژه‌ها',
					'url'   => hhd_page_url( 'projects' ),
					'style' => 'light',
				)
			);
			?>
		</div>

		<div class="contact-list" style="margin-block-start:var(--space-2xl); max-width:520px">
			<div class="contact-item">
				<span class="contact-item__label">جست‌وجو در سایت</span>
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
