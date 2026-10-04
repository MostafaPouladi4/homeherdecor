<?php
/**
 * Projects archive and single project case study.
 *
 * Both views are served by this template: /projects/ lists the work,
 * /projects/<slug>/ renders the case study. When projects move to a
 * custom post type, only the routing in inc/routing.php changes.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

// Header state has to be decided before get_header() renders it.
$hhd_is_single               = hhd_is_project_single();
$GLOBALS['hhd_header_solid'] = true;
$GLOBALS['hhd_header_dark']  = $hhd_is_single;

get_header();

if ( $hhd_is_single ) {
	get_template_part( 'template-parts/projects/single' );
} else {
	?>

	<?php
	hhd_page_hero(
		array(
			'eyebrow' => 'نمونه کارها',
			'title'   => 'پروژه‌ها',
			'text'    => 'مجموعه‌ای از فضاهای مسکونی، تجاری و اداری که در استودیو طراحی شده‌اند. برای دیدن جزئیات هر پروژه، روی آن بزنید.',
			'image'   => 'proj-kitchen-grey',
			'trail'   => array(
				array( 'label' => 'پروژه‌ها' ),
			),
		)
	);
	?>

	<section class="section">
		<div class="container container--wide">
			<div class="archive-head">
				<div>
					<h2 class="h3"><?php esc_html_e( 'همه پروژه‌ها', 'homehrdecor' ); ?></h2>
					<p class="archive-count">
						<span data-filter-count><?php echo esc_html( hhd_fa_num( count( hhd_projects() ) ) ); ?></span>
						<?php esc_html_e( 'پروژه', 'homehrdecor' ); ?>
					</p>
				</div>

				<div class="filter-bar" data-filters role="group" aria-label="<?php esc_attr_e( 'فیلتر پروژه‌ها بر اساس دسته', 'homehrdecor' ); ?>">
					<?php foreach ( hhd_project_categories() as $hhd_key => $hhd_label ) : ?>
						<button class="filter-btn" type="button"
							data-filter="<?php echo esc_attr( $hhd_key ); ?>"
							aria-pressed="<?php echo 'all' === $hhd_key ? 'true' : 'false'; ?>">
							<?php echo esc_html( $hhd_label ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="projects-grid">
				<?php
				$hhd_layouts = array( 'landscape', 'portrait', 'portrait', 'landscape' );
				foreach ( hhd_projects() as $hhd_index => $hhd_project ) :
					hhd_project_card(
						$hhd_project,
						array(
							'layout' => $hhd_layouts[ $hhd_index % 4 ],
							'class'  => 'reveal',
							'sizes'  => '(max-width: 720px) 100vw, (max-width: 1080px) 50vw, 40vw',
						)
					);
				endforeach;
				?>
			</div>

			<div class="empty-state" data-filter-empty hidden>
				<p class="empty-state__title"><?php esc_html_e( 'به‌زودی', 'homehrdecor' ); ?></p>
				<p><?php esc_html_e( 'در این دسته هنوز پروژه‌ای منتشر نشده است.', 'homehrdecor' ); ?></p>
			</div>
		</div>
	</section>

	<?php
	hhd_cta(
		array(
			'eyebrow' => 'پروژه شما',
			'title'   => 'پروژه‌ی شما می‌تواند مورد بعدی باشد.',
			'text'    => 'برای گفت‌وگو درباره فضای خودتان و بررسی امکان‌سنجی پروژه، با استودیو در تماس باشید.',
		)
	);
	?>

	<?php
}

get_footer();
