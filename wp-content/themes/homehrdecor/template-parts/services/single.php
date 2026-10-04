<?php
/**
 * Single service detail.
 *
 * Structure: hero → introduction → what we provide → process →
 * related projects → FAQ → consultation CTA.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_service = hhd_get_service( hhd_current_service_slug() );

if ( ! $hhd_service ) {
	get_template_part( '404' );
	return;
}

$hhd_all   = hhd_services();
$hhd_other = array();
foreach ( $hhd_all as $hhd_item ) {
	if ( $hhd_item['slug'] !== $hhd_service['slug'] ) {
		$hhd_other[] = $hhd_item;
	}
}

$hhd_featured_projects = array_slice( hhd_projects(), 0, 3 );
?>
<article class="service-single">
	<header class="detail-hero">
		<div class="container container--wide">
			<div class="detail-hero__inner">
				<div>
					<?php
					hhd_breadcrumb(
						array(
							array(
								'label' => 'خدمات',
								'url'   => hhd_page_url( 'services' ),
							),
							array( 'label' => $hhd_service['title'] ),
						)
					);
					echo hhd_eyebrow( 'خدمت ' . $hhd_service['number'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<h1 class="detail-hero__title"><?php echo esc_html( $hhd_service['title'] ); ?></h1>
					<p class="detail-hero__text"><?php echo esc_html( $hhd_service['summary'] ); ?></p>
				</div>
			</div>

			<div class="detail-hero__media">
				<?php
				echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$hhd_service['image'],
					sprintf(
						/* translators: %s: service title */
						__( 'نمونه‌ای از خدمت %s', 'homehrdecor' ),
						$hhd_service['title']
					),
					array(
						'sizes'         => '100vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			</div>
		</div>
	</header>

	<section class="section">
		<div class="container container--wide">
			<div class="detail-body">
				<aside class="detail-body__aside">
					<?php
					echo hhd_eyebrow( 'معرفی خدمت' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<p class="detail-block__text"><?php echo esc_html( $hhd_service['intro'] ); ?></p>
					<?php
					echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'درخواست مشاوره',
							'url'   => hhd_page_url( 'consultation' ),
							'style' => 'solid',
						)
					);
					?>
				</aside>

				<div class="detail-body__content">
					<section class="detail-block">
						<h2 class="detail-block__title">این خدمت شامل چه مواردی است؟</h2>
						<ul class="materials-list">
							<?php foreach ( $hhd_service['features'] as $hhd_feature ) : ?>
								<li class="material-tag"><?php echo esc_html( $hhd_feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>

					<section class="detail-block">
						<h2 class="detail-block__title">خدمات مرتبط</h2>
						<ul class="link-list">
							<?php foreach ( array_slice( $hhd_other, 0, 4 ) as $hhd_item ) : ?>
								<li>
									<a class="link-arrow" href="<?php echo esc_url( hhd_service_url( $hhd_item['slug'] ) ); ?>">
										<span><?php echo esc_html( $hhd_item['title'] ); ?></span>
										<?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				</div>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/sections/process' ); ?>

	<section class="section section--surface">
		<div class="container container--wide">
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => 'نمونه کارها',
					'title'   => 'پروژه‌های مرتبط',
					'text'    => 'چند نمونه از پروژه‌هایی که این خدمت در آن‌ها اجرا شده است.',
				)
			);
			?>
			<div class="related-projects__grid">
				<?php foreach ( $hhd_featured_projects as $hhd_project ) : ?>
					<?php hhd_project_card( $hhd_project, array( 'layout' => 'portrait', 'sizes' => '(max-width: 720px) 100vw, 30vw' ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container container--narrow">
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => 'پرسش‌های متداول',
					'title'   => 'سوال‌هایی که زیاد پرسیده می‌شود',
				)
			);
			?>
			<div class="accordion">
				<?php foreach ( hhd_faqs() as $hhd_index => $hhd_faq ) : ?>
					<?php $hhd_panel_id = 'faq-' . $hhd_service['slug'] . '-' . $hhd_index; ?>
					<div class="accordion__item">
						<h3>
							<button class="accordion__trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $hhd_panel_id ); ?>">
								<span><?php echo esc_html( $hhd_faq['q'] ); ?></span>
								<span class="accordion__icon" aria-hidden="true"></span>
							</button>
						</h3>
						<div class="accordion__panel" id="<?php echo esc_attr( $hhd_panel_id ); ?>">
							<div class="accordion__panel-inner"><?php echo esc_html( $hhd_faq['a'] ); ?></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	hhd_cta(
		array(
			'eyebrow' => 'شروع کنیم',
			'title'   => 'برای این خدمت آماده‌اید؟',
			'text'    => 'فرم درخواست مشاوره را پر کنید تا در کوتاه‌ترین زمان با شما تماس گرفته شود.',
		)
	);
	?>
</article>
