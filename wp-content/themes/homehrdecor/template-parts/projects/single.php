<?php
/**
 * Single project — an architectural case study.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_project = hhd_get_project( hhd_current_project_slug() );

if ( ! $hhd_project ) {
	get_template_part( '404' );
	return;
}

$hhd_related = array();
foreach ( hhd_projects() as $hhd_candidate ) {
	if ( $hhd_candidate['slug'] === hhd_current_project_slug() ) {
		continue;
	}
	if ( $hhd_candidate['category'] === $hhd_project['category'] ) {
		$hhd_related[] = $hhd_candidate;
	}
}
if ( count( $hhd_related ) < 3 ) {
	foreach ( hhd_projects() as $hhd_candidate ) {
		if ( $hhd_candidate['slug'] === hhd_current_project_slug() ) {
			continue;
		}
		foreach ( $hhd_related as $hhd_existing ) {
			if ( $hhd_existing['slug'] === $hhd_candidate['slug'] ) {
				continue 2;
			}
		}
		$hhd_related[] = $hhd_candidate;
		if ( count( $hhd_related ) >= 3 ) {
			break;
		}
	}
}
$hhd_related = array_slice( $hhd_related, 0, 3 );
?>
<article class="project-single">
	<header class="detail-hero">
		<div class="container container--wide">
			<div class="detail-hero__inner">
				<div>
					<?php
					hhd_breadcrumb(
						array(
							array(
								'label' => 'پروژه‌ها',
								'url'   => hhd_page_url( 'projects' ),
							),
							array( 'label' => $hhd_project['title'] ),
						)
					);
					echo hhd_eyebrow( hhd_project_category_label( $hhd_project['category'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<h1 class="detail-hero__title"><?php echo esc_html( $hhd_project['title'] ); ?></h1>
					<p class="detail-hero__text"><?php echo esc_html( $hhd_project['excerpt'] ); ?></p>
				</div>
			</div>

			<div class="detail-hero__media">
				<?php
				echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$hhd_project['image'],
					sprintf(
						/* translators: 1: project title, 2: location */
						__( 'نمایی از پروژه %1$s در %2$s', 'homehrdecor' ),
						$hhd_project['title'],
						$hhd_project['location']
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
			<dl class="meta-list">
				<div class="meta-list__item">
					<dt class="meta-list__label">نوع پروژه</dt>
					<dd class="meta-list__value"><?php echo esc_html( hhd_project_category_label( $hhd_project['category'] ) ); ?></dd>
				</div>
				<div class="meta-list__item">
					<dt class="meta-list__label">مکان</dt>
					<dd class="meta-list__value"><?php echo esc_html( $hhd_project['location'] ); ?></dd>
				</div>
				<div class="meta-list__item">
					<dt class="meta-list__label">سبک</dt>
					<dd class="meta-list__value"><?php echo esc_html( $hhd_project['style'] ); ?></dd>
				</div>
				<div class="meta-list__item">
					<dt class="meta-list__label">متراژ</dt>
					<dd class="meta-list__value"><?php echo esc_html( $hhd_project['area'] ); ?></dd>
				</div>
				<div class="meta-list__item">
					<dt class="meta-list__label">سال</dt>
					<dd class="meta-list__value"><?php echo esc_html( $hhd_project['year'] ); ?></dd>
				</div>
				<div class="meta-list__item">
					<dt class="meta-list__label">کارفرما</dt>
					<dd class="meta-list__value"><?php echo esc_html( $hhd_project['client'] ); ?></dd>
				</div>
			</dl>
		</div>
	</section>

	<section class="section section--flush-top">
		<div class="container container--wide">
			<div class="detail-body">
				<aside class="detail-body__aside">
					<?php
					echo hhd_eyebrow( 'معرفی پروژه' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<p class="detail-block__text"><?php echo esc_html( $hhd_project['intro'] ); ?></p>
					<?php
					echo hhd_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'درخواست مشاوره',
							'url'   => hhd_page_url( 'consultation' ),
						)
					);
					?>
				</aside>

				<div class="detail-body__content">
					<section class="detail-block">
						<h2 class="detail-block__title">چالش</h2>
						<p class="detail-block__text"><?php echo esc_html( $hhd_project['challenge'] ); ?></p>
					</section>

					<section class="detail-block">
						<h2 class="detail-block__title">کانسپت طراحی</h2>
						<p class="detail-block__text"><?php echo esc_html( $hhd_project['concept'] ); ?></p>
					</section>

					<section class="detail-block">
						<h2 class="detail-block__title">متریال و جزئیات</h2>
						<ul class="materials-list">
							<?php foreach ( $hhd_project['materials'] as $hhd_material ) : ?>
								<li class="material-tag"><?php echo esc_html( $hhd_material ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--flush-top">
		<div class="container container--wide">
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => 'گالری',
					'title'   => 'تصاویر پروژه',
				)
			);
			?>
			<div class="gallery-grid">
				<?php foreach ( $hhd_project['gallery'] as $hhd_image ) : ?>
					<figure class="gallery-grid__item reveal-media">
						<?php
						echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$hhd_image,
							sprintf(
								/* translators: %s: project title */
								__( 'تصویری از پروژه %s', 'homehrdecor' ),
								$hhd_project['title']
							),
							array( 'sizes' => '(max-width: 720px) 100vw, 60vw' )
						);
						?>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/projects/reviews', null, array( 'project' => $hhd_project ) ); ?>

	<?php if ( $hhd_related ) : ?>
		<section class="section section--surface">
			<div class="container container--wide">
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => 'ادامه بدهید',
						'title'   => 'پروژه‌های مرتبط',
					)
				);
				?>
				<div class="related-projects__grid">
					<?php foreach ( $hhd_related as $hhd_item ) : ?>
						<?php hhd_project_card( $hhd_item, array( 'layout' => 'portrait', 'sizes' => '(max-width: 720px) 100vw, 30vw' ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	hhd_cta(
		array(
			'eyebrow' => 'پروژه‌ی مشابه',
			'title'   => 'فضایی شبیه این برای خودتان می‌خواهید؟',
			'text'    => 'هر پروژه از یک گفت‌وگو شروع می‌شود. برای بررسی امکان‌سنجی و برآورد اولیه با ما در تماس باشید.',
		)
	);
	?>
</article>
