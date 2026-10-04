<?php
/**
 * Fallback template.
 *
 * WordPress requires index.php in every theme; in practice the archive
 * and singular templates below it handle every real request.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_solid'] = true;

get_header();

if ( have_posts() ) :
	?>
	<section class="section">
		<div class="container container--wide">
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => 'آرشیو',
					'title'   => is_search() ? 'نتایج جست‌وجو' : get_the_archive_title(),
				)
			);
			?>

			<div class="journal-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					hhd_journal_card(
						array(
							'title'    => get_the_title(),
							'url'      => get_permalink(),
							'category' => get_post_type() === 'post' ? 'مقاله' : 'برگه',
							'date'     => hhd_fa_num( get_the_date() ),
							'excerpt'  => wp_trim_words( get_the_excerpt(), 24 ),
							'image'    => 'proj-detail',
						)
					);
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'class'     => 'pagination',
					'prev_text' => 'جدیدتر',
					'next_text' => 'قدیمی‌تر',
				)
			);
			?>
		</div>
	</section>
	<?php
else :
	?>
	<section class="section">
		<div class="container container--narrow">
			<div class="empty-state">
				<p class="empty-state__title">چیزی برای نمایش نیست</p>
				<p>در این بخش هنوز محتوایی منتشر نشده است.</p>
				<?php
				echo hhd_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label' => 'بازگشت به خانه',
						'url'   => home_url( '/' ),
						'style' => 'outline',
					)
				);
				?>
			</div>
		</div>
	</section>
	<?php
endif;

get_footer();
